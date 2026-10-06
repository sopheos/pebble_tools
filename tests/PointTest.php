<?php

use Pebble\Tools\Point;
use Pebble\Tools\Points;
use PHPUnit\Framework\TestCase;

class PointTest extends TestCase
{
    private function square(): array
    {
        return [[0, 0], [0, 2], [2, 2], [2, 0]];
    }

    // -------------------------------------------------------------------------
    // Factories and getters
    // -------------------------------------------------------------------------

    public function testFactoriesAndExports()
    {
        $point = Point::fromRadians(deg2rad(48.5), deg2rad(2.25));

        self::assertEqualsWithDelta(48.5, $point->getLat(), 1e-9);
        self::assertSame(['lat' => 48.0, 'lng' => 2.0], Point::fromDegrees(48, 2)->latlng());
        self::assertSame(['type' => 'Point', 'coordinates' => [2.0, 48.0]], (new Point(48, 2))->geojson());
    }

    public function testFromMN95ConvertsBernOrigin()
    {
        $point = Point::fromMN95(2600000, 1200000);

        self::assertEqualsWithDelta(46.9511, $point->lat, 1e-4);
        self::assertEqualsWithDelta(7.4386, $point->lon, 1e-4);
    }

    public function testFromExifGPSHandlesHemispheresAndRoundsTo7Decimals()
    {
        $point = Point::fromExifGPS([
            'GPSLatitude' => ['48/1', '51/1', '2400/100'],
            'GPSLatitudeRef' => 'S',
            'GPSLongitude' => ['2/1', '21/1', '3/1'],
            'GPSLongitudeRef' => 'W',
        ]);

        self::assertSame([-48.8566667, -2.3508333], $point->coordinates());
    }

    public function testDistanceTo()
    {
        $paris = new Point(48.8566, 2.3522);
        $lyon = new Point(45.764, 4.8357);

        self::assertEqualsWithDelta(391937.5, $paris->distanceTo($lyon), 1);
    }

    public function testBounds()
    {
        [$min, $max] = (new Point(48.85, 2.35))->bounds(1000);

        self::assertEqualsWithDelta(48.841, $min->lat, 1e-3);
        self::assertEqualsWithDelta(48.859, $max->lat, 1e-3);
        self::assertLessThan(2.35, $min->lon);
        self::assertGreaterThan(2.35, $max->lon);
    }

    // -------------------------------------------------------------------------
    // Inside
    // -------------------------------------------------------------------------

    public function testInsidePolyUsesLatLon()
    {
        self::assertTrue((new Point(1, 1))->insidePoly($this->square()));
        self::assertFalse((new Point(3, 1))->insidePoly($this->square()));
        self::assertTrue((new Point(1, 1))->insidePath((object) ['outer' => [$this->square()]]));
        self::assertFalse((new Point(1, 1))->insidePath(null));
    }

    public function testInsidePolyAndInsideGeoJsonUseOppositeAxisOrders()
    {
        // lat 0..2, lon 4..6
        $point = new Point(1, 5);

        self::assertTrue($point->insidePoly([[0, 4], [0, 6], [2, 6], [2, 4]]));
        self::assertTrue($point->insideGeoJson(['type' => 'Polygon', 'coordinates' => [[[4, 0], [6, 0], [6, 2], [4, 2], [4, 0]]]]));
        self::assertFalse($point->insideGeoJson(['type' => 'Polygon', 'coordinates' => [[[0, 4], [0, 6], [2, 6], [2, 4], [0, 4]]]]));
    }

    public function testInsideGeoJsonHandlesHoles()
    {
        $outer = [[0, 0], [0, 2], [2, 2], [2, 0], [0, 0]];
        $hole = [[0.5, 0.5], [0.5, 1.5], [1.5, 1.5], [1.5, 0.5], [0.5, 0.5]];

        self::assertTrue((new Point(1, 1))->insideGeoJson(['type' => 'Polygon', 'coordinates' => [$outer]]));
        self::assertFalse((new Point(1, 1))->insideGeoJson(['type' => 'Polygon', 'coordinates' => [$outer, $hole]]));
        self::assertTrue((new Point(1, 1))->insideGeoJson(['type' => 'MultiPolygon', 'coordinates' => [[$outer]]]));
        self::assertFalse((new Point(1, 1))->insideGeoJson(['type' => 'Point', 'coordinates' => [1, 1]]));
    }

    // -------------------------------------------------------------------------
    // Points
    // -------------------------------------------------------------------------

    public function testPointsCenterIsTheBoundingBoxCenter()
    {
        $points = (new Points())->add(new Point(0, 0))->add(new Point(10, 20))->add(new Point(4, 9));

        self::assertSame([5.0, 10.0], $points->center()->coordinates());
        self::assertSame([4.0, 9.0], $points->closer()->coordinates());
        self::assertCount(3, $points->all());
    }

    public function testPointsEmptyCases()
    {
        self::assertNull(Points::getCloser(new Point(), []));
        self::assertSame([0.0, 0.0], Points::getCenter([])->coordinates());
    }

    // -------------------------------------------------------------------------
    // Known bugs (see TODO.md)
    // -------------------------------------------------------------------------

    public function testToPointsReadsCharactersInsteadOfColumns()
    {
        // BUG: `$row[0]` / `$row[1]` are the first two characters of the row,
        // not the exploded `$cols`.
        $points = Point::toPoints('48.85,2.35 45.1,4.2');

        self::assertSame([4.0, 8.0], $points[0]->coordinates());
        self::assertSame([4.0, 5.0], $points[1]->coordinates());
    }

    public function testBoundsNearAPoleUsesMaxForTheUpperLatitude()
    {
        // BUG: `max($max_lat, $MAXLAT)` instead of min(): above 90° near the
        // north pole, and always 90° near the south pole.
        [, $north] = (new Point(89.99, 0))->bounds(10000);
        [, $south] = (new Point(-89.99, 0))->bounds(10000);

        self::assertGreaterThan(90, $north->lat);
        self::assertSame(90.0, $south->lat);
    }

    public function testFromDMSReturnsNullOnMissingKeys()
    {
        // BUG: documented `@return static`, but returns null without any error.
        self::assertNull(Point::fromDMS(['lat_ref' => 'N']));
    }
}
