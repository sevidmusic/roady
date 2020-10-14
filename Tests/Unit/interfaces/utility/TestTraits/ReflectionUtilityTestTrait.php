<?php /** @noinspection PhpMultipleClassesDeclarationsInOneFile */

namespace UnitTests\interfaces\utility\TestTraits;

use DarlingDataManagementSystem\classes\primary\Classifiable as CoreClassifiable;
use DarlingDataManagementSystem\classes\primary\Exportable as CoreExportable;
use DarlingDataManagementSystem\classes\primary\Identifiable as CoreIdentifiable;
use DarlingDataManagementSystem\classes\primary\Storable as CoreStorable;
use DarlingDataManagementSystem\classes\primary\Switchable as CoreSwitchable;
use DarlingDataManagementSystem\classes\utility\ReflectionUtility as CoreReflectionUtility;
use DarlingDataManagementSystem\dev\traits\Logger;
use DarlingDataManagementSystem\interfaces\utility\ReflectionUtility as ReflectionUtilityInterface;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use UnitTests\TestTraits\ArrayTester;
use UnitTests\TestTraits\StringTester;

trait ReflectionUtilityTestTrait
{

    use ArrayTester;
    use StringTester;
    use Logger;

    private string $errFailedToReflectClass = <<<EOD
ReflectionUtilityTestTrait Error: Failed to reflect class %s.
Defaulting to reflect empty stdClass() instance.
EOD;

    private string  $errFailedToReflectMockStd = <<<EOD
ReflectionUtilityTestTrait Fatal Error: Failed to reflect class %s,
and also failed to reflect empty stdClass() by default.
EOD;

    private string  $errRandomBytesFailed = <<<EOD
ReflectionUtilityTestTrait Warning:
Failed to generate alpha-numeric string using random_bytes(), defaulting to
str_shuffle(). You can safely ignore this warning if the generated string
does not need to be cryptographically secure.
EOD;
    private string  $errMethodNotDefined = <<<EOD
ReflectionUtilityTestTrait Warning:
The specified method %s() is not defined in class %s.
You may safely ignore this warning if this is expected.
EOD;

    private string  $errSpecifiedMethodCouldNotBeReflected = <<<EOD
ReflectionUtilityTestTrait Error:
The specified method %s() could not be reflected for class %s.
Defaulting to stdClass().
EOD;

    private string  $errFailedToReflectStdMethod = <<<EOD
ReflectionUtilityTestTrait Fatal Error:
The specified method %s() could not be reflected for class %s,
and also failed to default to an empty instance of stdClass().
EOD;

    private string  $errInvalidClassParameter = <<<EOD
ReflectionUtilityTestTrait Error: Invalid type %s passed to %s
EOD;

    private ReflectionUtilityInterface $reflectionUtility;

    /**
     * @var ReflectionUtilityTestClass|string
     */
    private $classToReflect;
    private string  $booleanType = 'boolean';
    private string  $integerType = 'integer';
    private string  $doubleType = 'double';
    private string  $stringType = 'string';
    private string  $arrayType = 'array';
    private string  $nullType = 'NULL';
    private string  $objectType = 'object';
    private string $constructMethod = '__construct';

    /**
     * @before
     */
    public function initializeClassToReflect()
    {
        $this->classToReflect = $this->getRandomClassInstanceOrFullyQualifiedClassname();
    }

    private function getRandomClassInstanceOrFullyQualifiedClassname()
    {
        $testClasses = array(
            new Baz(),
            new Bazzer(),
            new Foo(true, 234987, 420.234, 'Some string', array([], [1, 2, 3], true), new Bar('Bar string'), null),
            new Bar('Some bar string'),
            '\UnitTests\interfaces\utility\TestTraits\Baz',
            '\UnitTests\interfaces\utility\TestTraits\Bazzer',
            '\UnitTests\interfaces\utility\TestTraits\Foo',
            '\UnitTests\interfaces\utility\TestTraits\Bar',
            new CoreIdentifiable('Name'),
            new CoreClassifiable(),
            new CoreSwitchable(),
            new CoreStorable('Name', 'Location', 'Container'),
            new CoreExportable(),
            new CoreReflectionUtility(),
            '\\' . CoreIdentifiable::class,
            '\\' . CoreClassifiable::class,
            '\\' . CoreSwitchable::class,
            '\\' . CoreStorable::class,
            '\\' . CoreExportable::class,
            '\\' . CoreReflectionUtility::class
        );
        return $testClasses[array_rand($testClasses)];
    }

