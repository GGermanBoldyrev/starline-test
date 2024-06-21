<?php

namespace src\cli;

use src\exceptions\CliException;
use src\interfaces\CliInterface;

class Teams implements CliInterface
{
      const UINT32_MAX = 4294967295;
      private array $params;
      private int $teamsCount;

      public function __construct(array $params)
      {
            $this->params = $params;
            $this->checkParams();
            $this->setCount();
      }

      public function getCount(): int
      {
            return $this->teamsCount;
      }

      private function setCount(): void
      {
            if ($this->getParam('teams') < 0 || $this->getParam('teams') > self::UINT32_MAX) {
                  throw new CliException("Количество команд должно быть целым числом от 0 до "
                        . self::UINT32_MAX . "." . PHP_EOL);
            }
            $this->teamsCount = $this->getParam('teams');
      }

      private function checkParams(): void
      {
            $this->ensureParamsExist('teams', 'uint32');
      }

      private function getParam(string $param): int|null
      {
            return $this->params[$param] ?? null;
      }

      private function ensureParamsExist(string $param, string $paramType): void
      {
            if (!isset($this->params[$param])) {
                  throw new CliException("Отсутствует обязательный параметр --$param=($paramType)." . PHP_EOL);
            }
      }
}