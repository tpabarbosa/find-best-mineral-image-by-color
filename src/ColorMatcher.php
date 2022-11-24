<?php

namespace Tpabarbosa\FindBestImageByColor;

class ColorMatcher
{
    private $userColorHex = '#000000';
    private $userRgbColor = [0, 0, 0];
    private $userLabColor = array();
    private $bestRgb = array();
    private $bestLab = array();

    public function __construct($images, $userColorHex)
    {
        $this->images = $images;
        $this->userColorHex = $userColorHex;
        $this->userRgbColor = ColorConverter::hexToRgb($this->userColorHex);
        $this->userLabColor = ColorConverter::rgbToLab($this->userRgbColor);
    }

    public function search($n)
    {
        foreach ($this->images as $image => $colors) {
            $bestRgb[$image] = $this->getBestRgb($image, $colors);
            $bestLab[$image] = $this->getBestLab($image, $colors);
        }

        usort($bestLab, fn($a, $b) => $a['distance'] <=> $b['distance']);
        usort($bestRgb, fn($a, $b) => $a['distance'] <=> $b['distance']);
        $this->bestRgb = $bestRgb;
        $this->bestLab = $bestLab;

        $result = array();
        for ($i=0; $i < $n; $i++) {
            $result[] = ['lab' => $this->bestLab[$i], 'rgb' => $this->bestRgb[$i]];
        }

        return $result;
    }

    private function getBestRgb($image, $colors)
    {
        $bestRgb = array();
        foreach ($colors as $rgbColor) {
            $rgbDistance = $this->getWeightedRgbDistance($rgbColor, $this->userRgbColor);
            if (!$bestRgb || $rgbDistance < $bestRgb['distance']) {
                $bestRgb = $this->formatBest($image, $rgbColor, $rgbDistance);
            }
        }
        return $bestRgb;
    }

    private function getBestLab($image, $colors)
    {
        $bestLab = array();
        foreach ($colors as $rgbColor) {
            $labColor = ColorConverter::rgbToLab($rgbColor);
            $labDistance = $this->getLabDistance($labColor, $this->userLabColor);
            if (!$bestLab || $labDistance < $bestLab['distance']) {
                $bestLab = $this->formatBest($image, $rgbColor, $labDistance);
            }
        }
        return $bestLab;
    }

    private function formatBest($image, $color, $distance)
    {
        return [
            'image' => $image,
            'color' => $color,
            'distance' => number_format((float)$distance, 2, '.', ''),
        ];
    }

    private function getWeightedRgbDistance($a, $b)
    {
        $medR = ($a[0] + $b[0]) / 2;
        $diff = sqrt((2+($medR/256))*(($a[0] - $b[0])**2) + 4*(($a[1] - $b[1])**2) + (2+((255-$medR)/256))*(($a[2] - $b[2])**2));
        return $diff;
    }

    private function getLabDistance($a, $b)
    {
        $deltaE = sqrt(($a[0] - $b[0])**2 + ($a[1] - $b[1])**2 + ($a[2] - $b[2])**2);
        return $deltaE;
    }
}