    public function testGetClassPropertyNamesReturnsArrayWhoseValuesAreSpecifiedClassesExpectedPropertyNames(): void
    {
        $this->getArrayTestUtility()->arraysAreEqual(
            $this->getClassPropertyNames($this->getClassToReflect()),
            $this->getReflectionUtility()->getClassPropertyNames($this->getClassToReflect())
        );
    }

    public function getClassPropertyNames($class): array
    {
        $propertyNames = array();
        foreach ($this->getClassPropertyReflections($class) as $reflectionProperty) {
            array_push($propertyNames, $reflectionProperty->getName());
        }
        return array_unique($propertyNames);
    }

    private function getClassPropertyReflections($class): array
    {
        if ($this->classParameterIsValidClassNameOrClassInstance($class, __METHOD__) === false) {
            return array();
        }
        $selfReflection = $this->getClassReflection($class);
        if ($selfReflection->getParentClass() === false) {
            return $selfReflection->getProperties();
        }
        $propertyReflections = $selfReflection->getProperties();
        while ($parent = $selfReflection->getParentClass()) {
            $propertyReflections = array_merge($propertyReflections, $parent->getProperties());
            $selfReflection = $parent;
        }
        return $propertyReflections;
    }

    private function classParameterIsValidClassNameOrClassInstance($class, string $caller): bool
    {
        if (is_string($class) === false && is_object($class) === false) {
            $this->log(
                $this->errInvalidClassParameter,
                gettype($class),
                $caller
            );
            return false;
        }
        return true;
    }

    public function getClassReflection($class): ReflectionClass
    {
        try {
            return new ReflectionClass($class);
        } catch (ReflectionException $e) {
            $this->log($this->errFailedToReflectClass, $this->getClass($class));
            try {
                return new ReflectionClass((object)[]);
            } catch (ReflectionException $e) {
                $this->log(
                    $this->errFailedToReflectMockStd,
                    $this->getClass($class)
                );
                exit(0);
            }
        }
    }

    private function getClass($class): string
    {
        return (is_string($class) ? $class : get_class($class));
    }

    private function getClassToReflect()
    {
        return $this->classToReflect;
    }

    protected function getReflectionUtility(): ReflectionUtilityInterface
    {
        return $this->reflectionUtility;
    }

    protected function setReflectionUtility(ReflectionUtilityInterface $reflectionUtility): void
    {
        $this->reflectionUtility = $reflectionUtility;
    }

    public function testGetClassPropertyTypesReturnsArrayWhoseValuesAreSpecifiedClassesExpectedPropertyTypes(): void
    {
        $this->getArrayTestUtility()->arraysAreEqual(
            $this->getClassPropertyTypes($this->getClassToReflect()),
            $this->getReflectionUtility()->getClassPropertyTypes($this->getClassToReflect())
        );
    }

    public function getClassPropertyTypes($class): array
    {
        $propertyTypes = array();
        foreach ($this->getClassPropertyReflections($class) as $reflectionProperty) {
            $reflectionProperty->setAccessible(true);
            $propertyTypes[$reflectionProperty->getName()] = gettype(
                $reflectionProperty->getValue($this->getClassInstance($class))
            );
        }
        return $propertyTypes;
    }

    public function getClassInstance($class, array $constructorArguments = array())
    {
        if ($this->classParameterIsValidClassNameOrClassInstance($class, __METHOD__) === false) {
            return (object)[];
        }
        if (method_exists($class, $this->constructMethod) === false) {
            return $this->getClassReflection($class)->newInstanceArgs([]);
        }
        if (empty($constructorArguments) === true) {
            return $this->getClassReflection($class)->newInstanceArgs($this->generateMockClassMethodArguments($class, $this->constructMethod));
        }
        return $this->getClassReflection($class)->newInstanceArgs($constructorArguments);
    }

