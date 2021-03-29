<?php declare(strict_types=1);

namespace Noriel\SSO\Auth\Exception;

use InvalidArgumentException;

class InvalidTokenException extends InvalidArgumentException implements ThirdPartyExceptionInterface
{
}
