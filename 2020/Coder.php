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
        if ($byr < 1920 || $byr > 2002) {
            return false;
        }
        if ($iyr < 2010 || $iyr > 2020) {
            return false;
        }
        if ($eyr < 2020 || $eyr > 2030) {
            return false;
        }
        $intHgt = intval($hgt);
        if (strripos($hgt, 'cm') !== false) {
            if ($intHgt < 150 || $intHgt > 193) {
                return false;
            }
        } elseif (strripos($hgt, 'in') !== false) {
            if ($intHgt < 59 || $intHgt > 76) {
                return false;
            }
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
        } catch ( \Exception $e ) {
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
                } catch ( \Exception $e ) {
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

        $max = max($this->targetData);
        $arr = array_merge([0, $max + 3], $this->targetData);
        sort($arr);

        $this->targetData = $arr;
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
        $arr = $this->targetData;
        $data = $this->getTotalJoltageWays($arr);
        return $data; // 16198260678656
    }

    protected function getJoltageData(array $arr)
    {
        $data = [];
        foreach ($arr as $i => $v) {
            if ($i == 0) {
                continue;
            }
            $diff = $v - $arr[$i - 1];
            if (isset($data[$diff])) {
                $data[$diff][] = $v;
            } else {
                $data[$diff] = [$v];
            }
        }
        return $data;
    }

    protected function getTotalJoltageWays(array $arr)
    {
        $data = [];

        $len = count($arr);
        $start = 0;
        for ($i = 1; $i < $len; $i++) {
            $diff = $arr[$i] - $arr[$i - 1];
            if ($diff >= 3) {
                $temp = array_slice($arr, $start, $i - $start + 1);
                $this->joltage = 0;
                $this->rollFetchJoltage($temp);
                $data[] = $this->joltage;
                $start = $i;
            }
        }

        return array_product($data);
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
        if (empty($arr) || $num >= max($arr)) {
            $this->joltage += 1;
            return null;
        }

        foreach ($arr as $k => $v) {
            if (($v > $num) && (($v - $num) <= 3)) {
                $tempData = array_merge($data, [$v]);
                $tempArr = array_slice($arr, $k);
                $this->rollFetchJoltage($tempArr, $tempData);
            }
        }
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
        [$x, $y, $direction] = $this->execNavigationInstructions(0, 0, 2);
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
            } elseif ($move == 8) {
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
            } elseif ($op == 8) {
                $y += $val;
//                dump("{$op}--{$val} waypoint({$x},{$y})--my({$myx},{$myy})");
            } else {
                // -1 or 1
                [$direction, $x, $y] = $this->turnNavigationPoint($op, $val, $direction, $x, $y);
            }
        }
        return [$myx, $myy, $direction];
    }

    protected $statTime;

    protected function init13()
    {
        $this->statTime = intval($this->sourceData[0]);

        foreach ($this->sourceData as $i => $str) {
            if ($i > 0) {
                $this->targetData = explode(',', $str);
            }
        }
    }

    protected function run13Part1()
    {
        $arr = $this->getCarMinTimeAfter($this->statTime);
        $first = array_shift($arr);
        $data = $first['car'] * $first['diff'];
        return $data; // 3246
    }

    protected function run13Part2()
    {
        $data = $this->getCarTimestamp($this->targetData);
        return $data; // 1010182346291467
    }

    public function getCarMinTimeAfter(int $startTime)
    {
        $data = [];
        foreach ($this->targetData as $car) {
            if ($car != 'x') {
                $time = $this->getFirstMultipleNum($startTime, $car);
                $data[$time] = [
                    'car' => $car,
                    'time' => $time,
                    'diff' => $time - $startTime,
                ];
            }
        }
        ksort($data);
        return $data;
    }

    protected function getFirstMultipleNum(int $num, int $base, $isCeil = true)
    {
        $remainder = $num % $base;
        if (!$remainder) {
            return $remainder;
        }
        return $isCeil ? $num + ($base - $remainder) : $num - $remainder;
    }

    protected function getCarTimestamp(array $arr)
    {
        $t = null;
        $divisor = null;
        foreach ($arr as $i => $v) {
            if ($v != 'x') {
                if (is_null($t)) {
                    $t = $v;
                    $divisor = $v;
                } else {
                    $t = $this->getCarTimestampBoth($t, $divisor, $v, $i);
                    $divisor *= $v;
                }
            }
        }
        return $t;
    }

    protected function getCarTimestampBoth(int $base, int $divisor, int $num, int $diff)
    {
        $timestamp = null;
        $i = 0;
        while (is_null($timestamp)) {
            $t = $base + ($divisor * $i);
            $remainder = ($t + $diff) % $num;
            if ($remainder == 0) {
                $timestamp = $t;
            }
            $i++;
        }
        return $timestamp;
    }

    protected function init14()
    {
        $mask = '';
        foreach ($this->sourceData as $str) {
            if (strstr($str, 'mask') !== false) {
                $mask = str_replace('mask = ', '', $str);
            } else {
                [$memStr, $valStr] = explode(' = ', $str);
                $mem = substr($memStr, 4, -1);
                $valueBinary = $this->getBinary36($valStr);
                $memBinary = $this->getBinary36($mem);
                $this->targetData[] = [
                    'mask' => $mask,
                    'mem' => $mem,
                    'mem_binary' => $memBinary,
                    'value' => intval($valStr),
                    'value_binary' => $valueBinary,
                ];
            }
        }
    }

    protected function getBinary36($decimal): string
    {
        $bin = base_convert($decimal, 10, 2);
        if (strlen($bin) > 36) {
            dd('over length limit');
        }
        $binary = sprintf('%036s', $bin);
        return $binary;
    }

    protected function run14Part1()
    {
        $data = $this->callMaskInit($this->targetData);
        $total = array_sum($data);
        return $total; // 11612740949946
    }

    protected function run14Part2()
    {
        $data = $this->callMaskInit2($this->targetData);
        $total = array_sum($data);
        return $total; // 3394509207186
    }

    protected function callMaskInit(array &$arr)
    {
        $data = [];
        foreach ($arr as $i => $item) {
            $resultBin = $this->getResultFromMask($item['mask'], $item['value_binary']);
            $resultDec = base_convert($resultBin, 2, 10);
            $result = intval($resultDec);
            $data[$item['mem']] = $result;
            $arr[$i]['result_decimal'] = $result;
            $arr[$i]['result_binary'] = $resultBin;
        }
        return $data;
    }

    protected function getResultFromMask(string $mask, string $bin = null): string
    {
        if (is_null($bin)) {
            $bin = sprintf('%036s', '');
        }

        $data = [];
        $len = strlen($mask);
        for ($i = 0; $i < $len; $i++) {
            $data[] = $mask[$i] === 'X' ? $bin[$i] : $mask[$i];
        }

        return implode('', $data);
    }

    protected function callMaskInit2(array &$arr)
    {
        $data = [];
        foreach ($arr as $i => $item) {
            $possibleValues = $this->getResultFromMask2($item['mask'], $item['mem_binary']);
            foreach ($possibleValues as $memBin) {
                $memDecimal = base_convert($memBin, 2, 10);
                $mem = intval($memDecimal);
                $data[$mem] = $item['value'];
            }
            $arr[$i]['float_memes'] = $possibleValues;
        }
        return $data;
    }

    protected function getResultFromMask2(string $mask, string $bin = null): array
    {
        if (is_null($bin)) {
            $bin = sprintf('%036s', '');
        }

        $vale = '';
        $allX = [];
        $len = strlen($mask);
        for ($i = 0; $i < $len; $i++) {
            if ($mask[$i] == 'X') {
                $vale .= 'X';
                $allX[] = $i;
            } elseif ($mask[$i] == 1) {
                $vale .= 1;
            } else {
                $vale .= $bin[$i];
            }
        }

        $totalX = count($allX);
        $maps = [];
        for ($n = 0; $n < $totalX; $n++) {
            $temp0 = [0];
            $temp1 = [1];
            if (empty($maps)) {
                $maps[] = $temp0;
                $maps[] = $temp1;
            } else {
                foreach ($maps as $i => $map) {
                    $maps[$i] = array_merge($map, $temp0);
                    $maps[] = array_merge($map, $temp1);
                }
            }
        }
        sort($maps);

        $floatLen = pow(2, $totalX);
        $floats = [];
        for ($j = 0; $j < $floatLen; $j++) {
            $floats[] = $vale;
        }

        foreach ($floats as $k => $result) {
            $map = array_shift($maps);
            foreach ($allX as $key) {
                $floats[$k][$key] = array_shift($map);
            }
        }

        return $floats;
    }

    protected function init15()
    {
        $data = explode(',', $this->sourceData[0]);
        foreach ($data as $v) {
            $this->targetData[] = intval($v);
        }
    }

    protected function run15Part1()
    {
        $data = $this->getLoopNumOf(2020);
        return $data; // 1294
    }

    protected function run15Part2()
    {
        $data = $this->getLoopNumOf(30000000);
        return $data; // 573522
    }

    protected function getLoopNumOf(int $index)
    {
        $data = $this->targetData;

        foreach ($data as $k => $v) {
            $spoken[$v][] = $k;
        }

        $temp = 'null';

        $i = count($data);
        while ($i < $index) {
            if (!isset($spoken[$temp]) || count($spoken[$temp]) <= 1) {
                $temp = 0;
            } else {
                $last = end($spoken[$temp]);
                $lastTwo = prev($spoken[$temp]);
                $temp = $last - $lastTwo;
            }

            $spoken[$temp][] = $i;

            $i++;
        }

        return $temp;
    }

    protected $myTicket = [];
    protected $ticketRules = [];

    protected function init16()
    {
        $i = 0;
        foreach ($this->sourceData as $str) {
            if (empty($str)) {
                $i++;
            } else {
                if ($i > 0) {
                    if (strstr($str, ',') !== false) {
                        $temp = explode(',', $str);
                        if ($i == 1) {
                            $this->myTicket = $temp;
                        } else {
                            $this->targetData[] = $temp;
                        }
                    }
                } else {
                    [$k, $nums] = explode(': ', $str);
                    [$first, $second] = explode(' or ', $nums);
                    $this->ticketRules[$k] = [
                        explode('-', $first),
                        explode('-', $second),
                    ];
                }
            }
        }
    }

    protected function run16Part1()
    {
        $data = $this->getInvalidTickets($this->targetData);
        $rate = array_sum($data);
        return $rate; // 27850
    }

    protected function run16Part2()
    {
        $realRules = $this->getRealTicketRules($this->remainingTickets);
        $ticket = $this->fillTicketKey($realRules, $this->myTicket);

        $total = 1;
        foreach ($ticket as $k => $num) {
            if (strstr($k, 'departure') !== false) {
                $total *= $num;
            }
        }
        return $total; // 491924517533
    }

    protected $remainingTickets = [];

    protected function getInvalidTickets(array $arr)
    {
        $data = [];
        foreach ($arr as $i => $item) {
            $isValid = true;
            foreach ($item as $num) {
                if ($this->isValidTicketNum($num) == false) {
                    $data[] = $num;
                    $isValid = false;
                }
            }
            if ($isValid) {
                $this->remainingTickets[] = $item;
            }
        }
        return $data;
    }

    protected function isValidTicketNum(int $num): bool
    {
        foreach ($this->ticketRules as $rules) {
            if ($this->checkTicketNumWith($rules, $num)) {
                return true;
            }
        }
        return false;
    }

    protected function checkTicketNumWith(array $rule, $num)
    {
        foreach ($rule as $item) {
            [$left, $right] = $item;
            if ($num >= $left && $num <= $right) {
                return true;
            }
        }
        return false;
    }

    protected function getRealTicketRules(array $arr)
    {
        $data = [];
        $len = count($arr[0] ?? []);
        for ($i = 0; $i < $len; $i++) {
            $nums = array_column($arr, $i);
            foreach ($this->ticketRules as $k => $rule) {
                $isRule = true;
                foreach ($nums as $num) {
                    if ($this->checkTicketNumWith($rule, $num) == false) {
                        $isRule = false;
                        break;
                    }
                }
                if ($isRule) {
                    $data[$i][] = $k;
                }
            }
        }

        $temp = [];
        foreach ($data as $i => $ks) {
            $temp[$i] = count($ks);
        }
        asort($temp);

        $real = [];
        foreach ($temp as $i => $total) {
            $keys = $data[$i];
            do {
                $one = array_pop($keys);
            } while (in_array($one, $real));
            $real[$i] = $one;
        }
        ksort($real);

        return $real;
    }

    protected function fillTicketKey(array $realRules, array $ticket)
    {
        $data = [];
        foreach ($realRules as $i => $key) {
            $data[$key] = $ticket[$i];
        }
        return $data;
    }

    protected function init17()
    {
        $this->targetData = $this->sourceData;
    }

    protected function run17Part1()
    {
        $cubes = $this->fillSlice2Dto3D($this->targetData);
        $data = $this->loopConwayCube($cubes, 6);
        dd($data);
    }

    protected function run17Part2()
    {

    }

    protected function fillSlice2Dto3D(array $arr)
    {
        $temp = [];
        $row = count($arr);
        for ($i = 0; $i < $row; $i++) {
            $temp[$i] = '';
            $column = strlen($arr[$i]);
            for ($j = 0; $j < $column; $j++) {
                $temp[$i] .= $arr[$i][$j];//'.';//
            }
        }
        $data[-1] = $temp;
        $data[0] = $arr;
        $data[1] = $temp;
        return $data;
    }

    protected function loopConwayCube(array $cubes, int $cycle)
    {
        dump($cubes);
        for ($i = 0; $i < $cycle; $i++) {
            $newCubes = $this->boomConwayCube($cubes);
            $cubes = $this->changeConwayCube($newCubes);
            dd($cubes);
        }
        return $cubes;
    }

    protected function boomConwayCube(array $cube)
    {
        return $cube;
    }

    protected function changeConwayCube(array $arr)
    {
        $data = [];
        foreach ($arr as $z => $square) {
            $row = count($arr[$z]);
            for ($x = 0; $x < $row; $x++) {
                $data[$z][$x] = '';
                $column = strlen($arr[$z][$x]);
                for ($y = 0; $y < $column; $y++) {
                    $near = $this->getAtomNear($arr, $z, $x, $y);
                    $atom = $arr[$z][$x][$y];
                    $newAtom = $this->changeConwayAtom($near, $atom);
                    $data[$z][$x] .= $newAtom;
                }
            }
        }
        return $data;
    }

    protected function getAtomNear(array $arr, int $z, int $x, int $y)
    {
        $near = [];
        for ($i = $z - 1; $i <= $z + 1; $i++) {
            for ($j = $x - 1; $j <= $x + 1; $j++) {
                for ($k = $y - 1; $k <= $y + 1; $k++) {
                    if ($i == $z && $j == $x && $k == $y) {
                        continue;
                    } else {
                        $near[] = $arr[$i][$j][$k] ?? '.';
                    }
                }
            }
        }
        return $near;
    }

    protected function changeConwayAtom(array $near, string $atom)
    {
        $nearCount = array_count_values($near);
        $inactive = $nearCount['#'] ?? 0;
        if ($atom == '#') {
            $newAtom = in_array($inactive, [2, 3]) ? '#' : '.';
        } else {
            $newAtom = $inactive == 3 ? '#' : '.';
        }
//        dump(json_encode($nearCount)."--$inactive--$atom => $newAtom");
        return $newAtom;
    }

    protected function init18()
    {
        $this->targetData = str_replace(' ', '', $this->sourceData);
    }

    protected function run18Part1()
    {
        $data = 0;
        foreach ($this->targetData as $str) {
            $data += $this->calculateHouseWork($str);
        }
        return $data; // 1890866893020
    }

    protected function run18Part2()
    {
        $data = 0;
        foreach ($this->targetData as $str) {
            $num = $this->calculateHouseWork2($str);
            $data += intval($num);
        }
        return $data; // 34646237037193
    }

    protected function calculateHouseWork($str)
    {
        $sum = null;
        $op = null;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            if (empty($str[$i]) || $str[$i] == ' ') {
                continue;
            }
            if (is_numeric($str[$i])) {
                $sum = $this->calculateHouseWorkOp($sum, $op, $str[$i]);
            } elseif (in_array($str[$i], ['+', '*'])) {
                $op = $str[$i];
            } elseif ($str[$i] == '(') {
                $rightLen = $this->fetchRightParentheses($i, $str);
                $tempStr = substr($str, $i + 1, $rightLen - 1);
                $num = $this->calculateHouseWork($tempStr);
                $sum = $this->calculateHouseWorkOp($sum, $op, $num);
                $i += $rightLen;
            }
        }
        return $sum;
    }

    protected function calculateHouseWorkOp($sum, $op, $num)
    {
        if (is_null($sum)) {
            return $num;
        }
        if ($op == '+') {
            $sum += $num;
        } elseif ($op == '*') {
            $sum *= $num;
        } else {
            dd('Unknown op');
        }
        return $sum;
    }

    protected function fetchRightParentheses(int $leftIndex, string $str)
    {
        $right = $leftIndex;
        $num = 0;
        for ($i = $right; $i < strlen($str); $i++) {
            if ($str[$i] == '(') {
                $num++;
            } elseif ($str[$i] == ')') {
                $num--;
            }
            if (!$num) {
                $right = $i;
                break;
            }
        }
        return $right - $leftIndex;
    }

    protected function calculateHouseWorkItem(string $str, string $op)
    {
        $i = strpos($str, $op);
        while ($i !== false) {
            [$key1, $num1] = $this->fetchHouseWorkItemNum($str, $i);
            [$key2, $num2] = $this->fetchHouseWorkItemNum($str, $i, false);
            $num = $this->calculateHouseWorkOp($num1, $op, $num2);

            $l = !$key1 ? '' : substr($str, 0, $key1);
            $r = $key2 >= strlen($str) ? '' : substr($str, $key2 + 1);
            $str = $l . $num . $r;

            $i = strpos($str, $op);
        }
        return $str;
    }

    public function calculateHouseWork2(string $str)
    {
        $start = 0;
        $i = strpos($str, '(');
        while ($i !== false) {
            $rightLen = $this->fetchRightParentheses($i, $str);
            $tempStr = substr($str, $i + 1, $rightLen - 1);
            $num = $this->calculateHouseWork2($tempStr);

            $l = substr($str, $start, $i);
            $r = substr($str, $i + $rightLen + 1);
            $str = $l . $num . $r;

            $i = strpos($str, '(');
        }

        $str = $this->calculateHouseWorkItem($str, '+');
        $str = $this->calculateHouseWorkItem($str, '*');

        return $str;
    }

    protected function fetchHouseWorkItemNum(string $str, int $index, $isLeft = true)
    {
        $key = null;
        $temp = '';
        if ($isLeft) {
            for ($i = $index - 1; $i >= 0; $i--) {
                if (in_array($str[$i], ['*', '+'])) {
                    break;
                }
                $temp = $str[$i] . $temp;
                $key = $i;
            }
        } else {
            $len = strlen($str);
            for ($i = $index + 1; $i < $len; $i++) {
                if (in_array($str[$i], ['*', '+'])) {
                    break;
                }
                $temp = $temp . $str[$i];
                $key = $i;
            }
        }
        return [$key, intval($temp)];
    }

    protected $messageBases = [];
    protected $messageRules = [];

    protected function init19()
    {
        $this->messageRules = [];
        $index = array_search('', $this->sourceData);
        // rules
        $bases = [];
        $rules = array_slice($this->sourceData, 0, $index);
        foreach ($rules as $str) {
            [$i, $ruleStr] = explode(': ', $str);
            if (preg_match('/\"(.*)\"/', $ruleStr, $matches)) {
                $this->messageBases[$matches[1]] = intval($i);
            } elseif (strstr($ruleStr, '|') !== false) {
                $this->messageRules[intval($i)] = explode(' | ', $ruleStr);
            } else {
                $bases[intval($i)] = $ruleStr;
            }
        }
        ksort($this->messageRules);
        // target
        $target = array_slice($this->sourceData, $index + 1);
        foreach ($target as $str) {
            $arr = str_split($str);
            $temp = [];
            foreach ($arr as $alphabet) {
                $temp[] = $this->messageBases[$alphabet];
            }
            $this->targetData[] = $temp;
        }

        dd($bases);
    }

    protected function run19Part1()
    {
        $total = 0;
        foreach ($this->targetData as $arr) {
            if ($this->checkWithMessagesRules($arr)) {
                $total++;
            }
        }
        return $total;
    }

    protected function run19Part2()
    {
    }

    protected $day19Array = [];

    protected function checkWithMessagesRules(array $arr)
    {
        $num = array_pop($arr);

        $data = [];
        foreach ($this->messageRules as $i => $rules) {
            foreach ($rules as $rule) {
                $temp = explode(' ', $rule);
                if (array_pop($temp) == $num) {
                    $data[] = $rule;
                }
            }
        }

        dd($data);

        do {
            foreach ($data as $rule) {

            }
        } while(true);
    }

    protected function checkMessageEnd(array $arr, int $index)
    {

    }

    protected function fetchMessagesRules(string $str)
    {
        $this->day19Array = [];
        $this->loopMessagesRules($str);
        dd($this->day19Array);
        return $this->day19Array;
    }

    protected function checkMessageRules(array $arr)
    {
        foreach ($arr as $v) {
            if (!in_array($v, ['a', 'b'])) {
                return false;
            }
        }
        return true;
    }

    protected function loopMessagesRules(string $ruleStr)
    {
        dump('str = ' . $ruleStr);
        $arr = explode(' ', $ruleStr);

        $data = [$arr];

        foreach ($arr as $key => $item) {
            if (!in_array($item, ['a', 'b', ' '])) {
                $replace = $this->messageRules[$item];
                dump("replace = $replace");
                if (strstr($replace, '|') === false) {
                    foreach ($data as $i => $r) {
                        $data[$i][$key] = $replace;
                    }
                } else {
                    $temp = explode(' | ', $replace);
                    foreach ($temp as $s) {
                        $arr[$key] = $s;
                        $n = implode(' ', $arr);
                        dump('or == '.$n);
                        foreach ($data as $i => $r) {
                            $r[$key] = $s;
                            $data[$i][$key] = $s;
                        }
                    }
                }
            }
        }

        $this->loopMessagesRules($n);

        $res = implode('', $arr);
        if ($this->checkMessageRules($arr)) {
            if (!in_array($res, $this->day19Array)) {
                dump('res = ' . implode('', $arr));
                $this->day19Array[] = $res;
            }
        } else {
            $res = implode(' ', $arr);
//            dd($res);
            $this->loopMessagesRules($res);
        }
    }

    protected function init20()
    {
        $id = null;
        $temp = [];
        foreach ($this->sourceData as $str) {
            if (empty($str)) {
                $this->targetData[$id] = $this->getCameraArray($temp);
                $temp = [];
            } elseif (preg_match('/^Tile (.*):$/', $str, $matches)) {
                $id = intval($matches[1]);
            } else {
                $temp[] = $str;
            }
        }
    }

    protected function getCameraArray(array $arr)
    {
        $top = reset($arr);
        $down = end($arr);
        $left = '';
        $right = '';
        foreach ($arr as $str) {
            $left .= $str[0];
            $right .= substr($str, -1);
        }
        $data [] = [
            'top' => $top,
            'down' => $down,
            'left' => $left,
            'right' => $right,
        ];
        $data [] = [
            'top' => strrev($top),
            'down' => strrev($down),
            'left' => $left,
            'right' => $right,
        ];
        $data [] = [
            'top' => $top,
            'down' => $down,
            'left' => strrev($left),
            'right' => strrev($right),
        ];
        $data [] = [
            'top' => strrev($top),
            'down' => strrev($down),
            'left' => strrev($left),
            'right' => strrev($right),
        ];
        return $data;
    }

    protected function run20Part1()
    {
        dd($this->targetData);
        $data = [];
        foreach ($this->targetData as $id => $arr) {
            if ($this->checkOutermost($id)) {
                $data[] = $id;
            }
        }
        dd($data);
    }

    protected function run20Part2() {}

    protected function checkOutermost($id)
    {
        $data = $this->targetData;
        unset($data[$id]);
        $num = 0;
        foreach ($this->targetData[$id] as $str) {
            $isOut = true;
            foreach ($data as $arr) {
                if (in_array($str, $arr)) {
                    $isOut = false;
                    break;
                }
            }
            if ($isOut) {
                $num++;
            }
            if ($num >= 2) {
                return true;
            }
        }
        return false;
    }

    protected function init21()
    {
        foreach ($this->sourceData as $str) {
            preg_match('/\(contains (.*)\)/', $str, $matches);
            $foodsStr = str_replace(" {$matches[0]}", '', $str);
            $foods = explode(' ', $foodsStr);
            $contains = explode(', ', $matches[1]);
            $this->targetData[] = [
                'foods' => $foods,
                'contains' => $contains,
            ];
        }
    }

    protected function run21Part1()
    {
        $data = $this->fetchFoodSafeIngredients($this->targetData);
        return count($data); //
    }

    protected function run21Part2() {}

    protected function fetchFoodSafeIngredients(array $arr)
    {
        $allergens = $this->fetchFoodAllergens($arr);
        $foods = [];
        $contains = [];
        foreach ($this->targetData as $item) {
            $foods = array_merge($foods, $item['foods']);
            $contains = array_merge($contains, $item['contains']);
        }

        dump(count(array_unique($foods)).'---'.count(array_unique($contains)));
        dump($allergens);

        $safes = array_diff($foods, $allergens);
//        dd($safes);
        return $safes;
    }

    protected function fetchFoodAllergens(array $arr)
    {
        $allergens = [];
        $len = count($arr);
        for ($i = 0; $i < $len - 1; $i++) {
            $item1 = $arr[$i];
//            dump("=====$i=====");
            for ($j = $i + 1; $j < $len; $j++) {
                $item2 = $arr[$j];
                // both
                $bothContains = array_intersect($item1['contains'], $item2['contains']);
                $bothFoods = array_intersect($item1['foods'], $item2['foods']);
                foreach ($bothContains as $contain) {
                    foreach ($bothFoods as $food) {
                        if (isset($allergens[$food])){
                            if (!in_array($contain, $allergens[$food])) {
                                $allergens[$food][] = $contain;
                            }
                        } else {
                            $allergens[$food] = [$contain];
                        }
                    }
                }
                // diff item2 between self
                $diffContains = array_diff($item2['contains'], $item1['contains']);
                $diffFoods = array_diff($item2['foods'], $item1['foods']);
                foreach ($diffContains as $contain) {
                    foreach ($diffFoods as $food) {
                        if (isset($allergens[$food])){
                            if (!in_array($contain, $allergens[$food])) {
                                $allergens[$food][] = $contain;
                            }
                        } else {
                            $allergens[$food] = [$contain];
                        }
                    }
                }
            }
            // diff self between allergens
            $diffContains = array_diff($item1['contains'], $allergens);
            $diffFoods = array_diff($item1['foods'], array_keys($allergens));
            foreach ($diffContains as $contain) {
                foreach ($diffFoods as $food) {
                    if (isset($allergens[$food])){
                        if (!in_array($contain, $allergens[$food])) {
                            $allergens[$food][] = $contain;
                        }
                    } else {
                        $allergens[$food] = [$contain];
                    }
                }
            }
//            dump(json_encode($allergens));
        }
        return $allergens;
    }

    protected function init22()
    {
        $i = null;
        foreach ($this->sourceData as $str) {
            if (empty($str)) {
                continue;
            }
            if (preg_match('/Player (.*):/', $str, $matches)) {
                $i = $matches[1] - 1;
            } else {
                $this->targetData[$i][] = intval($str);
            }
        }
    }

    protected function run22Part1()
    {
        [$player1, $player2] = $this->targetData;
        $data = $this->vsPlayer($player1, $player2);
        $total = $this->calculateCardResultPoints($data['result']);
        return $total; // 32856
    }

    protected function run22Part2()
    {
        [$player1, $player2] = $this->targetData;
        $data = $this->vsPlayer2($player1, $player2);
        $total = $this->calculateCardResultPoints($data['result']);
        return $total; // 33805
    }

    protected function calculateCardResultPoints(array $arr)
    {
        $result = array_reverse($arr);
        $total = 0;
        foreach ($result as $i => $v) {
            $total += ($v * ($i + 1));
        }
        return $total;
    }

    protected function vsPlayer($player1, $player2)
    {
        $times = 0;
        while (count($player1) && count($player2)) {
            $first1 = array_shift($player1);
            $first2 = array_shift($player2);
            if ($first1 >= $first2) {
                array_push($player1, $first1, $first2);
            } else {
                array_push($player2, $first2, $first1);
            }
            $times++;
        }
        $data = [
            'times' => $times,
        ];
        if (count($player1)) {
            $data['winner'] = 1;
            $data['result'] = $player1;
        } else {
            $data['winner'] = 2;
            $data['result'] = $player2;
        }
        return $data;
    }

    protected function vsPlayer2($player1, $player2): array
    {
        $player1Cards = [];
        $player2Cards = [];

        while (count($player1) && count($player2)) {
            $temp1 = implode(',', $player1);
            $temp2 = implode(',', $player2);
            if (in_array($temp1, $player1Cards) || in_array($temp2, $player2Cards)) {
                return ['winner' => 1, 'result' => $player1];
            }
            $player1Cards[] = $temp1;
            $player2Cards[] = $temp2;

            $first1 = array_shift($player1);
            $first2 = array_shift($player2);

            if ($first1 <= count($player1) && $first2 <= count($player2)) {
                $new1 = array_slice($player1, 0, $first1);
                $new2 = array_slice($player2, 0, $first2);
                $result = $this->vsPlayer2($new1, $new2);
                if ($result['winner'] == 1) {
                    array_push($player1, $first1, $first2);
                } else {
                    array_push($player2, $first2, $first1);
                }
            } else {
                if ($first1 >= $first2) {
                    array_push($player1, $first1, $first2);
                } else {
                    array_push($player2, $first2, $first1);
                }
            }
        }

        if (count($player1)) {
            return ['winner' => 1, 'result' => $player1];
        } else {
            return ['winner' => 2, 'result' => $player2];
        }
    }

    protected function init23()
    {
        $this->targetData = str_split($this->sourceData[0]);
    }

    protected function run23Part1()
    {
        $arr = $this->targetData;
        $final = $this->moveCups($arr, 100);
        $index = array_search(1, $final);
        $temp = $this->fixCupsLocation($final, count($final) - $index);
        $data = array_slice($temp, 1);
        $num = implode('', $data);
        return intval($num); // 24798635
    }

    protected function run23Part2()
    {
        $arr = $this->targetData;
        $arr = [3, 8, 9, 1, 2, 5, 4, 6, 7];
//        for ($i = 10; $i <= 1000000; $i++) {
//            $arr[] = $i;
//        }
        $data = [];
        for ($i = 0; $i <= 10000; $i++) {
            $final = $this->moveCups($arr, $i);
            $temp = json_encode($final);
            dump("$i  " . $temp);
            if (!in_array($temp, $data)) {
                $data[] = $temp;
            }
        }
        dd($temp);
    }

    protected function moveCups(array $arr, int $num = 10)
    {
        $times = 1;
        $length = count($arr);
        $offset = 0;

        while ($times <= $num) {
            $arr = $this->clockwiseCups($arr, $offset);
            $offset++;
            if ($offset >= $length) {
                $offset = 0;
            }
            $times++;
        }

        return $arr;
    }

    protected function clockwiseCups(array $arr, int $currentIndex)
    {
        $length = count($arr);

        // current cup
        $currentValue = $arr[$currentIndex];
        $threeIndex = ($currentIndex + 3) % $length;
        // pick up three cups
        if ($threeIndex < 3) {
            $threePath1 = $currentIndex + 1 >= $length ? [] : array_slice($arr, $currentIndex + 1);
            $threePath2 = array_slice($arr, 0, 3 - count($threePath1));
            $three = array_merge($threePath1, $threePath2);
        } else {
            $three = array_slice($arr, $currentIndex + 1, 3);
        }
        // others cups
        $notPicks = array_diff($arr, $three, [$currentValue]);
        // destination
        $destinationValue = null;
        for ($i = $currentValue - 1; $i > 0; $i--) {
            if (!in_array($i, $three)) {
                $destinationValue = $i;
                break;
            }
        }
        if (empty($destinationValue)) {
            $destinationValue = max($notPicks);
        }
        $destinationIndex = array_search($destinationValue, $arr);
        // new arr
        if ($destinationIndex >= $currentIndex) {
            $one = array_slice($arr, 0, $currentIndex + 1);
            $two = array_slice($arr, $currentIndex + 4, $destinationIndex - $currentIndex - 3);
            $four = array_slice($arr, $destinationIndex + 1);
            $data = array_merge($one, $two, $three, $four);
        } else {
            $one = $currentIndex + 4 < $length ? array_slice($arr, $currentIndex + 4) : [];
            $start = $threeIndex < 3 ? $threeIndex + 1 : 0;
            $two = array_slice($arr, $start, $destinationIndex - $start + 1);
            $four = array_slice($arr, $destinationIndex + 1,$currentIndex - $destinationIndex - 1);
            $data = array_merge([$currentValue], $one, $two, $three, $four);
            $data = $this->fixCupsLocation($data, $currentIndex);
        }
        return $data;
    }

    protected function fixCupsLocation(array $arr, int $index)
    {
        $length = count($arr);
        $data = [];
        for ($i = 0; $i < $length; $i++) {
            $key = ($i + $index) % $length;
            $data[$key] = $arr[$i];
        }
        ksort($data);
        return $data;
    }

    protected function moveCups2(array $arr, int $num = 10000000)
    {
        $times = 1;
        $length = count($arr);
        $currentIndex = 0;
        do {
            // current cup
            $currentValue = $currentIndex < $length ? $arr[$currentIndex] : $currentIndex;
            $threeIndex = ($currentIndex + 3) % $length;
            // pick up three cups
            if ($threeIndex < 3) {
                $threePath1 = $currentIndex + 1 >= $length ? [] : array_slice($arr, $currentIndex + 1);
                $threePath2 = array_slice($arr, 0, 3 - count($threePath1));
                $three = array_merge($threePath1, $threePath2);
            } else {
                $three = array_slice($arr, $currentIndex + 1, 3);
            }
            // others cups
            $notPicks = array_diff($arr, $three, [$currentValue]);
            // destination
            $destinationValue = null;
            for ($i = $currentValue - 1; $i > 0; $i--) {
                if (!in_array($i, $three)) {
                    $destinationValue = $i;
                    break;
                }
            }
            if (empty($destinationValue)) {
                $destinationValue = max($notPicks);
            }
            $destinationIndex = array_search($destinationValue, $arr);
            // new arr
            if ($destinationIndex >= $currentIndex) {
                $one = array_slice($arr, 0, $currentIndex + 1);
                $two = array_slice($arr, $currentIndex + 4, $destinationIndex - $currentIndex - 3);
                $four = array_slice($arr, $destinationIndex + 1);
                $data = array_merge($one, $two, $three, $four);
            } else {
                $one = $currentIndex + 4 < $length ? array_slice($arr, $currentIndex + 4) : [];
                $start = $threeIndex < 3 ? $threeIndex + 1 : 0;
                $two = array_slice($arr, $start, $destinationIndex - $start + 1);
                $four = array_slice($arr, $destinationIndex + 1,$currentIndex - $destinationIndex - 1);
                $data = array_merge([$currentValue], $one, $two, $three, $four);
                $data = $this->fixCupsLocation($data, $currentIndex);
            }

            $arr = $data;

            $currentIndex++;
            if ($currentIndex >= 1000000) {
                $currentIndex = $currentIndex % 1000000;
            }
            $times++;
        } while ($times <= $num);

        return $arr;
    }
}