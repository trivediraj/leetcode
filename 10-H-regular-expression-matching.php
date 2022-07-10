<?php
class Solution {

    /**
     * @param String $s
     * @param String $p
     * @return Boolean
     */
    function isMatch($s, $p) {
        $sLen = strlen($s);
        $pLen = strlen($p);
        if ($pLen < 1) return false;
        $dp[$sLen][$pLen] = true;
        for ($i = $sLen; $i >= 0; $i--){
            for ($j = $pLen - 1; $j >= 0; $j--){
                $first_match = ($i < $sLen && (substr($p, $j, 1) == substr($s, $i, 1) || substr($p, $j, 1) == '.'));

                if ($j + 1 < $pLen && substr($p, $j+1, 1) == '*'){
                    $dp[$i][$j] = $dp[$i][$j+2] || $first_match && $dp[$i+1][$j];
                } else {
                    $dp[$i][$j] = $first_match && $dp[$i+1][$j+1];
                }
            }
        }
        return $dp[0][0];
        //return return preg_match('/(^'. $p .'$)/i', $s);;
    }
}

$solution = new Solution();
//$returnArr = $solution->isMatch("b","*?*?");
// $returnArr = $solution->isMatch("","*a*");
// $returnArr = $solution->isMatch("","***");
$returnArr = $solution->isMatch("aab","c*a*b");
echo "<pre>";
print_r($returnArr);
echo "</pre>";
?>