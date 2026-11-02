<?php

namespace Okolaa\TermiiPHP\Exceptions;

use Okolaa\TermiiPHP\RequestException;

/**
 * 403 Forbidden: the API key may not perform this request.
 */
class PermissionException extends RequestException
{
}
