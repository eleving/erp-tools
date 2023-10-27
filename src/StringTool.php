<?php

namespace Common\Tool;

class StringTool
{
    public static function startsWith($haystack, $needle, $case = true)
    {
        mb_internal_encoding('UTF-8');

        if ($case) {
            return mb_strpos($haystack, $needle, 0) === 0;
        }
        return mb_stripos($haystack, $needle, 0) === 0;
    }

    public static function endsWith($haystack,$needle, $case = true)
    {
        mb_internal_encoding('UTF-8');
        
        $expectedPosition = mb_strlen($haystack) - mb_strlen($needle);
        if ($case) {
            return mb_strrpos($haystack, $needle, 0) === $expectedPosition;
        }
        return mb_strripos($haystack, $needle, 0) === $expectedPosition;
    }
    
    public static function contains($haystack, $needle)
    {
        mb_internal_encoding('UTF-8');
        
        return mb_strpos($haystack, $needle) !== false;
    }
    
    public static function capitalizeFirstLetter($string)
    {
        mb_internal_encoding('UTF-8');
        
        return mb_convert_case($string, MB_CASE_TITLE);
    }
    
    public static function removePrefix($string, $prefix)
    {
        return mb_substr($string, mb_strlen($prefix));
    }
    
    /**
     * Remove any non-ASCII characters and convert known non-ASCII characters 
     * to their ASCII equivalents, if possible.
     * @param string $string
     * @return string
     * @link http://gist.github.com/119517
     */
    public static function convertAscii($string)
    {
        // Replace Single Curly Quotes
        $search[]  = chr(226).chr(128).chr(152);
        $replace[] = "'";
        $search[]  = chr(226).chr(128).chr(153);
        $replace[] = "'";

        // Replace Smart Double Curly Quotes
        $search[]  = chr(226).chr(128).chr(156);
        $replace[] = '"';
        $search[]  = chr(226).chr(128).chr(157);
        $replace[] = '"';

        // Replace En Dash
        $search[]  = chr(226).chr(128).chr(147);
        $replace[] = '-';

        // Replace Em Dash
        $search[]  = chr(226).chr(128).chr(148);
        $replace[] = '-';

        // Replace Bullet
        $search[]  = chr(226).chr(128).chr(162);
        $replace[] = '*';

        // Replace Middle Dot
        $search[]  = chr(194).chr(183);
        $replace[] = '*';

        // Replace Ellipsis with three consecutive dots
        $search[]  = chr(226).chr(128).chr(166);
        $replace[] = '...';

        // Apply Replacements
        $string = str_replace($search, $replace, $string);

        // Remove any non-ASCII Characters (keeps cyrillic)
        $string = preg_replace("/[^\x80-\xFF, \x01-\x7F]/","", $string);

        return $string; 
    }

    
    public static function unaccent($string)
    {
        
    }
    
    public static function cleanSmsContent($message, $transliterateCyrillic = true)
    {
        $message = trim($message);
        $message = Transliterator::unaccent($message, $transliterateCyrillic);

        return self::convertAscii($message, $transliterateCyrillic);
    }     

    public static function getArrayWithRemovedPrefixRecursive(array $input, $prefix)
    {          
        mb_internal_encoding('UTF-8');   

        $return = array();
        foreach ($input as $key => $value) {
            if (strpos($key, $prefix) === 0)
                $key = substr($key, mb_strlen($prefix));

            if (is_array($value))
                $value = self::getArrayWithRemovedPrefixRecursive($value, $prefix); 

            $return[$key] = $value;
        }
        return $return;
    }    

    // possible TODO fix type Combionation to Combination
    public static function createCombionationsFromStringWords($phrase = '')
    {
        $phrase = preg_replace('/\s+/', ' ', strtolower($phrase));
        $words = explode(' ', $phrase);

        return self::createCombionationsFromWords($words);
    }

    public static function createCombionationsFromWords($words = [])
    {
        array_walk($words, 'trim');

        foreach ($words as $x => $value) {
            if (trim($value) === '') {
                unset($words[$x]);
            }
        }

        return self::permuteCombinations($words);
    }

