# Pebble/Tools

Boîte à outils PHP 8.1+ : chaînes, tableaux, dates en français, drapeaux binaires, adresses IP, points GPS et images Imagick.

Chaque classe est indépendante et s'utilise surtout en statique. Aucune dépendance runtime, sauf `ext-imagick` pour la classe `Image`.

## Installation

```bash
composer require sopheos/pebble_tools
```

`Pebble\Tools\Image` étend `Imagick` : sans l'extension `imagick`, charger cette classe provoque une erreur fatale. Les autres classes n'en ont pas besoin.

## Claude Code

Ce package fournit un skill Claude Code dans [`skills/pebble-tools/`](skills/pebble-tools/). Il documente les patterns d'usage et les pièges de la librairie : `T::split()` inutilisable, `Bitwise::add()` qui ne modifie pas sa référence, `Date` qui vaut 1970 sur une chaîne invalide, hexadécimal IPv6 arrondi, `Point::toPoints()` cassé, `crop()` qui inverse les miroirs, etc.

Dans un projet qui dépend de `sopheos/pebble_tools`, copie-le une fois dans `.claude/skills/` après `composer install` pour que Claude Code le charge automatiquement. Le nom du dossier doit correspondre au `name` déclaré dans `SKILL.md` :

```bash
cp -r vendor/sopheos/pebble_tools/skills/pebble-tools .claude/skills/pebble-tools
```

Pour la maintenance de la lib elle-même, voir [`CLAUDE.md`](CLAUDE.md). Les bugs connus sont listés dans [`TODO.md`](TODO.md).

## T (chaînes)

* `removeAccents(string $str) : string` Remplace toutes les lettres accentuées par leur équivalent ASCII (`œ` → `oe`).
* `convertAccents(string $str, array $accents = T::FR) : string` Par défaut, ne convertit que les accents **non français** (é, è, à, ç, œ… sont conservés).
* `alias(string $str, string $separator = '-') : string` Slug : sans accents, en minuscules, chaque caractère non alphanumérique remplacé par le séparateur, sans doublon ni séparateur aux extrémités.
* `split(string $str, string $separator) : string` Explose et trim. **Cassée** : lève toujours une `TypeError` (voir [`TODO.md`](TODO.md)).
* `ucfirst(string $str) : string` / `lcfirst(string $str) : string` Versions multioctets.
* `toCamelCase()`, `toPascalCase()`, `toSnakeCase()` Conversions de casse, accents retirés.
* `remove_emoji($string)` Retire les principales plages d'emoji (pas toutes).
* `xss(mixed $value) : string` Échappe une chaîne, `''` pour `null`, `'1'`/`'0'` pour un booléen, JSON échappé pour un tableau ou un objet.
* `htmlEncode(?string $value) : string` / `htmlDecode(?string $value) : string` `htmlspecialchars` / `html_entity_decode` en HTML5 UTF-8.
* `number(float $amount, int $decimal = 2) : string` Format français, l'espace des milliers est l'entité `&#8239;`.
* `money(float $amount, string $currency = '&nbsp;€') : string` `number()` avec 2 décimales et la devise.

## A (tableaux)

* `parse(mixed $value) : array` Convertit une chaîne JSON, un tableau ou un objet en tableau associatif. Renvoie `[]` pour un JSON invalide ou un scalaire.
* `equal(array $a, array $b) : bool` Mêmes valeurs, sans tenir compte de l'ordre ni des clés. **Attention** : la comparaison ne se fait que dans un sens.
* `rand(array $data)` Une valeur au hasard, `null` si le tableau est vide.
* `unset(array $values, string $key) : array` Copie du tableau sans la clé.
* `unique(array $rows, string|int|null $column_key = null) : array` Valeurs uniques, réindexées, sans `null`, `''` ni `[]`. Avec `$column_key`, travaille sur cette colonne (la colonne `0` est ignorée, voir [`TODO.md`](TODO.md)).

## Bitwise

* `has(int $value, int $key) : bool` Vrai si au moins un bit de `$key` est présent dans `$value`.
* `add(int &$value, int $key) : int` Renvoie `$value` avec le bit ajouté.
* `del(int &$value, int $key) : int` Renvoie `$value` sans le bit.

`add()` et `del()` **ne modifient pas** la variable passée, malgré la référence : utiliser la valeur de retour. Elles n'acceptent pas de littéral.

```php
$flags = Bitwise::add($flags, self::FLAG_ADMIN);
```

## Date

`\Pebble\Tools\Date` encapsule un timestamp (propriété publique `timestamp`).

* `new Date(int|string|null $timestamp = null)` / `Date::create(...)` Maintenant si vide, sinon un timestamp ou une chaîne lue par `strtotime()`. Une chaîne invalide ou numérique donne silencieusement `0` (1970).
* `fromYmd(int $ymd) : static` Depuis un entier `20240229`.
* `format(string $format) : string` `date()` avec les noms de jours et de mois en français.
* `ymd() : int`, `toSql() : string`, `toIso() : string`
* `isLeap() : bool`, `days() : int` Nombre de jours du mois.
* Propriétés magiques en lecture : `seconds`, `minutes`, `hours`, `mday`, `wday`, `mon`, `year`, `yday` (comme `getdate()`, `0` pour un nom inconnu).
* `jsonSerialize()` Le timestamp et toutes ces propriétés.

Méthodes statiques :

