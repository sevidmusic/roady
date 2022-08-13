<?php

namespace tests;

use PHPUnit\Framework\TestCase;

/**
 * Defines tests to make sure PHPUnit tests can be run.
 *
 * Test Methods:
 *
 * ```
 * public function test_php_unit_tests_are_run(): void
 *
 * ```
 *
 */
class PHPUnitTest extends TestCase
{

    /**
     * Test that PHPUnit tests run.
     *
     * If this test does not run then PHPUnit is not set up properly.
     *
     */
    public function test_php_unit_tests_are_run(): void
    {
        $this->assertTrue(
            true,
            'This test should run if PHPUnit is set up correctly.'
        );
    }
}
