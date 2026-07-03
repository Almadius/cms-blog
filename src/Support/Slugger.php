<?php

declare(strict_types=1);

namespace App\Support;

final class Slugger
{
    private const TRANSLIT = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
    ];

    public static function slugify(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $result = '';

        foreach ($chars as $char) {
            $result .= self::TRANSLIT[$char] ?? $char;
        }

        $result = preg_replace('/[^a-z0-9\s-]/', '', $result) ?? '';
        $result = preg_replace('/[\s-]+/', '-', trim($result)) ?? '';

        return $result !== '' ? $result : 'item';
    }
}
