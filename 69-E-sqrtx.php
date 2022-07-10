<?php
class Solution {

    /**
     * @param Integer $x
     * @return Integer
     */
    function mySqrt($x) {
        if($x < 2) return $x;
        $left = 2;
        $right = $x / 2;
        while($left <= $right){
            $mid = (int) ($left + ($right - $left)/2);
            $numberFound = $mid * $mid;
            if($numberFound == $x) return $mid;
            else if($numberFound > $x) $right = $mid -1;
            else $left = $mid +1; 
        }        
        return intval($right);
    }
}

$solution = new Solution();
$returnArr = $solution->mySqrt(8);

echo "<pre>";
print_r($returnArr);
echo "</pre>";
?>