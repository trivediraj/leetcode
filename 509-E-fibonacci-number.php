<?php
class Solution {

    /**
     * @param Integer $n
     * @return Integer
     */
    /*function fib($n) {
        if($n <= 1){ return 1;}
        $goldenRatio = (1 + sqrt(5)) / 2;
        echo round(pow($goldenRatio, $n)/ sqrt(5));
        return (int)round(pow($goldenRatio, $n)/ sqrt(5));
    }*/

    /*function fib($n) {
        if($n <= 1){ return 1;}
        $temp = array();
        $temp[1] = 1;
        for ($i = 2; $i <= $n; $i++) {
            $temp[$i] = $temp[$i - 1] + $temp[$i - 2];
        }
        return $temp[$n];
    }*/

    function fib($n) {
        if($n <= 1){ return 1;}
        $current = 0;
        $prev1 = 0;
        $prev2 = 1;
        for ($i = 2; $i <= $n; $i++) {
            $current = $prev1 + $prev2;
            $prev1 = $prev2;
            $prev2 = $current;
        }
        return $current;
    }
}

$solution = new Solution();
$returnArr = $solution->fib(8);

echo "<pre>";
print_r($returnArr);
echo "</pre>";

?>

