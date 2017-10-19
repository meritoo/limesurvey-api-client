<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\Common\Utilities;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\Cookie;
use Transliterator;

/**
 * Miscellaneous methods (only static functions)
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class Miscellaneous
{
    /**
     * Returns directory's content (names of directories and files)
     *
     * @param string $directoryPath Path of directory who content should be returned
     * @param bool   $recursive     (optional) If is set to true, sub-directories are also searched for content.
     *                              Otherwise - only content of given directory is returned.
     * @param int    $maxFilesCount (optional) Maximum files that will be returned. If it's null, all files are
     *                              returned.
     * @return array|null
     */
    public static function getDirectoryContent($directoryPath, $recursive = false, $maxFilesCount = null)
    {
        /*
         * Path of directory is unknown or does not exist and is not readable?
         * Nothing to do
         */
        if (empty($directoryPath) || !is_readable($directoryPath)) {
            return null;
        }

        $files = [];
        $startFileName = '';

        if (self::isFilePath($directoryPath)) {
            $startDirectoryPath = dirname($directoryPath);
            $startFileName = str_replace($startDirectoryPath, '', $directoryPath);

            $directoryPath = $startDirectoryPath;
        }

        $count = 0;
        $startFileFound = false;

        if (!Regex::endsWith($directoryPath, '/')) {
            $directoryPath .= '/';
        }

        if (Regex::startsWith($startFileName, '/')) {
            $startFileName = mb_substr($startFileName, 1);
        }

        $directoryContent = scandir($directoryPath);

        if (!empty($directoryContent)) {
            foreach ($directoryContent as $fileName) {
                if ('.' != $fileName && '..' != $fileName) {
                    $content = null;

                    if (!empty($startFileName) && !$startFileFound) {
                        if ($fileName == $startFileName) {
                            $startFileFound = true;
                        }

                        continue;
                    }

                    if ($recursive && is_dir($directoryPath . $fileName)) {
                        $content = self::getDirectoryContent($directoryPath . $fileName, true, $maxFilesCount - $count);
                    }

                    if (null !== $content) {
                        $files[$fileName] = $content;

                        if (!empty($maxFilesCount)) {
                            $count += Arrays::getNonArrayElementsCount($content);
                        }
                    } else {
                        $files[] = $fileName;

                        if (!empty($maxFilesCount)) {
                            ++$count;
                        }
                    }

                    if (!empty($maxFilesCount) && $count >= $maxFilesCount) {
                        break;
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Returns information if given path it's a file's path, if the path contains file name
     *
     * @param string $path The path to check
     * @return bool
     */
    public static function isFilePath($path)
    {
        $info = pathinfo($path);

        return isset($info['extension']) && !empty($info['extension']);
    }

    /**
     * Converts checkbox value to boolean
     *
     * @param string $checkboxValue Checkbox value
     * @return bool
     */
    public static function checkboxValue2Boolean($checkboxValue)
    {
        $mapping = [
            'on'  => true,
            'off' => false,
        ];

        $clearValue = strtolower(trim($checkboxValue));

        if (isset($mapping[$clearValue])) {
            return $mapping[$clearValue];
        }

        return false;
    }

    /**
     * Converts checkbox value to integer
     *
     * @param string $checkboxValue Checkbox value
     * @return int
     */
    public static function checkboxValue2Integer($checkboxValue)
    {
        return (int)self::checkboxValue2Boolean($checkboxValue);
    }

    /**
     * Returns name of file with given extension after verification if it contains the extension
     *
     * @param string $fileName  The file name to verify
     * @param string $extension The extension to verify and include
     * @return string
     */
    public static function includeFileExtension($fileName, $extension)
    {
        if (self::getFileExtension($fileName, true) != strtolower($extension)) {
            return sprintf('%s.%s', $fileName, $extension);
        }

        return $fileName;
    }

    /**
     * Returns file extension
     *
     * @param string $fileName    File name
     * @param bool   $asLowerCase (optional) if true extension is returned as lowercase string
     * @return string
     */
    public static function getFileExtension($fileName, $asLowerCase = false)
    {
        $extension = '';
        $matches = [];

        if (preg_match('|(.+)\.(.+)|', $fileName, $matches)) {
            $extension = end($matches);
        }

        if ($asLowerCase) {
            return strtolower($extension);
        }

        return $extension;
    }

    /**
     * Returns file name from given path
     *
     * @param string $path A path that contains file name
     * @return string
     */
    public static function getFileNameFromPath($path)
    {
        $matches = [];
        $pattern = sprintf('|([^\%s.]+\.[A-Za-z0-9.]+)$|', DIRECTORY_SEPARATOR);

        if ((bool)preg_match($pattern, $path, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Returns unique name for file based on given original name
     *
     * @param string $originalFileName Original name of the file
     * @param int    $objectId         (optional) Object ID, the ID of database's row. May be included into the
     *                                 generated / unique name.
     * @return string
     */
    public static function getUniqueFileName($originalFileName, $objectId = 0)
    {
        /*
         * Get parts of the file name:
         * - without extension
         * - and... the extension
         */
        $withoutExtension = self::getFileNameWithoutExtension($originalFileName);
        $extension = self::getFileExtension($originalFileName, true);

        /*
         * Let's clear name of file
         *
         * Attention. The name without extension may be cleared / urlized only
         * to avoid incorrect name by replacing "." with "-".
         */
        $withoutExtension = Urlizer::urlize($withoutExtension);

        /*
         * Now I have to complete the template used to build / generate unique name
         */
        $template = '%s-%s'; // file's name and unique key

        if ($objectId > 0) {
            $template .= '-%s'; // object ID
        }

        $template .= '.%s'; // file's extension

        /*
         * Add some uniqueness
         */
        $unique = uniqid(mt_rand(), true);

        /*
         * Finally build and return the unique name
         */
        if ($objectId > 0) {
            return sprintf($template, $withoutExtension, $unique, $objectId, $extension);
        }

        return sprintf($template, $withoutExtension, $unique, $extension);
    }

    /**
     * Returns file name without extension
     *
     * @param string $fileName The file name
     * @return string
     */
    public static function getFileNameWithoutExtension($fileName)
    {
        $matches = [];

        if (is_string($fileName) && (bool)preg_match('|(.+)\.(.+)|', $fileName, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Converts value to non-negative integer (element of the set {0, 1, 2, 3, ...})
     *
     * @param mixed $value               Value to convert
     * @param int   $negativeReplacement (optional) Replacement for negative value
     * @return int
     */
    public static function value2NonNegativeInteger($value, $negativeReplacement = 0)
    {
        $effect = (int)$value;

        if ($effect < 0) {
            return $negativeReplacement;
        }

        return $effect;
    }

    /**
     * Displays variable content as preformatted text (fixed-width font and preserves both spaces and line breaks)
     *
     * If xdebug php module is loaded, displays variable using var_dump(), otherwise <pre>var_dump()</pre>.
     * You can pass as many variables as you wish.
     *
     * Pass each variable as argument of this function. Amount unlimited. Variables are loaded using the
     * func_get_args() function (@see http://pl1.php.net/manual/en/function.func-get-args.php).
     */
    public static function variableDump()
    {
        $xdebugLoaded = self::isPhpModuleLoaded('xdebug');

        if (!$xdebugLoaded) {
            echo '<pre>';
        }

        $arguments = func_get_args();

        foreach ($arguments as $argument) {
            var_dump($argument);
        }

        if (!$xdebugLoaded) {
            echo '</pre>';
        }
    }

    /**
     * Returns information if given PHP module is compiled and loaded
     *
     * @param string $phpModuleName PHP module name
     * @return bool
     */
    public static function isPhpModuleLoaded($phpModuleName)
    {
        $phpModulesArray = get_loaded_extensions();

        return in_array($phpModuleName, $phpModulesArray);
    }

    /**
     * Converts given string characters to latin characters
     *
     * @param string $string          String to convert
     * @param bool   $lowerCaseHuman  (optional) If is set to true, converted string is returned as lowercase and
     *                                human-readable. Otherwise - as original.
     * @param string $replacementChar (optional) Replacement character for all non-latin characters and uppercase
     *                                letters, if 2nd argument is set to true
     * @return string
     */
    public static function toLatin($string, $lowerCaseHuman = true, $replacementChar = '-')
    {
        if (is_string($string)) {
            $string = trim($string);
        }

        /*
         * Empty value?
         * Nothing to do
         */
        if (empty($string)) {
            return '';
        }

        $converter = Transliterator::create('Latin-ASCII;');

        /*
         * Oops, cannot instantiate converter
         * Nothing to do
         */
        if (null === $converter) {
            return '';
        }

        $converted = $converter->transliterate($string);

        /*
         * Make the string lowercase and human-readable
         */
        if ($lowerCaseHuman) {
            $matches = [];
            $matchCount = preg_match_all('|[A-Z]{1}[^A-Z]*|', $converted, $matches);

            if ($matchCount > 0) {
                $parts = $matches[0];
                $converted = mb_strtolower(implode($replacementChar, $parts));
            }
        }

        /*
         * Let's replace special characters to spaces
         * ...and finally spaces to $replacementChar
         */
        $replaced = preg_replace('|[^a-zA-Z0-9]|', ' ', $converted);

        return preg_replace('| +|', $replacementChar, trim($replaced));
    }

    /**
     * Returns unique string
     *
     * @param string $prefix (optional) Prefix of the unique string. May be used while generating the unique
     *                       string simultaneously on several hosts at the same microsecond.
     * @param bool   $hashed (optional) If is set to true, the unique string is hashed additionally. Otherwise - not.
     * @return string
     */
    public static function getUniqueString($prefix = '', $hashed = false)
    {
        $unique = uniqid($prefix, true);

        if ($hashed) {
            return sha1($unique);
        }

        return $unique;
    }

    /**
     * Replaces part of string with other string or strings.
     * There is a few combination of what should be searched and with what it should be replaced.
     *
     * @param string|array $subject      The string or an array of strings to search and replace
     * @param string|array $search       String or pattern or array of patterns to find. It may be: string, an array
     *                                   of strings or an array of patterns.
     * @param string|array $replacement  The string or an array of strings to replace. It may be: string or an array
     *                                   of strings.
     * @param bool         $quoteStrings (optional) If is set to true, strings are surrounded with single quote sign
     * @return string
     *
     * Example:
     * a) an array of strings to search
     * $subject = [
     *      'Lorem ipsum dolor sit amet.',
     *      'Etiam ullamcorper. Suspendisse a pellentesque dui, non felis.',
     * ];
     *
     * b) an array of patterns
     * $search = [
     *      '|ipsum|',
     *      '|pellentesque|',
     * ];
     *
     * c) an array of strings to replace
     * $replacement = [
     *      'commodo',
     *      'interdum',
     * ];
     *
     * The result:
     * [
     *      'Lorem commodo dolor sit amet.',
     *      'Etiam ullamcorper. Suspendisse a interdum dui, non felis.',
     * ];
     */
    public static function replace($subject, $search, $replacement, $quoteStrings = false)
    {
        $effect = $subject;

        $searchIsString = is_string($search);
        $searchIsArray = is_array($search);

        /*
         * Value to find is neither a string nor an array OR it's an empty string?
         * Nothing to do
         */
        if ((!$searchIsString && !$searchIsArray) || ($searchIsString && 0 == strlen($search))) {
            return $effect;
        }

        $replacementIsString = is_string($replacement);
        $replacementIsArray = is_array($replacement);

        $bothAreStrings = $searchIsString && $replacementIsString;
        $bothAreArrays = $searchIsArray && $replacementIsArray;

        /*
         * First step: replace strings, simple operation with strings
         */
        if ($searchIsString && $replacementIsString) {
            if ($quoteStrings) {
                $replacement = '\'' . $replacement . '\'';
            }

            $effect = str_replace($search, $replacement, $subject);
        }

        /*
         * Second step: replace with regular expressions.
         * Attention. Searched and replacement value should be the same type: strings or arrays.
         */
        if ($effect == $subject && ($bothAreStrings || $bothAreArrays)) {
            if ($quoteStrings && $replacementIsString) {
                $replacement = '\'' . $replacement . '\'';
            }

            /*
             * I have to avoid string that contains spaces only, e.g. "  ".
             * It's required to avoid bug: preg_replace(): Empty regular expression.
             */
            $search = trim($search);

            if ($searchIsArray || ($searchIsString && !empty($search))) {
                $effect = preg_replace($search, $replacement, $subject);
            }
        }

        /*
         * Third step: complex replace of the replacement defined as an array.
         * It may be useful when you want to search for a one string and replace the string with multiple values.
         */
        if ($effect == $subject && $searchIsString && $replacementIsArray) {
            $subjectIsArray = is_array($subject);
            $effect = '';

            if ($subjectIsArray) {
                $effect = [];
            }

            /*
             * I have to make the subject an array...
             */
            $subject = Arrays::makeArray($subject);

            /*
             * ...and use iterate through the subjects,
             * because explode() function expects strings as both arguments (1st and 2nd)
             */
            foreach ($subject as $subSubject) {
                $subEffect = '';

                $exploded = explode($search, $subSubject);
                $explodedCount = count($exploded);

                if ($quoteStrings) {
                    foreach ($replacement as &$item) {
                        if (is_string($item)) {
                            $item = '\'' . $item . '\'';
                        }
                    }

                    unset($item);
                }

                foreach ($exploded as $key => $item) {
                    $subEffect .= $item;

                    /*
                     * The replacement shouldn't be included when the searched string was not found
                     */
                    if ($explodedCount > 1 && $key < $explodedCount - 1 && isset($replacement[$key])) {
                        $subEffect .= $replacement[$key];
                    }
                }

                if ($subjectIsArray) {
                    $effect[] = $subEffect;
                    continue;
                }

                $effect .= $subEffect;
            }
        }

        return $effect;
    }

    /**
     * Returns new file name after adding prefix or suffix (or both of them) to the name
     *
     * @param string $fileName The file name
     * @param string $prefix   File name prefix
     * @param string $suffix   File name suffix
     * @return string
     */
    public static function getNewFileName($fileName, $prefix, $suffix)
    {
        $effect = $fileName;

        if (!empty($fileName) && (!empty($prefix) || !empty($suffix))) {
            $name = self::getFileNameWithoutExtension($fileName);
            $extension = self::getFileExtension($fileName);

            $effect = sprintf('%s%s%s.%s', $prefix, $name, $suffix, $extension);
        }

        return $effect;
    }

    /**
     * Returns operating system name PHP is running on
     *
     * @return string
     */
    public static function getOperatingSystemNameServer()
    {
        return php_uname('s');
    }

    /**
     * Returns part of string preserving words
     *
     * @param string $text      The string / text
     * @param int    $maxLength Maximum length of given string
     * @param string $suffix    (optional) The suffix to add at the end of string
     * @return string
     */
    public static function substringToWord($text, $maxLength, $suffix = '...')
    {
        $effect = $text;

        $textLength = mb_strlen($text, 'utf-8');
        $suffixLength = mb_strlen($suffix, 'utf-8');

        $maxLength -= $suffixLength;

        if ($textLength > $maxLength) {
            $effect = mb_substr($text, 0, $maxLength, 'utf-8');
            $lastSpacePosition = mb_strrpos($effect, ' ', 'utf-8');

            if (false !== $lastSpacePosition) {
                $effect = mb_substr($effect, 0, $lastSpacePosition, 'utf-8');
            }

            $effect .= $suffix;
        }

        return $effect;
    }

    /**
     * Breaks long text
     *
     * @param string $text                   The text to check and break
     * @param int    $perLine                (optional) Characters count per line
     * @param string $separator              (optional) Separator that is placed beetwen lines
     * @param string $encoding               (optional) Character encoding. Used by mb_substr().
     * @param int    $proportionalAberration (optional) Proportional aberration for chars (percent value)
     * @return string
     */
    public static function breakLongText(
        $text,
        $perLine = 100,
        $separator = '<br>',
        $encoding = 'utf-8',
        $proportionalAberration = 20
    ) {
        $effect = $text;
        $textLength = mb_strlen($text);

        if (!empty($text) && $textLength > $perLine) {
            /*
             * The html_entity_decode() function is used here, because while operating
             * on string that contains only special characters the string is divided
             * incorrectly, e.g. "<<<<<" -> "&lt;&lt;&lt;&lt;&<br />lt;".
             */
            //$text = htmlspecialchars_decode($text);
            $text = html_entity_decode($text, ENT_QUOTES);

            $effect = '';
            $currentPosition = 0;

            $charsAberration = ceil($perLine * ($proportionalAberration / 100));
            $charsPerLineDefault = $perLine;

            while ($currentPosition <= $textLength) {
                $insertSeparator = false;

                /*
                 * Looking for spaces before and after current position. It was done, because text wasn't
                 * broken properly and some words were breaked and placed into two lines.
                 */
                if ($charsAberration > 0) {
                    $length = $perLine + $charsAberration;
                    $lineWithAberration = mb_substr($text, $currentPosition, $length, $encoding);

                    if (!Regex::contains($lineWithAberration, ' ')) {
                        $length = $perLine - $charsAberration;
                        $lineWithAberration = mb_substr($text, $currentPosition, $length, $encoding);
                    }

                    if (Regex::startsWith($lineWithAberration, ' ')) {
                        ++$currentPosition;
                        $lineWithAberration = ltrim($lineWithAberration);
                    }

                    $spacePosition = mb_strrpos($lineWithAberration, ' ', 0, $encoding);

                    if ($spacePosition > 0) {
                        $perLine = $spacePosition;
                        $insertSeparator = true;
                    }
                }

                $charsOneLine = mb_substr($text, $currentPosition, $perLine, $encoding);

                /*
                 * The htmlspecialchars() function is used here, because...
                 * Reason and comment the same as above for html_entity_decode() function.
                 */

                $effect .= htmlspecialchars($charsOneLine);
                //$effect .= $charsOneLine;

                $currentPosition += $perLine;
                $oneLineContainsSpace = Regex::contains($charsOneLine, ' ');

                if (($insertSeparator || !$oneLineContainsSpace) && $currentPosition <= $textLength) {
                    $effect .= $separator;
                }

                $perLine = $charsPerLineDefault;
            }
        }

        return $effect;
    }

    /**
     * Removes the directory.
     * If not empty, removes also contents.
     *
     * @param string $directoryPath Directory path
     * @param bool   $contentOnly   (optional) If is set to true, only content of the directory is removed, not
     *                              directory. Otherwise - directory is removed too.
     * @return bool
     */
    public static function removeDirectory($directoryPath, $contentOnly = false)
    {
        if (!file_exists($directoryPath)) {
            return true;
        }

        if (!is_dir($directoryPath)) {
            return unlink($directoryPath);
        }

        foreach (scandir($directoryPath) as $item) {
            if ('.' == $item || '..' == $item) {
                continue;
            }

            if (!self::removeDirectory($directoryPath . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        if (!$contentOnly) {
            return rmdir($directoryPath);
        }

        return true;
    }

    /**
     * Returns information if value is decimal
     *
     * @param mixed $value The value to check
     * @return bool
     */
    public static function isDecimal($value)
    {
        return is_scalar($value) && floor($value) !== (float)$value;
    }

    /**
     * Returns the string in camel case
     *
     * @param string $string    The string to convert e.g. this-is-eXamplE (return: thisIsExample)
     * @param string $separator (optional) Separator used to find parts of the string, e.g. '-' or ','
     * @return string
     */
    public static function getCamelCase($string, $separator = ' ')
    {
        if (empty($string)) {
            return '';
        }

        $effect = '';
        $members = explode($separator, $string);

        foreach ($members as $key => $value) {
            $value = mb_strtolower($value);

            if (0 == $key) {
                $effect .= self::lowercaseFirst($value);
            } else {
                $effect .= self::uppercaseFirst($value);
            }
        }

        return $effect;
    }

    /**
     * Make a string's first character lowercase
     *
     * @param string    $text          The text to get first character lowercase
     * @param bool|null $restLowercase (optional) Information that to do with rest of given string
     * @return string
     *
     * Values of the $restLowercase argument:
     * - null (default): nothing is done with the string
     * - true: the rest of string is lowercased
     * - false: the rest of string is uppercased
     *
     * Some explanation:
     * Function lcfirst() is available for PHP >= 5.30, so I wrote my own function that lowercases first character of
     * the string.
     */
    public static function lowercaseFirst($text, $restLowercase = null)
    {
        if (empty($text)) {
            return '';
        }

        $effect = $text;

        if ($restLowercase) {
            $effect = mb_strtolower($effect);
        } elseif (false === $restLowercase) {
            $effect = mb_strtoupper($effect);
        }

        if (function_exists('lcfirst')) {
            $effect = lcfirst($effect);
        } else {
            $first = mb_strtolower($effect[0]);
            $rest = mb_substr($effect, 1);

            $effect = $first . $rest;
        }

        return $effect;
    }

    /**
     * Make a string's first character uppercase
     *
     * @param string    $text          The text to get uppercase
     * @param bool|null $restLowercase (optional) Information that to do with rest of given string
     * @return string
     *
     * Values of the $restLowercase argument:
     * - null (default): nothing is done with the string
     * - true: the rest of string is lowercased
     * - false: the rest of string is uppercased
     */
    public static function uppercaseFirst($text, $restLowercase = null)
    {
        if (empty($text)) {
            return '';
        }

        $effect = $text;

        if ($restLowercase) {
            $effect = mb_strtolower($effect);
        } elseif (false === $restLowercase) {
            $effect = mb_strtoupper($effect);
        }

        if (function_exists('ucfirst')) {
            $effect = ucfirst($effect);
        } else {
            $first = mb_strtoupper($effect[0]);
            $rest = mb_substr($effect, 1);

            $effect = $first . $rest;
        }

        return $effect;
    }

    /**
     * Quotes given value with apostrophes or quotation marks
     *
     * @param mixed $value         The value to quote
     * @param bool  $useApostrophe (optional) If is set to true, apostrophes are used. Otherwise - quotation marks.
     * @return string
     */
    public static function quoteValue($value, $useApostrophe = true)
    {
        if (is_string($value)) {
            $quotes = '"';

            if ($useApostrophe) {
                $quotes = '\'';
            }

            $value = sprintf('%s%s%s', $quotes, $value, $quotes);
        }

        return $value;
    }

    /**
     * Returns size (of file or directory) in human readable format
     *
     * @param int $sizeInBytes The size in bytes
     * @return string
     */
    public static function getHumanReadableSize($sizeInBytes)
    {
        $units = [
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB',
        ];
        $index = floor(log($sizeInBytes, 1024));

        $size = round($sizeInBytes / pow(1024, $index), 2);
        $unit = $units[(int)$index];

        return sprintf('%s %s', $size, $unit);
    }

    /**
     * Returns string without the last element.
     * The string should contain given separator.
     *
     * @param string $string    The string to check
     * @param string $separator The separator which divides elements of string
     * @return string
     */
    public static function getStringWithoutLastElement($string, $separator)
    {
        $elements = self::getStringElements($string, $separator);
        $lastKey = Arrays::getLastKey($elements);

        unset($elements[$lastKey]);

        return implode($separator, $elements);
    }

    /**
     * Returns elements of given string divided by given separator
     *
     * @param string $string    The string to check
     * @param string $separator The separator which divides elements of string
     * @return array
     */
    public static function getStringElements($string, $separator)
    {
        $matches = [];
        $pattern = sprintf('|[^\%s]+|', $separator);
        $matchCount = preg_match_all($pattern, $string, $matches);

        if ($matchCount > 1) {
            return $matches[0];
        }

        return [];
    }

    /**
     * Returns the last element of given string divided by given separator
     *
     * @param string $string    The string to check
     * @param string $separator The separator which divides elements of string
     * @return string|null
     */
    public static function getLastElementOfString($string, $separator)
    {
        $elements = self::getStringElements($string, $separator);

        /*
         * No elements?
         * Nothing to do
         */
        if (empty($elements)) {
            return null;
        }

        $element = Arrays::getLastElement($elements);

        return trim($element);
    }

    /**
     * Returns smartly trimmed string.
     * If the string is empty, contains only spaces, e.g. " ", nothing is done and the original string is returned.
     *
     * @param string $string The string to trim
     * @return string
     */
    public static function trimSmart($string)
    {
        $trimmed = trim($string);

        if (empty($trimmed)) {
            return $string;
        }

        return $trimmed;
    }

    /**
     * Returns concatenated given paths
     *
     * The paths may be passed as:
     * - an array of paths / strings
     * - strings passed as following arguments
     *
     * Examples:
     * - concatenatePaths(['path/first', 'path/second', 'path/third']);
     * - concatenatePaths('path/first', 'path/second', 'path/third');
     *
     * @param string|array $paths Paths co concatenate. As described above: an array of paths / strings or strings
     *                            passed as following arguments.
     * @return string
     */
    public static function concatenatePaths($paths)
    {
        /*
         * If paths are not provided as array, get the paths from methods' arguments
         */
        if (!is_array($paths)) {
            $paths = func_get_args();
        }

        /*
         * No paths provided?
         * Nothing to do
         */
        if (empty($paths)) {
            return '';
        }

        /*
         * Some useful variables
         */
        $concatenated = '';
        $firstWindowsBased = false;
        $separator = DIRECTORY_SEPARATOR;

        foreach ($paths as $path) {
            $path = trim($path);

            /*
             * Empty paths are useless
             */
            if (empty($path)) {
                continue;
            }

            /*
             * Does the first path is a Windows-based path?
             */
            if (Arrays::isFirstElement($paths, $path)) {
                $firstWindowsBased = Regex::isWindowsBasedPath($path);

                if ($firstWindowsBased) {
                    $separator = '\\';
                }
            }

            /*
             * Remove the starting / beginning directory's separator
             */
            $path = self::removeStartingDirectorySeparator($path, $separator);

            /*
             * Removes the ending directory's separator
             */
            $path = self::removeEndingDirectorySeparator($path, $separator);

            /*
             * If OS is Windows, first part of the concatenated path should be the first passed path,
             * because in Windows paths starts with drive letter, e.g. "C:", and the directory separator is not
             * necessary at the beginning.
             */
            if ($firstWindowsBased && empty($concatenated)) {
                $concatenated = $path;
                continue;
            }

            /*
             * Concatenate the paths / strings with OS-related directory separator between them (slash or backslash)
             */
            $concatenated = sprintf('%s%s%s', $concatenated, $separator, $path);
        }

        return $concatenated;
    }

    /**
     * Removes the starting / beginning directory's separator
     *
     * @param string $text      Text that may contain a directory's separator at the start / beginning
     * @param string $separator (optional) The directory's separator, e.g. "/". If is empty (not provided), separator
     *                          provided by operating system will be used.
     * @return string
     */
    public static function removeStartingDirectorySeparator($text, $separator = '')
    {
        /*
         * Not a string?
         * Nothing to do
         */
        if (!is_string($text)) {
            return '';
        }

        if (empty($separator)) {
            $separator = DIRECTORY_SEPARATOR;
        }

        $effect = trim($text);

        if (Regex::startsWithDirectorySeparator($effect, $separator)) {
            $effect = mb_substr($effect, mb_strlen($separator));
        }

        return $effect;
    }

    /**
     * Removes the ending directory's separator
     *
     * @param string $text      Text that may contain a directory's separator at the end
     * @param string $separator (optional) The directory's separator, e.g. "/". If is empty (not provided), system's
     *                          separator is used.
     * @return string
     */
    public static function removeEndingDirectorySeparator($text, $separator = '')
    {
        /*
         * Not a string?
         * Nothing to do
         */
        if (!is_string($text)) {
            return '';
        }

        if (empty($separator)) {
            $separator = DIRECTORY_SEPARATOR;
        }

        $effect = trim($text);

        if (Regex::endsWithDirectorySeparator($effect, $separator)) {
            $effect = mb_substr($effect, 0, mb_strlen($effect) - mb_strlen($separator));
        }

        return $effect;
    }

    /**
     * Returns safely value of global variable, found in one of the global arrays / variables, e.g. $_GET
     *
     * @param int    $globalSourceType Represents the global array / variable. One of constants: INPUT_GET, INPUT_POST,
     *                                 INPUT_COOKIE, INPUT_SERVER, or INPUT_ENV.
     * @param string $variableName     Name of the variable to return value
     * @return mixed
     */
    public static function getSafelyGlobalVariable($globalSourceType, $variableName)
    {
        $value = filter_input($globalSourceType, $variableName);

        if (null === $value) {
            $globalSource = null;

            switch ($globalSourceType) {
                case INPUT_GET:
                    $globalSource = $_GET;
                    break;

                case INPUT_POST:
                    $globalSource = $_POST;
                    break;

                case INPUT_COOKIE:
                    $globalSource = $_COOKIE;
                    break;

                case INPUT_SERVER:
                    $globalSource = $_SERVER;
                    break;

                case INPUT_ENV:
                    $globalSource = $_ENV;
                    break;
            }

            if (null !== $globalSource && isset($globalSource[$variableName])) {
                $value = $globalSource[$variableName];

                if (!ini_get('magic_quotes_gpc')) {
                    $value = addslashes($value);
                }
            }
        }

        return $value;
    }

    /**
     * Returns a CURL response with parsed HTTP headers as array with "headers", "cookies" and "content" keys
     *
     * The headers and cookies are parsed and returned as an array, and an array of Cookie objects. Returned array looks
     * like this example:
     * <code>
     * [
     *      'headers' => [
     *          'Content-Type' => 'text/html; charset=UTF-8',
     *          ...
     *      ],
     *      'cookies' => [
     *          new Symfony\Component\HttpFoundation\Cookie(),
     *          new Symfony\Component\HttpFoundation\Cookie(),
     *          ...
     *      ],
     *      'content' => '<html>...</html>'
     * ]
     * </code>
     *
     * If you want to attach HTTP headers into response content by CURL you need to set "CURLOPT_HEADER" option
     * to "true". To read exact length of HTTP headers from CURL you can use "curl_getinfo()" function
     * and read "CURLINFO_HEADER_SIZE" option.
     *
     * @param string $response   the full content of response, including HTTP headers
     * @param int    $headerSize The length of HTTP headers in content
     * @return array
     */
    public static function getCurlResponseWithHeaders($response, $headerSize)
    {
        $headerContent = mb_substr($response, 0, $headerSize);
        $content = mb_substr($response, $headerSize);
        $headers = [];
        $cookies = [];

        /*
         * Let's transform headers content into two arrays: headers and cookies
         */
        foreach (explode("\r\n", $headerContent) as $i => $line) {
            /*
             * First line is only HTTP status and is unneeded so skip it
             */
            if (0 === $i) {
                continue;
            }

            if (Regex::contains($line, ':')) {
                list($key, $value) = explode(': ', $line);

                /*
                 * If the header is a "set-cookie" let's save it to "cookies" array
                 */
                if ('Set-Cookie' === $key) {
                    $cookieParameters = explode(';', $value);

                    $name = '';
                    $value = '';
                    $expire = 0;
                    $path = '/';
                    $domain = null;
                    $secure = false;
                    $httpOnly = true;

                    foreach ($cookieParameters as $j => $parameter) {
                        $param = explode('=', $parameter);

                        /*
                         * First parameter will be always a cookie name and it's value. It is not needed to run
                         * further actions for them, so save the values and move to next parameter.
                         */
                        if (0 === $j) {
                            $name = trim($param[0]);
                            $value = trim($param[1]);
                            continue;
                        }

                        /*
                         * Now there would be the rest of cookie parameters, names of params are sent different way so
                         * I need to lowercase the names and remove unneeded spaces.
                         */
                        $paramName = mb_strtolower(trim($param[0]));
                        $paramValue = true;

                        /*
                         * Some parameters don't have value e.g. "secure", but the value for them if they're specified
                         * is "true". Otherwise - just read a value for parameter if exists.
                         */
                        if (array_key_exists(1, $param)) {
                            $paramValue = trim($param[1]);
                        }

                        switch ($paramName) {
                            case 'expires':
                                $expire = $paramValue;
                                break;
                            case 'path':
                                $path = $paramValue;
                                break;
                            case 'domain':
                                $domain = $paramValue;
                                break;
                            case 'secure':
                                $secure = $paramValue;
                                break;
                            case 'httponly':
                                $httpOnly = $paramValue;
                                break;
                        }
                    }

                    /*
                     * Create new Cookie object and add it to "cookies" array.
                     * I must skip to next header as cookies shouldn't be saved in "headers" array.
                     */
                    $cookies[] = new Cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
                    continue;
                }

                /*
                 * Save response header which is not a cookie
                 */
                $headers[$key] = $value;
            }
        }

        return [
            'headers' => $headers,
            'cookies' => $cookies,
            'content' => $content,
        ];
    }

    /**
     * Adds missing the "0" characters to given number until given length is reached
     *
     * Example:
     * - number: 201
     * - length: 6
     * - will be returned: 000201
     *
     * If "before" parameter is false, zeros will be inserted after given number. If given number is longer than
     * given length the number will be returned as it was given to the method.
     *
     * @param mixed $number Number for who the "0" characters should be inserted
     * @param int   $length Wanted length of final number
     * @param bool  $before (optional) If false, 0 characters will be inserted after given number
     * @return string
     */
    public static function fillMissingZeros($number, $length, $before = true)
    {
        /*
         * It's not a number? Empty string is not a number too.
         * Nothing to do
         */
        if (!is_numeric($number)) {
            return '';
        }

        $text = trim($number);
        $textLength = mb_strlen($text);

        if ($length <= $textLength) {
            return $text;
        }

        for ($i = ($length - $textLength); 0 < $i; --$i) {
            if ($before) {
                $text = '0' . $text;
                continue;
            }

            $text = $text . '0';
        }

        return $text;
    }

    /**
     * Returns information if given value is located in interval between given utmost left and right values
     *
     * @param int|float $value Value to verify
     * @param int|float $left  Left utmost value of interval
     * @param int|float $right Right utmost value of interval
     * @return bool
     */
    public static function isBetween($value, $left, $right)
    {
        return $value > $left && $value < $right;
    }

    /**
     * Returns type of given variable.
     * If it's an object, full class name is returned.
     *
     * @param mixed $variable Variable who type should be returned
     * @return string
     */
    public static function getType($variable)
    {
        if (is_object($variable)) {
            return Reflection::getClassName($variable);
        }

        return gettype($variable);
    }

    /**
     * Returns valid value of color's component (e.g. red).
     * If given value is greater than 0, returns the value. Otherwise - 0.
     *
     * @param int  $colorComponent Color's component to verify. Decimal value, e.g. 255.
     * @param bool $asHexadecimal  (optional) If is set to true, hexadecimal value is returned (default behaviour).
     *                             Otherwise - decimal.
     * @return int|string
     */
    public static function getValidColorComponent($colorComponent, $asHexadecimal = true)
    {
        $colorComponent = (int)$colorComponent;

        if ($colorComponent < 0 || $colorComponent > 255) {
            $colorComponent = 0;
        }

        if ($asHexadecimal) {
            $hexadecimal = dechex($colorComponent);

            if (1 == strlen($hexadecimal)) {
                return sprintf('0%s', $hexadecimal, $hexadecimal);
            }

            return $hexadecimal;
        }

        return $colorComponent;
    }

    /**
     * Returns inverted value of color for given color
     *
     * @param string $color Hexadecimal value of color to invert (with or without hash), e.g. "dd244c" or "#22a5fe"
     * @return string
     */
    public static function getInvertedColor($color)
    {
        /*
         * Prepare the color for later usage
         */
        $color = trim($color);
        $withHash = Regex::startsWith($color, '#');

        /*
         * Verify and get valid value of color.
         * An exception will be thrown if the value is not a color.
         */
        $color = Regex::getValidColorHexValue($color);

        /*
         * Grab color's components
         */
        $red = hexdec(substr($color, 0, 2));
        $green = hexdec(substr($color, 2, 2));
        $blue = hexdec(substr($color, 4, 2));

        /*
         * Calculate inverted color's components
         */
        $redInverted = self::getValidColorComponent(255 - $red);
        $greenInverted = self::getValidColorComponent(255 - $green);
        $blueInverted = self::getValidColorComponent(255 - $blue);

        /*
         * Voila, here is the inverted color
         */
        $invertedColor = sprintf('%s%s%s', $redInverted, $greenInverted, $blueInverted);

        if ($withHash) {
            return sprintf('#%s', $invertedColor);
        }

        return $invertedColor;
    }

    /**
     * Returns project's root path.
     * Looks for directory that contains composer.json.
     *
     * @return string
     */
    public static function getProjectRootPath()
    {
        $projectRootPath = '';

        $fileName = 'composer.json';
        $directoryPath = __DIR__;

        /*
         * Path of directory it's not the path of last directory?
         */
        while (DIRECTORY_SEPARATOR !== $directoryPath) {
            $filePath = static::concatenatePaths($directoryPath, $fileName);

            /*
             * Is here file we are looking for?
             * Maybe it's a project's root path
             */
            if (file_exists($filePath)) {
                $projectRootPath = $directoryPath;
            }

            $directoryPath = dirname($directoryPath);
        }

        return $projectRootPath;
    }
}
