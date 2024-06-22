<?php

namespace src\core;

use GdImage;

class GridGenerator
{
      private string $fontFile = 'fonts/Roboto-Regular.ttf';
      private int $teamsCount;
      private int $imageWidth = 1920;
      private int $imageHeight = 1080;
      private int $matchWidth = 350;
      private int $matchHeight = 125;
      private int $padding = 50;
      private int $fontSize = 18;
      private GdImage $image;
      private array $gridData;

      public function __construct(int $teamsCount)
      {
            $this->teamsCount = $teamsCount;
            $this->initImage();
            $this->generateGridData();
      }

      private function initImage(): void
      {
            // Создаем главное изображение 
            $this->image = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
            // Задаем цвет фона
            $bgColor = imagecolorallocate($this->image, 255, 255, 255);
            imagefill($this->image, 0, 0, $bgColor);
      }

      private function generateGridData(): void
      {
            $roundsCount = $this->calculateRoundsCount();
            $this->gridData = [];
            $matchNumber = 1; // Счетчик матчей

            for ($round = 0; $round < $roundsCount; $round++) {
                  $matchNumber = $this->generateRoundData($round, $roundsCount, $matchNumber);
            }
      }

      private function generateRoundData(int $round, int $roundsCount, int &$matchNumber): int
      {
            $matchesCount = pow(2, $roundsCount - $round - 1);
            $y = $this->padding + $round * ($this->matchHeight + $this->padding) * 2;

            for ($matchIndex = 0; $matchIndex < $matchesCount; $matchIndex++) {
                  $this->generateMatchData($round, $matchIndex, $y, $matchNumber);
                  $matchNumber++;
            }

            return $matchNumber;
      }

      private function generateMatchData(int $round, int $matchIndex, int $y, int &$matchNumber): int
      {
            // Рассчитываем горизонтальное положение матча
            if ($round === 0) {
                  // В первом раунде матчи располагаются равномерно
                  $x = $this->padding + $matchIndex * ($this->imageWidth - 2 * $this->padding) / ($this->teamsCount / 2);
            } else {
                  // В остальных раундах матчи располагаются между предыдущими
                  $previousRoundMatchIndex = $matchIndex * 2;
                  $previousMatch1X = $this->gridData[$round - 1][$previousRoundMatchIndex]['x'];
                  $previousMatch2X = $this->gridData[$round - 1][$previousRoundMatchIndex + 1]['x'];
                  $x = ($previousMatch1X + $previousMatch2X) / 2;
            }

            // Определяем номера матчей, из которых выходят участники текущего матча
            $previousMatch1 = null;
            $previousMatch2 = null;
            if ($round > 0) {
                  $previousMatch1 = $this->gridData[$round - 1][$matchIndex * 2]['matchNumber'];
                  if ($matchIndex * 2 + 1 < count($this->gridData[$round - 1])) {
                        $previousMatch2 = $this->gridData[$round - 1][$matchIndex * 2 + 1]['matchNumber'];
                  }
            }

            // Добавляем данные о матче в массив
            $this->gridData[$round][] = [
                  'matchNumber' => $matchNumber,
                  'x' => $x,
                  'y' => $y,
                  'previousMatch1' => $previousMatch1,
                  'previousMatch2' => $previousMatch2,
            ];

            return $matchNumber + 1;
      }

      private function getPreviousMatchNumber(int $currentRound, int $previousRoundMatchIndex): ?int
      {
            return isset($this->gridData[$currentRound - 1][$previousRoundMatchIndex])
                  ? $this->gridData[$currentRound - 1][$previousRoundMatchIndex]['matchNumber']
                  : null;
      }

      public function drawGrid(): void
      {
            $black = imagecolorallocate($this->image, 0, 0, 0);

            foreach ($this->gridData as $round => $matches) {
                  $this->drawRound($round, $black);

                  // Добавляем горизонтальные линии после каждого раунда, кроме последнего
                  if ($round < $this->calculateRoundsCount() - 1) {
                        $this->drawHorizontalConnectorLines($round, $black);
                  }
            }
      }

      private function drawHorizontalConnectorLines(int $round, int $black): void
      {
            $nextRound = $this->gridData[$round + 1];

            for ($i = 0; $i < count($nextRound); $i++) {
                  $match1X = (int) ($this->gridData[$round][$i * 2]['x'] + $this->matchWidth / 2);
                  $match2X = (int) ($this->gridData[$round][$i * 2 + 1]['x'] + $this->matchWidth / 2);
                  $lineY = (int) ($this->gridData[$round][$i * 2]['y'] + $this->matchHeight + $this->padding * 2);

                  imageline(
                        $this->image,
                        $match1X,
                        $lineY,
                        $match2X,
                        $lineY,
                        $black
                  );

                  $lineDownX = floor(($match1X + $match2X) / 2);
                  $line2Y = $lineY + $this->matchHeight;

                  imageline(
                        $this->image,
                        $lineDownX,
                        $lineY,
                        $lineDownX,
                        $line2Y,
                        $black
                  );
            }
      }

      private function drawRound(int $round, int $black): void
      {
            foreach ($this->gridData[$round] as $match) {
                  $this->drawMatch($match, $black);
                  if ($round < $this->calculateRoundsCount() - 1) {
                        $this->drawConnectorLine($match, $black);
                  }
            }
      }

      private function drawMatch(array $match, int $black): void
      {
            imagerectangle(
                  $this->image,
                  (int) $match['x'],
                  (int) $match['y'],
                  (int) ($match['x'] + $this->matchWidth),
                  (int) ($match['y'] + $this->matchHeight),
                  $black
            );

            imagettftext(
                  $this->image,
                  $this->fontSize,
                  0,
                  (int) ($match['x'] + $this->padding),
                  (int) ($match['y'] + $this->padding),
                  $black,
                  $this->fontFile,
                  $this->generateMatchText($match)
            );
      }

      private function generateMatchText(array $match): string
      {
            $text = "             " . "Match {$match['matchNumber']}\n\n";
            if ($match['previousMatch1'] !== null) {
                  $text .= "  " . "Winner {$match['previousMatch1']}";
            }
            if ($match['previousMatch2'] !== null) {
                  $text .= " vs Winner {$match['previousMatch2']}";
            }
            return $text;
      }

      private function drawConnectorLine(array $match, int $black): void
      {
            imageline(
                  $this->image,
                  (int) ($match['x'] + $this->matchWidth / 2),
                  (int) ($match['y'] + $this->matchHeight),
                  (int) ($match['x'] + $this->matchWidth / 2),
                  (int) ($match['y'] + $this->matchHeight + $this->padding * 2),
                  $black
            );
      }


      private function calculateRoundsCount(): int
      {
            return ceil(log($this->teamsCount, 2));
      }

      public function saveImage(string $filename): void
      {
            if (!file_exists('output')) {
                  mkdir('output', 0777, true);
            }
            imagepng($this->image, 'output/' . $filename);
      }
}