    public function generateMockClassMethodArguments($class, string $method): array
    {
        $defaults = array();
        foreach ($this->getClassMethodParameterTypes($class, $method) as $type) {
            if ($type === $this->booleanType) {
                array_push($defaults, false);
                continue;
            }
            if ($type === $this->integerType) {
                array_push($defaults, 1);
                continue;
            }
            if ($type === $this->doubleType) {
                array_push($defaults, 1.2345);
                continue;
            }
            if ($type === $this->stringType) {
                array_push($defaults, $this->generateRandomAlphaNumString());
                continue;
            }
            if ($type === $this->arrayType) {
                array_push($defaults, array());
                continue;
            }
            if ($type === $this->nullType) {
                array_push($defaults, null);
                continue;
            }
            /** For unknown types assume class instance. */
            array_push($defaults, $this->getClassInstance('\\' . $type));
        }
        return $defaults;
    }

    public function getClassMethodParameterTypes($class, string $method): array
    {
        $parameterTypes = array();
        $methodReflection = $this->getClassMethodReflection($class, $method);
        if (is_null($methodReflection) === true) {
            return array();
        }
        foreach ($methodReflection->getParameters() as $reflectionParameter) {
            array_push($parameterTypes, $this->getParameterType($reflectionParameter));
        }
        return $parameterTypes;
    }

    private function getClassMethodReflection($class, string $methodName)
    {
        if ($this->classParameterIsValidClassNameOrClassInstance($class, __METHOD__) === false) {
            return null;
        }
        if (method_exists($class, $methodName) === false) {
            $this->log($this->errMethodNotDefined, $methodName, $this->getClass($class));
            return null;
        }
        return $this->getMethodReflection($class, $methodName);
    }

    private function getMethodReflection($class, string $methodName): ReflectionMethod
    {
        try {
            return new ReflectionMethod($this->getClass($class), $methodName);
        } catch (ReflectionException $e) {
            $this->log(
                $this->errSpecifiedMethodCouldNotBeReflected,
                $methodName,
                $this->getClass($class)
            );
            try {
                return new ReflectionMethod((object)[], $methodName);
            } catch (ReflectionException $e) {
                $this->log(
                    $this->errFailedToReflectStdMethod,
                    $methodName,
                    $this->getClass($class)
                );
                exit();
            }
        }
    }

    private function getParameterType(ReflectionParameter $reflectionParameter): string
    {
        if (is_null($reflectionParameter->getType()) === true) {
            return $this->nullType;
        }
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        return $this->convertReflectionTypeStringToGettypeString($reflectionParameter->getType()->getName());
    }

    private function convertReflectionTypeStringToGettypeString(string $type)
    {
        if ($type === 'bool') {
            return $this->booleanType;
        }
        if ($type === 'float') {
            return $this->doubleType;
        }
        if ($type === 'int') {
            return $this->integerType;
        }
        return $type;
    }

    private function generateRandomAlphaNumString(): string
    {
        try {
            return preg_replace("/[^a-zA-Z0-9]+/", "", random_bytes(12));
        } catch (Exception $e) {
            $this->log($this->errRandomBytesFailed);
            return str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz');
        }
    }

    public function testGetClassPropertyValuesReturnsInstancesPropertyValues(): void
    {
        $instance = $this->getClassInstance($this->getClassToReflect());
        $this->getArrayTestUtility()->arraysAreEqual(
            $this->getClassPropertyValues($instance),
            $this->getReflectionUtility()->getClassPropertyValues($instance)
        );
    }

    public function getClassPropertyValues($class): array
    {
        $propertyValues = array();
        foreach ($this->getClassPropertyReflections($class) as $reflectionProperty) {
            $reflectionProperty->setAccessible(true);
            $propertyValues[$reflectionProperty->getName()] = (
            is_string($class) === true
                ? $reflectionProperty->getValue($this->getClassInstance($class))
                : $reflectionProperty->getValue($class)
            );
        }
        return $propertyValues;
    }

    public function testGetClassInstanceReturnsInstanceOfSpecifiedClass(): void
    {
        $this->assertEquals(
            $this->getFullyQualifiedClassname($this->getClassToReflect()),
            '\\' . get_class($this->getReflectionUtility()->getClassInstance($this->getClassToReflect()))
        );
        $this->assertEquals(
            get_class($this->getClassInstance($this->getClassToReflect())),
            get_class($this->getReflectionUtility()->getClassInstance($this->getClassToReflect()))
        );
    }

