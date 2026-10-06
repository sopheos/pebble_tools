# TODO — pebble_tools

Problèmes restant à traiter, détectés lors de l'audit du 2026-10-06. Le code `src/` n'a **pas** été modifié. Chaque bug est figé par un test qui vérifie le comportement actuel : il faut l'adapter au moment de la correction.

## Bugs

- [ ] **`T::split()` lève toujours une `TypeError`.** `src/T.php:472`.
  - La méthode est déclarée `: string` mais renvoie le tableau de `preg_split()`. Elle est inutilisable.
  - Correctif : déclarer `: array`.
  - Test : `tests/TTest.php::testSplitAlwaysThrowsATypeError`.
- [ ] **`A::parse()` passe `JSON_THROW_ON_ERROR` comme profondeur.** `src/A.php:13`.
  - Le flag (4194304) devient `$depth` : le `catch (JsonException)` est mort (un JSON invalide renvoie `null`, puis `[]` par chance), et un JSON de plus de 512 niveaux est décodé puis fait échouer `json_encode()` ligne 20, d'où `null` renvoyé par une méthode `: array`, donc une `TypeError`.
  - Correctif : `json_decode($value, true, 512, JSON_THROW_ON_ERROR)`.
  - Test : `tests/ATest.php::testParseDeepJsonRaisesATypeError`.
- [ ] **`A::unique()` ignore la colonne `0`.** `src/A.php:51`.
  - `if ($column_key)` est faux pour `0` : les lignes elles-mêmes passent dans `array_unique()`, avec des warnings « Array to string conversion » et un résultat faux.
  - Correctif : `if ($column_key !== null)`.
  - Test : `tests/ATest.php::testUniqueIgnoresColumnZero`.
- [ ] **`A::equal()` ne compare que dans un sens.** `src/A.php:28`.
  - Avec le même nombre d'éléments, seul `array_diff($a, $b)` est vérifié : `equal([1, 1, 2], [1, 2, 3])` vaut `true`, alors que `equal([1, 2, 3], [1, 1, 2])` vaut `false`. Des sous-tableaux produisent des warnings.
  - Correctif : tester aussi `array_diff($b, $a)`, ou comparer les tableaux triés si les doublons comptent.
  - Test : `tests/ATest.php::testEqualIsAOneWayDiff`.
- [ ] **`Bitwise::add()` et `del()` prennent l'entier par référence sans le modifier.** `src/Bitwise.php:12, 20`.
  - `int &$value` laisse croire à une modification en place, alors que seule la valeur de retour change. Le passage par référence interdit aussi les littéraux et les expressions (`Bitwise::add(1, 2)` lève une `Error`).
  - Correctif : retirer le `&` (la valeur de retour est déjà correcte).
  - Test : `tests/BitwiseTest.php::testAddTakesAReferenceButNeverModifiesIt`, `tests/BitwiseTest.php::testAddRejectsALiteral`.
- [ ] **`Date` vaut silencieusement le 1er janvier 1970 pour une chaîne non reconnue.** `src/Date.php:96`.
  - `strtotime()` renvoie `false`, converti en `0` par la propriété `int` (mode coercitif, pas de `TypeError` contrairement à ce que laissait penser l'audit). Une chaîne numérique (`'1700000000'`) donne aussi `0`. Même chose pour `fromYmd()` avec une date invalide.
  - Correctif : accepter les chaînes numériques comme timestamp et lever une exception si `strtotime()` échoue.
  - Test : `tests/DateTest.php::testUnparsableStringSilentlyGivesTheEpoch`.
- [ ] **`Date::seasonDate()` donne une heure fausse de 20 minutes à plus de 2 heures.** `src/Date.php:252, 264, 288, 290`.
  - Quatre erreurs cumulées. Ligne 252 : `sin($m * 2)` et `sin($m * 3)` sans `* $rad`. Ligne 264 : la boucle s'arrête dès que la correction est négative (pas d'`abs()`). Ligne 288 : les secondes valent `floor($frac * 24 - $hour - $minute)` (souvent négatif) au lieu de `floor(((($frac * 24 - $hour) * 60) - $minute) * 60)`. Ligne 290 : `mktime()` lit une heure UT comme une heure locale. Exemples : équinoxe de printemps 2024 donnée à 03:45 (Paris) au lieu de 04:06, solstice d'hiver 2024 à 08:57 au lieu de 10:20. Le jour reste juste sur les années testées.
  - Correctif : corriger les trois formules et utiliser `gmmktime()`.
  - Test : `tests/DateTest.php::testSeasonDateTimeIsOff`.
- [ ] **`IP::toHex()` / `fromHex()` perdent de la précision en IPv6.** `src/IP.php:239, 294`.
  - `base_convert()` passe par un float au-delà de 2^53 : chaque moitié de 64 bits est arrondie (`…:2345:6789` devient `…:2345:6800`). Si l'arrondi atteint 2^64, le hex fait 17 chiffres et `str_repeat()` lève une `ValueError` dès la construction (`ffff:…:ffff`).
  - Correctif : convertir par blocs de 16 bits (`bindec`/`dechex` ou `bin2hex(inet_pton())`).
  - Test : `tests/IPTest.php::testIPv6HexLosesPrecision`, `tests/IPTest.php::testIPv6HexOverflowThrows`.
