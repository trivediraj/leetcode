<?php
class Solution {

    /**
     * @param Integer[] $nums
     * @param Integer $target
     * @return Integer
     */
    function search($nums, $target) {
        $numsC = $right = count($nums)-1;
        $left = 0; 
        while($left <= $right){
            $mid = $left + round(($right - $left) / 2);
            if ($nums[$mid] == $target) return $mid;
            else if($nums[$mid] >= $nums[$left]){
                if(($target >= $nums[$left]) && $target < $nums[$mid]) $right = $mid -1;
                else $left = $mid + 1;
            }else{
                if(($target <= $nums[$right]) && $target > $nums[$mid])$left = $mid + 1;
                else $right = $mid - 1;
            }
        }

        return -1;
    }
}

$solution = new Solution();
// $nums = [4,5,6,7,0,1,2];
// $target = 0;

$nums = [5,1,3];
$target = 3;
$returnArr = $solution->search($nums, $target);

echo "<pre>";
print_r($returnArr);
echo "</pre>";
?>