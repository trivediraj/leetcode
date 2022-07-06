<?php
class Solution {

    /**
     * @param String $ransomNote
     * @param String $magazine
     * @return Boolean
     */

    function canConstruct($ransomNote, $magazine) {
        $noteChar = count_chars($ransomNote, 1);
        $magChar = count_chars($magazine, 1);
        
        foreach ($noteChar as $key=>$item) {
            if (!array_key_exists($key, $magChar)) {
                return false;
            }
            if ($item > $magChar[$key]) {
                return false;
            }
        }
        
        return true;
    }

}

$solution = new Solution();
$returnArr = $solution->canConstruct("aa", "abb");

echo "<pre>";
print_r($returnArr);
echo "</pre>";
?>