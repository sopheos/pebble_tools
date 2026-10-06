# CLAUDE.md — pebble_tools

Ce fichier guide Claude Code quand il **maintient** cette librairie. Pour l'**utiliser** depuis un projet, voir le skill [`skills/pebble-tools/`](skills/pebble-tools/SKILL.md).

## Rôle

`sopheos/pebble_tools`, namespace `Pebble\Tools\`, PHP >= 8.1, aucune dépendance runtime (`ext-imagick` seulement pour `Image`). La lib est une boîte à outils de classes indépendantes, presque toutes statiques :
- chaînes (`T`), tableaux (`A`), drapeaux binaires (`Bitwise`) ;
- dates en français et dates des saisons (`Date`) ;
- conversions d'adresses IP texte / hexadécimal / binaire (`IP`) ;
- points GPS, distances, polygones (`Point`, `Points`) ;
- traitement d'images au-dessus d'Imagick (`Image`) ;
- lancement d'une commande en arrière-plan (`Shell`).

## Commandes

```bash
composer install
vendor/bin/phpunit            # toute la suite
vendor/bin/phpunit --filter PointTest
```

## Carte de `src/`

| Fichier | Rôle |
|---|---|
| `A.php` | Tableaux : `parse()` (JSON/objet vers tableau), `equal()`, `rand()`, `unset()`, `unique()` |
| `Bitwise.php` | `has()`, `add()`, `del()` sur un entier de drapeaux |
| `Date.php` | Objet date immuable autour d'un timestamp, propriétés magiques (`year`, `mon`…), `fr()`/`toFr()`, conversions SQL/ISO, `seasonDate()` |
| `IP.php` | Objet IP construit depuis texte, hexadécimal ou binaire, IPv4 et IPv6 |
| `Image.php` | `extends Imagick` : `prepare()`, `fitBounds()`, `crop()` (données cropper.js), `toJpeg()`/`toPng()`/`toWebp()`, `save()`, hash perceptuel |
| `Point.php` | Point lat/lon : fabriques (degrés, radians, MN95 suisse, EXIF, DMS), `distanceTo()`, `bounds()`, tests d'inclusion (polygone, GeoJSON) |
| `Points.php` | Collection de `Point` : centre de la boîte englobante, point le plus proche |
| `Shell.php` | `exec()` en arrière-plan (`nohup … &` ou `start /B`), `isWin()` |
| `T.php` | Chaînes : accents, `alias()` (slug), casse, `xss()`/`htmlEncode()`, `number()`/`money()` au format français |

## Tests

- PHPUnit 9.5. Une classe de test par classe de `src/` (`Points` est testé dans `PointTest`).
- Les classes de test n'ont pas de namespace. Les méthodes s'appellent `testPhraseEnCamelCase`, les assertions passent par `self::assertSame`, et des bannières `// ----` séparent les sections.
- `ImageTest` est sauté (`markTestSkipped`) sans `ext-imagick`. Il écrit ses fichiers dans un dossier temporaire supprimé au `tearDown()`.
- `ShellTest` ne lance que `sleep`, sans effet de bord. Ne jamais y ajouter une commande qui modifie le système.
- `tests/bootstrap.php` fixe le fuseau `Europe/Paris` : les tests de `Date` en dépendent.

## Conventions du code

Respecter le style existant, sans le « moderniser » au passage :
- pas de `declare(strict_types=1)` (plusieurs comportements reposent sur la coercition, par exemple `false` vers `0` dans `Date`) ;
- constantes de classe sans visibilité ;
- `if` d'une ligne sans accolades tolérés ;
- docblocks `@return static`, commentaires parfois en français ;
- méthodes statiques pour les helpers, pas d'état global sauf le cache `Shell::$isWin`.

Une modification de comportement doit être répercutée dans `skills/pebble-tools/` (SKILL.md, `references/api-reference.md`, `references/gotchas.md`) et dans le `README.md`.

## Bugs connus

Ils sont listés dans [`TODO.md`](TODO.md). Chacun est **figé par un test** annoté `// BUG:` qui vérifie le comportement *actuel*, dans la section « Known bugs » du fichier `tests/XTest.php` de la classe concernée.

Pour corriger un bug :
1. Corriger `src/`.
2. Réécrire le test `// BUG:` pour qu'il vérifie le comportement attendu.
3. Mettre à jour l'entrée « (bug) » de `skills/pebble-tools/references/gotchas.md` et le SKILL.md.
4. Retirer l'entrée de `TODO.md` (il ne liste que ce qui reste à faire).
