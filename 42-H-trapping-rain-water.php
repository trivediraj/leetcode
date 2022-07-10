<?php
class Solution {
    /**
     * @param Integer[] $height
     * @return Integer
     */
    function trap($height) {
        $right = count($height) - 1;
        $left = $ans = 0;
        $left_max = $right_max = 0;
        while ($left < $right) {
            echo "<br/>".$height[$left].'==='.$height[$right];
            if ($height[$left] < $height[$right]) {
                if( $height[$left] >= $left_max )
                    $left_max = $height[$left];
                else
                    $ans += ($left_max - $height[$left]);

                ++$left;
            } else {
                if( $height[$right] >= $right_max )
                    $right_max = $height[$right];
                else
                    $ans += ($right_max - $height[$right]);

                    --$right;
            }
        }
        return $ans;
    }
}
$solution = new Solution();
$returnArr = $solution->trap(array(0,1,0,2,1,0,1,3,2,1,2,1));

echo "<pre>";
print_r($returnArr);
echo "</pre>";
?>