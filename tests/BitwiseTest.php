<?php

use Pebble\Tools\Bitwise;
use PHPUnit\Framework\TestCase;

class BitwiseTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Nominal
    // -------------------------------------------------------------------------

    public function testHas()
    {
        self::assertTrue(Bitwise::has(5, 4));
        self::assertFalse(Bitwise::has(5, 2));
    }

    public function testHasIsTrueWhenAnyBitOfTheKeyMatches()
    {
        // 6 = 2|4, and only 4 is set in 5.
        self::assertTrue(Bitwise::has(5, 6));
    }

    public function testAddAndDelReturnTheNewValue()
    {
        $flags = 1;

        self::assertSame(3, Bitwise::add($flags, 2));
        self::assertSame(1, Bitwise::add($flags, 1));
        self::assertSame(0, Bitwise::del($flags, 1));
        self::assertSame(1, Bitwise::del($flags, 2));
    }

    // -------------------------------------------------------------------------
    // Known bugs (see TODO.md)
    // -------------------------------------------------------------------------

    public function testAddTakesAReferenceButNeverModifiesIt()
    {
        // BUG: `int &$value` is never written, the variable is unchanged.
        $flags = 1;
        Bitwise::add($flags, 2);
        Bitwise::del($flags, 1);

        self::assertSame(1, $flags);
    }

    public function testAddRejectsALiteral()
    {
        // BUG: the by-reference parameter forbids literals and expressions.
        $this->expectException(Error::class);
        $this->expectExceptionMessage('could not be passed by reference');

        Bitwise::add(1, 2);
    }
}
