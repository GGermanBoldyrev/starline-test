<?php

namespace src\core;

use GdImage;

class GridGenerator
{
      private int $teamsCount;
      private int $imageWidth;
      private int $imageHeight;
      private int $matchWidth;
      private int $matchHeight;
      private int $padding;
      private int $fontSize;
      private GdImage $image;

      public function __construct(int $teamsCount)
      {
            $this->teamsCount = $teamsCount;

            // Создание изображения
            $this->image = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
            // Цвет фона белый
            $bgColor = imagecolorallocate($this->image, 255, 255, 255);
            imagefill($this->image, 0, 0, $bgColor);
      }

      public function resolve()
      {

      }
}