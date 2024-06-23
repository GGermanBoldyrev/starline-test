<?php

namespace src\cli;

use src\exceptions\CliException;

class Teams
{
      const UINT32_MAX = 4294967295;
      private array $params;
      private int $teamsCount;

    /**
     * @throws CliException
     */
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

    /**
     * @throws CliException
     */
    private function setCount(): void
      {
            if ($this->getParam('teams') < 0 || $this->getParam('teams') > self::UINT32_MAX) {
                  throw new CliException("Количество команд должно быть целым числом от 0 до "
                        . self::UINT32_MAX . "." . PHP_EOL);
            }
            $this->teamsCount = $this->getParam('teams');
      }

    /**
     * @throws CliException
     */
    private function checkParams(): void
      {
            $this->ensureParamsExist('teams', 'uint32');
      }

      private function getParam(string $param): int|null
      {
            return $this->params[$param] ?? null;
      }

    /**
     * @throws CliException
     */
    private function ensureParamsExist(string $param, string $paramType): void
      {
            if (!isset($this->params[$param])) {
                  throw new CliException("Отсутствует обязательный параметр --$param=($paramType)." . PHP_EOL);
            }
      }
}