- [ ] **`Point::toPoints()` lit des caractères au lieu des colonnes.** `src/Point.php:314`.
  - `new static((float) $row[0], (float) $row[1])` prend les deux premiers caractères de la ligne : `'48.85,2.35'` donne le point (4, 8).
  - Correctif : utiliser `$cols[0]` et `$cols[1]`.
  - Test : `tests/PointTest.php::testToPointsReadsCharactersInsteadOfColumns`.
- [ ] **`Point::bounds()` utilise `max()` pour la latitude haute près d'un pôle.** `src/Point.php:266`.
  - Près du pôle nord la latitude max dépasse 90°, près du pôle sud elle vaut toujours 90°.
  - Correctif : `$max_lat = min($max_lat, $MAXLAT);`.
  - Test : `tests/PointTest.php::testBoundsNearAPoleUsesMaxForTheUpperLatitude`.
- [ ] **`Point::fromDMS()` renvoie `null` si une clé manque.** `src/Point.php:136`.
  - Le docblock annonce `@return static`. L'appelant obtient une erreur plus loin (`->lat` sur `null`). `fromExifGPS()` hérite du problème.
  - Correctif : lever une `InvalidArgumentException`, ou typer `?static` et le documenter.
  - Test : `tests/PointTest.php::testFromDMSReturnsNullOnMissingKeys`.
- [ ] **`Image::similarHash()` complète les bits du mauvais côté.** `src/Image.php:494`.
  - `str_pad(..., 4, STR_PAD_LEFT)` passe la constante (`0`) comme chaîne de remplissage et garde le remplissage à droite : `'1'` devient `1000`, comme `'8'`. La distance entre deux hash est donc fausse (`similarHash('1', '8')` vaut 0). `getHash()` lui-même est correct.
  - Correctif : `str_pad(decbin(hexdec($hash[$i])), 4, '0', STR_PAD_LEFT)`.
  - Test : `tests/ImageTest.php::testSimilarHashPadsBitsOnTheWrongSide`.
- [ ] **`Image::crop()` inverse `scaleX` et `scaleY`.** `src/Image.php:283-290`.
  - `scaleX = -1` (miroir horizontal dans cropper.js) appelle `flipImage()`, qui retourne verticalement, et `scaleY = -1` appelle `flopImage()`.
  - Correctif : échanger `flipimage()` et `flopimage()`.
  - Test : `tests/ImageTest.php::testCropScaleXFlipsVertically`.
- [ ] **`Image::crop()` passe des floats à `cropImage()`.** `src/Image.php:274-277, 299`.
  - Les coordonnées de cropper.js sont décimales : elles sont tronquées avec une dépréciation « Implicit conversion from float … to int loses precision » par valeur.
  - Correctif : `(int) round(...)` sur les quatre valeurs.
  - Test : `tests/ImageTest.php::testCropPassesFloatsToCropImage`.

## Sécurité

- [ ] `src/Shell.php:12-16` : la commande est concaténée telle quelle dans `passthru()`/`popen()`. C'est voulu, mais l'appelant doit échapper chaque argument avec `escapeshellarg()`. À documenter dans le docblock, ou proposer une variante qui prend un tableau d'arguments.

## Dette / qualité

- [ ] `composer.lock` local (non versionné) était périmé : il contenait `itamair/geophp` alors que `composer.json` ne le requiert pas. Il a été régénéré. Vérifier qu'aucun projet ne comptait sur geoPHP via cette lib.
- [ ] `ext-imagick` est déclaré en `suggest` (seule `Image` en dépend). Le passer en `require` si `Image` doit être garanti partout.
- [ ] `src/Point.php:142-143` : `number_format()` renvoie une chaîne avec séparateur de milliers. Sans effet pour des coordonnées valides, mais `fromDMS()` produit un warning et une valeur fausse au-delà de 999°. Préférer `round($lat, 7)`.
- [ ] `src/Points.php:60, 86` : `getCloser()` renvoie `null` (docblock `@return Point`) et `getCenter([])` renvoie le point (0, 0). Le centre est celui de la boîte englobante, faux autour de l'antiméridien.
- [ ] `src/Image.php:298-300` : pas de `setImagePage(0, 0, 0, 0)` après `cropImage()`. Le canevas virtuel garde l'offset, ce qui décale les PNG/GIF enregistrés.
- [ ] `src/Bitwise.php:7` : `has()` est vrai si **un** des bits de la clé est présent. Documenter que la clé doit être un seul bit, ou comparer `($value & $key) === $key`.
- [ ] `src/IP.php` : une entrée invalide donne silencieusement `0.0.0.0` / `'0'`, et l'hexadécimal/binaire IPv4 n'est pas complété à 8/32 caractères.
- [ ] `src/IP.php:122, 135, 148` : `isIPv6Bin()`, `isIPv6Hex()` et `isIPv6Str()` sont appelées avec un second argument `false` qu'elles n'attendent pas.
- [ ] `src/Point.php:138` : `extract($dms)` crée des variables depuis un tableau d'entrée.
- [ ] `src/T.php:427` : `remove_emoji()` est en snake_case, sans type, et ne couvre qu'une partie des plages emoji (pas les drapeaux, ni U+1F900+).
- [ ] `src/Image.php` : docblocks `@return \static`, propriétés `$bgColor`/`$quality` non typées, `$str` inutilisé dans `bits2hash()`.
- [ ] `src/Date.php` : `isLeapYear()` et `daysInMonth()` ne sont pas typées, `daysInMonth()` compare `$m === 2` en strict (une chaîne `'2'` donne 30).