* `fr(string $format, ?int $timestamp = null) : string` `date()` en français (`D`, `l`, `M`, `F` traduits, les lettres échappées sont respectées).
* `toFr(string $date) : string` Traduit les noms anglais d'une date déjà formatée.
* `isLeapYear($y)`, `daysInMonth($m, $y)`
* `seasonDate(int $year, int $season) : int` Début de la saison (`Date::SPRING`, `SUMMER`, `AUTUMN`, `WINTER`). Le jour est juste, l'heure est fausse de 20 minutes à plus de 2 heures. `0` pour une saison inconnue.
* `sqlToTimestamp()`, `isoToTimestamp()`, `timestampToIso()`, `timestampToSql()`, `sqlToIso()`, `isoToSql()`, `timestampToYmd(?int $ts = null) : int`

Constantes : jours (`SUNDAY` = 0 … `SATURDAY` = 6) et `DAYS`, mois (`JANUARY` = 1 …) et `MONTHS`, formats `DATETIME_SQL`, `DATE_SQL`, `TIME_SQL`.

## IP

* `IP::fromString(string $ip)`, `IP::fromHex(string $ip)`, `IP::fromBin(string $ip)` Fabriques. Une entrée invalide donne `0.0.0.0` sans erreur.
* `toString()`, `toHex()`, `toBin()` Les trois représentations.
* `isIPv4()`, `isIPv6()`
* Helpers statiques : `isIPStr()`, `isIPHex()`, `isIPBin()`, `isIPv6Str()`, `isIPv6Hex()`, `isIPv6Bin()`, `strToBin()`, `binToStr()`, `binToHex()`, `hexToBin()`.

L'IPv4 n'est pas complétée par des zéros (`1.2.3.4` → `1020304`). En IPv6, `toHex()` et `fromHex()` arrondissent les adresses (et lèvent une `ValueError` pour certaines) : passer par `toBin()` / `fromBin()`, qui sont exacts.

## Point et Points

`\Pebble\Tools\Point` : propriétés publiques `lat` et `lon` en degrés.

* `new Point(float $lat = 0, float $lon = 0)`, `fromDegrees()`, `fromRadians()`
* `fromMN95(float $e, float $n)` Coordonnées suisses.
* `fromExifGPS(array $exif_gps)` Depuis `exif_read_data()['GPS…']`. Arrondi à 7 décimales.
* `fromDMS(array $dms)` Degrés, minutes, secondes (`lat_ref`, `lat_degrees`, …). Renvoie `null` si une clé manque.
* `getLat()`, `getLon()`, `getRadLat()`, `getRadLon()`, `coordinates()` (`[lat, lon]`), `latlon()`, `latlng()`, `geojson()` (`[lon, lat]`)
* `setLat()`, `setLon()`
* `distanceTo(Point $point)` Distance en mètres (haversine).
* `bounds(float $distance)` `[min, max]` du carré englobant, en mètres. Faux près des pôles.
* `insidePoly(array $coords)` Polygone de `[lat, lon]`.
* `insidePath(object $path)` Objet avec `outer` et `inner`, listes de polygones `[lat, lon]`.
* `insideGeoJson(array $geojson)` `Polygon` ou `MultiPolygon` (coordonnées `[lon, lat]`), trous compris.
* `toPoints(string $str)` Lit `"lat,lon lat,lon"`. **Cassée** (voir [`TODO.md`](TODO.md)).
* `insideVertices($x, $y, array $vertices)` Algorithme du lancer de rayon.

`\Pebble\Tools\Points` :

* `add(Point $point) : static`, `all() : array`
* `center()` / `getCenter(array $points)` Centre de la boîte englobante (`(0, 0)` si vide).
* `closer()` / `getCloser(Point $point, array $points)` Point le plus proche (`null` si vide).

## Image

`\Pebble\Tools\Image` étend `Imagick` (nécessite `ext-imagick`).

* `new Image(...$files)` / `Image::create(...$files)`
* `getName()`, `getFileSize()`, `getImageMimeType()` (sans `x-`), `getImageLength()`
* `setBgColor()` / `getBgColor()` Couleur de fond pour la rotation. `setQuality()` / `getQuality()` Qualité JPEG/WebP (75).
* `prepare()` CMYK vers RGB puis suppression des EXIF (le profil ICC est gardé).
* `isInside(int $width, int $height, bool $strict_orientation = false)` / `fitBounds(...)` Teste ou réduit l'image dans une boîte, la boîte étant tournée pour suivre l'orientation de l'image sauf si `$strict_orientation`. Jamais d'agrandissement.
* `toJpeg()`, `toPng()`, `toWebp()` Changent le format (`$force` pour réappliquer les options).
* `crop(array $data)` Données de cropper.js (`x`, `y`, `width`, `height`, `rotate`, `scaleX`, `scaleY`). **Attention** : `scaleX`/`scaleY` sont inversés et le canevas virtuel n'est pas réinitialisé.
* `watermark(Image $img, int $x, int $y)`
* `save(?string $to = null) : static` Écrit l'image, l'extension suit le format. Renvoie un nouvel objet si le chemin change.
* `getHash()` Hash perceptuel (64 caractères hexadécimaux).
* `similarHash(string $hash1, string $hash2, float &$percent = 0) : int` Distance de Hamming. **Fausse** dès qu'un chiffre hexadécimal est inférieur à 8 (voir [`TODO.md`](TODO.md)).

## Shell

* `Shell::exec(string $command)` Lance la commande en arrière-plan et rend la main tout de suite, sortie ignorée. La commande **n'est pas échappée** : utiliser `escapeshellarg()` sur chaque argument.
* `Shell::isWin() : bool`

## Tests

```bash
composer install
vendor/bin/phpunit
```

Les tests `Image` sont sautés sans `ext-imagick`. Les bugs connus sont figés par des tests annotés `// BUG:` qui vérifient le comportement actuel.
