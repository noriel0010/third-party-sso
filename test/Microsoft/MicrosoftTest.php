<?php declare(strict_types=1);

namespace Noriel\SSO\Test\Microsoft;

use Noriel\SSO\Auth\Service\ThirdPartyService;
use PHPUnit\Framework\TestCase;

class MicrosoftTest extends TestCase
{
    /**
     * @var Auth\Service\ThirdPartyService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new ThirdPartyService();
    }

    public function testIfMicrosoftReturnExpectedObjectFromMicrosoftApi(): void
    {
        $microsoft = $this->service->microsoft(
            'paste.your.token.here'
        )->authenticate();

        self::assertObjectHasAttribute('id', $microsoft);
    }
}
