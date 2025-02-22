<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Service;

use LD\LanguageDetection\Service\Normalizer;
use LD\LanguageDetection\Tests\Unit\AbstractTest;

/**
 * @internal
 * @coversNothing
 */
class NormalizerTest extends AbstractTest
{
    /**
     * @dataProvider normalizeProvider
     *
     * @covers \LD\LanguageDetection\Service\Normalizer
     */
    public function testNormalize(string $base, string $result): void
    {
        $normalizer = new Normalizer();

        self::assertEquals($result, $normalizer->normalize($base));
    }

    /**
     * @covers \LD\LanguageDetection\Service\Normalizer
     */
    public function testNormalizeList(): void
    {
        $normalizer = new Normalizer();

        $base = array_map(fn ($item): string => $item[0], $this->normalizeProvider());

        $result = array_map(fn ($item): string => $item[1], $this->normalizeProvider());

        self::assertEquals($result, $normalizer->normalizeList($base));
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function normalizeProvider(): array
    {
        return [
            'default' => [
                'de-de',
                'de_DE',
            ],
            'default with encoding' => [
                'de_DE.UTF-8',
                'de_DE',
            ],
            'lower and upper wrong in country' => [
                'en-gB',
                'en_GB',
            ],
            'lower and upper wrong in language' => [
                'EN-us',
                'en_US',
            ],
            'lower and upper wrong in language and language only' => [
                'EN',
                'en',
            ],
        ];
    }
}
