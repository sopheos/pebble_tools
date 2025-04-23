<?php

namespace Pebble\Tools;

use JsonSerializable;

/**
 * @property int $seconds Numeric representation of seconds [0,59]
 * @property int $minutes Numeric representation of minutes	[0,59]
 * @property int $hours Numeric representation of hours	[0,23]
 * @property int $mday Numeric representation of the day of the month [1,31]
 * @property int $wday Numeric representation of the day of the week [0=Sunday,6=Saturday]
 * @property int $mon Numeric representation of a month	[1,12]
 * @property int $year A full numeric representation of a year, 4 digits
 * @property int $yday Numeric representation of the day of the year [0,365]
 */
class Date implements JsonSerializable
{
    /**
     * The day constants
     */
    const SUNDAY    = 0;
    const MONDAY    = 1;
    const TUESDAY   = 2;
    const WEDNESDAY = 3;
    const THURSDAY  = 4;
    const FRIDAY    = 5;
    const SATURDAY  = 6;

    const DAYS = [
        self::SUNDAY    => 'Dimanche',
        self::MONDAY    => 'Lundi',
        self::TUESDAY   => 'Mardi',
        self::WEDNESDAY => 'Mercredi',
        self::THURSDAY  => 'Jeudi',
        self::FRIDAY    => 'Vendredi',
        self::SATURDAY  => 'Samedi'
    ];

    /**
     * The month constants
     */
    const JANUARY   = 1;
    const FEBRUARY  = 2;
    const MARCH     = 3;
    const APRIL     = 4;
    const MAY       = 5;
    const JUNE      = 6;
    const JULY      = 7;
    const AUGUST    = 8;
    const SEPTEMBER = 9;
    const OCTOBER   = 10;
    const NOVEMBER  = 11;
    const DECEMBER  = 12;

    const MONTHS = [
        self::JANUARY   => 'Janvier',
        self::FEBRUARY  => 'Février',
        self::MARCH     => 'Mars',
        self::APRIL     => 'Avril',
        self::MAY       => 'Mai',
        self::JUNE      => 'Juin',
        self::JULY      => 'Juillet',
        self::AUGUST    => 'Août',
        self::SEPTEMBER => 'Septembre',
        self::OCTOBER   => 'Octobre',
        self::NOVEMBER  => 'Novembre',
        self::DECEMBER  => 'Décembre'
    ];

    /**
     * The seasons constants
     */
    const SPRING = 0;
    const SUMMER = 1;
    const AUTUMN = 2;
    const WINTER = 3;

    /**
     *  Formats
     */
    const DATETIME_SQL = 'Y-m-d H:i:s';
    const DATE_SQL     = 'Y-m-d';
    const TIME_SQL     = 'H:i:s';

    // -------------------------------------------------------------------------

    public int $timestamp;
    public ?array $date = null;

