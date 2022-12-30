<?php

namespace tests\interfaces\utilities;

use \ReflectionClass;
use \ReflectionMethod;
use \ReflectionNamedType;
use \ReflectionParameter;
use \ReflectionUnionType;
use roady\classes\constituents\Identifiable;
use roady\classes\strings\Id;
use roady\classes\strings\Name;
use roady\classes\strings\Text;
use roady\interfaces\strings\ClassString;
use roady\interfaces\utilities\Reflection;
use tests\RoadyTest;

/**
 * The ReflectionTestTrait defines common tests for implementations
 * of the Reflection interface.
 *
 * @see Reflection
 *
 */
trait ReflectionTestTrait
{

    /**
     * @var Reflection $reflection An instance of a Reflection
     *                             implementation to test.
     */
    private Reflection $reflection;

    /**
     * @var class-string|object $reflectedClass A class-string or
     *                                          an object instance
     *                                          to be reflected by
     *                                          the Reflection
     *                                          implementation
     *                                          instance being
     *                                          tested.
     */
    private string|object $reflectedClass;

    /**
     * Return a numerically indexed array of the names of
     * the methods defined by the class or object instance
     * reflected by the Reflection implementation instance
     * being tested.
     *
     * @param int|null $filter Determine what method names are
     *                         included in the returned array
     *                         based on the following filters:
     *
     *                         ReflectionMethod::IS_ABSTRACT
     *                         ReflectionMethod::IS_FINAL
     *                         ReflectionMethod::IS_PRIVATE
     *                         ReflectionMethod::IS_PROTECTED
     *                         ReflectionMethod::IS_PUBLIC
     *                         ReflectionMethod::IS_STATIC
     *
     *                         All methods defined by the reflected
     *                         class or object instance that meet the
     *                         expectation of the given filters will
     *                         be included in the returned array.
     *
     *                         If no filters are specified, then
     *                         the names of all of the methods
     *                         defined by the reflected class or
     *                         object instance will be included
     *                         in the returned array.
     *
     *                         Note: Note that some bitwise
     *                         operations will not work with these
     *                         filters. For instance a bitwise
     *                         NOT (~), will not work as expected.
     *                         For example, it is not possible to
     *                         retrieve all non-static methods via
     *                         a call like:
     *
     *                         ```
     *                         $this->
     *                         determineReflectedClassesMethodNames(
     *                             ~Reflection::IS_STATIC
     *                         );
     *
     *                         ```
     *
     * @return array <int, string>
     *
     * @example
     *
     * ```
     * var_dump($this->determineReflectedClassesMethodNames());
     *
     * // example output:
     *
     * array(2) {
     *   [0]=>
     *   string(7) "method1"
     *   [1]=>
     *   string(7) "method2"
     * }
     *
     * var_dump(
     *     $this->determineReflectedClassesMethodNames(
     *         ReflectionMethod::IS_PUBLIC
     *     )
     * );
     *
     * // example output:
     *
     * array(1) {
     *   [0]=>
     *   string(7) "method1"
     * }
     *
     * var_dump(
     *     $this->determineReflectedClassesMethodNames(
     *         ReflectionMethod::IS_PRIVATE
     *     )
     * );
     *
     * // example output:
     *
     * array(1) {
     *   [0]=>
     *   string(7) "method2"
     * }
     *
     * ```
     */
    protected function determineReflectedClassesMethodNames(
        int|null $filter = null
    ): array
    {
        $reflectionClass = $this->reflectionClass(
            $this->reflectedClass()
        );
        $methodNames = [];
        foreach(
            $reflectionClass->getMethods($filter)
            as
            $reflectionMethod
        ) {
            array_push($methodNames, $reflectionMethod->getName());
        }
        return $methodNames;
    }

