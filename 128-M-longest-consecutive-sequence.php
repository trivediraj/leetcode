<?php
class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer
     */
    function longestConsecutive($nums) {
        $longest = (empty($nums)) ? 0 : 1;
        sort($nums);
        $currentStreak = 1;
        for($i=1; $i<count($nums); $i++){
            if (($nums[$i]) === $nums[$i-1] +1 ) {
                $currentStreak++;
            }else if(($nums[$i]) === $nums[$i-1]){
               // Nothing;
            }else{
                $currentStreak = 1;
            }
            $longest = max($currentStreak, $longest);
        }

        // foreach ($nums as $num) {
        //     if (($current + 1)=== $num) {
        //         echo $currentStreak++;
        //     } else{         
        //         $currentStreak = 1;
        //     }
        //     $current = $num;
        // }
        return $longest;
    }
}

$solution = new Solution();
$returnArr = $solution->longestConsecutive(array(1,2,0,1));

echo "<pre>";
print_r($returnArr);
echo "</pre>";
?>