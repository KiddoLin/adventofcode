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

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getBagTotalParents($bag = 'shiny gold')
    {
        $this->initDataToBagRules();
        $data = $this->getBagRules($bag);
        return count($data);
    }

    public function getBagTotalChildren($bag = 'shiny gold')
    {
        $total = 0;
        foreach ($this->targetData[$bag] as $name => $num) {
            $total += $num;
            $childTotal = $this->getBagTotalChildren($name);
            $total += ($num * $childTotal);
        }
        return $total;
    }

    protected function initDataToBagRules()
    {
        $this->targetData = [];

        foreach ($this->sourceData as $str) {
            $str = str_replace('.', '', $str);
            [$name, $rule] = explode(' contain ', $str);
            $name = $this->initBagName($name);
            $children = [];
            if (intval($rule)) {
                $items = explode(', ', $rule);
                foreach ($items as $item) {
                    $num = intval($item);
                    $childName = str_replace("{$num} ", '', $item);
                    $childName = $this->initBagName($childName);
                    $children[$childName] = $num;
                }
            }
            if (isset($this->targetData[$name])) {
                dd('exit');
            }
            $this->targetData[$name] = $children;
        }
    }

    protected function initBagName(string $name) 
    {
        $name = str_replace(' bags', '', $name);
        $name = str_replace(' bag', '', $name);
        return $name;
    }

    protected function getBagRules(string $bag)
    {
        $data = [];
        foreach ($this->targetData as $name => $children) {
            if ($this->checkBagInChildren($children, $bag)) {
                $data[] = $name;
            }
        }
        return $data;
    }

    protected function checkBagInChildren(array $rules, string $bag)
    {
        if (count($rules) < 1) {
            return false;
        }
        if (isset($rules[$bag])) {
            return true;
        }
        foreach ($rules as $name => $num) {
            if ($this->checkBagInChildren($this->targetData[$name], $bag)) {
                return true;
            }
        }
        return false;
    }

    public function getDay8($part = 1)
    {
        $this->initDataToAccumulator();

        if ($part == 1) {
            $value = $this->getValueBeforeAccumulatorLoop();
        } else {
            $value = $this->getValueOfFixedAccumulator();
        }
        return $value;
    }

    protected function initDataToAccumulator()
    {
        $this->targetData = [];

        foreach ($this->sourceData as $str) {
            [$op, $num] = explode(' ', $str);
            $this->targetData[] = [
                'op' => $op,
                'num' => intval($num),
            ];
        }
    }

    protected function getValueBeforeAccumulatorLoop()
    {
        try {
            $value = $this->runAccumulator($this->targetData);
        } catch (\Exception $e) {
            $value = $e->getCode();
        }
        return $value;
    }

    protected function getValueOfFixedAccumulator()
    {
        $value = null;
        foreach ($this->targetData as $i => $item) {
            $op = $item['op'];
            if (in_array($op, ['jmp', 'nop'])) {
                $data = $this->targetData;
                $data[$i]['op'] = $op == 'jpm' ? 'nop' : 'nop';
                try {
                    $value = $this->runAccumulator($data);
                    dump("Fix item {$i} " . json_encode($item));
                } catch (\Exception $e) {
                    //
                }
            }
        }
        return $value;
    }

    protected function runAccumulator(array $ops, int $times = 1)
    {
        $data = 0;

        $total = count($ops);

        $i = 0;

        $runIds = [];

        do {
            for ($i = $i; $i < $total; $i++) {
                if (isset($runIds[$i])) {
                    $runIds[$i]++;
                } else {
                    $runIds[$i] = 0;
                }

                $item = $ops[$i];

                if ($times > 0 && $runIds[$i] >= $times) {
                    throw new \Exception('Over loop times', $data);
                }
                
                if ($item['op'] == 'acc') {
                    $data += $item['num'];
                } elseif ($item['op'] == 'jmp') {
                    $i = $i + $item['num'];
                    break;
                } else {
                    // dump($item['op']); nop
                }
            }
        } while ($i < $total);

        return $data;
    }

    public function getDay9()
    {
        $this->initDataToXMAS();
        $data = $this->fetchFirstNumberAfterPreamble();
        return $data;
    }

    public function fetchEncryptedNumberOfXMAS($num = 15690279)
    {
        $len = array_search($num, $this->targetData);

        $arr = array_slice($this->targetData, 0, $len);

        for ($chunk = 2; $chunk <= $len; $chunk++) {
            for ($i = 0; $i < $len - $chunk; $i++) {
                $temp = array_slice($arr, $i, $chunk);
                if (array_sum($temp) == $num) {
                    $min = min($temp);
                    $max = max($temp);
                    $data = $min + $max;
                    return $data;
                }
            }
        }
       return null;
    }

    protected function initDataToXMAS()
    {
        $this->targetData = [];

        foreach ($this->sourceData as $str) {
            $this->targetData[] = intval($str);
        }
    }

    protected function fetchFirstNumberAfterPreamble($index = 25)
    {
        $total = count($this->targetData);

        for ($i = $index; $i < $total; $i++) {
            $num = $this->targetData[$i];
            $arr = array_slice($this->targetData, 0, $i);
            if (!$this->isValidNUmberOfXMAS($arr, $num)) {
                return $num;
            }
        }
        return null;
    }

    protected function isValidNUmberOfXMAS(array $arr, int $num)
    {
        $total = count($arr);
        for ($i = 0; $i < $total - 1; $i++) {
            for ($j = $i + 1; $j < $total; $j++) {
                if ($arr[$i] + $arr[$j] == $num) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getDay10()
    {
        $this->initDataToaAdapters();
        $data = $this->getJoltageData($this->targetData);

        $a = count($data[1] ?? []);
        $b = count($data[3] ?? []);
        $num = $a * $b;
        return $num;
    }

    public function initDataToaAdapters()
    {
        $this->targetData = [];

        foreach ($this->sourceData as $str) {
            $this->targetData[] = intval($str);
        }
    }

    public function getJoltageData(array $data)
    {
        $max = max($data);

        $arr = array_merge([0, $max + 3], $data);
        sort($arr);
        // $arr = [0, 1, 4, 5, 6, 7, 10, 11, 12, 15, 16, 19, 22];

        $data = [];
        foreach ($arr as $i => $v) {
            if ($i == 0) {
                continue;
            }
            $diff = $v - $arr[$i-1];
            if (isset($data[$diff])) {
                $data[$diff][] = $v;
            } else {
                $data[$diff] = [$v];
            }
        }

        return $data;
    }

    // protected

    public function getTotalJoltageWays()
    {
        $arr = $this->targetData;

        $max = max($this->targetData);
        $arr = array_merge([0], $this->targetData);
        sort($arr);

        $arr = [0, 1, 2, 3, 4, 7, 8, 9, 10, 11, 14, 17, 18, 19, 20, 23, 24, 25, 28, 31, 32, 33, 34, 35, 38, 39, 42, 45, 46, 47, 48, 49, 52];
        $arr = [0, 1, 2, 3, 4, 7];
        // dd($arr);

        // $this->joltage = 0;
        // $this->rollFetchJoltage($arr);
        // dd($this->joltage);

        rsort($arr);

        $total = $this->jump(count($arr)-1, $arr);
        dd($total);

        $total = 1;
        foreach ($arr as $i => $v) {
            for ($j = 3; $j > 2; $j--) {
                $ni = $i + $j;
                $next = $arr[$ni] ?? null;
                if ($next) {
                    $diff = $next - $v;
                    if ($diff <= $i) {
                        $temp = [
                            3 => 4,
                            2 => 2,
                            1 => 1,
                        ];
                        $total *= $temp[$j];
                        break;
                    }
                }
            }
        }
        dd($total + 1);

        $exp = 1;

        $total = count($arr);

        $data = [];
        foreach ($arr as $v) {
            for ($i = 1; $i <= 3; $i++) {
                $num = $v + $i;
                if (in_array($num, $arr)) {
                    $data[$i][] = $i;
                    // if (isset($data[$i])) {
                    //     $data[$i][] = $i;
                    // } else {
                    //     $data[$i][] = $i;
                    // }
                }
            }
        }

        dd($data);

        dd($exp);
        
        return pow(2, $exp);
    }

    protected $joltage = 0;

    protected function rollFetchJoltage(array $arr, array $data = [])
    {
        $data = empty($data) ? [min($arr)] : $data;
        $num = max($data);
        // dump($num . '--' . json_encode($data));


        if ($num == max($arr)) {
            $this->joltage += 1;
            // dump(json_encode($data));
            return $this->joltage;
        }

        foreach ($arr as $v) {
            if (($v > $num) && (($v - $num) <= 3)) {
                $temp = array_merge($data, [$v]);
                $this->rollFetchJoltage($arr, $temp);
            }
        }
    }

    protected function jump($i, array $arr) 
    {
        if ($i == 0) return 0;
        if ($i == 1) return 1;
        if ($i == 2) return 2;
        if ($i == 3) return 3;

        $num = $arr[$i];

        $next1 = $arr[$i - 1] ?? null;
        $next2 = $arr[$i - 2] ?? null;
        $next3 = $arr[$i - 3] ?? null;

        return ($next1 && $num-$next1 >= 3 ? $this->jump($next1) : 0) 
        + ($next2 && $num-$next2 >= 2 ? $this->jump($next2) : 0) 
        + ($next3 && $num-$next3 >= 1? $this->jump($next3) : 0);
    }
}