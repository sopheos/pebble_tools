# pebble-tools — gotchas

Things the method names don't tell you, grouped by class. Every item below is pinned by a test in `tests/`. Items marked **(bug)** are listed in the package's `TODO.md` and may be fixed in a later version. Check the test of the same name in `vendor/sopheos/pebble_tools/tests/` to see the current behavior.

## T

- **(bug) `split()` always throws a `TypeError`.** It is declared `: string` and returns `preg_split()`'s array. Call `preg_split()` yourself.
- **`convertAccents()` keeps French letters by default.** The default map `T::FR` leaves é, è, à, ç, ù, œ… untouched and only converts other accents (`Æ` → `AE`). `removeAccents()` converts everything.
- **`alias()` with an empty separator just deletes non-alphanumerics.** `alias('a b', '')` is `'ab'`.
- **`number()` and `money()` return HTML entities**: a narrow no-break space `&#8239;` for thousands and `&nbsp;` before `€`. Do not use them in plain text, CSV or JSON.
- **`xss()` depends on the type.** `null` → `''`, booleans → `'1'`/`'0'`, numbers are cast without escaping, arrays and objects are JSON-encoded then escaped.
- **`remove_emoji()` leaves the surrounding spaces** and covers only some Unicode blocks.

## A

- **`parse()` never throws.** Invalid JSON and scalar JSON (`'"x"'`, `'12'`) give `[]`. Objects are converted recursively through a JSON round trip.
- **(bug) `parse()` raises a `TypeError` on JSON nested deeper than 512 levels**, because `JSON_THROW_ON_ERROR` is passed as the depth.
- **`equal()` ignores order and keys** (`['a' => 1]` equals `['b' => 1]`).
- **(bug) `equal()` is a one-way diff.** `equal([1, 1, 2], [1, 2, 3])` is `true`, the reverse is `false`.
- **`unique()` drops `null`, `''` and `[]` and reindexes.**
- **(bug) `unique($rows, 0)` ignores column `0`**: the rows themselves are deduplicated, with "Array to string conversion" warnings.
- **`unset()` returns a copy**, the input array is unchanged.

## Bitwise

- **(bug) `add()` and `del()` never modify their by-reference argument.** Only the return value changes: `$flags = Bitwise::add($flags, 2)`.
- **(bug) They reject literals and expressions** (`Bitwise::add(1, 2)` throws an `Error`) because of the reference.
- **`has()` is true when any bit of the key matches.** `has(5, 6)` is `true`. Test one bit at a time.

## Date

- **(bug) An unparsable string silently gives timestamp `0`** (1970-01-01). So does a numeric string such as `'1700000000'`: pass timestamps as ints.
- **Magic properties are `getdate()` values**: `mon` is 1-12, `wday` is 0 (Sunday) to 6, an unknown name gives `0` instead of an error.
- **`format()`/`fr()` translate day and month names into French**, abbreviated ones included (`D` → `Lun.`, `M` → `Mars`). Escaped letters (`\D`) are left alone.
- **`sqlToTimestamp()` returns `false`** on an invalid string.
- **`seasonDate()` returns `0` for an unknown season.**
- **(bug) `seasonDate()` gives the right day but a wrong time**, off by 20 minutes to over 2 hours (wrong formulas, UT read as local time).

## IP

- **Invalid input never throws.** It gives `0.0.0.0`, hex `'0'`, binary `'0'`, and `isIPv4()` is `true`. Validate with `IP::isIPStr()` first.
- **IPv4 hex and binary are not zero-padded.** `1.2.3.4` → `'1020304'`. Pad them yourself before comparing or sorting as strings.
- **IPv6 binary round trips are exact.**
- **(bug) IPv6 hex is rounded.** `toHex()`/`fromHex()` go through floats: `…:2345:6789` becomes `…:2345:6800`.
- **(bug) Some IPv6 addresses throw a `ValueError` at construction**, e.g. `ffff:ffff:…:ffff`, when the rounded half overflows.

## Point / Points

- **`insidePoly()` and `insidePath()` take `[lat, lon]` pairs; `insideGeoJson()` takes GeoJSON `[lon, lat]`.** Mixing them up silently gives wrong answers.
- **`insideGeoJson()` handles holes and `MultiPolygon`**, and returns `false` for any other type.
- **`fromExifGPS()` and `fromDMS()` round to 7 decimals** and negate for `S`/`W`.
- **(bug) `fromDMS()` returns `null`** when a key is missing, despite `@return static`.
- **(bug) `toPoints()` reads characters, not columns.** `'48.85,2.35'` gives the point (4, 8).
- **(bug) `bounds()` is wrong near the poles.** The upper latitude goes above 90° near the north pole and is always 90° near the south pole.
- **`Points::center()` is the bounding-box centre**, not a centroid. `getCenter([])` gives (0, 0) and `getCloser()` on an empty list gives `null`.

## Image

- **`hasFormat()` returns `false` on an empty image** instead of throwing.
- **`save()` returns a new object when the path changes**, and the extension follows the image format (`toJpeg()->save()` writes `.jpg`). Keep using the returned instance.
- **`fitBounds()`/`isInside()` rotate the box to match the image orientation** unless `$strict_orientation` is `true`. They never enlarge.
- **`crop()` does not reset the virtual canvas.** The page keeps the original size and the crop offset; call `setImagePage(0, 0, 0, 0)` before saving PNG/GIF.
- **(bug) `crop()` mirrors on the wrong axis.** `scaleX = -1` flips vertically and `scaleY = -1` flips horizontally.
- **(bug) `crop()` passes floats to `cropImage()`**: decimals are truncated with an "Implicit conversion" deprecation per value. Round them first.
- **`getHash()` is 64 hex digits**, and two identical images give a distance of 0.
- **(bug) `similarHash()` pads bits on the wrong side**, so any hex digit below 8 is miscounted: `similarHash('1', '8')` is 0.

## Shell

- **`exec()` returns immediately** (the command runs in the background) and returns nothing: no output, no exit code.
