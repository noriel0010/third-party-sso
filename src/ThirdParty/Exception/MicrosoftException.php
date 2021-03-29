<?php declare(strict_types=1);

namespace Noriel\SSO\ThirdParty\Exception;

use RuntimeException;
use Noriel\SSO\Auth\Exception\ThirdPartyExceptionInterface;

class MicrosoftException extends RuntimeException implements ThirdPartyExceptionInterface
{
}
