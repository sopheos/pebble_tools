---
name: pebble-tools
description: How to correctly use the sopheos/pebble_tools PHP helper library (namespace Pebble\Tools — static helpers T for strings/slugs/HTML escaping/French number formatting, A for arrays, Bitwise for flag integers, Date for French-formatted dates and season dates, IP for text/hex/binary IPv4 and IPv6 conversion, Point and Points for GPS coordinates, distances, bounding boxes and polygon/GeoJSON inclusion, Image as an Imagick subclass for resizing, cropper.js crops, format conversion and perceptual hashes, and Shell for background commands). Use this whenever the project's composer.json requires sopheos/pebble_tools, code imports from Pebble\Tools\*, or you're asked to slugify or escape a string, format a price or a French date, store an IP address, test bit flags, compute a distance or check whether a GPS point lies in a zone, resize/crop/convert an uploaded image or compare images, or start a background process in a PHP project that has this library available — even if the request is phrased generically without naming the library. Also check this before writing your own helper for any of these in such a project, since this library already provides them but has non-obvious and in places broken behavior (T::split() always throws, Bitwise::add()/del() never modify their reference, an invalid date string silently becomes 1970, IPv6 hex conversion rounds addresses, Point::toPoints() parses characters, crop() swaps scaleX and scaleY, similarHash() miscounts, A::equal() is one-way) that a naive caller would miss.
---

# pebble-tools

`sopheos/pebble_tools` is a grab bag of independent PHP 8.1+ helpers, mostly static. No runtime dependency except `ext-imagick`, needed only by `Image` (which `extends Imagick` and fatals without it). There is no shared state, no configuration and no service to boot.

Namespace: `Pebble\Tools\*`. Source lives in `vendor/sopheos/pebble_tools/src/`. Read it directly when you need an exact signature; this skill focuses on *which helper to pick* and the behavior that isn't obvious from the method names.

## Orientation

- `T` — strings: accents (`removeAccents`, `convertAccents`), slugs (`alias`), case conversion, multibyte `ucfirst`/`lcfirst`, HTML escaping (`xss`, `htmlEncode`, `htmlDecode`), French `number`/`money`.
- `A` — arrays: `parse` (JSON/object → array, never throws), `equal`, `rand`, `unset`, `unique`.
- `Bitwise` — `has`/`add`/`del` on an int of flags. **Use the return value.**
- `Date` — an object wrapping a public `int $timestamp`, with `getdate()` magic properties, French `format()`, SQL/ISO converters and `seasonDate()`.
- `IP` — `IP::fromString/fromHex/fromBin()` then `toString/toHex/toBin()`, IPv4 and IPv6.
- `Point` / `Points` — lat/lon in degrees, haversine `distanceTo()` in metres, `bounds()`, `insidePoly()` / `insidePath()` / `insideGeoJson()`, collection centre and closest point.
- `Image` — an `Imagick` subclass: `prepare()`, `fitBounds()`, `crop()` from cropper.js data, `toJpeg()`/`toPng()`/`toWebp()`, `save()`, `getHash()`/`similarHash()`.
- `Shell` — `exec()` fires a command in the background (`nohup … &`) and returns immediately.

For a method cheat sheet, see `references/api-reference.md`. For the complete list of easy-to-miss behaviors, see `references/gotchas.md`. Read it before relying on any helper not shown below.

## Core recipes

### Slugs, escaping, money

```php
use Pebble\Tools\T;

T::alias("L'été à Noël !");        // 'l-ete-a-noel'
T::toSnakeCase('helloWorld');      // 'hello_world'
echo T::xss($userInput);           // escapes strings, '' for null, JSON for arrays
echo T::money(1234.5);             // '1&#8239;234,50&nbsp;€' — HTML entities, not for plain text
```

Do not call `T::split()`: it always throws a `TypeError`. Use `preg_split('#\s*,\s*#', $str, -1, PREG_SPLIT_NO_EMPTY)`.

### Bit flags

```php
use Pebble\Tools\Bitwise;

$user->flags = Bitwise::add($user->flags, User::ADMIN);   // assign the result
if (Bitwise::has($user->flags, User::ADMIN)) { /* ... */ }
```

The parameter is declared by reference but never written, and literals are rejected. Keep each key a single bit: `has()` is true when **any** bit of the key matches.

