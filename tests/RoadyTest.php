<?php

namespace tests;

use PHPUnit\Framework\TestCase;

use \ReflectionClass;
use roady\classes\strings\Id;
use roady\classes\strings\Name;
use roady\classes\strings\Text;
use roady\classes\utilities\Reflection;
use tests\dev\mock\classes\ReflectedAbstractClass;
use tests\dev\mock\classes\ReflectedClass;

/**
 * Defines common methods that may be useful to all roady test
 * classes.
 *
 * All roady test classes must extend from this class.
 *
 */
class RoadyTest extends TestCase
{

    /**
     * Test that PHPUnit tests run.
     *
     * If this test does not run then PHPUnit is not set up correctly.
     *
     */
    public function test_php_unit_tests_are_run(): void
    {
        $this->assertTrue(
            true,
            $this->testFailedMessage(
                $this,
                'test_php_unit_tests_are_run',
                'run if PHPUnit is set up correctly'
            )
        );
    }

    /**
     * Return a message that indicates the failure of a test.
     *
     * @param object $testedInstance The object instance that
     *                               was tested.
     *
     * @param string $testedMethod The name of the method that was
     *                             tested.
     *
     *                             Note: If the test is not specific
     *                             to a method, than an empty string
     *                             can be passed as the $testedMethod.
     *
     * @param string $expectation A brief description of what was
     *                            expected by the test.
     *
     * @example
     *
     * ```
     * echo $this->testFailedMessage(
     *     $this,
     *     'test_php_unit_tests_are_run',
     *     'run if PHPUnit is set up correctly'
     * );
     * // example output:
     *    The tests\RoadyTestCase implementation's
     *    test_php_unit_tests_are_run() method must
     *    run if PHPUnit is set up correctly.
     *
     * ```
     */
    protected function testFailedMessage(
        object $testedInstance,
        string $testedMethod,
        string $expectation
    ): string
    {
        return match(empty($testedMethod)) {
            true => 'The ' .
                    $testedInstance::class .
                    ' implementation fails to fulfill the ' .
                    'following expectation:' .
                    str_repeat(PHP_EOL, 2) .
                    $expectation .
                    '.',
            default => 'The ' .
                       $testedInstance::class .
                       ' implementation\'s ' .
                       $testedMethod .
                       '() method must ' .
                       $expectation .
                       '.'
        };
    }

    /**
     * Return a string composed of a random number of randomly
     * generated characters.
     *
     * @return string
     *
     * @example
     *
     * ```
     * echo $this->randomChars();
     * // example output: rqEzm*g1vRI7!lz#-%q
     *
     * echo $this->randomChars();
     * // example output: @Lz%R+bgR#79l!mz-
     *
     * ```
     *
     */
    protected function randomChars(): string
    {
        $string = str_shuffle(
            'abcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_-=+'
        );
        try {
            $string .=
                random_bytes(random_int(1, 100)) .
                $string .
                random_bytes(random_int(1, 100));
        } catch(\Exception $e) {
        }
        return str_shuffle($string);
    }

    /**
     * Return a random float.
     *
     * @return float
     *
     * @example
     *
     * ```
     * echo strval($this->randomFloat());
     * // example output: 1029.917
     *
     * ```
     *
     */
    protected function randomFloat(): float
    {
        return floatval(
            strval(rand(-100000000000, 100000000000)) .
            '.' .
            strval(rand(0, 100000000000))
        );
    }

    /**
     * Return a random fully qualified class name, or object instance.
     *
     * @return class-string|object
     *
     * @example
     *
     * ```
     * var_dump(
     *     $this->randomClassStringOrObjectInstance()::class
     * );
     *
     * // example output:
     * string(26) "roady\classes\strings\Text"
     *
     * var_dump(
     *     $this->randomClassStringOrObjectInstance()::class
     * );
     *
     * // example output:
     * string(26) "roady\classes\constituents\Identifiable"
     *
     * ```
     *
     */
    protected function randomClassStringOrObjectInstance(): string|object
    {
        $classStringsAndObjects = [
            ReflectionClass::class,
            Reflection::class,
            Id::class,
            Name::class,
            RoadyTest::class,
            ReflectedAbstractClass::class,
            new Id(),
            new Text('Foo'),
            new Name(new Text('Foo')),
            new Reflection(
                new ReflectionClass(new Id())
            ),
            new ReflectionClass(new Id()),
            new ReflectedClass(),
        ];
        return $classStringsAndObjects[
            array_rand($classStringsAndObjects)
        ];
    }

}

