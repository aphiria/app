<?php

declare(strict_types=1);

namespace App\Demo\Auth;

use Exception;

/**
 * Defines the exception that's thrown when credentials are invalid
 *
 * TODO: Should this be moved into Aphiria?
 */
final class InvalidCredentialsException extends Exception
{
}
