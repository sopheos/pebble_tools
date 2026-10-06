<?php

use Pebble\Tools\A;
use PHPUnit\Framework\TestCase;

class ATest extends TestCase
{
    // -------------------------------------------------------------------------
    // parse
    // -------------------------------------------------------------------------

    public function testParseDecodesJsonStringsAndObjects()
    {
        self::assertSame(['a' => ['b' => 1]], A::parse('{"a":{"b":1}}'));
        self::assertSame(['a' => ['b' => 1]], A::parse((object) ['a' => (object) ['b' => 1]]));
    }

    public function testParseReturnsAnEmptyArrayForInvalidJsonAndScalars()
    {
        self::assertSame([], A::parse('{bad'));
        self::assertSame([], A::parse('"x"'));
        self::assertSame([], A::parse(12));
        self::assertSame([], A::parse(null));
    }

    // -------------------------------------------------------------------------
    // equal / rand / unset
    // -------------------------------------------------------------------------

    public function testEqualIgnoresOrderAndKeys()
    {
        self::assertTrue(A::equal([1, 2], [2, 1]));
        self::assertTrue(A::equal(['a' => 1], ['b' => 1]));
        self::assertFalse(A::equal([1, 2], [1, 2, 3]));
    }

    public function testRandReturnsAValueOrNull()
    {
        self::assertNull(A::rand([]));
        self::assertContains(A::rand(['x', 'y']), ['x', 'y']);
    }

    public function testUnsetReturnsACopyWithoutTheKey()
    {
        $values = ['a' => 1, 'b' => 2];

        self::assertSame(['b' => 2], A::unset($values, 'a'));
        self::assertSame(['a' => 1, 'b' => 2], $values);
    }

    // -------------------------------------------------------------------------
    // unique
    // -------------------------------------------------------------------------

    public function testUniqueDropsDuplicatesAndEmptyValuesAndReindexes()
    {
        self::assertSame(['a', 'b'], A::unique(['a', '', 'a', null, 'b']));
    }

    public function testUniqueOnAColumn()
    {
        $rows = [['id' => 1], ['id' => 1], ['id' => 2]];

        self::assertSame([1, 2], A::unique($rows, 'id'));
    }

    // -------------------------------------------------------------------------
    // Known bugs (see TODO.md)
    // -------------------------------------------------------------------------

    public function testUniqueIgnoresColumnZero()
    {
        // BUG: `if ($column_key)` is false for 0, so the rows themselves go
        // through array_unique() (with "Array to string conversion" warnings).
        $rows = [[1, 'a'], [1, 'b'], [2, 'c']];

        self::assertSame([[1, 'a']], @A::unique($rows, 0));
    }

    public function testEqualIsAOneWayDiff()
    {
        // BUG: only array_diff($a, $b) is checked, so $b may hold values absent from $a.
        self::assertTrue(A::equal([1, 1, 2], [1, 2, 3]));
        self::assertFalse(A::equal([1, 2, 3], [1, 1, 2]));
    }

    public function testParseDeepJsonRaisesATypeError()
    {
        // BUG: JSON_THROW_ON_ERROR is passed as $depth. The decode accepts any
        // depth, then json_encode() (depth 512) fails and parse() returns null.
        $this->expectException(TypeError::class);

        A::parse(str_repeat('[', 600) . str_repeat(']', 600));
    }
}
