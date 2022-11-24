<?php

namespace Tpabarbosa\FindBestImageByColor;

class ColorConverter
{
    public static function hexToRgb($hex, $alpha = false)
    {
        $hex      = str_replace('#', '', $hex);
        $length   = strlen($hex);
        $rgb[] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb[] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb[] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
        if ($alpha) {
            $rgb[] = $alpha;
        }
        return $rgb;
    }

    public static function rgbToXyz($rgb)
    {
        $rgb = array_map(function ($c) {
            $c = $c / 255;
            if ($c > 0.04045) {
                $c = pow(($c + 0.055 ) / 1.055, 2.4);
            } else {
                $c = $c / 12.92;
            }
            return $c;
        }, $rgb);

        $XYZ[0] = $rgb[0] * 0.4124 + $rgb[1] * 0.3576 + $rgb[2] * 0.1805;
        $XYZ[1] = $rgb[0] * 0.2126 + $rgb[1] * 0.7152 + $rgb[2] * 0.0722;
        $XYZ[2] = $rgb[0] * 0.0193 + $rgb[1] * 0.1192 + $rgb[2] * 0.9505;

        $XYZ = array_map(function ($c) {
            return $c * 100;
        }, $XYZ);
        return $XYZ;
    }

    public static function rgbToLab($rgb)
    {
        $XYZ = self::rgbToXyz($rgb);
        return self::xyzToLab($XYZ);
    }

    public static function xyzToLab($xyz)
    {

        $D65 = [95.047, 100, 108.883];
        $xyz = array_map(function ($v, $i) use ($D65) {
            $v = $v / $D65[$i];
            return $v > 0.008856 ? pow($v, 1 / 3) : ($v * 7.787) + (16 / 116);
        }, $xyz, array_keys($xyz));

        $l = 116 * $xyz[1] - 16;
        $a = 500 * ($xyz[0] - $xyz[1]);
        $b = 200 * ($xyz[1] - $xyz[2]);

        return [$l, $a, $b];
    }
}
