<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\Common\Utilities;

use Meritoo\Common\Exception\Regex\IncorrectColorHexLengthException;
use Meritoo\Common\Exception\Regex\InvalidColorHexValueException;

/**
 * Useful regular expressions methods
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class Regex
{
    /**
     * Patterns used to validate / verify values
     *
     * @var array
     */
    private static $patterns = [
        'email'            => '/[\w-]{2,}@[\w-]+\.[\w]{2,}+/',
        'phone'            => '/^\+?[0-9 ]+$/',
        'camelCasePart'    => '/([a-z]|[A-Z]){1}[a-z]*/',
        'urlProtocol'      => '/^([a-z]+:\/\/)',
        'urlDomain'        => '([\da-z\.-]+)\.([a-z\.]{2,6})(\/)?([\w\.\-]*)?(\?)?([\w \.\-\/=&]*)\/?$/i',
        'letterOrDigit'    => '/[a-zA-Z0-9]+/',
        'htmlEntity'       => '/&[a-z0-9]+;/',
        'fileName'         => '/.+\.\w+$/',
        'isQuoted'         => '/^[\'"]{1}.+[\'"]{1}$/',
        'windowsBasedPath' => '/^[A-Z]{1}:\\\.*$/',
        'money'            => '/^[-+]?\d+([\.,]{1}\d*)?$/',
        'color'            => '/^[a-f0-9]{6}$/i',
    ];

    /**
     * Returns information if given e-mail address is valid
     *
     * @param string $email E-mail address to validate / verify
     * @return bool
     *
     * Examples:
     * a) valid e-mails:
     * - ni@g-m.pl
     * - ni@gm.pl
     * - ni@g_m.pl
     * b) invalid e-mails:
     * - ni@g-m.p
     * - n@g-m.pl
     */
    public static function isValidEmail($email)
    {
        $pattern = self::getEmailPattern();

        return (bool)preg_match($pattern, $email);
    }

    /**
     * Returns information if given tax ID (in polish: NIP) is valid
     *
     * @param string $taxidString Tax ID (NIP) string
     * @return bool
     */
    public static function isValidTaxid($taxidString)
    {
        if (!empty($taxidString)) {
            $weights = [
                6,
                5,
                7,
                2,
                3,
                4,
                5,
                6,
                7,
            ];
            $taxid = preg_replace('/[\s-]/', '', $taxidString);
            $sum = 0;

            if (10 == strlen($taxid) && is_numeric($taxid)) {
                for ($x = 0; $x <= 8; ++$x) {
                    $sum += $taxid[$x] * $weights[$x];
                }

                if ((($sum % 11) % 10) == $taxid[9]) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns information if given url address is valid
     *
     * @param string $url             The url to validate / verify
     * @param bool   $requireProtocol (optional) If is set to true, the protocol is required to be passed in the url.
     *                                Otherwise - not.
     * @return bool
     */
    public static function isValidUrl($url, $requireProtocol = false)
    {
        $pattern = self::getUrlPattern($requireProtocol);

        return (bool)preg_match($pattern, $url);
    }

    /**
     * Returns information if given phone number is valid
     *
     * @param string $phoneNumber The phone number to validate / verify
     * @return bool
     */
    public static function isValidPhoneNumber($phoneNumber)
    {
        $pattern = self::getPhoneNumberPattern();

        return (bool)preg_match($pattern, $phoneNumber);
    }

    /**
     * Returns array values that matches given pattern (or values that keys matches)
     *
     * @param string $pattern       Pattern to match
     * @param array  $dataArray     The array
     * @param bool   $itsKeyPattern (optional) If is set to true, keys are checks if they match pattern. Otherwise -
     *                              values are checks.
     * @return array
     */
    public static function getArrayValuesByPattern($pattern, $dataArray, $itsKeyPattern = false)
    {
        if ($itsKeyPattern) {
            $effect = [];

            if (!empty($dataArray)) {
                $matches = [];

                foreach ($dataArray as $key => $value) {
                    if (preg_match($pattern, $key, $matches)) {
                        $effect[$key] = $value;
                    }
                }
            }

            return $effect;
        }

        return preg_grep($pattern, $dataArray);
    }

    /**
     * Filters array by given expression and column
     *
     * Expression can be simple compare expression, like ' == 2', or regular expression.
     * Returns filtered array.
     *
     * @param array  $array                The array that should be filtered
     * @param string $arrayColumnKey       Column name
     * @param string $filterExpression     Filter expression, e.g. '== 2' or '!= \'home\''
     * @param bool   $itsRegularExpression (optional) If is set to true, means that filter expression is a
     *                                     regular expression
     * @return array
     */
    public static function arrayFilter($array, $arrayColumnKey, $filterExpression, $itsRegularExpression = false)
    {
        $effect = [];

        if (!empty($array)) {
            $effect = $array;

            foreach ($effect as $key => &$item) {
                if (isset($item[$arrayColumnKey])) {
                    $value = $item[$arrayColumnKey];

                    if ($itsRegularExpression) {
                        $matches = [];
                        $pattern = '|' . $filterExpression . '|';
                        $matchesCount = preg_match($pattern, $value, $matches);

                        $remove = 0 == $matchesCount;
                    } else {
                        if ('' == $value) {
                            $value = '\'\'';
                        } elseif (is_string($value)) {
                            $value = '\'' . $value . '\'';
                        }

                        eval('$isTrue = ' . $value . $filterExpression . ';');

                        /* @var bool $isTrue */
                        $remove = !$isTrue;
                    }

                    if ($remove) {
                        unset($effect[$key]);
                    }
                }
            }
        }

        return $effect;
    }

    /**
     * Perform regular expression match with many given patterns.
     * Returns information if given $subject matches one or all given $patterns.
     *
     * @param array|string $patterns     The patterns to match
     * @param string       $subject      The string to check
     * @param bool         $mustAllMatch (optional) If is set to true, $subject must match all $patterns. Otherwise -
     *                                   not.
     * @return bool
     */
    public static function pregMultiMatch($patterns, $subject, $mustAllMatch = false)
    {
        $effect = false;
        $patterns = Arrays::makeArray($patterns);

        if (!empty($patterns)) {
            if ($mustAllMatch) {
                $effect = true;
            }

            foreach ($patterns as $pattern) {
                $matches = [];
                $matched = (bool)preg_match_all($pattern, $subject, $matches);

                if ($mustAllMatch) {
                    $effect = $effect && $matched;
                } else {
                    if ($matched) {
                        $effect = $matched;
                        break;
                    }
                }
            }
        }

        return $effect;
    }

    /**
     * Returns string in human readable style generated from given camel case string / text
     *
     * @param string $string              The string / text to convert
     * @param bool   $applyUpperCaseFirst (optional) If is set to true, first word / element from the converted
     *                                    string is uppercased. Otherwise - not.
     * @return string
     */
    public static function camelCase2humanReadable($string, $applyUpperCaseFirst = false)
    {
        $parts = self::getCamelCaseParts($string);

        if (!empty($parts)) {
            $elements = [];

            foreach ($parts as $part) {
                $elements[] = strtolower($part);
            }

            $string = implode(' ', $elements);

            if ($applyUpperCaseFirst) {
                $string = ucfirst($string);
            }
        }

        return $string;
    }

    /**
     * Returns parts of given camel case string / text
     *
     * @param string $string The string / text to retrieve parts
     * @return array
     */
    public static function getCamelCaseParts($string)
    {
        $pattern = self::getCamelCasePartPattern();
        $matches = [];
        preg_match_all($pattern, $string, $matches);

        return $matches[0];
    }

    /**
     * Returns simple, lowercase string generated from given camel case string / text
     *
     * @param string $string         The string / text to convert
     * @param string $separator      (optional) Separator used to concatenate parts of the string, e.g. '-' or '_'
     * @param bool   $applyLowercase (optional) If is set to true, returned string will be lowercased. Otherwise - not.
     * @return string
     */
    public static function camelCase2simpleLowercase($string, $separator = '', $applyLowercase = true)
    {
        $parts = self::getCamelCaseParts($string);

        if (!empty($parts)) {
            $string = implode($separator, $parts);

            if ($applyLowercase) {
                $string = strtolower($string);
            }
        }

        return $string;
    }

    /**
     * Returns pattern used to validate / verify or get e-mail address
     *
     * @return string
     */
    public static function getEmailPattern()
    {
        return self::$patterns['email'];
    }

    /**
     * Returns pattern used to validate / verify or get phone number
     *
     * @return string
     */
    public static function getPhoneNumberPattern()
    {
        return self::$patterns['phone'];
    }

    /**
     * Returns pattern used to validate / verify or get camel case parts of string
     *
     * @return string
     */
    public static function getCamelCasePartPattern()
    {
        return self::$patterns['camelCasePart'];
    }

    /**
     * Returns pattern used to validate / verify or get url address
     *
     * @param bool $requireProtocol (optional) If is set to true, the protocol is required to be passed in the url.
     *                              Otherwise - not.
     * @return string
     */
    public static function getUrlPattern($requireProtocol = false)
    {
        $urlProtocol = self::$patterns['urlProtocol'];
        $urlDomain = self::$patterns['urlDomain'];
        $protocolPatternPart = '?';

        if ($requireProtocol) {
            $protocolPatternPart = '';
        }

        return sprintf('%s%s%s', $urlProtocol, $protocolPatternPart, $urlDomain);
    }

    /**
     * Returns information if given path is sub-path of another path, e.g. path file is owned by path of directory
     *
     * @param string $subPath Path to verify, probably sub-path
     * @param string $path    Main / parent path
     * @return bool
     */
    public static function isSubPathOf($subPath, $path)
    {
        /*
         * Empty path?
         * Nothing to do
         */
        if (empty($path) || empty($subPath)) {
            return false;
        }

        /*
         * I have to escape all slashes (directory separators): "/" -> "\/"
         */
        $prepared = preg_quote($path, '/');

        /*
         * Slash at the ending is optional
         */
        if (self::endsWith($path, '/')) {
            $prepared .= '?';
        }

        $pattern = sprintf('/^%s.*/', $prepared);

        return (bool)preg_match($pattern, $subPath);
    }

    /**
     * Returns pattern used to validate / verify letter or digit
     *
     * @return string
     */
    public static function getLetterOrDigitPattern()
    {
        return self::$patterns['letterOrDigit'];
    }

    /**
     * Returns information if given character is a letter or digit
     *
     * @param string $char Character to check
     * @return bool
     */
    public static function isLetterOrDigit($char)
    {
        $pattern = self::getLetterOrDigitPattern();

        return is_scalar($char) && (bool)preg_match($pattern, $char);
    }

    /**
     * Returns information if the string starts with given beginning / characters
     *
     * @param string $string    String to check
     * @param string $beginning The beginning of string, one or more characters
     * @return bool
     */
    public static function startsWith($string, $beginning)
    {
        if (!empty($string) && !empty($beginning)) {
            if (1 == strlen($beginning) && !self::isLetterOrDigit($beginning)) {
                $beginning = '\\' . $beginning;
            }

            $pattern = sprintf('|^%s|', $beginning);

            return (bool)preg_match($pattern, $string);
        }

        return false;
    }

    /**
     * Returns information if the string ends with given ending / characters
     *
     * @param string $string String to check
     * @param string $ending The ending of string, one or more characters
     * @return bool
     */
    public static function endsWith($string, $ending)
    {
        if (1 == strlen($ending) && !self::isLetterOrDigit($ending)) {
            $ending = '\\' . $ending;
        }

        return (bool)preg_match('|' . $ending . '$|', $string);
    }

    /**
     * Returns information if the string starts with directory's separator
     *
     * @param string $string    String that may contain a directory's separator at the start / beginning
     * @param string $separator (optional) The directory's separator, e.g. "/". If is empty (not provided), system's
     *                          separator is used.
     * @return bool
     */
    public static function startsWithDirectorySeparator($string, $separator = '')
    {
        if (empty($separator)) {
            $separator = DIRECTORY_SEPARATOR;
        }

        return self::startsWith($string, $separator);
    }

    /**
     * Returns information if the string ends with directory's separator
     *
     * @param string $text      String that may contain a directory's separator at the end
     * @param string $separator (optional) The directory's separator, e.g. "/". If is empty (not provided), system's
     *                          separator is used.
     * @return string
     */
    public static function endsWithDirectorySeparator($text, $separator = '')
    {
        if (empty($separator)) {
            $separator = DIRECTORY_SEPARATOR;
        }

        return self::endsWith($text, $separator);
    }

    /**
     * Returns information if uri contains parameter
     *
     * @param string $uri           Uri string (e.g. $_SERVER['REQUEST_URI'])
     * @param string $parameterName Uri parameter name
     * @return bool
     */
    public static function isSetUriParameter($uri, $parameterName)
    {
        return (bool)preg_match('|[?&]{1}' . $parameterName . '=|', $uri); // e.g. ?name=phil&type=4 -> '$type='
    }

    /**
     * Returns pattern used to validate / verify html entity
     *
     * @return string
     */
    public static function getHtmlEntityPattern()
    {
        return self::$patterns['htmlEntity'];
    }

    /**
     * Returns information if the string contains html entities
     *
     * @param string $string String to check
     * @return bool
     */
    public static function containsEntities($string)
    {
        $pattern = self::getHtmlEntityPattern();

        return (bool)preg_match_all($pattern, $string);
    }

    /**
     * Returns information if one string contains another string
     *
     * @param string $haystack The string to search in
     * @param string $needle   The string to be search for
     * @return bool
     */
    public static function contains($haystack, $needle)
    {
        if (1 == strlen($needle) && !self::isLetterOrDigit($needle)) {
            $needle = '\\' . $needle;
        }

        return (bool)preg_match('|.*' . $needle . '.*|', $haystack);
    }

    /**
     * Returns pattern used to validate / verify name of file
     *
     * @return string
     */
    public static function getFileNamePattern()
    {
        return self::$patterns['fileName'];
    }

    /**
     * Returns information if given name of file is a really name of file.
     * Verifies if given name contains a dot and an extension, e.g. "My File 001.jpg".
     *
     * @param string $fileName Name of file to check. It may be path of file also.
     * @return bool
     */
    public static function isFileName($fileName)
    {
        $pattern = self::getFileNamePattern();

        return (bool)preg_match($pattern, $fileName);
    }

    /**
     * Returns pattern used to validate / verify if value is quoted (by apostrophes or quotation marks)
     *
     * @return string
     */
    public static function getIsQuotedPattern()
    {
        return self::$patterns['isQuoted'];
    }

    /**
     * Returns information if given value is quoted (by apostrophes or quotation marks)
     *
     * @param mixed $value The value to check
     * @return bool
     */
    public static function isQuoted($value)
    {
        $pattern = self::getIsQuotedPattern();

        return is_scalar($value) && (bool)preg_match($pattern, $value);
    }

    /**
     * Returns pattern used to validate / verify if given path is a Windows-based path, e.g. "C:\path\to\file.jpg"
     *
     * @return string
     */
    public static function getWindowsBasedPathPattern()
    {
        return self::$patterns['windowsBasedPath'];
    }

    /**
     * Returns information if given path is a Windows-based path, e.g. "C:\path\to\file.jpg"
     *
     * @param string $path The path to verify
     * @return bool
     */
    public static function isWindowsBasedPath($path)
    {
        $pattern = self::getWindowsBasedPathPattern();

        return (bool)preg_match($pattern, $path);
    }

    /**
     * Returns information if given NIP number is valid
     *
     * @param string $nip A given NIP number
     * @return bool
     *
     * @see https://pl.wikipedia.org/wiki/NIP#Znaczenie_numeru
     */
    public static function isValidNip($nip)
    {
        $nip = preg_replace('/[^0-9]/', '', $nip);

        $invalidNips = [
            '1234567890',
            '0000000000',
        ];

        if (!preg_match('/^[0-9]{10}$/', $nip) || in_array($nip, $invalidNips)) {
            return false;
        }

        $sum = 0;
        $weights = [
            6,
            5,
            7,
            2,
            3,
            4,
            5,
            6,
            7,
        ];

        for ($i = 0; $i < 9; ++$i) {
            $sum += $weights[$i] * $nip[$i];
        }

        $modulo = $sum % 11;
        $numberControl = (10 == $modulo) ? 0 : $modulo;

        return $numberControl == $nip[9];
    }

    /**
     * Returns pattern used to validate / verify if given value is money-related value
     *
     * @return string
     */
    public static function getMoneyPattern()
    {
        return self::$patterns['money'];
    }

    /**
     * Returns information if given value is valid money-related value
     *
     * @param mixed $value Value to verify
     * @return bool
     */
    public static function isValidMoneyValue($value)
    {
        $pattern = self::getMoneyPattern();

        return (bool)preg_match($pattern, $value);
    }

    /**
     * Returns valid given hexadecimal value of color.
     * If the value is invalid, throws an exception or returns false.
     *
     * @param string $color          Color to verify
     * @param bool   $throwException (optional) If is set to true, throws an exception if given color is invalid
     *                               (default behaviour). Otherwise - not.
     * @return string|bool
     *
     * @throws IncorrectColorHexLengthException
     * @throws InvalidColorHexValueException
     */
    public static function getValidColorHexValue($color, $throwException = true)
    {
        $color = Miscellaneous::replace($color, '/#/', '');
        $length = strlen($color);

        if (3 === $length) {
            $color = Miscellaneous::replace($color, '/(.)(.)(.)/', '$1$1$2$2$3$3');
        } else {
            if (6 !== $length) {
                if ($throwException) {
                    throw new IncorrectColorHexLengthException($color);
                }

                return false;
            }
        }

        $pattern = self::$patterns['color'];
        $match = (bool)preg_match($pattern, $color);

        if (!$match) {
            if ($throwException) {
                throw new InvalidColorHexValueException($color);
            }

            return false;
        }

        return strtolower($color);
    }
}
