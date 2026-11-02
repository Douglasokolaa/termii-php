<?php

namespace Okolaa\TermiiPHP\Exceptions;

use Okolaa\TermiiPHP\RequestException;

/**
 * 400 Bad Request or 422 Unprocessable Entity: Termii rejected the request data.
 */
class ValidationException extends RequestException
{
}
