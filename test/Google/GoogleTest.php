<?php declare(strict_types=1);

namespace Noriel\SSO\Test\Google;

use Noriel\SSO\Auth\Service\ThirdPartyService;
use PHPUnit\Framework\TestCase;

class GoogleTest extends TestCase
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

    public function testIfGoogleReturnExpectedObjectFromGoogleApi(): void
    {
        $google = $this->service->google(
            'paste.your.token.here'
            ,'your_google_key_here'
        )->authenticate();

        self::assertObjectHasAttribute('names', $google);
    }
}
