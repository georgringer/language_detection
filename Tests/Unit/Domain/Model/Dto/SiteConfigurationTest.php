<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Domain\Model\Dto;

use LD\LanguageDetection\Domain\Model\Dto\SiteConfiguration;
use LD\LanguageDetection\Tests\Unit\AbstractTest;

/**
 * @internal
 * @coversNothing
 */
class SiteConfigurationTest extends AbstractTest
{
    /**
     * @covers \LD\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     */
    public function testGetterOfDto(): void
    {
        $dto = new SiteConfiguration(
            true,
            false,
            'value',
            false,
            102,
            'none',
            5
        );

        self::assertTrue($dto->isEnableLanguageDetection());
        self::assertFalse($dto->isDisableRedirectWithBackendSession());
        self::assertEquals('value', $dto->getAddIpLocationToBrowserLanguage());
        self::assertFalse($dto->isAllowAllPaths());
        self::assertEquals(102, $dto->getRedirectHttpStatusCode());
        self::assertEquals('none', $dto->getForwardRedirectParameters());
        self::assertEquals(5, $dto->getFallbackDetectionLanguage());
    }
}
