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
    // Цвета
    private int $blackColor;
    private int $matchBackgroundColor;
    private int $whiteColor;

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

        // Задаем базовые цвета
        $this->blackColor = imagecolorallocate($this->image, 0, 0, 0);
        $this->whiteColor = imagecolorallocate($this->image, 255, 255, 255);
        $this->matchBackgroundColor = imagecolorallocate($this->image, 120, 255, 120);

        // Задаем цвет фона основному изображению
        imagefill($this->image, 0, 0, $this->whiteColor);
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

    private function generateMatchData(int $round, int $matchIndex, int $y, int $matchNumber): void
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

    }

    public function drawGrid(): void
    {
        foreach ($this->gridData as $round => $matches) {
            $this->drawRound($round);

            // Добавляем горизонтальные линии после каждого раунда, кроме последнего
            if ($round < $this->calculateRoundsCount() - 1) {
                $this->drawConnectorLines($round);
            }
        }
    }

    private function drawConnectorLines(int $round): void
    {
        $nextRound = $this->gridData[$round + 1];

        for ($i = 0; $i < count($nextRound); $i++) {
            $match1X = (int)($this->gridData[$round][$i * 2]['x'] + $this->matchWidth / 2);
            $match2X = (int)($this->gridData[$round][$i * 2 + 1]['x'] + $this->matchWidth / 2);
            $lineY = (int)($this->gridData[$round][$i * 2]['y'] + $this->matchHeight + $this->padding * 2);

            imageline(
                $this->image,
                $match1X,
                $lineY,
                $match2X,
                $lineY,
                $this->blackColor
            );

            // Линия вниз до матча от горизонтальной
            $lineDownX = floor(($match1X + $match2X) / 2);
            $line2Y = $lineY + $this->matchHeight;

            imageline(
                $this->image,
                $lineDownX,
                $lineY,
                $lineDownX,
                $line2Y,
                $this->blackColor
            );

            // Рисуем треугольник в конце линии, чтобы обозначить направление
            $triangleHeight = 12;
            $triangleBase = 12;

            // Координаты треугольника
            $trianglePoints = [
                // Вершина треугольника
                $lineDownX, $line2Y,
                // Левая точка основания
                $lineDownX - $triangleBase / 2, $line2Y - $triangleHeight,
                // Правая точка основания
                $lineDownX + $triangleBase / 2, $line2Y - $triangleHeight
            ];

            imagefilledpolygon($this->image, $trianglePoints, $this->blackColor);
        }
    }

    private function drawRound(int $round): void
    {
        foreach ($this->gridData[$round] as $match) {
            $this->drawMatch($match);
            if ($round < $this->calculateRoundsCount() - 1) {
                $this->drawConnectorLine($match);
            }
        }
    }

    private function drawMatch(array $match): void
    {
        // Закрашиваем фон прямоугольника
        imagefilledrectangle(
            $this->image,
            (int)$match['x'],
            (int)$match['y'],
            (int)($match['x'] + $this->matchWidth),
            (int)($match['y'] + $this->matchHeight),
            $this->matchBackgroundColor
        );

        // Граница прямоугольника
        imagerectangle(
            $this->image,
            (int)$match['x'],
            (int)$match['y'],
            (int)($match['x'] + $this->matchWidth),
            (int)($match['y'] + $this->matchHeight),
            $this->blackColor
        );

        //
        imagettftext(
            $this->image,
            $this->fontSize,
            0,
            (int)($match['x'] + $this->padding),
            (int)($match['y'] + $this->padding),
            $this->blackColor,
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

    private function drawConnectorLine(array $match): void
    {
        imageline(
            $this->image,
            (int)($match['x'] + $this->matchWidth / 2),
            (int)($match['y'] + $this->matchHeight),
            (int)($match['x'] + $this->matchWidth / 2),
            (int)($match['y'] + $this->matchHeight + $this->padding * 2),
            $this->blackColor
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
        // Вывод картинки
        imagepng($this->image, 'output/' . $filename);
    }
}