<?php


class Coder
{
    protected $day;
    protected $sourceData = [];
    protected $targetData = [];
    protected $answerData = [];

    public function __construct($day)
    {
        $this->init($day);
    }

    protected function init($day)
    {
        $this->day = intval($day);
        $this->sourceData = loadData($this->day);
        // reset
        $this->targetData = [];
        // init for target day
        $method = "init{$this->day}";
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    protected function initInteger()
    {
        foreach ($this->sourceData as $str) {
            $this->targetData[] = intval($str);
        }
    }

    public function run($day = null)
    {
        if ($day) {
            $this->init($day);
        }

        dump('-------------------- part 1 run --------------------');
        $part1 = $this->runPart1();
        dump('-------------------- part 2 run --------------------');
        $part2 = $this->runPart2();
        dump('==================== part 1 & 2 over ====================');

        return [$part1, $part2];
    }

    public function runPart1($day = null)
    {
        if ($day) {
            $this->init($day);
        }

        $part1 = '';
        $method1 = "run{$this->day}Part1";
        if (method_exists($this, $method1)) {
            $part1 = $this->$method1();
        }
        return $part1;
    }

    public function runPart2($day = null)
    {
        if ($day) {
            $this->init($day);
        }

        $part2 = '';
        $method2 = "run{$this->day}Part2";
        if (method_exists($this, $method2)) {
            $part2 = $this->$method2();
        }
        return $part2;
    }

    protected function init1()
    {
        $this->initInteger();
    }

    protected function run1Part1()
    {
        return $this->getProduct(2); // 605364
    }

    protected function run1Part2()
    {
        return $this->getProduct(3); // 128397680
    }

    protected function getProduct(int $chunk = 3): float
    {
        $data = $this->getTargetNums($this->targetData, $chunk);
        return array_product($data);
    }

    /**
     * Day 1 temp data
     *
     * @var array
     */
    protected $day1Array = [];

    protected function getTargetNums(array $data, int $chunk, int $num = 2020): array
    {
        $this->day1Array = [];

        $this->rollFetch($data, $chunk);

        foreach ($this->day1Array as $args) {
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
                $this->day1Array[] = $args;
            } else {
                $this->rollFetch($data, $chunk, $i + 1, $args);
            }
        }
    }

