<?php declare(strict_types=1);

namespace Noriel\SSO\Test\Linkedin;

use Noriel\SSO\Auth\Service\ThirdPartyService;
use PHPUnit\Framework\TestCase;

class LinkedinTest extends TestCase
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

    public function testIfLinkedinReturnExpectedObjectFromLinkedinApi(): void
    {
        $linkedin = $this->service->linkedin(
            'paste.your.token.here'
        )->authenticate();

        self::assertObjectHasAttribute('id', $linkedin);
    }
}