    private function getFullyQualifiedClassname($class)
    {
        if ($this->classParameterIsValidClassNameOrClassInstance($class, __METHOD__) === false) {
            return '\\' . get_class((object)[]);
        }
        return (is_string($class) ? $class : '\\' . get_class($class));
    }

    public function testGenerateMockClassMethodArgumentsReturnsArrayWhoseValuesTypesAreMethodsExpectedArgumentTypes(): void
    {
        $generatedTypes = array();
        foreach ($this->getReflectionUtility()->generateMockClassMethodArguments($this->getClassToReflect(), $this->constructMethod) as $argumentValue) {
            array_push($generatedTypes, $this->getRealType($argumentValue));
        }
        $this->getArrayTestUtility()->arraysAreEqual(
            $this->getClassMethodParameterTypes($this->getClassToReflect(), $this->constructMethod),
            $generatedTypes
        );
    }

    private function getRealType($var): string
    {
        if (gettype($var) === $this->objectType) {
            return get_class($var);
        }
        return gettype($var);
    }

    public function testGetClassMethodParameterNamesReturnsArrayWhoseValuesAreSpecifiedClassMethodsParameterNames(): void
    {
        $this->getArrayTestUtility()->arraysAreEqual(
            $this->getClassMethodParameterNames($this->getClassToReflect(), $this->constructMethod),
            $this->getReflectionUtility()->getClassMethodParameterNames($this->getClassToReflect(), $this->constructMethod)
        );
    }

    public function getClassMethodParameterNames($class, string $method): array
    {
        $parameterNames = array();
        $methodReflection = $this->getClassMethodReflection($class, $method);
        if (is_null($methodReflection) === true) {
            return array();
        }
        foreach ($methodReflection->getParameters() as $reflectionParameter) {
            array_push($parameterNames, $reflectionParameter->name);
        }
        return $parameterNames;
    }

    public function testGetClassMethodParameterTypesReturnsArrayWhoseValuesAreSpecifiedClassMethodsExpectedParameterTypes(): void
    {
        $this->getArrayTestUtility()->arraysAreEqual(
            $this->getClassMethodParameterTypes($this->getClassToReflect(), $this->constructMethod),
            $this->getReflectionUtility()->getClassMethodParameterTypes($this->getClassToReflect(), $this->constructMethod)
        );
    }

    public function testGetClassReflectionReturnsReflectionOfSpecifiedClass(): void
    {
        $this->getStringTestUtility()->stringsMatch(
            $this->getReflectionUtility()->getClassReflection($this)->getName(),
            get_class($this)
        );
    }

}

/**
 * The following classes are used by the ReflectionUtilityTestTrait to test the
 * \DarlingDataManagementSystem\abstractions\utility\ReflectionUtility class's methods.
 */
interface ReflectionUtilityTestClass
{
    public function isTestClass(): bool;
}

class Baz implements ReflectionUtilityTestClass
{
    public array $fooBarBaz = array();

    public function isTestClass(): bool
    {
        return true;
    }

    public function getFooBarBaz(): array
    {
        return $this->fooBarBaz;
    }
}

class Bazzer extends Baz implements ReflectionUtilityTestClass
{
    private string $baz = '12345';

    public function getBaz(): string
    {
        return sprintf('%s: %s', $this->baz, json_encode($this->getFooBarBaz()));
    }
}

class Foo extends Bazzer implements ReflectionUtilityTestClass
{
    public float $float;
    protected bool $bool;
    protected string $str;
    protected ReflectionUtilityTestClass $bar;
    private int $int;
    private array $arr;
    private $null;

    public function __construct(bool $bool, int $int, float $float, string $str, array $arr, Bar $bar, $null = null)
    {
        $this->bool = $bool;
        $this->int = $int;
        $this->float = $float;
        $this->str = $str . $this->getBaz();
        $this->arr = $arr;
        $this->null = $null;
        $this->bar = $bar;
    }
}

class Bar implements ReflectionUtilityTestClass
{
    private string $str;

    public function isTestClass(): bool
    {
        return true;
    }

    public function __construct(string $str)
    {
        $this->str = $str;
    }
}