    private static function permuteCombinations($items, $perms = []): array
    {
        $back = [];

        if (empty($items)) { 
            $back[] = implode(' ', $perms);
        } else {
            for ($i = count($items) - 1; $i >= 0; --$i) {
                $newitems = $items;
                $newperms = $perms;
                list($foo) = array_splice($newitems, $i, 1);
                array_unshift($newperms, $foo);
                $back = array_merge($back, self::permuteCombinations($newitems, $newperms));
            }
        }

        return $back;
    }

    public static function toLower($string): string
    {
        mb_internal_encoding('UTF-8');

        return mb_strtolower($string);
    }

    public static function toUpper(?string $string): string
    {
        mb_internal_encoding('UTF-8');

        if($string === null) {
            return '';
        }

        return mb_strtoupper($string);
    }

    public static function toUTF8($string)
    {
        mb_internal_encoding('UTF-8');
        return iconv('UTF-8', 'UTF-8//TRANSLIT', $string);
    }

    /**
     * @param int $length
     * @return string
     */
    public static function random($length = 8)
    {
        $numbers = '0123456789';
        $numbersLength = strlen($numbers);

        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);

        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $symbol = $numbers[rand(0, $numbersLength - 1)];
            if (rand(0, 1)) {
                $symbol = $characters[rand(0, $charactersLength - 1)];
                if (rand(0, 1)) {
                    $symbol = self::toUpper($symbol);
                }
            }

            $string .= $symbol;
        }

        return $string;
    }

    /**
     * @param $length
     * @return string
     */
    public static function password($length = 8)
    {
        if ($length < 8) {
            throw new \InvalidArgumentException('Length must be more than 7');
        }

        $string = self::random($length);

        if (!preg_match('/[a-z]/', $string)) {
            return self::random($length);
        }

        if (!preg_match('/[A-Z]/', $string)) {
            return self::random($length);
        }

        if (!preg_match('/\d/', $string)) {
            return self::random($length);
        }

        return $string;
    }

    /**
     * Transliterates polish chars to latin char equivalents
     * @param $string
     * @return mixed
     */
    public static function polishToLatin($string)
    {
        $polishChars = ['Ą','Ć','Ę','Ł','Ó','Ś','Ź','Ż','Ń','ą','ć','ę','ł','ó','ś','ź','ż','ń'];
        $latinChars = ['A','C','E','L','O','S','Z','Z','N','a','c','e','l','o','s','z','z','n'];

        return str_replace($polishChars, $latinChars, $string);
    }

    /**
     * Tries to parse correct string from various price string formats
     *
     * '2000 EUR' -> '2000.00'
     * '2000.00' -> '2000.00'
     * '2,000 EUR' -> '2000.00'
     * '2.000' -> '2000.00'
     * '2.320,34 EUR' -> '2320.34'
     * '2,000,000.31'-> '2000000.31'
     * '2.000.000' -> '2000000.00'
     * '2.00' -> '2.00'
     * '2,000€' -> '2000.00'
     *
     * @param $price
     * @return string
     */
    public static function cleanupPriceString($price)
    {
        $price = preg_replace('/[^0-9\.,]+/', '', $price);
        $commaPosition = strpos($price, ',');
        $dotPosition = strpos($price, '.');
        $hasCommaSeparator = $commaPosition > 0;
        $hasDotSeparator = $dotPosition > 0;

        $explodeBy = null;

        if ($hasDotSeparator && $hasCommaSeparator) {
            if ($commaPosition > $dotPosition) {
                $explodeBy = ',';
            } else {
                $explodeBy = '.';
            }
        } elseif ($hasDotSeparator) {
            $explodeBy = '.';
        } elseif ($hasCommaSeparator) {
            $explodeBy = ',';
        }

        $decimals = '00';

        if ($explodeBy) {
            $exploded = explode($explodeBy, $price);
            $possibleDecimals = $exploded[count($exploded) - 1];

            if (strlen($possibleDecimals) === 2) {
                unset($exploded[count($exploded) - 1]);
                $decimals = $possibleDecimals;
            }

            $price = implode('', $exploded);
        }

        $price = preg_replace('/[^0-9]+/', '', $price);
        $price = $price . '.' . $decimals;

        return $price;
    }
}
