<?php

namespace tests\interfaces\constituents;

use roady\interfaces\constituents\Switchable;

/**
 * The SwitchableTestTrait defines common tests for implementations of
 * the Switchable interface.
 *
 * @see Switchable
 *
 */
trait SwitchableTestTrait
{

    /**
     * @var Switchable $switchable An instance of a Switchable
     *                             implementation to test.
     */
    protected Switchable $switchable;

    /**
     * @var bool $expectedState The expected boolean state of the
     *                          Switchable implementation instance
     *                          being tested.
     */
    private bool $expectedState;

    /**
     * Return the Switchable implementation instance to test.
     *
     * @return Switchable
     *
     */
    protected function switchableTestInstance(): Switchable
    {
        return $this->switchable;
    }

    /**
     * Set the Switchable implementation instance to test.
     *
     * @param Switchable $switchableTestInstance An instance of an
     *                                           implementation of
     *                                           the Switchable
     *                                           interface to test.
     *
     * @return void
     *
     */
    protected function setSwitchableTestInstance(
        Switchable $switchableTestInstance
    ): void
    {
        $this->switchable = $switchableTestInstance;
    }


    /**
     * Set the expected boolean state of the Switchable implementation
     * instance being tested.
     *
     * @para bool $expectedState The expected boolean state.
     *
     * @return void
     *
     */
    protected function setExpectedState(bool $expectedState): void
    {
        $this->expectedState = $expectedState;
    }

    /**
     * Return the expected boolean state of the Switchable
     * implementation instance being tested.
     *
     * @return bool
     *
     */
    protected function expectedState(): bool
    {
        return $this->expectedState;
    }

    /**
     * Test the state() method returns the expected state.
     *
     * @return void
     *
     */
    public function testStateReturnsExpectedState(): void
    {
        $this->assertEquals(
            $this->expectedState(),
            $this->switchableTestInstance()->state(),
            'The' .
            $this->switchableTestInstance()::class .
            '\'s state() method must return the expected state.'
        );
    }

    /**
     * Test the switchState() method switches the expected state.
     *
     * @return void
     *
     */
    public function testSwitchStateSwtichesTheState(): void
    {
        $this->setExpectedState(
            (
                $this->switchableTestInstance()->state()
                ? false
                : true
            )
        );
        $this->switchableTestInstance()->switchState();
        $this->assertEquals(
            $this->expectedState(),
            $this->switchableTestInstance()->state(),
            'The' .
            $this->switchableTestInstance()::class .
            '\'s switchState() method must switch the expected state.'
        );
    }

}

