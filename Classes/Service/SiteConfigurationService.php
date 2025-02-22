<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use LD\LanguageDetection\Domain\Model\Dto\SiteConfiguration;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

class SiteConfigurationService
{
    public function getConfiguration(SiteInterface $site): SiteConfiguration
    {
        $config = $site instanceof Site ? $site->getConfiguration() : [];

        return new SiteConfiguration(
            !\array_key_exists('enableLanguageDetection', $config) || (bool) $config['enableLanguageDetection'],
            (bool) ($config['disableRedirectWithBackendSession'] ?? false),
            (string) ($config['addIpLocationToBrowserLanguage'] ?? ''),
            (bool) ($config['allowAllPaths'] ?? false),
            (int) ($config['redirectHttpStatusCode'] ?? 307),
            (string) ($config['forwardRedirectParameters'] ?? ''),
            (int) ($config['fallbackDetectionLanguage'] ?? 0),
        );
    }
}
