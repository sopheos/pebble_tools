<?php

use Pebble\Tools\IP;
use PHPUnit\Framework\TestCase;

class IPTest extends TestCase
{
    // -------------------------------------------------------------------------
    // IPv4
    // -------------------------------------------------------------------------

    public function testIPv4Conversions()
    {
        $ip = IP::fromString('192.168.1.1');

        self::assertTrue($ip->isIPv4());
        self::assertSame('c0a80101', $ip->toHex());
        self::assertSame('11000000101010000000000100000001', $ip->toBin());
        self::assertSame('192.168.1.1', IP::fromHex('c0a80101')->toString());
        self::assertSame('192.168.1.1', IP::fromBin($ip->toBin())->toString());
    }

    public function testIPv4HexAndBinAreNotPadded()
    {
        self::assertSame('1020304', IP::fromString('1.2.3.4')->toHex());
        self::assertSame('1.2.3.4', IP::fromHex('1020304')->toString());
    }

    // -------------------------------------------------------------------------
    // IPv6
    // -------------------------------------------------------------------------

    public function testIPv6Conversions()
    {
        $ip = IP::fromString('2001:db8::1');

        self::assertTrue($ip->isIPv6());
        self::assertSame('20010db8000000000000000000000001', $ip->toHex());
        self::assertSame(128, strlen($ip->toBin()));
        self::assertSame('2001:db8::1', IP::fromHex($ip->toHex())->toString());
    }

    public function testIPv6BinRoundTripIsExact()
    {
        $str = '2a01:e0a:1234:5679:abcd:ef01:2345:6789';

        self::assertSame($str, IP::fromBin(IP::fromString($str)->toBin())->toString());
    }

    // -------------------------------------------------------------------------
    // Invalid input
    // -------------------------------------------------------------------------

    public function testInvalidInputSilentlyGivesZero()
    {
        $ip = IP::fromString('nope');

        self::assertSame('0.0.0.0', $ip->toString());
        self::assertSame('0', $ip->toHex());
        self::assertSame('0', $ip->toBin());
        self::assertTrue($ip->isIPv4());
    }

    // -------------------------------------------------------------------------
    // Known bugs (see TODO.md)
    // -------------------------------------------------------------------------

    public function testIPv6HexLosesPrecision()
    {
        // BUG: base_convert() goes through a float above 2^53, so the low bits
        // of each 64-bit half are rounded.
        self::assertSame(
            '2a010e0a12345679abcdef0123456800',
            IP::fromString('2a01:e0a:1234:5679:abcd:ef01:2345:6789')->toHex()
        );
    }

    public function testIPv6HexOverflowThrows()
    {
        // BUG: a 64-bit half rounded up to 2^64 gives 17 hex digits, and the
        // negative padding makes str_repeat() throw.
        $this->expectException(ValueError::class);

        IP::fromString('ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff');
    }
}
