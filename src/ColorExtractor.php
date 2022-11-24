<?php
namespace Tpabarbosa\FindBestImageByColor;

class ColorExtractor
{
    public $img;

    protected $percent = 5;
    protected $steps = 10;

    public $w;
    public $h;
    public $sample_w = 0;
    public $sample_h = 0;

    public function __construct($imagefile)
    {
        $type = mime_content_type($imagefile);
        if ($type === 'image/jpeg') {
            $image = imagecreatefromjpeg($imagefile);
        } elseif ($type === 'image/png') {
            $image = imagecreatefrompng($imagefile);
        } elseif ($type === 'image/gif') {
            $image = imagecreatefromgif($imagefile);
        } else {
            die("Invalid image type: {$type}");
        }

        if (!$this->img = $image) {
            die("Error loading image: {$imagefile}");
        }
        $this->w = imagesx($this->img);
        $this->h = imagesy($this->img);
    }

    public function setPercent($percent)
    {
        $percent = intval($percent);
        if (($percent < 1) || ($percent > 50)) {
            die("Your \$percent value needs to be between 1 and 50.");
        }
        $this->percent = $percent;
    }

    public function setSteps($steps)
    {
        $steps = intval($steps);
        if (($steps < 1) || ($steps > 50)) {
            die("Your \$steps value needs to be between 1 and 50.");
        }
        $this->steps = $steps;
    }

    private function getPixelColor($x, $y)
    {
        $rgb = imagecolorat($this->img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        return [$r, $g, $b];
    }

    public function extract($n)
    {
        if (($this->steps - $n) % 2 !== 0) {
            $this->steps += 1;
        }

        $this->sample_w = $this->w / $this->steps;
        $this->sample_h = $this->h / $this->steps;

        if (($this->sample_w < 2) || ($this->sample_h < 2)) {
            die("Your sampling size is too small for this image - reduce the \$steps value.");
        }

        $sample_size = round($this->sample_w * $this->sample_h * $this->percent / 100);

        $init = ($this->steps - $n)/2;


        for ($i=0, $y=$init * $this->sample_h; $i < $n; $i++, $y += $this->sample_h) {
            $retval = [];
            for ($j=0, $x=$init * $this->sample_w; $j < $n; $j++, $x += $this->sample_w) {
                $total_r = $total_g = $total_b = 0;
                for ($k=0; $k < $sample_size; $k++) {
                    $pixel_x = $x + rand(0, $this->sample_w-1);
                    $pixel_y = $y + rand(0, $this->sample_h-1);
                    list($r, $g, $b) = $this->getPixelColor($pixel_x, $pixel_y);
                    $total_r += $r;
                    $total_g += $g;
                    $total_b += $b;
                }
                $avg_r = round($total_r/$sample_size);
                $avg_g = round($total_g/$sample_size);
                $avg_b = round($total_b/$sample_size);

                $row_retval[] = [$avg_r, $avg_g, $avg_b];
            }
            $retval[] = $row_retval;
        }
        // for ($i=0, $y=0; $i < $this->steps; $i++, $y += $this->sample_h) {
        //     $row_retval = [];
        //     for ($j=0, $x=0; $j < $this->steps; $j++, $x += $this->sample_w) {
        //         $total_r = $total_g = $total_b = 0;
        //         for ($k=0; $k < $sample_size; $k++) {
        //             $pixel_x = $x + rand(0, $this->sample_w-1);
        //             $pixel_y = $y + rand(0, $this->sample_h-1);
        //             list($r, $g, $b) = $this->getPixelColor($pixel_x, $pixel_y);
        //             $total_r += $r;
        //             $total_g += $g;
        //             $total_b += $b;
        //         }
        //         $avg_r = round($total_r/$sample_size);
        //         $avg_g = round($total_g/$sample_size);
        //         $avg_b = round($total_b/$sample_size);

        //         $row_retval[] = [$avg_r, $avg_g, $avg_b];
        //     }
        //     $retval[] = $row_retval;
        // }

        return $retval;
    }
}
