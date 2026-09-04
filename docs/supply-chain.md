# Supply chain artifacts

Every tagged release of `okolaa/termiiphp` publishes a CycloneDX SBOM, a
source archive, SLSA build provenance, and Sigstore signatures. This
document says what those are, how to verify them, and — the part worth
reading before you cite any of it in a compliance document — what they do
not give you.

## What each release publishes

| Asset | What it is |
| ----- | ---------- |
| `termii-php-<version>.cdx.json` | CycloneDX 1.6 SBOM of the **production** dependency tree |
| `termii-php-<version>.tar.gz` | Source archive of the tagged tree |
| `checksums.txt` | SHA-256 of the two above |
| `*.sig` / `*.pem` | Detached Sigstore signature and signing certificate for each asset |

In addition, two attestations are stored in GitHub's attestation API rather
than as release assets:

- **SLSA build provenance** for the source archive — which workflow, at
  which commit, on which runner, produced it
- **An SBOM attestation** binding the SBOM to that same archive

## Verifying

### With the GitHub CLI

```bash
gh attestation verify termii-php-1.0.0.tar.gz --repo Douglasokolaa/termii-php
```

This checks the provenance and the SBOM attestation together, and is the
shortest path if you already have `gh`.

### With cosign

```bash
cosign verify-blob \
  --certificate      termii-php-1.0.0.cdx.json.pem \
  --signature        termii-php-1.0.0.cdx.json.sig \
  --certificate-identity-regexp '^https://github\.com/Douglasokolaa/termii-php/\.github/workflows/release\.yml@refs/tags/v.+$' \
  --certificate-oidc-issuer https://token.actions.githubusercontent.com \
  termii-php-1.0.0.cdx.json
```

Signing is keyless: there is no long-lived private key to steal, and no
public key for you to pin. Trust is anchored on the certificate identity
above — the release workflow, in this repository, running on a tag. If you
pin anything, pin that identity regex.

## Using the SBOM

The SBOM is CycloneDX 1.6 JSON with `pkg:composer/...` PURLs, which is what
Dependency-Track, Grype, Trivy and OWASP dependency-check all consume
directly.

```bash
# Continuous monitoring
curl -X POST https://your-dependency-track/api/v1/bom \
  -H "X-Api-Key: $DT_API_KEY" \
  -F "project=$PROJECT_UUID" \
  -F "bom=@termii-php-1.0.0.cdx.json"

# One-shot scan
grype sbom:termii-php-1.0.0.cdx.json
```

If you are assembling an SBOM for a product that includes this package,
merge this document into yours rather than re-deriving the tree — it is
generated from the lock file at release time and carries resolved versions,
licences and PURLs already.

## Limitations

Read this before relying on any of the above.

**Composer does not verify signatures.** `composer require okolaa/termiiphp`
performs no signature or attestation check. There is no PHP equivalent of
Sigstore-aware install tooling in general use. Everything here supports
**audit after the fact and SBOM assembly**; none of it establishes
install-time trust. Anyone claiming a signed Composer supply chain today is
overselling it.

**The signed archive is not what Composer installs.** Packagist resolves
`okolaa/termiiphp` to a GitHub-generated archive or a git clone, neither of
which is the `termii-php-<version>.tar.gz` signed here. Provenance covers the
artifact this workflow built, and you can compare its contents against what
you installed, but the bytes Composer fetched are not the bytes signed.

**The SBOM describes this repository's resolved tree, not yours.** This is a
library. Composer resolves dependency versions against the *consuming*
project's constraints, so the versions your application ends up with may
differ from the ones in this SBOM. Treat it as an authoritative statement of
*which* packages this library pulls in and under what constraints — and
generate your own SBOM from your own lock file for the versions you actually
ship. Under Annex I, Part II(1) of the CRA, the SBOM you owe is the one for
your product, not the one your upstream published.

**Dev dependencies are excluded.** The SBOM is generated with `--omit=dev`.
The test and tooling tree is not part of what you install and is
deliberately absent.

**One maintainer, no HSM, no air gap.** Signing happens in GitHub Actions
using an OIDC token. If GitHub Actions is compromised, so is this. That
is the same trust boundary as almost every other keyless-signed open-source
project, and it is worth naming rather than implying something stronger.

## Reproducing the SBOM

```bash
composer install
composer CycloneDX:make-sbom \
  --output-format=JSON --spec-version=1.6 \
  --omit=dev --omit=plugin \
  --validate --output-reproducible \
  --mc-version=1.0.0 \
  --output-file=sbom.cdx.json
```

`--output-reproducible` strips timestamps and random values, so the same
tag and the same lock file produce a byte-identical SBOM. If yours differs
from the published one, something moved that should not have.
