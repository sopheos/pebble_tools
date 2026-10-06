# pebble-tools — API cheat sheet

Quick lookup by intent. This is not exhaustive. Read the source in `vendor/sopheos/pebble_tools/src/` for exact signatures and for edge cases not covered here.

## T (`Pebble\Tools\T`, static)

| Intent | Method |
| ------ | ------ |
| Strip every accent to ASCII | `removeAccents(string $str): string` |
| Strip only non-French accents (default map `T::FR`) | `convertAccents(string $str, array $accents = T::FR): string` |
| Slug | `alias(string $str, string $separator = '-'): string` |
| camelCase / PascalCase / snake_case | `toCamelCase()` / `toPascalCase()` / `toSnakeCase()` (`string → string`) |
| Multibyte first letter | `ucfirst(string $str)` / `lcfirst(string $str)` |
| Remove common emoji blocks | `remove_emoji($string)` |
| Escape any value for HTML | `xss(mixed $value): string` |
| Escape / unescape a string (HTML5, UTF-8, `ENT_QUOTES`) | `htmlEncode(?string)` / `htmlDecode(?string)` |
| French number, HTML entities | `number(float $amount, int $decimal = 2): string` |
| French price, HTML entities | `money(float $amount, string $currency = '&nbsp;€'): string` |
| Split and trim | `split(string $str, string $separator)` — **broken, always throws** |

Constants: `T::ACCENTS` (full map), `T::FR` (map without French letters).

## A (`Pebble\Tools\A`, static)

| Intent | Method |
| ------ | ------ |
| JSON string / object / array → associative array (`[]` otherwise) | `parse(mixed $value): array` |
| Same values, any order or keys (one-way, see gotchas) | `equal(array $a, array $b): bool` |
| Random value or `null` | `rand(array $data)` |
| Copy without a key | `unset(array $values, string $key): array` |
| Unique non-empty values, reindexed, optionally from a column | `unique(array $rows, string\|int\|null $column_key = null): array` |

## Bitwise (`Pebble\Tools\Bitwise`, static)

| Intent | Method |
| ------ | ------ |
| Any bit of `$key` set | `has(int $value, int $key): bool` |
| Value with the bit set (assign the result) | `add(int &$value, int $key): int` |
| Value with the bit cleared (assign the result) | `del(int &$value, int $key): int` |

## Date (`Pebble\Tools\Date`, implements `JsonSerializable`)

| Intent | Method |
| ------ | ------ |
| Now, a timestamp or a `strtotime()` string | `new Date(int\|string\|null $timestamp = null)` / `Date::create(...)` |
| From `20240229` | `Date::fromYmd(int $ymd): static` |
| French `date()` | `format(string $format): string` |
| `20240229` / SQL / ISO 8601 | `ymd(): int` / `toSql(): string` / `toIso(): string` |
| Leap year / days in the month | `isLeap(): bool` / `days(): int` |
| `getdate()` parts | `$date->seconds`, `minutes`, `hours`, `mday`, `wday`, `mon`, `year`, `yday` |
| Raw timestamp | `$date->timestamp` (public `int`) |

Static helpers:

| Intent | Method |
| ------ | ------ |
| French `date()` for any timestamp | `Date::fr(string $format, ?int $timestamp = null): string` |
| Translate English names in a formatted date | `Date::toFr(string $date): string` |
| Calendar | `Date::isLeapYear($y)`, `Date::daysInMonth($m, $y)` |
| Start of a season (right day, approximate time) | `Date::seasonDate(int $year, int $season): int` |
| Conversions | `sqlToTimestamp()`, `isoToTimestamp()`, `timestampToIso()`, `timestampToSql()`, `sqlToIso()`, `isoToSql()`, `timestampToYmd(?int $ts = null): int` |

Constants: `SUNDAY` (0) … `SATURDAY` (6), `DAYS`; `JANUARY` (1) … `DECEMBER` (12), `MONTHS` (French names); `SPRING` (0), `SUMMER`, `AUTUMN`, `WINTER`; `DATETIME_SQL`, `DATE_SQL`, `TIME_SQL`.

