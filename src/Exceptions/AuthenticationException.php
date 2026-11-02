<?php

namespace Okolaa\TermiiPHP\Exceptions;

use Okolaa\TermiiPHP\RequestException;

/**
 * 401 Unauthorized: the API key is missing or invalid.
 */
class AuthenticationException extends RequestException
{
}
