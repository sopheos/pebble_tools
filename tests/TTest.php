<?php

use Pebble\Tools\T;
use PHPUnit\Framework\TestCase;

class TTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Accents
    // -------------------------------------------------------------------------

    public function testRemoveAccents()
    {
        self::assertSame('Ca ete ou oe', T::removeAccents('Ça été où œ'));
    }

    public function testConvertAccentsKeepsFrenchLettersByDefault()
    {
        // T::FR leaves é, à, ç, ù, œ... untouched and only converts the rest.
        self::assertSame('Ça été où œ AE', T::convertAccents('Ça été où œ Æ'));
    }

    // -------------------------------------------------------------------------
    // Slugs and cases
    // -------------------------------------------------------------------------

    public function testAlias()
    {
        self::assertSame('eleve-a-l-ecole', T::alias("  Élève à l'école !! "));
        self::assertSame('eleve_a', T::alias('Élève à', '_'));
        self::assertSame('ab', T::alias('a b', ''));
    }

    public function testCaseConversions()
    {
        self::assertSame('helloWorldFoo', T::toCamelCase('hello world-foo'));
        self::assertSame('HelloWorld', T::toPascalCase('hello world'));
        self::assertSame('hello_world_foo', T::toSnakeCase('helloWorldFoo'));
        self::assertSame('hello_world', T::toSnakeCase('HelloWorld'));
    }

    public function testUcfirstAndLcfirstAreMultibyte()
    {
        self::assertSame('Élan', T::ucfirst('élan'));
        self::assertSame('élan', T::lcfirst('Élan'));
    }

    public function testRemoveEmoji()
    {
        self::assertSame('ok  fin', T::remove_emoji('ok 😀 fin'));
    }

    // -------------------------------------------------------------------------
    // HTML
    // -------------------------------------------------------------------------

    public function testXssDependsOnTheType()
    {
        self::assertSame('&lt;a href=&quot;x&quot;&gt;', T::xss('<a href="x">'));
        self::assertSame('', T::xss(null));
        self::assertSame('1', T::xss(true));
        self::assertSame('0', T::xss(false));
        self::assertSame('12', T::xss(12.0));
        self::assertSame('{&quot;a&quot;:&quot;&lt;b&gt;&quot;}', T::xss(['a' => '<b>']));
    }

    public function testHtmlEncodeDecodeRoundTrip()
    {
        self::assertSame('<b>"l\'été"</b>', T::htmlDecode(T::htmlEncode('<b>"l\'été"</b>')));
        self::assertSame('', T::htmlEncode(null));
    }

    // -------------------------------------------------------------------------
    // Numbers
    // -------------------------------------------------------------------------

    public function testNumberAndMoneyReturnHtmlEntities()
    {
        self::assertSame('1&#8239;234&#8239;567,89', T::number(1234567.891));
        self::assertSame('12,50&nbsp;€', T::money(12.5));
    }

    // -------------------------------------------------------------------------
    // Known bugs (see TODO.md)
    // -------------------------------------------------------------------------

    public function testSplitAlwaysThrowsATypeError()
    {
        // BUG: split() is declared `: string` but returns preg_split()'s array.
        $this->expectException(TypeError::class);

        T::split('a , b', ',');
    }
}
