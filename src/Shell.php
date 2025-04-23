<?php

namespace Pebble\Tools;

class Shell
{
    private static ?bool $isWin = null;

    public static function exec(string $command)
    {
        if (self::isWin()) {
            pclose(popen("start /B " . $command, "r"));
        } elseif (file_exists("/usr/bin/nohup")) {
            passthru("/usr/bin/nohup {$command} > /dev/null 2>&1 &");
        } else {
            passthru("{$command} > /dev/null 2>&1 &");
        }
    }

    public static function isWin(): bool
    {
        if (self::$isWin === null) {
            self::$isWin = str_contains(php_uname(), 'Windows');
        }

        return self::$isWin;
    }
}
