# Security Policy

## Supported versions

| Version | Supported |
| ------- | --------- |
| 1.x     | ✅ Security fixes |
| < 1.0   | ❌ Not supported |

Only the latest minor release of a supported major line receives security
fixes. If you are pinned to an older minor, expect to upgrade within that
major line to receive a patch.

## Reporting a vulnerability

**Do not open a public issue for a security vulnerability.**

Report privately via GitHub Security Advisories:
[**Report a vulnerability**](https://github.com/Douglasokolaa/termii-php/security/advisories/new)

If GitHub advisories are unavailable to you, email
**okolaadouglas@gmail.com** with `[SECURITY] termii-php` in the subject.

Please include, as far as you can establish it:

- Affected version(s) and the `composer.lock` entry for `okolaa/termiiphp`
- A description of the flaw and the security impact you believe it has
- Reproduction steps or a proof of concept
- Any known mitigation or workaround

## Response commitments

This package is maintained by one person. These windows are what I can
actually meet, not aspirational figures:

| Stage | Target |
| ----- | ------ |
| Acknowledgement of your report | 3 business days |
| Initial assessment and severity call | 10 calendar days |
| Fix, or a dated mitigation plan | 90 calendar days from acknowledgement |

If I am going to miss one of these, I will tell you before the deadline
rather than after it.

## Coordinated disclosure

Default embargo is **90 days** from acknowledgement, or until a fix is
released, whichever comes first. I will negotiate a longer embargo where
a fix is genuinely complex, and a shorter one where the flaw is already
public or under active exploitation.

On release of a fix I will:

1. Publish a GitHub Security Advisory with a CVE where the flaw warrants one
2. Note the advisory in the release notes and `CHANGELOG`
3. Credit you by the name you ask for, or keep you anonymous — your call

I will not ask you to sign an NDA, and there is no bug bounty.

## Scope

**In scope** — anything in `src/` shipped to Packagist as `okolaa/termiiphp`:
credential handling in `TermiiAuthenticator`, request construction, response
parsing and DTO hydration, and any dependency this package forces into a
consumer's production tree.

**Out of scope** — the Termii API itself (report those to Termii), issues
that only occur under modified vendor code, and vulnerabilities in a
consumer's own application that merely surface through this SDK.

## Relationship to the EU Cyber Resilience Act

Read this before citing it in a compliance questionnaire.

I am a natural person publishing free and open-source software outside the
course of a commercial activity. Under Regulation (EU) 2024/2847 I am
therefore **not a "manufacturer"**, and — because an open-source software
steward must be a *legal* person — **not an "open-source software steward"**
either. No CRA obligation binds this package or me directly. The Article 14
reporting duty that starts on **11 September 2026** is a manufacturer's
duty, not mine.

What this policy is for is the party that *is* in scope: if you integrate
this package into a commercial product placed on the EU market, you are the
manufacturer, and you carry the due-diligence, SBOM and vulnerability-handling
obligations for the components you ship — this one included.

So this repository is run to make your compliance cheaper, voluntarily:

- A CycloneDX SBOM is published with every release, so you can fold this
  package's dependency tree into the SBOM you owe under Annex I, Part II(1)
- Releases carry SLSA provenance and Sigstore signatures, so you can
  evidence where the artifact came from
- The response windows above are published and dated, so you can point to a
  real upstream process in your own vulnerability-handling documentation

See [`docs/supply-chain.md`](docs/supply-chain.md) for how to verify all of
that, and for the limitations you should know about before you rely on it.
[`docs/vulnerability-handling.md`](docs/vulnerability-handling.md) is the
operational runbook behind the windows above.