    /**
     * Return a numerically indexed array of the names of the
     * parameters expected by the specified method of the class
     * or object instance reflected by the Reflection implementation
     * instance being tested.
     *
     * @param string $method The name of the method whose parameter
     *                       names should be included in the
     *                       returned array.
     *
     * @return array<int, string>
     *
     * @example
     *
     * ```
     * var_dump(
     *     $this->determineReflectedClassesMethodParameterNames('foo')
     * );
     *
     * // example output:
     *
     * array(7) {
     *   [0]=>
     *   string(10) "parameter1"
     *   [1]=>
     *   string(10) "parameter2"
     *   [2]=>
     *   string(10) "parameter3"
     *   [3]=>
     *   string(10) "parameter4"
     *   [4]=>
     *   string(10) "parameter5"
     *   [5]=>
     *   string(10) "parameter6"
     *   [6]=>
     *   string(10) "parameter7"
     * }
     *
     * ```
     *
     */
    protected function determineReflectedClassesMethodParameterNames(
        string $method
    ): array
    {
        if(empty($method)) {
            return [];
        }
        $reflectionClass = $this->reflectionClass(
            $this->reflectedClass()
        );
        $parameterNames = [];
        foreach(
            $this->reflectionMethod($method)->getParameters()
            as
            $reflectionParameter
        ) {
            array_push(
                $parameterNames,
                $reflectionParameter->getName()
            );
        }
        return $parameterNames;
    }

    /**
     * Returns an associatively indexed array of numerically
     * indexed arrays of strings indicating the types accepted
     * by the parameters expected by the specified method of
     * the class or object instance reflected by the Reflection
     * implementation being tested.
     *
     * The arrays will be indexed by the name of the parameter they
     * are associated with.
     *
     * @param string $method The name of the target method.
     *
     * @return array<string, array<int, string>>
     *
     * @example
     *
     * ```
     * var_dump(
     *     $this->determineReflectedClassesMethodParameterTypes()
     * );
     *
     * // example output:
     * array(2) {
     *   ["parameter1"]=>
     *   array(3) {
     *     [0]=>
     *     string(6) "string"
     *     [1]=>
     *     string(3) "int"
     *     [2]=>
     *     string(4) "null"
     *   }
     *   ["parameter2"]=>
     *   array(1) {
     *     [0]=>
     *     string(4) "bool"
     *   }
     * }
     *
     * ```
     *
     */
    protected function determineReflectedClassesMethodParameterTypes(
        string $method
    ): array
    {
        if(empty($method)) { return []; }
        $reflectionClass = $this->reflectionClass(
            $this->reflectedClass()
        );
        $parameterTypes = [];
        foreach(
            $this->reflectionMethod($method)->getParameters()
            as
            $reflectionParameter
        ) {
            $type = $reflectionParameter->getType();
            if(!$type instanceof \ReflectionType) { continue; }
            if($type instanceof ReflectionUnionType) {
                $this->addUnionTypesToArray(
                    $reflectionParameter,
                    $parameterTypes,
                    $type
                );
                continue;
            }
            if($type instanceof ReflectionNamedType) {
                $this->addNamedTypeToArray(
                    $reflectionParameter,
                    $parameterTypes,
                    $type
                );
            }
        }
        return $parameterTypes;
    }

    /**
     * Add an array of strings indicating the types represented by
     * the specified $reflectionUnionType to the specified array of
     * $parameterTypes.
     *
     * If the $reflectionUnionType is nullable, then the string "null"
     * will be included in the array.
     *
     * The array will be indexed in the specified $parameterTypes
     * array by the specified $reflectionParameter's name.
     *
     * @param ReflectionParameter $reflectionParameter
     *                                An instance of a
     *                                ReflectionParameter that
     *                                represents the parameter
     *                                whose types are to be
     *                                represented in the array.
     *
     * @param array<string, array<int, string>> &$parameterTypes
     *                                              The array of
     *                                              parameter types
     *                                              to add the array
     *                                              to.
     *
     * @param ReflectionUnionType $reflectionUnionType
     *                                An instance of a
     *                                ReflectionUnionType
     *                                that represents the
     *                                types expected by the
     *                                parameter whose types
     *                                are to be represented
     *                                in the array.
     * @return void
     *
     * @example
     *
     * ```
     * $this->addUnionTypesToArray(
     *     $reflectionParameter,
     *     $parameterTypes,
     *     $reflectionUnionType
     * );
     *
     * ```
     *
     */
    private function addUnionTypesToArray(
        ReflectionParameter $reflectionParameter,
        array &$parameterTypes,
        ReflectionUnionType $reflectionUnionType
    ): void
    {
            $reflectionUnionTypes = $reflectionUnionType->getTypes();
            foreach($reflectionUnionTypes as $unionType) {
                $parameterTypes[$reflectionParameter->getName()][]
                    = $unionType->getName();
            }
            if(
                !in_array(
                    'null',
                    $parameterTypes[$reflectionParameter->getName()]
                )
                &&
                $reflectionUnionType->allowsNull()
            ) {
                $parameterTypes[$reflectionParameter->getName()][]
                    = 'null';
            }
    }


