<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Negotiation;

use LD\LanguageDetection\Event\NegotiateSiteLanguage;
use LD\LanguageDetection\Negotiation\FallbackNegotiation;
use LD\LanguageDetection\Service\SiteConfigurationService;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @internal
 * @coversNothing
 */
class FallbackNegotiationTest extends AbstractTest
{
    /**
     * @covers \LD\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers       \LD\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers       \LD\LanguageDetection\Negotiation\FallbackNegotiation
     * @covers \LD\LanguageDetection\Service\SiteConfigurationService
     * @dataProvider dataInvalid
     *
     * @param mixed[]|array<string, array<int>>|array<string, string> $configuration
     */
    public function testInvalidFallbackLanguages(array $configuration): void
    {
        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn($configuration);

        $request = new ServerRequest(null, null, 'php://input', []);
        $event = new NegotiateSiteLanguage($site, $request, []);

        $botListener = new FallbackNegotiation(new SiteConfigurationService());
        $botListener($event);

        self::assertNull($event->getSelectedLanguage());
    }

    /**
     * @covers \LD\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers       \LD\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers       \LD\LanguageDetection\Negotiation\FallbackNegotiation
     * @covers       \LD\LanguageDetection\Service\SiteConfigurationService
     */
    public function testValidFallbackLanguages(): void
    {
        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn(['fallbackDetectionLanguage' => 2]);

        $siteLanguage1 = new SiteLanguage(1, 'de_DE', new Uri('/de/'), []);
        $siteLanguage2 = new SiteLanguage(2, 'en_GB', new Uri('/en/'), []);
        $siteLanguage3 = new SiteLanguage(3, 'fr_FR', new Uri('/fr/'), []);

        $site->method('getAllLanguages')->willReturn([$siteLanguage1, $siteLanguage2, $siteLanguage3]);

        $request = new ServerRequest(null, null, 'php://input', []);
        $event = new NegotiateSiteLanguage($site, $request, []);

        $botListener = new FallbackNegotiation(new SiteConfigurationService());
        $botListener($event);

        self::assertEquals($siteLanguage2, $event->getSelectedLanguage());
    }

    /**
     * @covers       \LD\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers       \LD\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers       \LD\LanguageDetection\Negotiation\FallbackNegotiation
     * @covers       \LD\LanguageDetection\Service\SiteConfigurationService
     */
    public function testValidConfigurationButNoFallback(): void
    {
        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn(['fallbackDetectionLanguage' => 9]);

        $siteLanguage1 = new SiteLanguage(1, 'de_DE', new Uri('/de/'), []);
        $siteLanguage2 = new SiteLanguage(2, 'en_GB', new Uri('/en/'), []);
        $siteLanguage3 = new SiteLanguage(3, 'fr_FR', new Uri('/fr/'), []);

        $site->method('getAllLanguages')->willReturn([$siteLanguage1, $siteLanguage2, $siteLanguage3]);

        $request = new ServerRequest(null, null, 'php://input', []);
        $event = new NegotiateSiteLanguage($site, $request, []);

        $botListener = new FallbackNegotiation(new SiteConfigurationService());
        $botListener($event);

        self::assertNull($event->getSelectedLanguage());
    }

    /**
     * @covers       \LD\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers       \LD\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers       \LD\LanguageDetection\Negotiation\FallbackNegotiation
     * @covers       \LD\LanguageDetection\Service\SiteConfigurationService
     */
    public function testCallWithNullSite(): void
    {
        $site = $this->createMock(NullSite::class);

        $request = new ServerRequest(null, null, 'php://input', []);
        $event = new NegotiateSiteLanguage($site, $request, []);

        $botListener = new FallbackNegotiation(new SiteConfigurationService());
        $botListener($event);

        self::assertNull($event->getSelectedLanguage());
    }

    /**
     * @return array<string, array<int, array<string, array<int, int>|string>>>
     */
    public function dataInvalid(): array
    {
        return [
            'empty' => [[]],
            'other' => [['dummy' => '1']],
            'text' => [['fallbackDetectionLanguage' => 'Wrong']],
            'array' => [['fallbackDetectionLanguage' => [1, 2, 3]]],
        ];
    }
}
