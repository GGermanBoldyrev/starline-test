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

            for ($round = 0; $round < $roundsCount; $round++) {
                  $this->generateRoundData($round, $roundsCount);
            }
      }

      private function generateRoundData(int $round, int $roundsCount): void
      {
            $matchesCount = pow(2, $roundsCount - $round - 1);
            $y = $this->padding + $round * ($this->matchHeight + $this->padding) * 2;

            for ($matchIndex = 0; $matchIndex < $matchesCount; $matchIndex++) {
                  $this->generateMatchData($round, $matchIndex, $y);
            }
      }

      private function generateMatchData(int $round, int $matchIndex, int $y): void
      {
            $x = $this->padding + $matchIndex * ($this->imageWidth - 2 * $this->padding) / pow(2, $this->calculateRoundsCount() - $round - 1);
            $previousRoundMatchIndex = $matchIndex * 2;

            $previousMatch1 = $this->getPreviousMatchNumber($round, $previousRoundMatchIndex);
            $previousMatch2 = $this->getPreviousMatchNumber($round, $previousRoundMatchIndex + 1);

            $this->gridData[$round][] = [
                  'matchNumber' => $matchIndex + 1 + array_sum(array_slice(range(1, $this->calculateRoundsCount() - 1), 0, $round)),
                  'x' => $x,
                  'y' => $y,
                  'previousMatch1' => $previousMatch1,
                  'previousMatch2' => $previousMatch2,
            ];
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

            imagestring(
                  $this->image,
                  $this->fontSize,
                  (int) ($match['x'] + $this->padding / 4),
                  (int) ($match['y'] + $this->padding / 4),
                  $this->generateMatchText($match),
                  $black
            );
      }

      private function generateMatchText(array $match): string
      {
            $text = "Match {$match['matchNumber']}\n";
            if ($match['previousMatch1'] !== null) {
                  $text .= "Winner {$match['previousMatch1']}";
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
                  (int) ($match['y'] + $this->matchHeight + $this->padding),
                  $black
            );
      }

      private function calculateRoundsCount(): int
      {
            return ceil(log($this->teamsCount, 2));
      }

      public function saveImage(string $filename): void
      {
            imagepng($this->image, $filename);
      }
}