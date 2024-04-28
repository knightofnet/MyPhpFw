<?php

namespace myphpfw\core\utils\lang;

class StringUtils
{
    public const NEW_LINE_PATTERN = "/\r\n|\n|\r/";

    public const ALPHA_NUM_CARS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public const ALPHA_NUM_CAR_EXTENDED = self::ALPHA_NUM_CARS. '&()-_@)#{[]+=}*;:/!';
    public const EMPTY = '';


    public static function str_ends_with(string $haystack, string $needle): bool
    {
        if ('' === $haystack && '' !== $needle) {
            return false;
        }
        $len = strlen($needle);
        return 0 === substr_compare($haystack, $needle, -$len, $len);
    }

    public static function str_starts_with(string $haystack, string $needle): bool
    {
        if ('' === $haystack && '' !== $needle) {
            return false;
        }
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }


    public static function generateRandomString($length = 10, $characters = self::ALPHA_NUM_CARS): string
    {
        ;
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param string $str
     * @return string[]
     */
    public static function splitEol(string $str): array
    {
        return preg_split(StringUtils::NEW_LINE_PATTERN, $str);
    }

    public static function toLower(string $str): string
    {
        return strtolower($str);
    }

    public static function toUpper(string $str): string
    {
        return strtoupper($str);
    }

    public static function ucFirst(string $str): string
    {
        return ucfirst($str);
    }

    public static function indexOf(string $haystack, string $needle, int $offset = 0): int
    {
        $r = strpos($haystack, $needle, $offset);
        if ($r === false) {
            return -1;
        }
        return $r;
    }

    public static function str_replace_first(string $search, string $replace, string $subject)
    {
        $pos = strpos($subject, $search);
        if ($pos !== false) {
            return substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;

    }

    public static function utf8ize($d)
    {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = self::utf8ize($v);
            }
        } else if (is_string($d)) {
            return $d; // utf8_encode($d);
        }
        return $d;
    }

    public static function substrAccents(string $text)
    {
        $accents = array(
            'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ'
        );

        $noAccents = array(
            'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'TH', 'ss', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'd', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'th', 'y'
        );

        return str_replace($accents, $noAccents, $text);
    }

    public static function encryptWithXor($string, $key) {
        $iv = random_bytes(16);
        $encryptedString = openssl_encrypt($string, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encryptedString);
    }

    public static function decryptWithXor($encryptedString, $key) {
        $data = base64_decode($encryptedString);
        $iv = substr($data, 0, 16);
        $encryptedString = substr($data, 16);
        $decryptedString = openssl_decrypt($encryptedString, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return $decryptedString;
    }

    public static function removeChars(string $string, array $charsToRemove) : string
    {
        foreach ($charsToRemove as $c) {
            if (strpos($string, $c) > -1) {
                $string = str_replace($c, '',$string);
            }
        }

        return $string;
    }

}