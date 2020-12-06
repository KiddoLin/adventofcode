<?php


class Coder
{
    protected $sourceData;
    protected $targetData = [];

    public function __construct(array $sourceData = [])
    {
        $this->sourceData = $sourceData;
    }

    public function getProduct(int $chunk = 3): float
    {
        $data = $this->getTargetNums($this->sourceData, $chunk);
        return array_product($data);
    }

    protected function getTargetNums(array $data, int $chunk, int $num = 2020): array
    {
        $this->targetData = [];

        $this->rollFetch($data, $chunk);

        foreach ($this->targetData as $args) {
            $total = array_sum($args);
            if ($total == $num) {
                return $args;
            }
        }

        return [];
    }

    protected function rollFetch(array $data, int $chunk, int $index = 0, $args = [])
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

    protected function toPasswordData()
    {
        $this->targetData = [];

        foreach ($this->sourceData as $str) {
            [$min, $max, $keyword, $temp, $code] = preg_split("/[:\s\-]/", $str);
            $this->targetData[] = [
                'min' => $min,
                'max' => $max,
                'keyword' => $keyword,
                'code' => $code,
            ];
        }
    }

    public function getTotalPassword($part = 2)
    {
        $this->toPasswordData();
        $list = $this->getPasswords($part);
        return count($list);
    }

    protected function getPasswords(int $part)
    {
        $list = [];

        foreach ($this->targetData as $rule) {
            if ($part == 1) {
                $isPassword = $this->checkKeyword1($rule['code'], $rule['keyword'], $rule['max'], $rule['min']);
            } elseif ($part == 2) {
                $isPassword = $this->checkKeyword2($rule['code'], $rule['keyword'], $rule['max'], $rule['min']);
            } else {
                $isPassword = false;
            }
            if ($isPassword) {
                $list[] = $rule['code'];
            }
        }

        return $list;
    }

    protected function checkKeyword1(string $code, string $keyword, int $max, int $min = 0)
    {
        $total = substr_count($code, $keyword);
        return $min <= $total && $total <= $max;
    }

    protected function checkKeyword2(string $code, string $keyword, int $max, int $min = 0)
    {
        $min--;
        $max--;
        return $code[$min] == $keyword xor $code[$max] == $keyword;
    }

    public function getTotalTrees($way = 2)
    {
        $data = $this->getRunningWayStep($way);
        $trees = array_filter($data, function ($v) {
            return $v == '#';
        });
        $num = count($trees);
        return $num;
    }

    public function getTotalTreeProduct()
    {
        $total = 1;
        for ($way = 1; $way <= 5; $way++) {
            $num = $this->getTotalTrees($way);
            $total = $total * $num;
        }
        return $total;
    }

    protected function getRunningWayStep(int $way)
    {
        $this->targetData = $this->sourceData;

        $data = [];
        $num = 0;

        $ig = false;

        foreach ($this->targetData as $v) {
            if ($ig) {
                $ig = false;
            } else {
                $len = strlen($v);
                $i = $num % $len;
                $data[] = $v[$i];

                if ($way == 1) {
                    $num++;
                } elseif ($way == 2) {
                    $num += 3;
                } elseif ($way == 3) {
                    $num += 5;
                } elseif ($way == 4) {
                    $num += 7;
                } elseif ($way == 5) {
                    $num++;
                    $ig = true;
                }
            }
        }
        return $data;
    }

    public function getDay4($part = 2)
    {
        $this->initToPassportData();
        $validPassports = $this->getValidPassports($part == 2);
        return count($validPassports);
    }

    protected function initToPassportData()
    {
        $this->targetData = [];

        $i = 0;
        foreach ($this->sourceData as $str) {
            if (empty($str)) {
                $i++;
            } else {
                $items = explode(' ', $str);
                array_walk($items, function ($item) use ($i) {
                    [$key, $val] = explode(':', $item);
                    $this->targetData[$i][$key] = $val;
                });
            }
        }
    }

    protected function getValidPassports(bool $isHard = true)
    {
        $must = ['byr', 'iyr', 'eyr', 'hgt', 'hcl', 'ecl', 'pid'];

        $validPassports = [];
        foreach ($this->targetData as $passport) {
            $keys = array_keys($passport);
            $intersectKeys = array_intersect($must, $keys);
            if (count($intersectKeys) == 7) {
                if (!$isHard || $this->checkPassport($passport)) {
                    $validPassports[] = $passport;
                }
            }
        }

        return $validPassports;
    }

