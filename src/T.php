<?php

namespace Pebble\Tools;

class T
{
    const ACCENTS = [
        'Æ' => 'AE',
        'Ǽ' => 'AE',
        'æ' => 'ae',
        'ǽ' => 'ae',
        'Ĳ' => 'IJ',
        'ĳ' => 'ij',
        'Œ' => 'OE',
        'œ' => 'oe',
        'ß' => 'ss',
        '’' => '\'',
        'Ä' => 'A',
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Å' => 'A',
        'Ǻ' => 'A',
        'Ā' => 'A',
        'Ă' => 'A',
        'Ą' => 'A',
        'Ǎ' => 'A',
        'ä' => 'a',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'å' => 'a',
        'ǻ' => 'a',
        'ā' => 'a',
        'ă' => 'a',
        'ą' => 'a',
        'ǎ' => 'a',
        'ª' => 'a',
        'Ç' => 'C',
        'Ć' => 'C',
        'Ĉ' => 'C',
        'Ċ' => 'C',
        'Č' => 'C',
        'ç' => 'c',
        'ć' => 'c',
        'ĉ' => 'c',
        'ċ' => 'c',
        'č' => 'c',
        'Ð' => 'D',
        'Ď' => 'D',
        'Đ' => 'D',
        'ð' => 'd',
        'ď' => 'd',
        'đ' => 'd',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ē' => 'E',
        'Ĕ' => 'E',
        'Ė' => 'E',
        'Ę' => 'E',
        'Ě' => 'E',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ē' => 'e',
        'ĕ' => 'e',
        'ė' => 'e',
        'ę' => 'e',
        'ě' => 'e',
        'ƒ' => 'f',
        'Ĝ' => 'G',
        'Ğ' => 'G',
        'Ġ' => 'G',
        'Ģ' => 'G',
        'ĝ' => 'g',
        'ğ' => 'g',
        'ġ' => 'g',
        'ģ' => 'g',
        'Ĥ' => 'H',
        'Ħ' => 'H',
        'ĥ' => 'h',
        'ħ' => 'h',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ĩ' => 'I',
        'Ī' => 'I',
        'Ĭ' => 'I',
        'Ǐ' => 'I',
        'Į' => 'I',
        'İ' => 'I',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ĩ' => 'i',
        'ī' => 'i',
        'ĭ' => 'i',
        'ǐ' => 'i',
        'į' => 'i',
        'ı' => 'i',
        'Ĵ' => 'J',
        'ĵ' => 'j',
        'Ķ' => 'K',
        'ķ' => 'k',
        'Ĺ' => 'L',
        'Ļ' => 'L',
        'Ľ' => 'L',
        'Ŀ' => 'L',
        'Ł' => 'L',
        'ĺ' => 'l',
        'ļ' => 'l',
        'ľ' => 'l',
        'ŀ' => 'l',
        'ł' => 'l',
        'Ñ' => 'N',
        'Ń' => 'N',
        'Ņ' => 'N',
        'Ň' => 'N',
        'ñ' => 'n',
        'ń' => 'n',
        'ņ' => 'n',
        'ň' => 'n',
        'ŉ' => 'n',
        'Ö' => 'O',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ō' => 'O',
        'Ŏ' => 'O',
        'Ǒ' => 'O',
        'Ő' => 'O',
        'Ơ' => 'O',
        'Ø' => 'O',
        'Ǿ' => 'O',
        'ö' => 'o',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ō' => 'o',
        'ŏ' => 'o',
        'ǒ' => 'o',
        'ő' => 'o',
        'ơ' => 'o',
        'ø' => 'o',
        'ǿ' => 'o',
        'º' => 'o',
        'Ŕ' => 'R',
        'Ŗ' => 'R',
        'Ř' => 'R',
        'ŕ' => 'r',
        'ŗ' => 'r',
        'ř' => 'r',
        'Ś' => 'S',
        'Ŝ' => 'S',
        'Ş' => 'S',
        'Š' => 'S',
        'ś' => 's',
        'ŝ' => 's',
        'ş' => 's',
        'š' => 's',
        'ſ' => 's',
        'Ţ' => 'T',
        'Ť' => 'T',
        'Ŧ' => 'T',
        'ţ' => 't',
        'ť' => 't',
        'ŧ' => 't',
        'Ü' => 'U',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ũ' => 'U',
        'Ū' => 'U',
        'Ŭ' => 'U',
        'Ů' => 'U',
        'Ű' => 'U',
        'Ų' => 'U',
        'Ư' => 'U',
        'Ǔ' => 'U',
        'Ǖ' => 'U',
        'Ǘ' => 'U',
        'Ǚ' => 'U',
        'Ǜ' => 'U',
        'ü' => 'u',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ũ' => 'u',
        'ū' => 'u',
        'ŭ' => 'u',
        'ů' => 'u',
        'ű' => 'u',
        'ų' => 'u',
        'ư' => 'u',
        'ǔ' => 'u',
        'ǖ' => 'u',
        'ǘ' => 'u',
        'ǚ' => 'u',
        'ǜ' => 'u',
        'Ŵ' => 'W',
        'ŵ' => 'w',
        'Ý' => 'Y',
        'Ÿ' => 'Y',
        'Ŷ' => 'Y',
        'ý' => 'y',
        'ÿ' => 'y',
        'ŷ' => 'y',
        'Ź' => 'Z',
        'Ż' => 'Z',
        'Ž' => 'Z',
        'ź' => 'z',
        'ż' => 'z',
        'ž' => 'z',
    ];

