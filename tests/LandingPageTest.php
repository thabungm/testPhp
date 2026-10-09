<?php

namespace Calculator\Tests;

use PHPUnit\Framework\TestCase;

class LandingPageTest extends TestCase
{
    private const LANDING_PAGE = __DIR__ . '/../public/index.html';

    public function testLandingPageExists(): void
    {
        $this->assertFileExists(self::LANDING_PAGE);
    }

    public function testLandingPageSaysComingSoon(): void
    {
        $this->assertStringContainsString('Coming Soon', file_get_contents(self::LANDING_PAGE));
    }
}
