<?php

use Pebble\Tools\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Construct
    // -------------------------------------------------------------------------

    public function testConstructFromStringIntAndYmd()
    {
        $ts = strtotime('2024-02-29 10:11:12');

        self::assertSame($ts, (new Date('2024-02-29 10:11:12'))->timestamp);
        self::assertSame($ts, Date::create($ts)->timestamp);
        self::assertSame('2024-02-29 00:00:00', Date::fromYmd(20240229)->toSql());
    }

    public function testMagicPropertiesAndJson()
    {
        $date = Date::create('2024-02-29 10:11:12');

        self::assertSame(2024, $date->year);
        self::assertSame(2, $date->mon);
        self::assertSame(10, $date->hours);
        self::assertSame(4, $date->wday);
        self::assertSame(0, $date->unknown);
        self::assertSame(
            '{"timestamp":1709197872,"year":2024,"mon":2,"mday":29,"hours":10,"minutes":11,"seconds":12,"wday":4,"yday":59}',
            json_encode($date)
        );
    }

    // -------------------------------------------------------------------------
    // Formats
    // -------------------------------------------------------------------------

    public function testFormatTranslatesDayAndMonthNames()
    {
        self::assertSame('Jeudi 29 Février 2024', Date::create('2024-02-29')->format('l j F Y'));
        self::assertSame('Lun. Mars', Date::create('2024-03-04')->format('D M'));
    }

    public function testFrLeavesEscapedLettersAlone()
    {
        self::assertSame('D Jeu.', Date::fr('\D D', 0));
    }

    public function testConversions()
    {
        self::assertSame(20240229, Date::create('2024-02-29')->ymd());
        self::assertSame('2024-02-29T10:11:12+01:00', Date::sqlToIso('2024-02-29 10:11:12'));
        self::assertSame('2024-02-29 11:11:12', Date::isoToSql('2024-02-29T10:11:12+00:00'));
        self::assertFalse(Date::sqlToTimestamp('nope'));
    }

    // -------------------------------------------------------------------------
    // Calendar
    // -------------------------------------------------------------------------

    public function testLeapYearsAndDaysInMonth()
    {
        self::assertTrue(Date::isLeapYear(2000));
        self::assertFalse(Date::isLeapYear(1900));
        self::assertSame(
            [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
            array_map(fn($m) => Date::daysInMonth($m, 2024), range(1, 12))
        );
        self::assertSame(29, Date::create('2024-02-10')->days());
    }

    public function testSeasonDateGivesTheRightDay()
    {
        self::assertSame('2024-03-20', date('Y-m-d', Date::seasonDate(2024, Date::SPRING)));
        self::assertSame('2024-06-20', date('Y-m-d', Date::seasonDate(2024, Date::SUMMER)));
        self::assertSame('2024-09-22', date('Y-m-d', Date::seasonDate(2024, Date::AUTUMN)));
        self::assertSame('2024-12-21', date('Y-m-d', Date::seasonDate(2024, Date::WINTER)));
        self::assertSame(0, Date::seasonDate(2024, 9));
    }

    // -------------------------------------------------------------------------
    // Known bugs (see TODO.md)
    // -------------------------------------------------------------------------

    public function testUnparsableStringSilentlyGivesTheEpoch()
    {
        // BUG: strtotime() returns false, coerced to 0 by the int property.
        // A numeric string timestamp is not handled either.
        self::assertSame(0, (new Date('not a date'))->timestamp);
        self::assertSame(0, (new Date('1700000000'))->timestamp);
    }

    public function testSeasonDateTimeIsOff()
    {
        // BUG: sin($m * 2) / sin($m * 3) miss `* $rad`, the loop exits on the
        // first negative correction, the seconds formula is wrong and mktime()
        // reads a UT time as local time. Real 2024 spring equinox: 04:06 Paris.
        self::assertSame('2024-03-20 03:45:14', date('Y-m-d H:i:s', Date::seasonDate(2024, Date::SPRING)));
        self::assertSame('2024-12-21 08:57:02', date('Y-m-d H:i:s', Date::seasonDate(2024, Date::WINTER)));
    }
}
