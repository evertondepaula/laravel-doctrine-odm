<?php

namespace Epsoftware\Laravel\Doctrine\Mongo\Helpers;

class Mapper
{
    public static function listAllFolders($dir)
    {
        $ffs = scandir($dir);

        $json = [];
        $newjson = [];
        $json[] = $dir;

        foreach ($ffs as $ff) {
            if ($ff != '.' && $ff != '..') {
                if (is_readable($dir . '/' . $ff)) {
                    if (is_dir($dir . '/' . $ff)) {
                        $json[] = $dir . '/' . $ff;
                        $newjson = self::listAllFolders($dir . '/' . $ff);
                    }
                    $json = array_merge($json, $newjson);
                }
            }
        }

        return array_values(array_unique($json));
    }
}
