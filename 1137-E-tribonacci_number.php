<?php
class Solution {

    /**
     * @param Integer $n
     * @return Integer
     */
    function tribonacci($n) {
        if ($n <= 1) {
            return $n;
        }
        $temp = array();
        $temp[1] = 1;
        $temp[2] = 1;
        for ($i = 3; $i <= $n; $i++) {
            $temp[$i] = $temp[$i - 1] + $temp[$i - 2] + $temp[$i - 3];
        }
        return $temp[$n];
    }
}

$solution = new Solution();
$returnArr = $solution->tribonacci(25);

echo "<pre>";
print_r($returnArr);
echo "</pre>";

?>
