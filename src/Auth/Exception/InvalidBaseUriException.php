<?php declare(strict_types=1);

namespace Noriel\SSO\Auth\Exception;

use InvalidArgumentException;

class InvalidBaseUriException extends InvalidArgumentException implements ThirdPartyExceptionInterface
{
}