    const FR = [
        'Æ' => 'AE',
        'Ǽ' => 'AE',
        'ǽ' => 'ae',
        'Ĳ' => 'IJ',
        'ĳ' => 'ij',
        'Œ' => 'OE',
        'ß' => 'ss',
        '’' => '\'',
        'Ä' => 'A',
        'Á' => 'A',
        'Ã' => 'A',
        'Å' => 'A',
        'Ǻ' => 'A',
        'Ā' => 'A',
        'Ă' => 'A',
        'Ą' => 'A',
        'Ǎ' => 'A',
        'ä' => 'a',
        'á' => 'a',
        'ã' => 'a',
        'å' => 'a',
        'ǻ' => 'a',
        'ā' => 'a',
        'ă' => 'a',
        'ą' => 'a',
        'ǎ' => 'a',
        'ª' => 'a',
        'Ć' => 'C',
        'Ĉ' => 'C',
        'Ċ' => 'C',
        'Č' => 'C',
        'ć' => 'c',
        'ĉ' => 'c',
        'ċ' => 'c',
        'č' => 'c',
        'Ð' => 'D',
        'Ď' => 'D',
        'Đ' => 'D',
        'ð' => 'd',
        'ď' => 'd',
        'đ' => 'd',
        'Ē' => 'E',
        'Ĕ' => 'E',
        'Ė' => 'E',
        'Ę' => 'E',
        'Ě' => 'E',
        'ē' => 'e',
        'ĕ' => 'e',
        'ė' => 'e',
        'ę' => 'e',
        'ě' => 'e',
        'ƒ' => 'f',
        'Ĝ' => 'G',
        'Ğ' => 'G',
        'Ġ' => 'G',
        'Ģ' => 'G',
        'ĝ' => 'g',
        'ğ' => 'g',
        'ġ' => 'g',
        'ģ' => 'g',
        'Ĥ' => 'H',
        'Ħ' => 'H',
        'ĥ' => 'h',
        'ħ' => 'h',
        'Ì' => 'I',
        'Í' => 'I',
        'Ĩ' => 'I',
        'Ī' => 'I',
        'Ĭ' => 'I',
        'Ǐ' => 'I',
        'Į' => 'I',
        'İ' => 'I',
        'ì' => 'i',
        'í' => 'i',
        'ĩ' => 'i',
        'ī' => 'i',
        'ĭ' => 'i',
        'ǐ' => 'i',
        'į' => 'i',
        'ı' => 'i',
        'Ĵ' => 'J',
        'ĵ' => 'j',
        'Ķ' => 'K',
        'ķ' => 'k',
        'Ĺ' => 'L',
        'Ļ' => 'L',
        'Ľ' => 'L',
        'Ŀ' => 'L',
        'Ł' => 'L',
        'ĺ' => 'l',
        'ļ' => 'l',
        'ľ' => 'l',
        'ŀ' => 'l',
        'ł' => 'l',
        'Ń' => 'N',
        'Ņ' => 'N',
        'Ň' => 'N',
        'ń' => 'n',
        'ņ' => 'n',
        'ň' => 'n',
        'ŉ' => 'n',
        'Ö' => 'O',
        'Ò' => 'O',
        'Ó' => 'O',
        'Õ' => 'O',
        'Ō' => 'O',
        'Ŏ' => 'O',
        'Ǒ' => 'O',
        'Ő' => 'O',
        'Ơ' => 'O',
        'Ø' => 'O',
        'Ǿ' => 'O',
        'ö' => 'o',
        'ò' => 'o',
        'ó' => 'o',
        'õ' => 'o',
        'ō' => 'o',
        'ŏ' => 'o',
        'ǒ' => 'o',
        'ő' => 'o',
        'ơ' => 'o',
        'ø' => 'o',
        'ǿ' => 'o',
        'º' => 'o',
        'Ŕ' => 'R',
        'Ŗ' => 'R',
        'Ř' => 'R',
        'ŕ' => 'r',
        'ŗ' => 'r',
        'ř' => 'r',
        'Ś' => 'S',
        'Ŝ' => 'S',
        'Ş' => 'S',
        'Š' => 'S',
        'ś' => 's',
        'ŝ' => 's',
        'ş' => 's',
        'š' => 's',
        'ſ' => 's',
        'Ţ' => 'T',
        'Ť' => 'T',
        'Ŧ' => 'T',
        'ţ' => 't',
        'ť' => 't',
        'ŧ' => 't',
        'Ú' => 'U',
        'Ũ' => 'U',
        'Ū' => 'U',
        'Ŭ' => 'U',
        'Ů' => 'U',
        'Ű' => 'U',
        'Ų' => 'U',
        'Ư' => 'U',
        'Ǔ' => 'U',
        'Ǖ' => 'U',
        'Ǘ' => 'U',
        'Ǚ' => 'U',
        'Ǜ' => 'U',
        'ú' => 'u',
        'ũ' => 'u',
        'ū' => 'u',
        'ŭ' => 'u',
        'ů' => 'u',
        'ű' => 'u',
        'ų' => 'u',
        'ư' => 'u',
        'ǔ' => 'u',
        'ǖ' => 'u',
        'ǘ' => 'u',
        'ǚ' => 'u',
        'ǜ' => 'u',
        'Ŵ' => 'W',
        'ŵ' => 'w',
        'Ý' => 'Y',
        'Ŷ' => 'Y',
        'ý' => 'y',
        'ŷ' => 'y',
        'Ź' => 'Z',
        'Ż' => 'Z',
        'Ž' => 'Z',
        'ź' => 'z',
        'ż' => 'z',
        'ž' => 'z',
    ];

