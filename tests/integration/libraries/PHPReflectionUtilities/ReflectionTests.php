<?php

/**
 * This file tests the integration of the PHPReflectionUtilities
 * library.
 *
 * Specifically, it tests the
 * `Darling\PHPReflectionUtilities\classes\utilities\Reflection`
 * class.
 *
 * The tests defined in this file can be run with the following
 * command:
 *
 * ```
 * php ReflectionTests.php
 *
 * ```
 *
 */

require str_replace(
    'tests' .
    DIRECTORY_SEPARATOR .
    'integration' .
    DIRECTORY_SEPARATOR .
    'libraries' .
    DIRECTORY_SEPARATOR .
    'PHPReflectionUtilities',
    '',
    __DIR__ . '/vendor/autoload.php'
);

use \Darling\PHPReflectionUtilities\classes\utilities\Reflection;

$reflection = new Reflection(new ReflectionClass(Reflection::class));

// Test the `methodNames()` method.
var_dump($reflection->methodNames());

// Test the `methodParameterNames()` method.
var_dump($reflection->methodParameterNames('methodNames'));

// Test the `methodParameterTypes()` method.
var_dump($reflection->methodParameterTypes('methodNames'));

// Test the `propertyNames()` method.
var_dump($reflection->propertyNames());

// Test the `propertyTypes()` method.
var_dump($reflection->propertyTypes());

// Test the `type()` method.
var_dump($reflection->type());