    protected function checkPassport(array $passport)
    {
        $byr = $passport['byr'] ?? null;
        $iyr = $passport['iyr'] ?? null;
        $eyr = $passport['eyr'] ?? null;
        $hgt = $passport['hgt'] ?? null;
        $hcl = $passport['hcl'] ?? null;
        $ecl = $passport['ecl'] ?? null;
        $pid = $passport['pid'] ?? null;
        if ($byr < 1920 || $byr > 2002) return false;
        if ($iyr < 2010 || $iyr > 2020) return false;
        if ($eyr < 2020 || $eyr > 2030) return false;
        $intHgt = intval($hgt);
        if (strripos($hgt, 'cm') !== false) {
            if ($intHgt < 150 || $intHgt > 193) return false;
        } elseif (strripos($hgt, 'in') !== false) {
            if ($intHgt < 59 || $intHgt > 76) return false;
        } else {
            return false;
        }
        if (preg_match('/^#[0-9a-f]{6}$/i', $hcl) < 1) {
            return false;
        }
        if (!in_array($ecl, ['amb', 'blu', 'brn', 'gry', 'grn', 'hzl', 'oth'])) {
            return false;
        }
        if (preg_match('/^[0-9]{9}$/', $pid) < 1) {
            return false;
        }
        return true;
    }

    protected function initToBoardingData()
    {
        $this->targetData = [];

        foreach ($this->sourceData as $str) {
            $str1 = substr($str, 0, 7);
            $str2 = substr($str, -3);

            $row = $this->getBoardingInfoItem($str1, 0, 127);
            $column = $this->getBoardingInfoItem($str2, 0, 7);
            $id = ($row * 8) + $column;

            $this->targetData[] = [
                'row' => intval($row),
                'column' => intval($column),
                'id' => intval($id),
            ];
        }
    }

    protected function getBoardingInfoItem(string $str, $min = 0, $max = 127)
    {
        $ops = str_split($str);

        $isMax = false;
        foreach ($ops as $op) {
            $isMax = in_array($op, ['B', 'R']);
            $med = ceil(($max - $min) / 2);
            if ($isMax) {
                $min += $med;
            } else {
                $max -= $med;
            }
        }
        return $isMax ? $max : $min;
    }

    public function getMaxBoardingPassId()
    {
        $this->initToBoardingData();
        $ids = $this->getColumnsOfBoardingPass('id');
        return max($ids);
    }

    public function getMyBoardingPassId()
    {
        $all = $this->fetchMissingBoardingPasses();
        $data = array_pop($all);
        return $data['id'] ?? null;
    }

    protected function fetchMissingBoardingPasses()
    {
        $ids = $this->getColumnsOfBoardingPass('id');
        $rows = $this->getColumnsOfBoardingPass('row');
        $columns = $this->getColumnsOfBoardingPass('column');

        $data = [];
        foreach ($rows as $row) {
            foreach ($columns as $column) {
                $id = $row * 8 + $column;
                if (in_array($id, $ids)) {
                    continue;
                }
                $nears = [$id - 1, $id + 1];
                $intersect = array_intersect($nears, $ids);
                if (count($intersect) == 2) {
                    $data[] = [
                        'row' => $row,
                        'column' => $column,
                        'id' => $id,
                    ];
                }
            }
        }
        return $data;
    }

    protected function getColumnsOfBoardingPass(string $index)
    {
        $arr = array_unique(array_column($this->targetData, $index));
        sort($arr);
        return $arr;
    }

    public function getDay6($part = 2)
    {
        $this->initDataToForms();
        $data = $this->uniqueFormAnswers($part == 2);
        $total = 0;
        foreach ($data as $answers) {
            $total += count($answers);
        }
        return $total;
    }

    protected function initDataToForms()
    {
        $this->targetData = [];

        $i = 0;
        foreach ($this->sourceData as $str) {
            if (empty($str)) {
                $i++;
            } else {
                $this->targetData[$i][] = str_split($str);
            }
        }
    }

    protected function uniqueFormAnswers(bool $intersect = true)
    {
        $data = [];
        foreach ($this->targetData as $group) {
            $temp = [];
            foreach ($group as $i => $form) {
                if ($intersect) {
                    $temp = $i ? array_intersect($form, $temp) : $form;
                } else {
                    $temp = array_merge($temp, $form);
                }
            }
            $data[] = array_unique($temp);
        }
        return $data;
    }
}