    /**
     * Add an array that contains a string indicating the type
     * represented by the specified $reflectionNamedType to the
     * specified array of $parameterTypes.
     *
     * If the $reflectionNamedType is nullable, then the string
     * "null" will be included in the array.
     *
     * The array will be indexed by the specified
     * $reflectionParameter's name.
     *
     * @param ReflectionParameter $reflectionParameter
     *                                An instance of a
     *                                ReflectionParameter that
     *                                represents the parameter
     *                                whose type is to be
     *                                represented in the array.
     *
     * @param array<string, array<int, string>> &$parameterTypes
     *                                              The array of
     *                                              parameter types
     *                                              to add the array
     *                                              to.
     *
     * @param ReflectionNamedType $reflectionNamedType
     *                                An instance of a
     *                                ReflectionNamedType
     *                                that represents the
     *                                type expected by the
     *                                parameter whose type
     *                                is to be represented
     *                                in the array.
     *
     * @return void
     *
     * @example
     *
     * ```
     * $this->addNamedTypeToArray(
     *     $reflectionParameter,
     *     $parameterTypes,
     *     $reflectionNamedType
     * );
     *
     * ```
     *
     */
    private function addNamedTypeToArray(
        ReflectionParameter $reflectionParameter,
        array &$parameterTypes,
        ReflectionNamedType $reflectionNamedType
    ): void
    {
        $parameterTypes[$reflectionParameter->getName()] =
            [$reflectionNamedType->getName()];
        if($reflectionNamedType->allowsNull()) {
            $parameterTypes[$reflectionParameter->getName()][] =
                'null';
        }
    }

    /**
     * Return a numerically indexed array of the names of
     * the properties defined by the class or object instance
     * reflected by the Reflection implementation instance
     * being tested.
     *
     * @param int|null $filter Determine what property names are
     *                         included in the returned array
     *                         based on the following filters:
     *
     *                         ReflectionMethod::IS_ABSTRACT
     *                         ReflectionMethod::IS_FINAL
     *                         ReflectionMethod::IS_PRIVATE
     *                         ReflectionMethod::IS_PROTECTED
     *                         ReflectionMethod::IS_PUBLIC
     *                         ReflectionMethod::IS_STATIC
     *
     *                         All properties defined by the reflected
     *                         class or object instance that meet the
     *                         expectation of the given filters will
     *                         be included in the returned array.
     *
     *                         If no filters are specified, then
     *                         the names of all of the properties
     *                         defined by the reflected class or
     *                         object instance will be included
     *                         in the returned array.
     *
     *                         Note: Note that some bitwise
     *                         operations will not work with these
     *                         filters. For instance a bitwise
     *                         NOT (~), will not work as expected.
     *                         For example, it is not possible to
     *                         retrieve all non-static properties
     *                         via a call like:
     *
     *                         ```
     *                         $this->
     *                         determineReflectedClassesPropertyNames(
     *                             ~Reflection::IS_STATIC
     *                         );
     *
     *                         ```
     *
     * @return array <int, string>
     *
     * @example
     *
     * ```
     * var_dump($this->determineReflectedClassesPropertyNames());
     *
     * // example output:
     *
     * array(2) {
     *   [0]=>
     *   string(9) "property1"
     *   [1]=>
     *   string(9) "property2"
     * }
     *
     * var_dump(
     *     $this->determineReflectedClassesPropertyNames(
     *         ReflectionMethod::IS_PUBLIC
     *     )
     * );
     *
     * // example output:
     *
     * array(1) {
     *   [0]=>
     *   string(9) "property1"
     * }
     *
     * var_dump(
     *     $this->determineReflectedClassesPropertyNames(
     *         ReflectionMethod::IS_PRIVATE
     *     )
     * );
     *
     * // example output:
     *
     * array(1) {
     *   [0]=>
     *   string(9) "property2"
     * }
     *
     * ```
     */
    protected function determineReflectedClassesPropertyNames(
        int|null $filter = null
    ): array
    {
        $reflectionClass = $this->reflectionClass(
            $this->reflectedClass()
        );
        $propertyNames = [];
        foreach(
            $reflectionClass->getProperties($filter)
            as
            $reflectionProperty
        ) {
            array_push($propertyNames, $reflectionProperty->getName());
        }
        return $propertyNames;
    }

