<?php


class Coder
{
    public $sourceData;
    public $targetData = [];

    public function __construct(array $sourceData = [])
    {
        $this->sourceData = $sourceData;
    }

    public function getProduct(int $chunk = 2): float
    {
        $data = $this->getTargetNums($this->sourceData, $chunk);
        return array_product($data);
    }

    public function getTargetNums(array $data, int $chunk, int $num = 2020): array
    {
        $this->rollFetch($data, $chunk);

        foreach ($this->targetData as $args) {
            $total = array_sum($args);
            if ($total == $num) {
                return $args;
            }
        }

        return [];
    }

    public function rollFetch(array $data, int $chunk, int $index = 0, $args = [])
    {
        $num = count($args);
        $len = count($data);

        $isTarget = ($num + 1) == $chunk;
        for ($i = $index; $i <= $len - $chunk + $num; $i++) {
            $args[$index] = $data[$i];
            if ($isTarget) {
                $this->targetData[] = $args;
            } else {
                $this->rollFetch($data, $chunk, $i + 1, $args);
            }
        }
    }
}