### Dates

```php
use Pebble\Tools\Date;

$d = Date::create($row['created_at']);   // SQL string, ISO string or int timestamp
$d->format('l j F Y');                    // 'Jeudi 29 Février 2024'
$d->year; $d->mon; $d->mday;              // getdate() values, read-only
$d->toSql();                              // 'Y-m-d H:i:s'
```

Validate strings first: `new Date('garbage')` and `new Date('1700000000')` both silently give timestamp `0`. Pass ints as ints. `seasonDate()` gives the right day but an hour that may be off by up to two hours.

### IP addresses

```php
use Pebble\Tools\IP;

$ip = IP::fromString($_SERVER['REMOTE_ADDR']);
$ip->isIPv6();
$ip->toBin();                              // exact, 32 or 128 chars for valid input
```

Store and compare IPv6 through `toBin()`/`fromBin()`. `toHex()`/`fromHex()` round IPv6 addresses and can throw `ValueError`. Invalid input silently gives `0.0.0.0`; validate with `IP::isIPStr()` first.

### GPS points

```php
use Pebble\Tools\Point;
use Pebble\Tools\Points;

$p = new Point($lat, $lon);
$metres = $p->distanceTo(new Point(45.764, 4.8357));
[$min, $max] = $p->bounds(5000);                      // SQL pre-filter box, not near a pole
$p->insideGeoJson(['type' => 'Polygon', 'coordinates' => $rings]);   // [lon, lat] order
$p->insidePoly([[$lat1, $lon1], [$lat2, $lon2], ...]);              // [lat, lon] order

$centre = (new Points())->add($a)->add($b)->center();  // bounding-box centre
```

Do not use `Point::toPoints()`, it reads characters. Parse `"lat,lon"` pairs yourself.

### Uploaded images

```php
use Pebble\Tools\Image;

$img = Image::create($tmpPath)->prepare();             // CMYK -> RGB, strip EXIF, keep ICC
$img->crop($cropperData)->fitBounds(1920, 1080)->setQuality(80)->toWebp();
$saved = $img->save('/var/www/uploads/photo');          // extension follows the format: photo.webp
```

`save()` returns a **new** `Image` when the path changes. `crop()` swaps `scaleX`/`scaleY` (mirror flags) and does not reset the virtual canvas: call `$img->setImagePage(0, 0, 0, 0)` after it before saving PNG/GIF. Round cropper.js decimals before passing them to avoid deprecations.

### Background command

```php
use Pebble\Tools\Shell;

Shell::exec('php ' . escapeshellarg($script) . ' ' . escapeshellarg($id));
```

Nothing is escaped for you and no output or exit code comes back.

## Behavior to keep in mind while writing code

- **`T::split()` always throws.** Its return type is `string` but it returns an array.
- **`Bitwise::add()`/`del()` never modify the variable**, despite `&$value`. Assign the result. Literals throw an `Error`.
- **`Date` turns any unparsable or numeric string into `0` (1970-01-01)**, without warning.
- **`A::equal()` is one-way**: `equal([1, 1, 2], [1, 2, 3])` is `true`. **`A::unique($rows, 0)` ignores column `0`.** `A::parse()` never throws: invalid JSON gives `[]`, very deep JSON a `TypeError`.
- **IPv6 hex is lossy.** Use the binary representation. IPv4 hex/binary are not zero-padded.
- **`Point::toPoints()` is broken** and **`bounds()` is wrong near the poles.** `fromDMS()` returns `null` on missing keys.
- **Polygon order differs**: `insidePoly()`/`insidePath()` take `[lat, lon]`, `insideGeoJson()` takes GeoJSON `[lon, lat]`.
- **`Image::crop()` mirrors on the wrong axis**, truncates float coordinates with a deprecation, and keeps the canvas offset.
- **`Image::similarHash()` miscounts** the distance whenever a hex digit is below 8. Equal hashes still give 0.
- **`T::number()`/`money()` return HTML entities** (`&#8239;`, `&nbsp;`), meant for HTML output only.
- **`T::convertAccents()` keeps French accents by default**; use `removeAccents()` for plain ASCII.

Read `references/gotchas.md` for the full list before assuming a helper behaves like its name suggests.