    /**
     * Return an instance of a ReflectionMethod for the specified
     * method of the class or object instance reflected by the
     * Reflection implementation instance being tested.
     *
     * @param string $method The name of the method to be reflected
     *                       by the returned ReflectionMethod
     *                       instance.
     *
     * @return ReflectionMethod
     *
     * @example
     *
     * ```
     * $this->reflectionMethod('methodName');
     *
     * ```
     *
     */
    final protected function reflectionMethod(
        string $method
    ): ReflectionMethod
    {
        return new ReflectionMethod(
            $this->reflectedClass(),
            $method
        );
    }

    /**
     * Return the class-string or object instance to be reflected by
     * the Reflection implementation instance being tested.
     *
     * @return class-string|object
     *
     * @example
     *
     * ```
     * var_dump($this->reflectedClass());
     *
     * // example output:
     * roady\classes\utilities\Reflection
     *
     * ```
     *
     */
    public function reflectedClass(): string|object
    {
        return $this->reflectedClass;
    }

    /**
     * Return the Reflection implementation instance to test.
     *
     * @return Reflection
     *
     * @example
     *
     * ```
     * $this->reflectionTestInstance();
     *
     * ```
     *
     */
    protected function reflectionTestInstance(): Reflection
    {
        return $this->reflection;
    }

    /**
     * Set the Reflection implementation instance to test.
     *
     * @param Reflection $reflectionTestInstance An instance of an
     *                                           implementation of
     *                                           the Reflection
     *                                           interface to test.
     *
     * @return void
     *
     * @example
     *
     * ```
     * $this->setReflectionTestInstance(
     *     new \roady\classes\utilities\Reflection(
     *         new \ReflectionClass(
     *             $this->randomClassStringOrObjectInstance()
     *         )
     *     )
     * );
     *
     * ```
     *
     */
    protected function setReflectionTestInstance(
        Reflection $reflectionTestInstance
    ): void
    {
        $this->reflection = $reflectionTestInstance;
    }

    /**
     * Set up an instance of a Reflection to test using a random
     * class string or object instance.
     *
     * This method must set the class or object instance that
     * is expected to be reflected by the Reflection implementation
     * instance to test via the setClassToBeReflected() method.
     *
     * This method must also set the Reflection implementation
     * instance to test via the setReflectionTestInstance()
     * method.
     *
     * This method may perform any additional set up required by
     * the Reflection implementation being tested.
     *
     * @return void
     *
     * @example
     *
     * ```
     * public function setUp(): void
     * {
     *     $class = $this->randomClassStringOrObjectInstance();
     *     $this->setClassToBeReflected($class);
     *     $this->setReflectionTestInstance(
     *         new \roady\classes\utilities\Reflection(
     *             $this->reflectionClass($class)
     *         )
     *     );
     * }
     *
     * ```
     *
     */
    abstract protected function setUp(): void;

