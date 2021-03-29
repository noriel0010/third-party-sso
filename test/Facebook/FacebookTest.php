<?php declare(strict_types=1);

namespace Noriel\SSO\Test\Facebook;

use Noriel\SSO\Auth\Service\ThirdPartyService;
use PHPUnit\Framework\TestCase;

class FacebookTest extends TestCase
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

    public function testIfFacebookReturnExpectedObjectFromFacebookApi(): void
    {
        $facebook = $this->service->facebook(
            'paste.your.token.here'
        )->authenticate();

        self::assertObjectHasAttribute('id', $facebook);
    }
}
