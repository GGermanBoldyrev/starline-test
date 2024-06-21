<?php

namespace src\cli;

use src\exceptions\CliException;

class Teams
{
      const UINT32_MAX = 4294967295;
      private array $params;
      private int $teamsCount;

      public function __construct(array $params)
      {
            $this->params = $params;
            $this->checkParams();
      }

      public function execute(): int
      {
            if ($this->getParam('teams') < 0 || $this->getParam('teams') > self::UINT32_MAX) {
                  throw new CliException("Количество команд должно быть целым числом от 0 до " . self::UINT32_MAX . ".");
            }
            return $this->getParam('teams');
      }

      private function checkParams(): void
      {
            $this->ensureParamsExist('teams');
      }

      private function getParam(string $param): int|null
      {
            return $this->params[$param] ?? null;
      }

      private function ensureParamsExist(string $param): void
      {
            if (!isset($this->params[$param])) {
                  throw new CliException("Обязательный параметр --<$param> отсутствует.");
            }
      }
}