    /**
     * Set the class-string or object instance to be reflected by
     * the Reflection implementation instance being tested.
     *
     * @param class-string|object $class The class-string or object
     *                                   instance to be reflected.
     *
     * @example
     *
     * ```
     * // Using an object instance
     * $this->setClassToBeReflected($this): void;
     *
     * // Using a class string
     * $this->setClassToBeReflected($this::class): void;
     *
     * ```
     *
     */
    abstract protected function setClassToBeReflected(
        string|object $class
    ): void;

    /**
     * Return an instance of a ReflectionClass instantiated
     * with the specified class string or object instance.
     *
     * @param class-string|object $class The class string or object
     *                                   instance the ReflectionClass
     *                                   instance will reflect.
     *
     * @return ReflectionClass <object>
     *
     * @example
     *
     * ```
     * // Using a class string:
     * $this->reflectionClass($this::class);
     *
     * // Using an object instance:
     * $this->reflectionClass($this);
     *
     * ```
     *
     */
    protected function reflectionClass(
        string|object $class
    ): ReflectionClass
    {
        return new ReflectionClass($class);
    }

    /**
     * Return the name of a randomly chosen method defined by
     * the reflected class or object instance.
     *
     * If the reflected class or object instance does not define
     * any methods, then an empty string will be returned.
     *
     * @return string
     *
     * @example
     *
     * ```
     * echo $this->randomMethodName();
     *
     * // example output:
     * someMethodDefinedByTheReflectedClassOrObjectInstance
     *
     * ```
     *
     */
    protected function randomMethodName(): string
    {
        $methodNames = $this->determineReflectedClassesMethodNames();
        return (
            empty($methodNames)
            ? ''
            : $methodNames[array_rand($methodNames)]
        );
    }

