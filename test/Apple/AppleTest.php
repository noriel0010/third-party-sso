<?php declare(strict_types=1);

namespace Noriel\SSO\Test\Apple;

use Noriel\SSO\Auth\Service\ThirdPartyService;
use PHPUnit\Framework\TestCase;

class AppleTest extends TestCase
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

    public function testIfAppleReturnExpectedObjectFromAppleJWT(): void
    {
        $apple = $this->service->apple(
            'paste.your.token.here'
            ,'your.audience.here'
        )->authenticate();

        self::assertObjectHasAttribute('sub', $apple);
    }
}