## IP (`Pebble\Tools\IP`)

| Intent | Method |
| ------ | ------ |
| Build | `IP::fromString(string)`, `IP::fromHex(string)`, `IP::fromBin(string)` |
| Read | `toString()`, `toHex()`, `toBin()` |
| Version | `isIPv4()`, `isIPv6()` |
| Validate | `IP::isIPStr()`, `IP::isIPv6Str()`, `IP::isIPHex()`, `IP::isIPv6Hex()`, `IP::isIPBin()`, `IP::isIPv6Bin()` |
| Convert | `IP::strToBin($ip, $v6)`, `IP::binToStr($ip, $v6)`, `IP::binToHex($ip, $v6)`, `IP::hexToBin($ip, $v6)` |

## Point (`Pebble\Tools\Point`)

Public `float $lat`, `float $lon` in degrees. `Point::EARTH_RADIUS = 6378137` metres.

| Intent | Method |
| ------ | ------ |
| Build | `new Point($lat, $lon)`, `fromDegrees()`, `fromRadians()`, `fromMN95($e, $n)`, `fromExifGPS(array)`, `fromDMS(array)` |
| Read | `getLat()`, `getLon()`, `getRadLat()`, `getRadLon()`, `coordinates()` (`[lat, lon]`), `latlon()`, `latlng()`, `geojson()` |
| Write (fluent) | `setLat(float)`, `setLon(float)` |
| Distance in metres (haversine) | `distanceTo(Point $point)` |
| Bounding box `[min, max]` around the point | `bounds(float $distance)` |
| Inside a `[lat, lon]` polygon | `insidePoly(array $coords)` |
| Inside `{outer: [...], inner: [...]}` `[lat, lon]` polygons | `insidePath($path)` |
| Inside a GeoJSON `Polygon` / `MultiPolygon` (`[lon, lat]`) | `insideGeoJson(array $geojson)` |
| Ray casting on raw vertices | `Point::insideVertices($x, $y, array $vertices)` |
| Parse `"lat,lon lat,lon"` | `Point::toPoints(string)` — **broken** |

## Points (`Pebble\Tools\Points`)

| Intent | Method |
| ------ | ------ |
| Collect | `add(Point $point): static`, `all(): Point[]` |
| Bounding-box centre | `center()` / `Points::getCenter(array $points)` |
| Closest point to the centre / to a point | `closer()` / `Points::getCloser(Point $point, array $points)` |

## Image (`Pebble\Tools\Image extends Imagick`)

| Intent | Method |
| ------ | ------ |
| Open files (none → empty image) | `new Image(...$files)` / `Image::create(...$files)` |
| File info | `getName()` (no extension), `getFileSize()`, `getImageMimeType()` (no `x-`), `getImageLength()` |
| Options | `setBgColor()`/`getBgColor()` (`#000`), `setQuality()`/`getQuality()` (75) |
| Upload clean-up: CMYK → sRGB, strip EXIF, keep ICC | `prepare()` (`cmynToRgb()` + `stripExif()`) |
| Fits / shrink into a box | `isInside(int $w, int $h, bool $strict_orientation = false)` / `fitBounds(...)` |
| Convert | `toJpeg(bool $force = false)`, `toPng()`, `toWebp()`, `hasFormat(string)` |
| Apply cropper.js data | `crop(array $data)` (`x`, `y`, `width`, `height`, `rotate`, `scaleX`, `scaleY`) |
| Overlay | `watermark(Image $img, int $x, int $y)` |
| Write, extension from the format | `save(?string $to = null): static` |
| Perceptual hash (16×16 grey, 64 hex digits) | `getHash()` |
| Hamming distance and similarity % | `Image::similarHash(string $h1, string $h2, float &$percent = 0): int` |

## Shell (`Pebble\Tools\Shell`, static)

| Intent | Method |
| ------ | ------ |
| Run in the background (`nohup … > /dev/null 2>&1 &`, `start /B` on Windows) | `exec(string $command)` |
| Windows host | `isWin(): bool` |
