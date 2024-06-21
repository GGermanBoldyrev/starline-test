<?php

namespace src\interfaces;

interface CliInterface
{
      public function __construct(array $params);
      public function getCount(): int;
      private function setCount(): void;
      private function checkParams(): void;
      private function getParam(string $param): int|null;
      private function ensureParamsExist(string $param, string $paramType): void;
}