    public function __construct(int|string|null $timestamp = null)
    {
        if ($timestamp === null || $timestamp === '') {
            $timestamp = time();
        } elseif (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        $this->timestamp = $timestamp;
    }

    public static function create(int|string|null $timestamp = null): static
    {
        return new static($timestamp);
    }

    public static function fromYmd(int $ymd): static
    {
        return new static(strtotime((string) $ymd));
    }

    // -------------------------------------------------------------------------

    public function format(string $format): string
    {
        return self::fr($format, $this->timestamp);
    }

    public function ymd(): int
    {
        return (int) $this->format('Ymd');
    }

    public function toSql(): string
    {
        return self::timestampToSql($this->timestamp);
    }

    public function toIso(): string
    {
        return self::timestampToIso($this->timestamp);
    }

    public function isLeap(): bool
    {
        return self::isLeapYear($this->year);
    }

    public function days(): int
    {
        return self::daysInMonth($this->mon, $this->year);
    }

    public function __get($name)
    {
        $date = $this->date();

        return match ($name) {
            'seconds' => $date['seconds'] ?? 0,
            'minutes' => $date['minutes'] ?? 0,
            'hours'   => $date['hours'] ?? 0,
            'mday'    => $date['mday'] ?? 0,
            'wday'    => $date['wday'] ?? 0,
            'mon'     => $date['mon'] ?? 0,
            'year'    => $date['year'] ?? 0,
            'yday'    => $date['yday'] ?? 0,
            default   => 0
        };
    }

    public function jsonSerialize(): mixed
    {
        $date = $this->date();

        return [
            'timestamp' => $this->timestamp,
            'year'      => $date['year'] ?? 0,
            'mon'       => $date['mon'] ?? 0,
            'mday'      => $date['mday'] ?? 0,
            'hours'     => $date['hours'] ?? 0,
            'minutes'   => $date['minutes'] ?? 0,
            'seconds'   => $date['seconds'] ?? 0,
            'wday'      => $date['wday'] ?? 0,
            'yday'      => $date['yday'] ?? 0,
        ];
    }

    private function date(): array
    {
        if ($this->date === null) {
            $this->date = getdate($this->timestamp);
        }

        return $this->date;
    }



    // -------------------------------------------------------------------------

    /**
     * Returns if a year is a leap year
     *
     * @param int $y
     * @return boolean
     */
    public static function isLeapYear($y)
    {
        return $y % 400 === 0 || ($y % 100 != 0 && $y % 4 === 0);
    }

    // -------------------------------------------------------------------------

    /**
     * Returns the number of days in a month
     *
     * @param int $m
     * @param int $y
     * @return int
     */
    public static function daysInMonth($m, $y)
    {
        return $m === 2 ? 28 + (int)self::isLeapYear($y) : 31 - ($m - 1) % 7 % 2;
    }

    /**
     * Get the beggining date of a season for a given year
     *
     * @param int $year
     * @param int $season
     * @return int
     */
    public static function seasonDate(int $year, int $season): int
    {
        $test = 0.0;
        $m = 0.0;
        $y1 = $year / 1000.0;

        $jd = match ($season) {
            self::SPRING => 1721139.2855 + 365.2421376 * $year + 0.067919 * pow($y1, 2) - 0.0027879 * pow($y1, 3),
            self::SUMMER => 1721233.2486 + 365.2417284 * $year - 0.053018 * pow($y1, 2) + 0.009332 * pow($y1, 3),
            self::AUTUMN => 1721325.6978 + 365.2425055 * $year - 0.126689 * pow($y1, 2) + 0.0019401 * pow($y1, 3),
            self::WINTER => 1721414.3920 + 365.2428898 * $year - 0.010965 * pow($y1, 2) - 0.0084885 * pow($y1, 3),
            default => null
        };

        if ($jd === null) {
            return 0;
        }

        $rad = M_PI / 180;
        $continue = true;

        while ($continue) {
            $t = ($jd - 2415020) / 36525;

            $l = 279.69668 + (36000.76892 * $t) + 0.0003025 * pow($t, 2);

            $m = (358.47583 + (35999.04975 * $t) - 0.00015 * pow($t, 2) - 0.0000033 * pow($t, 3)) / 360;
            $m = ($m - floor($m)) * 360;

            $c = (1.91946 - 0.004789 * $t - 0.000014 * pow($t, 2)) * sin($m * $rad) + (0.020094 - 0.0001 * $t) * sin($m * 2) + (0.000293 * sin($m * 3));

            $ome = (259.18 - 1934.142 * $t) / 360;
            $ome = ($ome - floor($ome)) * 360 * $rad;

            $ap = ($l + $c - 0.00569 - 0.00479 * sin($ome)) / 360;
            $ap = ($ap - floor($ap)) * 360;

            $test = $jd;
            $cor = 58 * sin(($season * 90 - $ap) * $rad);
            $jd = $jd + $cor;

            $continue = ($jd - $test) > 0.001;
        }

        $jd = $jd + 0.5;
        $z = floor($jd);
        if ($z < 2299161) {
            $a = $z;
        } else {
            $x = floor(($z - 1867216.25) / 36524.25);
            $a = $z + 1 + $x - floor($x / 4);
        }

        $b = $a + 1524;
        $c = floor(($b - 122.1) / 365.25);
        $d = floor(365.25 * $c);
        $e = floor(($b - $d) / 30.6001);
        $f = $jd - $z;
        $daydec = $b - $d - floor(30.6001 * $e) + $f;

        $month  = $e < 13.5 ? $e - 1 : $e - 13;
        $frac   = $daydec - floor($daydec);
        $day    = floor($daydec);
        $hour   = floor($frac * 24);
        $minute = floor(($frac * 24 - $hour) * 60);
        $second = floor($frac * 24 - $hour - $minute);

        return mktime($hour, $minute, $second, $month, $day, $year) ?: 0;
    }

    // -------------------------------------------------------------------------

    /**
     * @param string $value
     * @return int
     */
    public static function sqlToTimestamp($value)
    {
        return strtotime($value);
    }

    /**
     * @param string $value
     * @return int
     */
    public static function isoToTimestamp($value)
    {
        return self::sqlToTimestamp($value);
    }

    /**
     * @param int $value
     * @return string
     */
    public static function timestampToIso($value)
    {
        return date('c', $value);
    }

    /**
     * @param int $value
     * @return string
     */
    public static function timestampToSql($value)
    {
        return date(self::DATETIME_SQL, $value);
    }

    /**
     * @param string $value
     * @return string
     */
    public static function sqlToIso($value)
    {
        return self::timestampToIso(self::sqlToTimestamp($value));
    }

    /**
     * @param string $value
     * @return string
     */
    public static function isoToSql($value)
    {
        return self::timestampToSql(self::isoToTimestamp($value));
    }

    public static function timestampToYmd(?int $ts = null): int
    {
        return (int) self::fr('Ymd', $ts ?? time());
    }

    /**
     * date french version
     *
     * @param string $format
     * @param integer|null $timestamp
     * @return string
     */
    public static function fr(string $format, ?int $timestamp = null): string
    {
        if ($timestamp === null) {
            $timestamp = time();
        }

        $dt = date($format, $timestamp);

        if (preg_match("/[^\\\][DlFM]/", ' ' . $format)) {
            $dt = self::toFr($dt);
        }

        return $dt;
    }

    public static function toFr(string $date): string
    {
        return strtr($date, [
            'Wednesday' => 'Mercredi',
            'September' => 'Septembre',
            'December' => 'Décembre',
            'February' => 'Février',
            'Thursday' => 'Jeudi',
            'November' => 'Novembre',
            'Saturday' => 'Samedi',
            'January' => 'Janvier',
            'Tuesday' => 'Mardi',
            'October' => 'Octobre',
            'August' => 'Août',
            'Sunday' => 'Dimanche',
            'Monday' => 'Lundi',
            'Friday' => 'Vendredi',
            'April' => 'Avril',
            'March' => 'Mars',
            'July' => 'Juillet',
            'June' => 'Juin',
            'Aug' => 'Août',
            'Apr' => 'Avril',
            'Sun' => 'Dim.',
            'Dec' => 'Déc.',
            'Feb' => 'Févr.',
            'Jan' => 'Janv.',
            'Thu' => 'Jeu.',
            'Jul' => 'Juil.',
            'Jun' => 'Juin',
            'Mon' => 'Lun.',
            'May' => 'Mai',
            'Tue' => 'Mar.',
            'Mar' => 'Mars',
            'Wed' => 'Mer.',
            'Nov' => 'Nov.',
            'Oct' => 'Oct.',
            'Sat' => 'Sam.',
            'Sep' => 'Sept.',
            'Fri' => 'Ven.',
        ]);
    }

    // -------------------------------------------------------------------------

}
