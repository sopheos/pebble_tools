<?php

use Pebble\Tools\Shell;
use PHPUnit\Framework\TestCase;

class ShellTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Harmless calls only: exec() runs a real command in the background.
    // -------------------------------------------------------------------------

    public function testIsWinMatchesPhpUname()
    {
        self::assertSame(str_contains(php_uname(), 'Windows'), Shell::isWin());
    }

    public function testExecReturnsImmediately()
    {
        if (Shell::isWin()) {
            $this->markTestSkipped('Unix only');
        }

        $start = microtime(true);
        self::assertNull(Shell::exec('sleep 2'));
        self::assertLessThan(1.5, microtime(true) - $start);
    }
}