    /**
     * Remove accents
     */
    public static function removeAccents(string $str): string
    {
        return self::convertAccents($str, self::ACCENTS);
    }

    /**
     * Convert accents
     */
    public static function convertAccents(string $str, array $accents = self::FR): string
    {
        return strtr($str, $accents);
    }

    public static function remove_emoji($string)
    {
        // Match Emoticons
        $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clear_string = preg_replace($regex_emoticons, '', $string);

        // Match Miscellaneous Symbols and Pictographs
        $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clear_string = preg_replace($regex_symbols, '', $clear_string);

        // Match Transport And Map Symbols
        $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clear_string = preg_replace($regex_transport, '', $clear_string);

        // Match Miscellaneous Symbols
        $regex_misc = '/[\x{2600}-\x{26FF}]/u';
        $clear_string = preg_replace($regex_misc, '', $clear_string);

        // Match Dingbats
        $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
        $clear_string = preg_replace($regex_dingbats, '', $clear_string);

        return $clear_string;
    }

    /**
     * Remove accents, lowercase and replace non alpha numeric chars by separtor
     */
    public static function alias(string $str, string $separator = '-'): string
    {
        $str = self::removeAccents($str);
        $str = mb_strtolower($str);
        $str = preg_replace('#[^a-z0-9]#i', $separator, $str);

        if ($separator) {
            $str = trim($str, $separator);
            $str = preg_replace('#(' . preg_quote($separator, '#') . ')+#', $separator, $str);
        }

        return $str;
    }

    /**
     * Explode a string and trim all elements
     */
    public static function split(string $str, string $separator): string
    {
        $separator = preg_quote($separator, '#');
        return preg_split("#\s*{$separator}\s*#", $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Make a string's first character uppercase
     */
    public static function ucfirst(string $str): string
    {
        if ($str) {
            $str = mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1);
        }
        return $str;
    }

    /**
     * Make a string's first character lowercase
     */
    public static function lcfirst(string $str): string
    {
        if ($str) {
            $str = mb_strtolower(mb_substr($str, 0, 1)) . mb_substr($str, 1);
        }
        return $str;
    }

    /**
     * Convert special characters to HTML entities
     */
    public static function xss(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (is_string($value)) {
            return self::htmlEncode($value);
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return self::htmlEncode(json_encode($value));
    }

    /**
     * Convert special characters to HTML entities
     */
    public static function htmlEncode(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    /**
     * Convert HTML entities to their corresponding characters
     */
    public static function htmlDecode(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return html_entity_decode($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    /**
     * Number format to french standard
     */
    public static function number(float $amount, int $decimal = 2): string
    {
        return number_format($amount, $decimal, ',', '&#8239;');
    }

    /**
     * Money format to french standard
     */
    public static function money(float $amount, string $currency = '&nbsp;€'): string
    {
        return self::number($amount, 2) . $currency;
    }

    /**
     * String format to camelCase
     */
    public static function toCamelCase(string $str): string
    {
        if (!$str) {
            return $str;
        }

        $str = self::alias($str, '_');

        return preg_replace_callback('/_([a-z])/', function ($c) {
            return mb_strtoupper($c[1]);
        }, $str);
    }

    /**
     * String format to PascalCase
     */
    public static function toPascalCase(string $str): string
    {
        if (!$str) {
            return $str;
        }

        return self::ucfirst(self::toCamelCase($str));
    }

    /**
     * String format to snake_case
     */
    public static function toSnakeCase(string $str): string
    {
        if (!$str) {
            return $str;
        }

        $str = self::removeAccents($str);
        $str = preg_replace_callback('/([A-Z])/', function ($c) {
            return "_" . mb_strtolower($c[1]);
        }, $str);

        return self::alias($str, '_');
    }
}
