<?php

namespace Tpabarbosa\FindBestImageByColor;

class ImagesColorData
{
    private $rawData = [];
    private $data = [];
    private $imagesPath = null;
    private $dataFile = null;
    private const STEPS = 22;
    private const PERCENTAGE = 10;
    public const EXTRACT = 3;

    public function __construct($imagesPath, $dataFile)
    {
        $this->imagesPath = $imagesPath;
        $this->dataFile = $dataFile;
        $path = $imagesPath . '/*.{jpg,png,gif}';
        $images = glob($path, GLOB_BRACE);

        if (!is_file($dataFile)) {
            $this->create($images);
        } else {
            $this->data = $this->readFromFile();
            if (count($this->data) !== count($images)) {
                $this->create($images);
            }
        }
    }

    private function create($images)
    {
        $this->rawData = $this->getImagesData($images);

        $this->saveData($this->rawData);
    }

    private function getImagesData($images)
    {
        $data = [];

        foreach ($images as $image) {
            $imageBasename = basename($image);
            $extractor = new ColorExtractor($image);
            $extractor->setSteps(self::STEPS);
            $extractor->setPercent(self::PERCENTAGE);
            $matrix = $extractor->extract(self::EXTRACT);

            $data[$imageBasename] = array();

            foreach ($matrix as $color) {
                $data[$imageBasename] = $color;
            }
        }
        return $data;
    }

    private function saveData($data)
    {
        file_put_contents($this->dataFile, json_encode($data));
    }

    private function readFromFile()
    {
        return json_decode(file_get_contents($this->dataFile), true);
    }

    public function getData()
    {
        return $this->data;
    }
}
