<?php

namespace App\Support\Odontogram;

/**
 * مسارات SVG للأسنان مأخوذة من حزمة react-odontogram (ترخيص MIT).
 *
 * @see https://github.com/biomathcode/react-odontogram
 * @see https://www.npmjs.com/package/react-odontogram
 *
 * حقوق النشر الأصلية: Pratik Sharma / biomathcode — إعادة الاستخدام وفق MIT.
 */
final class LicensedReactOdontogramGlyphs
{
    /** @var list<array{name: string, type: string, outline: string, shadow: string, highlights: list<string>}>|null */
    private static ?array $glyphs = null;

    /**
     * @return list<array{name: string, type: string, outline: string, shadow: string, highlights: list<string>}>
     */
    public static function glyphs(): array
    {
        if (self::$glyphs !== null) {
            return self::$glyphs;
        }

        $path = base_path('app/Support/Odontogram/data/new_teeth_paths.json');
        $json = file_get_contents($path);
        if ($json === false) {
            throw new \RuntimeException('Missing odontogram glyph data: '.$path);
        }
        /** @var list<array{name: string, type: string, outline: string, shadow: string, highlights: list<string>}> $data */
        $data = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

        return self::$glyphs = $data;
    }

    /**
     * تحويلات المربعات كما في react-odontogram (src/utils.ts — newquadrants).
     *
     * @return list<array{transform: string, fdiBase: int, label: string}>
     */
    public static function quadrants(): array
    {
        return [
            [
                'transform' => '',
                'fdiBase' => 11,
                'label' => 'الربع العلوي الأيمن (١١–١٨)',
            ],
            [
                'transform' => 'translate(840, 0) scale(-1, 1) translate(-55, 0)',
                'fdiBase' => 21,
                'label' => 'الربع العلوي الأيسر (٢١–٢٨)',
            ],
            [
                'transform' => 'scale(1, -1) translate(0, -150)',
                'fdiBase' => 31,
                'label' => 'الربع السفلي الأيسر (٣١–٣٨)',
            ],
            [
                'transform' => 'translate(840, 0) scale(-1, -1) translate(-55, -150)',
                'fdiBase' => 41,
                'label' => 'الربع السفلي الأيمن (٤١–٤٨)',
            ],
        ];
    }

    /**
     * مراكز تقريبية لرقم FDI تحت كل سن في شريط ٩٠٠×١٥٠ (نفس تخطيط react-odontogram).
     *
     * @return list<array{x: float, y: float}>
     */
    public static function labelAnchors(): array
    {
        return [
            ['x' => 402, 'y' => 72],
            ['x' => 332, 'y' => 72],
            ['x' => 288, 'y' => 72],
            ['x' => 238, 'y' => 72],
            ['x' => 192, 'y' => 72],
            ['x' => 145, 'y' => 72],
            ['x' => 72, 'y' => 72],
            ['x' => 28, 'y' => 72],
        ];
    }
}
