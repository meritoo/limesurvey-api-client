<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\Common\Test\Base;

use DateTime;
use Meritoo\Common\Exception\Type\UnknownOopVisibilityTypeException;
use Meritoo\Common\Iterator\CommonIterator;
use Meritoo\Common\Type\OopVisibilityType;
use Meritoo\Common\Utilities\Miscellaneous;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * Base test case with common methods and data providers
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
abstract class BaseTestCase extends TestCase
{
    /**
     * Path of directory with data used by test cases
     *
     * @var string
     */
    private static $testsDataDirPath = '.data/tests';

    /**
     * Provides an empty value
     *
     * @return CommonIterator
     * //return Generator
     */
    public function provideEmptyValue()
    {
        return new CommonIterator([
            '',
            '   ',
            null,
            0,
            false,
            [],
        ]);

        /*
        yield[''];
        yield['   '];
        yield[null];
        yield[0];
        yield[false];
        yield[[]];
        */
    }

    /**
     * Provides boolean value
     *
     * @return CommonIterator
     * //return Generator
     */
    public function provideBooleanValue()
    {
        return new CommonIterator([
            true,
            false,
        ]);

        /*
        yield[false];
        yield[true];
        */
    }

    /**
     * Provides instance of DateTime class
     *
     * @return CommonIterator
     * //return Generator
     */
    public function provideDateTimeInstance()
    {
        return new CommonIterator([
            new DateTime(),
            new DateTime('yesterday'),
            new DateTime('now'),
            new DateTime('tomorrow'),
        ]);

        /*
        yield[new DateTime()];
        yield[new DateTime('yesterday')];
        yield[new DateTime('now')];
        yield[new DateTime('tomorrow')];
        */
    }

    /**
     * Provides relative / compound format of DateTime
     *
     * @return CommonIterator
     * //return Generator
     */
    public function provideDateTimeRelativeFormat()
    {
        return new CommonIterator([
            'now',
            'yesterday',
            'tomorrow',
            'back of 10',
            'front of 10',
            'last day of February',
            'first day of next month',
            'last day of previous month',
            'last day of next month',
            'Y-m-d',
            'Y-m-d 10:00',
        ]);

        /*
        yield['now'];
        yield['yesterday'];
        yield['tomorrow'];
        yield['back of 10'];
        yield['front of 10'];
        yield['last day of February'];
        yield['first day of next month'];
        yield['last day of previous month'];
        yield['last day of next month'];
        yield['Y-m-d'];
        yield['Y-m-d 10:00'];
        */
    }

    /**
     * Provides path of not existing file, e.g. "lorem/ipsum.jpg"
     *
     * @return CommonIterator
     * //return Generator
     */
    public function provideNotExistingFilePath()
    {
        return new CommonIterator([
            'lets-test.doc',
            'lorem/ipsum.jpg',
            'surprise/me/one/more/time.txt',
        ]);

        /*
        yield['lets-test.doc'];
        yield['lorem/ipsum.jpg'];
        yield['surprise/me/one/more/time.txt'];
        */
    }

    /**
     * Returns path of file used by tests.
     * It should be placed in /.data/tests directory of this project.
     *
     * @param string $fileName      Name of file
     * @param string $directoryPath (optional) Path of directory containing the file
     * @return string
     */
    public function getFilePathToTests($fileName, $directoryPath = '')
    {
        $rootPath = Miscellaneous::getProjectRootPath();

        $paths = [
            $rootPath,
            self::$testsDataDirPath,
            $directoryPath,
            $fileName,
        ];

        return Miscellaneous::concatenatePaths($paths);
    }

    /**
     * Verifies visibility and arguments of method
     *
     * @param string                  $classNamespace         Namespace of class that contains method to verify
     * @param string|ReflectionMethod $method                 Name of method or just the method to verify
     * @param string                  $visibilityType         Expected visibility of verified method. One of
     *                                                        OopVisibilityType class constants.
     * @param int                     $argumentsCount         (optional) Expected count/amount of arguments of the
     *                                                        verified method
     * @param int                     $requiredArgumentsCount (optional) Expected count/amount of required arguments
     *                                                        of the verified method
     * @throws UnknownOopVisibilityTypeException
     *
     * Attention. 2nd argument, the $method, may be:
     * - string - name of the method
     * - instance of ReflectionMethod - just the method (provided by ReflectionClass::getMethod() method)
     */
    protected static function assertMethodVisibilityAndArguments(
        $classNamespace,
        $method,
        $visibilityType,
        $argumentsCount = 0,
        $requiredArgumentsCount = 0
    ) {
        /*
         * Type of visibility is correct?
         */
        if (!(new OopVisibilityType())->isCorrectType($visibilityType)) {
            throw new UnknownOopVisibilityTypeException($visibilityType);
        }

        $reflection = new ReflectionClass($classNamespace);

        /*
         * Name of method provided only?
         * Let's find instance of the method (based on reflection)
         */
        if (!$method instanceof ReflectionMethod) {
            $method = $reflection->getMethod($method);
        }

        switch ($visibilityType) {
            case OopVisibilityType::IS_PUBLIC:
                static::assertTrue($method->isPublic());
                break;

            case OopVisibilityType::IS_PROTECTED:
                static::assertTrue($method->isProtected());
                break;

            case OopVisibilityType::IS_PRIVATE:
                static::assertTrue($method->isPrivate());
                break;
        }

        static::assertEquals($argumentsCount, $method->getNumberOfParameters());
        static::assertEquals($requiredArgumentsCount, $method->getNumberOfRequiredParameters());
    }

    /**
     * Verifies visibility and arguments of class constructor
     *
     * @param string $classNamespace         Namespace of class that contains constructor to verify
     * @param string $visibilityType         Expected visibility of verified method. One of OopVisibilityType class
     *                                       constants.
     * @param int    $argumentsCount         (optional) Expected count/amount of arguments of the verified method
     * @param int    $requiredArgumentsCount (optional) Expected count/amount of required arguments of the verified
     *                                       method
     * @throws UnknownOopVisibilityTypeException
     */
    protected static function assertConstructorVisibilityAndArguments(
        $classNamespace,
        $visibilityType,
        $argumentsCount = 0,
        $requiredArgumentsCount = 0
    ) {
        /*
         * Let's grab the constructor
         */
        $reflection = new ReflectionClass($classNamespace);
        $method = $reflection->getConstructor();

        return static::assertMethodVisibilityAndArguments($classNamespace, $method, $visibilityType, $argumentsCount, $requiredArgumentsCount);
    }

    /**
     * Asserts that class with given namespace has no constructor
     *
     * @param string $classNamespace Namespace of class that contains constructor to verify
     */
    protected static function assertHasNoConstructor($classNamespace)
    {
        /*
         * Let's grab the constructor
         */
        $reflection = new ReflectionClass($classNamespace);
        $constructor = $reflection->getConstructor();

        static::assertNull($constructor);
    }

    /**
     * Sets path of directory with data used by test cases
     *
     * @param string $testsDataDirPath Path of directory with data used by test cases
     */
    protected static function setTestsDataDirPath($testsDataDirPath)
    {
        static::$testsDataDirPath = $testsDataDirPath;
    }

    /**
     * Returns a mock object for the specified class
     *
     * @param string $originalClassName Name of the class to mock
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMock($originalClassName)
    {
        $methods = [];
        $arguments = [];
        $mockClassName = '';
        $callOriginalConstructor = false;

        return $this->getMock($originalClassName, $methods, $arguments, $mockClassName, $callOriginalConstructor);
    }
}