    protected function init2()
    {
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

    protected function run2Part1()
    {
        return $this->getTotalPassword(1); // 477
    }

    protected function run2Part2()
    {
        return $this->getTotalPassword(2); // 686
    }

    protected function getTotalPassword($part)
    {
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

    protected function init3()
    {
        $this->targetData = $this->sourceData;
    }

    protected function run3Part1()
    {
        return $this->getTotalTrees(); // 254
    }

    protected function run3Part2()
    {
        $total = 1;
        for ($way = 1; $way <= 5; $way++) {
            $num = $this->getTotalTrees($way);
            $total = $total * $num;
        }
        return $total; // 1666768320
    }

    protected function getTotalTrees($way = 2)
    {
        $data = $this->getRunningWayStep($way);
        $trees = array_filter($data, function ($v) {
            return $v == '#';
        });
        $num = count($trees);
        return $num;
    }

    protected function getRunningWayStep(int $way)
    {
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

    protected function init4()
    {
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

    protected function run4Part1()
    {
        $validPassports = $this->getValidPassports(false);
        return count($validPassports); // 245
    }

    protected function run4Part2()
    {
        $validPassports = $this->getValidPassports(true);
        return count($validPassports); // 133
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

    protected function init5()
    {
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

    protected function run5Part1()
    {
        $ids = $this->getColumnsOfBoardingPass('id');
        return max($ids); // 922
    }

    protected function run5Part2()
    {
        $all = $this->fetchMissingBoardingPasses();
        $data = array_pop($all);
        return $data['id'] ?? null; // 747
    }

    protected function getColumnsOfBoardingPass(string $index)
    {
        $arr = array_unique(array_column($this->targetData, $index));
        sort($arr);
        return $arr;
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

    protected function init6()
    {
        $i = 0;
        foreach ($this->sourceData as $str) {
            if (empty($str)) {
                $i++;
            } else {
                $this->targetData[$i][] = str_split($str);
            }
        }
    }

    protected function run6Part1()
    {
        $answers = $this->uniqueFormAnswers(false);
        $data = $this->countArray($answers);
        return $data; // 6521
    }

    protected function run6Part2()
    {
        $answers = $this->uniqueFormAnswers(true);
        $data = $this->countArray($answers);
        return $data; // 3305
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

    protected function countArray(array $data)
    {
        $total = 0;
        foreach ($data as $val) {
            $total += count($val);
        }
        return $total;
    }

    protected function init7()
    {
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

    protected function run7Part1()
    {
        $data = $this->getBagRules('shiny gold');
        return count($data); // 268
    }

    protected function run7Part2()
    {
        $total = $this->getBagTotalChildren('shiny gold');
        return $total; // 7867
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

    protected function getBagTotalChildren($bag)
    {
        $total = 0;
        foreach ($this->targetData[$bag] as $name => $num) {
            $total += $num;
            $childTotal = $this->getBagTotalChildren($name);
            $total += ($num * $childTotal);
        }
        return $total;
    }

    protected function init8()
    {
        foreach ($this->sourceData as $str) {
            [$op, $num] = explode(' ', $str);
            $this->targetData[] = [
                'op' => $op,
                'num' => intval($num),
            ];
        }
    }

    protected function run8Part1()
    {
        try {
            $value = $this->runAccumulator($this->targetData);
        } catch (\Exception $e) {
            $value = $e->getCode();
        }
        return $value; // 1675
    }

    protected function run8Part2()
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
        return $value; // 1532
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

    protected function init9()
    {
        $this->initInteger();
    }

    protected function run9Part1()
    {
        $data = $this->fetchFirstNumberAfterPreamble(25);
        return $data; // 15690279
    }

    protected function run9Part2()
    {
        $data = $this->fetchEncryptedNumberOfXMAS(15690279);
        return $data; // 2174232
    }

    protected function fetchFirstNumberAfterPreamble($index)
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

    protected function fetchEncryptedNumberOfXMAS($num)
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

    protected function init10()
    {
        $this->initInteger();
    }

    protected function run10Part1()
    {
        $data = $this->getJoltageData($this->targetData);

        $a = count($data[1] ?? []);
        $b = count($data[3] ?? []);
        $num = $a * $b;
        return $num; // 2100
    }

    protected function run10Part2()
    {
        $data = $this->getTotalJoltageWays();
        return $data; // null
    }

    protected function getJoltageData(array $data)
    {
        $max = max($data);

        $arr = array_merge([0, $max + 3], $data);
        sort($arr);

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

    protected function getTotalJoltageWays()
    {
        $arr = $this->targetData;

        $max = max($this->targetData);
        $arr = array_merge([0], $this->targetData);
        sort($arr);

        $arr = [0, 1, 2, 3, 4, 7, 8, 9, 10, 11, 14, 17, 18, 19, 20, 23, 24, 25, 28, 31, 32, 33, 34, 35, 38, 39, 42, 45, 46, 47, 48, 49, 52];
        $arr = [0, 1, 2, 3, 4, 7];
        // dd($arr);
        return $max;

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

    /**
     * Day10 temp data
     *
     * @var integer
     */
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

    protected function init11()
    {
        foreach ($this->sourceData as $str) {
            $this->targetData[] = str_split($str);
        }
    }

    protected function run11Part1()
    {
        $total = $this->getTotalSeats(1);
        return $total; // 2321
    }

    protected function run11Part2()
    {
        $total = $this->getTotalSeats(2);
        return $total; // 2102
    }

    protected function getTotalSeats($part)
    {
        $data = $this->refreshSeats($this->targetData, $part);
        $total = 0;
        foreach ($data as $row) {
            $temp = array_count_values($row);
            $total += $temp['#'] ?? 0;
        }
        return $total;
    }

    protected function refreshSeats(array $arr, $part = 2)
    {
        $data = [];

        $limit = $part == 2 ? 5 : 4;

        $hasChange = false;

        $total = count($arr);
        for ($i = 0; $i < $total; $i++) {
            $len = count($arr[$i]);
            for ($j = 0; $j < $len; $j++) {
                $around = [
                    $xl = $this->getNextSeatOf($arr, $i, $j, 'xl', $part),
                    $xr = $this->getNextSeatOf($arr, $i, $j, 'xr', $part),

                    $fl = $this->getNextSeatOf($arr, $i, $j, 'fl', $part),
                    $fx = $this->getNextSeatOf($arr, $i, $j, 'fx', $part),
                    $fr = $this->getNextSeatOf($arr, $i, $j, 'fr', $part),

                    $bl = $this->getNextSeatOf($arr, $i, $j, 'bl', $part),
                    $bx = $this->getNextSeatOf($arr, $i, $j, 'bx', $part),
                    $br = $this->getNextSeatOf($arr, $i, $j, 'br', $part),
                ];

                $xx = $arr[$i][$j];

                $valAround = array_count_values($around);
                $totalOccupied = $valAround['#'] ?? 0;

                if ($xx == 'L') {
                    if ($totalOccupied > 0) {
                        $data[$i][$j] = 'L';
                    } else {
                        $data[$i][$j] = '#';
                        $hasChange = true;
                    }
                } elseif ($xx == '#') {
                    if ($totalOccupied < $limit) {
                        $data[$i][$j] = '#';
                    } else {
                        $data[$i][$j] = 'L';
                        $hasChange = true;
                    }
                } else {
                    $data[$i][$j] = $xx;
                }
            }
        }

        if ($hasChange) {
            $data = $this->refreshSeats($data, $part);
        }

        return $data;
    }

    protected function getNextSeatOf(array $data, $i, $j, $direction, $part = 2)
    {
        if ($direction == 'fl') {
            $i = $i - 1;
            $j = $j - 1;
        } elseif ($direction == 'fx') {
            $i = $i - 1;
        } elseif ($direction == 'fr') {
            $i = $i - 1;
            $j = $j + 1;
        } elseif ($direction == 'xl') {
            $j = $j - 1;
        } elseif ($direction == 'xr') {
            $j = $j + 1;
        } elseif ($direction == 'bl') {
            $i = $i + 1;
            $j = $j - 1;
        } elseif ($direction == 'bx') {
            $i = $i + 1;
        } elseif ($direction == 'br') {
            $i = $i + 1;
            $j = $j + 1;
        } else {
            return $data[$i][$j] ?? 'null';
        }

        $seat = $data[$i][$j] ?? 'null';
        if ($part == 2 && $seat == '.') {
            $seat = $this->getNextSeatOf($data, $i, $j, $direction);
        }
        return $seat;
    }

    protected function init12()
    {
        foreach ($this->sourceData as $str) {
            $op = $str[0];
            $map = [
                'F' => 0,
                'E' => 2,
                'S' => 4,
                'W' => 6,
                'N' => 8,
                'L' => -1,
                'R' => 1,
            ];
            $val = substr($str, 1);
            $this->targetData[] = [
                'op' => $map[$op],
                'val' => $val,
            ];
        }
    }

    protected function run12Part1()
    {
        [$x, $y, $direction] = $this->execNavigationInstructions(0 , 0, 2);
        $data = abs($x) + abs($y);
        return $data; // 2847
    }

    protected function run12Part2()
    {
        [$x, $y, $direction] = $this->moveNavigationInstruction(10, 1, 2);
        $data = abs($x) + abs($y);
        return $data; // 29839
    }

    protected function execNavigationInstructions(int $x, int $y, int $direction)
    {
        foreach ($this->targetData as $item) {
            $op = $item['op'];
            $val = $item['val'];

            $move = $op != 0 ? $op : $direction;

            if ($move == 2) {
                $x += $val;
            } elseif ($move == 4) {
                $y -= $val;
            } elseif ($move == 6) {
                $x -= $val;
            } else if ($move == 8) {
                $y += $val;
            } else {
                // -1 or 1
                [$direction] = $this->turnNavigationPoint($op, $val, $direction);
            }
        }
        return [$x, $y, $direction];
    }

    protected function turnNavigationPoint(int $op, int $val, int $direction, int $x = 0, int $y = 0)
    {
        $times = $val / 90;
        $turn = $op * $times;
        $temp = (($turn * 2) + $direction) % 8;
        $newDirection = $temp > 0 ? $temp : $temp + 8;

        if (in_array($turn, [1, -3])) {
            $newX = $y;
            $newY = -$x;
        } elseif (in_array($turn, [2, -2])) {
            $newX = -$x;
            $newY = -$y;
        } elseif (in_array($turn, [3, -1])) {
            $newX = -$y;
            $newY = $x;
        } else {
            // 4, 0, -4
            $newX = $x;
            $newY = $y;

        }
//        dump("({$x},{$y}) turn ({$op},$val) to ({$newX},{$newY})");

        return [$newDirection, $newX, $newY];
    }

    protected function moveNavigationInstruction(int $x, int $y, int $direction)
    {
        $myx = 0;
        $myy = 0;

        foreach ($this->targetData as $item) {
            $op = $item['op'];
            $val = $item['val'];
            if (!$op) {
                $myx += ($x * $val);
                $myy += ($y * $val);
//                dump("{$op}--{$val} waypoint({$x},{$y})--my({$myx},{$myy})");
            } elseif ($op == 2) {
                $x += $val;
//                dump("{$op}--{$val} waypoint({$x},{$y})--my({$myx},{$myy})");
            } elseif ($op == 4) {
                $y -= $val;
//                dump("{$op}--{$val} waypoint({$x},{$y})--my({$myx},{$myy})");
            } elseif ($op == 6) {
                $x -= $val;
//                dump("{$op}--{$val} waypoint({$x},{$y})--my({$myx},{$myy})");
            } else if ($op == 8) {
                $y += $val;
//                dump("{$op}--{$val} waypoint({$x},{$y})--my({$myx},{$myy})");
            } else {
                // -1 or 1
                [$direction, $x, $y] = $this->turnNavigationPoint($op, $val, $direction, $x, $y);
            }
        }
        return [$myx, $myy, $direction];
    }

}