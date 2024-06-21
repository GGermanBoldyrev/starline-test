<?php

namespace src\cli;

use src\exceptions\CliException;

class Teams
{
      private array $params;

      public function __construct(array $params)
      {
            $this->params = $params;
            $this->checkParams();
      }

      public function execute(): void
      {
            echo $this->getParam('teams');
      }

      private function checkParams(): void
      {
            $this->ensureParamsExist('teams');
      }

      private function getParam(string $param): string|null
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