    /**
     * Test that the type() method returns the type of the
     * reflected class.
     *
     * @return void
     *
     */
    public function test_type_returns_type_of_reflected_class(): void
    {
        $this->assertEquals(
            (
                is_object($this->reflectedClass())
                ? $this->reflectedClass()::class
                : $this->reflectedClass()
            ),
            $this->reflectionTestInstance()->type(),
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                'type',
                'return the type of the reflected class'
            ),
        );
    }


    /**
     * Test that the methodNames() method returns a numerically
     * indexed array of the names of all the methods defined by
     * the reflected class if no filter is specified.
     *
     * @return void
     *
     */
    public function test_methodNames_returns_the_names_of_all_the_methods_defined_by_the_reflected_class_if_no_filter_is_specified(): void
    {
        $this->assertEquals(
            $this->determineReflectedClassesMethodNames(),
            $this->reflectionTestInstance()->methodNames(),
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                'methodNames',
                'return an array of the names of the methods ' .
                'defined by the reflected class'
            )
        );
    }

    /**
     * Test that the methodNames() method returns a numerically
     * indexed array of the names of the abstract methods defined
     * by the reflected class if the Reflection::IS_ABSTRACT
     * filter is specified.
     *
     * @return void
     *
     */
    public function test_methodNames_returns_the_names_of_the_abstract_methods_defined_by_the_reflected_class_if_the_ReflectionIS_ABSTRACT_filter_is_specified(): void
    {
        $this->assertEquals(
            /**
             * ReflectionMethod::IS_ABSTRACT is used intentionally to
             * test that the effect of passing Reflection::IS_ABSTRACT
             * to the methodNames() method is the same as passing
             * ReflectionMethod::IS_ABSTRACT to the methodNames()
             * method.
             */
            $this->determineReflectedClassesMethodNames(
                ReflectionMethod::IS_ABSTRACT
            ),
            $this->reflectionTestInstance()->methodNames(
                Reflection::IS_ABSTRACT
            ),
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                'methodNames',
                'return an array of the names of the abstract ' .
                'methods defined by the reflected class if the' .
                'ReflectionClass::IS_ABSTRACT filter is specified'
            )
        );
    }

    /**
     * Test that the methodNames() method returns a numerically
     * indexed array of the names of the final methods defined
     * by the reflected class if the Reflection::IS_FINAL
     * filter is specified.
     *
     * @return void
     *
     */
    public function test_methodNames_returns_the_names_of_the_final_methods_defined_by_the_reflected_class_if_the_ReflectionIS_FINAL_filter_is_specified(): void
    {
        $this->assertEquals(
            /**
             * ReflectionMethod::IS_FINAL is used intentionally to
             * test that the effect of passing Reflection::IS_FINAL
             * to the methodNames() method is the same as passing
             * ReflectionMethod::IS_FINAL to the methodNames()
             * method.
             */
            $this->determineReflectedClassesMethodNames(
                ReflectionMethod::IS_FINAL
            ),
            $this->reflectionTestInstance()->methodNames(
                Reflection::IS_FINAL
            ),
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                'methodNames',
                'return an array of the names of the final ' .
                'methods defined by the reflected class if the' .
                'ReflectionClass::IS_FINAL filter is specified'
            )
        );
    }
    /**
     * Test that the methodNames() method returns a numerically
     * indexed array of the names of the private methods defined
     * by the reflected class if the Reflection::IS_PRIVATE
     * filter is specified.
     *
     * @return void
     *
     */
    public function test_methodNames_returns_the_names_of_the_private_methods_defined_by_the_reflected_class_if_the_ReflectionIS_PRIVATE_filter_is_specified(): void
    {
        $this->assertEquals(
            /**
             * ReflectionMethod::IS_PRIVATE is used intentionally to
             * test that the effect of passing Reflection::IS_PRIVATE
             * to the methodNames() method is the same as passing
             * ReflectionMethod::IS_PRIVATE to the methodNames()
             * method.
             */
            $this->determineReflectedClassesMethodNames(
               ReflectionMethod::IS_PRIVATE
            ),
            $this->reflectionTestInstance()->methodNames(
                Reflection::IS_PRIVATE
            ),
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                'methodNames',
                'return an array of the names of the private ' .
                'methods defined by the reflected class if the' .
                'ReflectionClass::IS_PRIVATE filter is specified'
            )
        );
    }

    /**
     * Test that the methodNames() method returns a numerically
     * indexed array of the names of the protected methods defined
     * by the reflected class if the Reflection::IS_PROTECTED
     * filter is specified.
     *
     * @return void
     *
     */
    public function test_methodNames_returns_the_names_of_the_protected_methods_defined_by_the_reflected_class_if_the_ReflectionIS_PROTECTED_filter_is_specified(): void
    {
        $this->assertEquals(
            /**
             * ReflectionMethod::IS_PROTECTED is used
             * intentionally to test that the effect of
             * passing Reflection::IS_PROTECTED to the
             * methodNames() method is the same as passing
             * ReflectionMethod::IS_PROTECTED to the
             * methodNames() method.
             */
            $this->determineReflectedClassesMethodNames(
                ReflectionMethod::IS_PROTECTED
            ),
            $this->reflectionTestInstance()->methodNames(
                Reflection::IS_PROTECTED
            ),
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                'methodNames',
                'return an array of the names of the protected ' .
                'methods defined by the reflected class if the' .
                'ReflectionClass::IS_PROTECTED filter is specified'
            )
        );
    }

    /**
     * Test that the methodNames() method returns a numerically
     * indexed array of the names of the public methods defined
     * by the reflected class if the Reflection::IS_PUBLIC
     * filter is specified.
     *
     * @return void
     *
     */
    public function test_methodNames_returns_the_names_of_the_public_methods_defined_by_the_reflected_class_if_the_ReflectionIS_PUBLIC_filter_is_specified(): void
    {
        $this->assertEquals(
            /**
             * ReflectionMethod::IS_PUBLIC is used intentionally to
             * test that the effect of passing Reflection::IS_PUBLIC
             * to the methodNames() method is the same as passing
             * ReflectionMethod::IS_PUBLIC to the methodNames()
             * method.
             */
            $this->determineReflectedClassesMethodNames(
                ReflectionMethod::IS_PUBLIC
            ),
            $this->reflectionTestInstance()->methodNames(
                Reflection::IS_PUBLIC
            ),
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                'methodNames',
                'return an array of the names of the public ' .
                'methods defined by the reflected class if the' .
                'ReflectionClass::IS_PUBLIC filter is specified'
            )
        );
    }

    /**
     * Test that the methodNames() method returns a numerically
     * indexed array of the names of the static methods defined
     * by the reflected class if the Reflection::IS_STATIC
     * filter is specified.
     *
     * @return void
     *
     */
    public function test_methodNames_returns_the_names_of_the_static_methods_defined_by_the_reflected_class_if_the_ReflectionIS_STATIC_filter_is_specified(): void
    {
        $this->assertEquals(
            /**
             * ReflectionMethod::IS_STATIC is used intentionally to
             * test that the effect of passing Reflection::IS_STATIC
             * to the methodNames() method is the same as passing
             * ReflectionMethod::IS_STATIC to the methodNames()
             * method.
             */
            $this->determineReflectedClassesMethodNames(
                ReflectionMethod::IS_STATIC
            ),
            $this->reflectionTestInstance()->methodNames(
                Reflection::IS_STATIC
            ),
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                'methodNames',
                'return an array of the names of the static ' .
                'methods defined by the reflected class if the' .
                'ReflectionClass::IS_STATIC filter is specified'
            )
        );
    }

    /**
     * Test that the value of the Reflection::IS_ABSTRACT
     * constant is equal to the value of the
     * ReflectionMethod::IS_ABSTRACT constant.
     *
     * @return void
     *
     */
    public function test_ReflectionIS_ABSTRACT_constant_is_equal_to_ReflectionMethodIS_ABSTRACT_constant(): void
    {
        $this->assertEquals(
            ReflectionMethod::IS_ABSTRACT,
            Reflection::IS_ABSTRACT,
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                '',
                'The value of the Reflection::IS_ABSTRACT constant ' .
                'must be equal to the value of the ' .
                'ReflectionMethod::IS_ABSTRACT constant.'
            )
        );
    }

    /**
     * Test that the value of the Reflection::IS_FINAL constant
     * is equal to the value of the ReflectionMethod::IS_FINAL
     * constant.
     *
     * @return void
     *
     */
    public function test_ReflectionIS_FINAL_constant_is_equal_to_ReflectionMethodIS_FINAL_constant(): void
    {
        $this->assertEquals(
            ReflectionMethod::IS_FINAL,
            Reflection::IS_FINAL,
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                '',
                'The value of the Reflection::IS_FINAL constant ' .
                'must be equal to the value of the ' .
                'ReflectionMethod::IS_FINAL constant.'
            )
        );
    }

    /**
     * Test that the value of the Reflection::IS_PRIVATE constant
     * is equal to the value of the ReflectionMethod::IS_PRIVATE
     * constant.
     *
     * @return void
     *
     */
    public function test_ReflectionIS_PRIVATE_constant_is_equal_to_ReflectionMethodIS_PRIVATE_constant(): void
    {
        $this->assertEquals(
            ReflectionMethod::IS_PRIVATE,
            Reflection::IS_PRIVATE,
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                '',
                'The value of the Reflection::IS_PRIVATE constant ' .
                'must be equal to the value of the ' .
                'ReflectionMethod::IS_PRIVATE constant.'
            )
        );
    }

    /**
     * Test that the value of the Reflectionvalue::IS_PROTECTED
     * constant is equal to the value of the
     * ReflectionMethod::IS_PROTECTED constant.
     *
     * @return void
     *
     */
    public function test_ReflectionIS_PROTECTED_constant_is_equal_to_ReflectionMethodIS_PROTECTED_constant(): void
    {
        $this->assertEquals(
            ReflectionMethod::IS_PROTECTED,
            Reflection::IS_PROTECTED,
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                '',
                'The value of the Reflection::IS_PROTECTED ' .
                'constant must be equal to the value of the ' .
                'ReflectionMethod::IS_PROTECTED constant.'
            )
        );
    }

    /**
     * Test that the value of the Reflection::IS_PUBLIC constant
     * is equal to the value of the ReflectionMethod::IS_PUBLIC
     * constant.
     *
     * @return void
     *
     */
    public function test_ReflectionIS_PUBLIC_constant_is_equal_to_ReflectionMethodIS_PUBLIC_constant(): void
    {
        $this->assertEquals(
            ReflectionMethod::IS_PUBLIC,
            Reflection::IS_PUBLIC,
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                '',
                'The value of the Reflection::IS_PUBLIC constant ' .
                'must be equal to the value of the ' .
                'ReflectionMethod::IS_PUBLIC constant.'
            )
        );
    }

    /**
     * Test that the value of the Reflection::IS_STATIC constant
     * is equal to the value of the ReflectionMethod::IS_STATIC
     * constant.
     *
     * @return void
     *
     */
    public function test_ReflectionIS_STATIC_constant_is_equal_to_ReflectionMethodIS_STATIC_constant(): void
    {
        $this->assertEquals(
            ReflectionMethod::IS_STATIC,
            Reflection::IS_STATIC,
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                '',
                'The value of the Reflection::IS_STATIC constant ' .
                'must be equal to the value of the ' .
                'ReflectionMethod::IS_STATIC constant.'
            )
        );
    }

    /**
     * Test that the methodParameterNames() method returns a
     * numerically indexed array of the names of the parameters
     * defined by the specified method of the reflected class or
     * object instance.
     *
     * @return void
     *
     */
    public function test_methodParameterNames_returns_a_numerically_indexed_array_of_the_names_of_the_parameters_defined_by_the_specified_method(): void
    {
        $methodName = $this->randomMethodName();
        $this->assertEquals(
            $this->determineReflectedClassesMethodParameterNames(
                $methodName
            ),
            $this->reflectionTestInstance()->methodParameterNames(
                $methodName
            ),
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                'methodNames',
                'return a numerically indexed array of the names ' .
                'of the parameters defined by the specified method ' .
                'of the reflected class or object instance.'
            )
        );
    }

    /**
     * Test that the methodParameterTypes() method returns an
     * associatively indexed array of numerically indexed arrays
     * of strings indicating the types of the parameters defined
     * by the specified method of the reflected class or object
     * instance.
     *
     * @return void
     *
     */
    public function test_methodParameterTypes_returns_a_numerically_indexed_array_of_the_types_expected_by_the_parameters_defined_by_the_specified_method(): void
    {
        $methodNames = $this->determineReflectedClassesMethodNames();
        $methodName = $this->randomMethodName();
        $this->assertEquals(
            $this->determineReflectedClassesMethodParameterTypes(
                $methodName
            ),
            $this->reflectionTestInstance()->methodParameterTypes(
                $methodName
            ),
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                'methodParameterTypes',
                'return an associatively indexed array of ' .
                'numerically indexed arrays of strings indicating '.
                'the types of the parameters expected by ' .
                'the specified method of the reflected class ' .
                'or object instance.'
            )
        );
    }

    /**
     * Test that the propertyNames() method returns a numerically
     * indexed array of the names of all the properties defined by
     * the reflected class if no filter is specified.
     *
     * @return void
     *
     */
    public function test_propertyNames_returns_the_names_of_all_the_properties_defined_by_the_reflected_class_if_no_filter_is_specified(): void
    {
        $this->assertEquals(
            $this->determineReflectedClassesPropertyNames(),
            $this->reflectionTestInstance()->propertyNames(),
            $this->testFailedMessage(
                $this->reflectionTestInstance(),
                'methodNames',
                'return an array of the names of the properties ' .
                'defined by the reflected class'
            )
        );
    }

}

