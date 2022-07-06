<?php
$start_time = microtime(true);
class Solution {

    /**
     * @param String $s
     * @param String $p
     * @return Boolean
     */
    function isMatch($s, $p) {
        $sLen = strlen($s);
        $pLen = strlen($p);
        //echo "XX===".str_replace("*", '', $p). "===XX";
        if($sLen <= 1 && (substr($p, 0, 1) == '*' && strlen(str_replace("*", '', $p)) == 0)) return true;
        if ($s === $p) return true;
        if (($s == '') || ($p == '')) return false;
        //if (substr($p, 0, 1) == '*' || ($sLen == 1 && $p == '?')) return true;

        $sIdx = $pIdx = 0;
        $starIdx = $sTmpIdx = -1;
        while ($sIdx < $sLen) {
            if ($pIdx < $pLen && (substr($p, $pIdx, 1) == '?' || substr($p, $pIdx, 1)  == substr($s, $sIdx, 1))) {
                // echo "IN1" . substr($p, $pIdx, 1);
                ++$sIdx;
                ++$pIdx;
            } else if ($pIdx < $pLen &&  substr($p, $pIdx, 1) == '*') {
                // If pattern character = '*'
                // echo "IN" . substr($p, $pIdx, 1);
                $starIdx = $pIdx;
                $sTmpIdx = $sIdx;
                ++$pIdx;
            } else if ($starIdx == -1) {
                // echo "IN2" . substr($p, $pIdx, 1);

                // If pattern character != string character
                // or pattern is used up
                // and there was no '*' character in pattern 
                return false;
            } else {
                // echo "IN3" . substr($p, $pIdx, 1);

                // If pattern character != string character
                // or pattern is used up
                // and there was '*' character in pattern before
                $pIdx = $starIdx + 1;
                $sIdx = $sTmpIdx + 1;
                $sTmpIdx = $sIdx;
            }
        }

        for ($i = $pIdx; $i < $pLen; $i++) {
            if (substr($p, $i, 1) != '*') {
                return false;
            }
        }
        return true;

        //return preg_match("/^" . preg_replace("/\*+/", ".*" ,str_replace("?", ".", $p)) . "$/U", $s);
    }
}

$solution = new Solution();
$returnArr = $solution->isMatch("b","*?*?");
// $returnArr = $solution->isMatch("","*a*");
// $returnArr = $solution->isMatch("","***");

echo "<pre>";
print_r($returnArr);
echo "</pre>";

$execution_time = ($end_time - $start_time);
echo " Execution time of script = " . $execution_time . " sec";
?>

<h2> 44. Wildcard Matching </h2>
<hr>

<div>
    <p>Given an input string (<code>s</code>) and a pattern (<code>p</code>), implement wildcard pattern matching with support for <code>'?'</code> and <code>'*'</code> where:</p>

    <ul>
        <li><code>'?'</code> Matches any single character.</li>
        <li><code>'*'</code> Matches any sequence of characters (including the empty sequence).</li>
    </ul>

    <p>The matching should cover the <strong>entire</strong> input string (not partial).</p>

    <p>&nbsp;</p>
    <p><strong>Example 1:</strong></p>

    <pre><strong>Input:</strong> s = "aa", p = "a"
<strong>Output:</strong> false
<strong>Explanation:</strong> "a" does not match the entire string "aa".
</pre>

    <p><strong>Example 2:</strong></p>

    <pre><strong>Input:</strong> s = "aa", p = "*"
<strong>Output:</strong> true
<strong>Explanation:</strong>&nbsp;'*' matches any sequence.
</pre>

    <p><strong>Example 3:</strong></p>

    <pre><strong>Input:</strong> s = "cb", p = "?a"
<strong>Output:</strong> false
<strong>Explanation:</strong>&nbsp;'?' matches 'c', but the second letter is 'a', which does not match 'b'.
</pre>

    <p>&nbsp;</p>
    <p><strong>Constraints:</strong></p>

    <ul>
        <li><code>0 &lt;= s.length, p.length &lt;= 2000</code></li>
        <li><code>s</code> contains only lowercase English letters.</li>
        <li><code>p</code> contains only lowercase English letters, <code>'?'</code> or <code>'*'</code>.</li>
    </ul>
</div>


<div>
    <h2>Solution</h2>
    <hr>
    <h4 id="approach-1-recursion-with-memoization">Approach 1: Recursion with Memoization</h4>
    <p><strong>Intuition</strong></p>
    <p>The first idea here is a recursion. It is a relatively straightforward
        approach but quite time consuming because of huge recursion depth for long input strings.</p>
    <ul>
        <li>
            <p>If the strings are equal (<code>p == s</code>), then return <code>True</code>.</p>
        </li>
        <li>
            <p>If the pattern matches any string (<code>p == '*'</code>), then return <code>True</code>.</p>
        </li>
        <li>
            <p>If <code>p</code> is empty, or <code>s</code> is empty, return <code>False</code>.</p>
        </li>
        <li>
            <p>If the current characters match (<code>p[0] == s[0]</code> or <code>p[0] == '?'</code>),
                then compare the next ones and return <code>isMatch(s[1:], p[1:])</code>.</p>
        </li>
        <li>
            <p>If the current pattern character is a star (<code>p[0] == '*'</code>), then
                there are two possible situations:</p>
            <ul>
                <li>
                    <p>The star matches no characters, and hence the answer is
                        <code>isMatch(s, p[1:])</code>.
                    </p>
                </li>
                <li>
                    <p>The star matches one or more characters, and so the answer is
                        <code>isMatch(s[1:], p)</code>.
                    </p>
                </li>
            </ul>
        </li>
        <li>
            <p>If <code>p[0] != s[0]</code>, return <code>False</code>.</p>
        </li>
    </ul>
    <p><img src="../Figures/44/stupid.png" alt="pic"></p>
    <p>The problem of this algorithm is that it doesn't pass
        all test cases because of time limit issue,
        and hence has to be optimised.
        Here is what could be done:</p>
    <ol>
        <li>
            <p><em>Memoization</em>. That is a standard way to optimise the recursion.
                Let's have a memoization hashmap using pair <code>(s, p)</code> as a key and
                match/doesn't match as a boolean value.
                One could keep all already checked pairs <code>(s, p)</code> in this hashmap, so that
                if there are any duplicate checks, the answer is right here,
                and there is no need to proceed to the computations again.</p>
        </li>
        <li>
            <p><em>Clean up of the input data</em>. Whether the patterns with multiple stars
                in a row <code>a****bc**cc</code> are valid wildcards or not, they could be
                simplified without any data loss to <code>a*bc*cc</code>. Such a cleanup helps to decrease
                the recursion depth.</p>
        </li>
    </ol>
    <p><strong>Algorithm</strong></p>
    <p>Here is the algorithm.</p>
    <ul>
        <li>
            <p>Clean up the input by replacing more than one star in a row by a single star:
                <code>p = remove_duplicate_stars(p)</code>.
            </p>
        </li>
        <li>
            <p>Initiate the memoization hashmap <code>dp</code>.</p>
        </li>
        <li>
            <p>Return the helper function with a cleaned input: <code>helper(s, p)</code>.</p>
        </li>
        <li>
            <p><code>helper(s, p)</code>:</p>
            <ul>
                <li>
                    <p>If <code>(s, p)</code> is already known and stored in <code>dp</code>, return the value.</p>
                </li>
                <li>
                    <p>If the strings are equal (<code>p == s</code>), or the pattern matches any string (<code>p == '*'</code>),
                        add <code>dp[(s, p)] = True</code>.</p>
                </li>
                <li>
                    <p>Else if <code>p</code> is empty, or <code>s</code> is empty, add <code>dp[(s, p)] = False</code>.</p>
                </li>
                <li>
                    <p>Else if the current characters match (<code>p[0] == s[0]</code> or <code>p[0] == '?'</code>),
                        then compare the next ones and add <code>dp[(s, p)] = helper(s[1:], p[1:])</code>.</p>
                </li>
                <li>
                    <p>Else if the current pattern character is a star (<code>p[0] == '*'</code>), then
                        there are two possible situations: the star matches no characters,
                        and the star matches one or more characters:
                        <code>dp[(s, p)] = helper(s, p[1:]) or helper(s[1:], p)</code>.
                    </p>
                </li>
                <li>
                    <p>Else <code>p[0] != s[0]</code>, then add <code>dp[(s, p)] = False</code>.</p>
                </li>
                <li>
                    <p>Now when the value is computed, return it: <code>dp[(s, p)]</code>.</p>
                </li>
            </ul>
        </li>
    </ul>
    <p><strong>Implementation</strong></p>
    <iframe src="https://leetcode.com/playground/YN36qKvr/shared" frameborder="0" width="100%" height="500" name="YN36qKvr"></iframe>
    <p><strong>Complexity Analysis</strong></p>
    <ul>
        <li>
            <p>Time complexity: <span class="katex"><span class="katex-mathml"><math>
                            <semantics>
                                <mrow>
                                    <mi>O</mi>
                                    <mo>(</mo>
                                    <mi>S</mi>
                                    <mo>⋅</mo>
                                    <mi>P</mi>
                                    <mo>⋅</mo>
                                    <mo>(</mo>
                                    <mi>S</mi>
                                    <mo>+</mo>
                                    <mi>P</mi>
                                    <mo>)</mo>
                                    <mo>)</mo>
                                </mrow>
                                <annotation encoding="application/x-tex">O(S \cdot P \cdot (S + P))</annotation>
                            </semantics>
                        </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span><span class="mclose">)</span></span></span></span></p>
            <ul>
                <li>
                    <p>Removing duplicate stars requires us to traverse the string <code>p</code> once, this requires <span class="katex"><span class="katex-mathml"><math>
                                    <semantics>
                                        <mrow>
                                            <mi>O</mi>
                                            <mo>(</mo>
                                            <mi>P</mi>
                                            <mo>)</mo>
                                        </mrow>
                                        <annotation encoding="application/x-tex">O(P)</annotation>
                                    </semantics>
                                </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span> time.</p>
                </li>
                <li>
                    <p>Regarding the helper function, every non-memoized recursive call we will:</p>
                    <ol>
                        <li>
                            <p>Check if <code>helper(s, p)</code> has already been calculated. This takes <span class="katex"><span class="katex-mathml"><math>
                                            <semantics>
                                                <mrow>
                                                    <mi>O</mi>
                                                    <mo>(</mo>
                                                    <mi>S</mi>
                                                    <mo>+</mo>
                                                    <mi>P</mi>
                                                    <mo>)</mo>
                                                </mrow>
                                                <annotation encoding="application/x-tex">O(S + P)</annotation>
                                            </semantics>
                                        </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span> time to create a hash of the tuple <code>(s, p)</code> the first time and <span class="katex"><span class="katex-mathml"><math>
                                            <semantics>
                                                <mrow>
                                                    <mi>O</mi>
                                                    <mo>(</mo>
                                                    <mn>1</mn>
                                                    <mo>)</mo>
                                                </mrow>
                                                <annotation encoding="application/x-tex">O(1)</annotation>
                                            </semantics>
                                        </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord">1</span><span class="mclose">)</span></span></span></span> time to check if the result has already been cached.</p>
                        </li>
                        <li>
                            <p>Go through our if statements. If <code>(s, p)</code> is one of the base cases, this will take <span class="katex"><span class="katex-mathml"><math>
                                            <semantics>
                                                <mrow>
                                                    <mi>O</mi>
                                                    <mo>(</mo>
                                                    <mi>m</mi>
                                                    <mi>i</mi>
                                                    <mi>n</mi>
                                                    <mo>(</mo>
                                                    <mi>S</mi>
                                                    <mo separator="true">,</mo>
                                                    <mi>P</mi>
                                                    <mo>)</mo>
                                                    <mo>)</mo>
                                                </mrow>
                                                <annotation encoding="application/x-tex">O(min(S, P))</annotation>
                                            </semantics>
                                        </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault">m</span><span class="mord mathdefault">i</span><span class="mord mathdefault">n</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mpunct">,</span><span class="mspace" style="margin-right:0.16666666666666666em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span><span class="mclose">)</span></span></span></span> time for the string equality check or just <span class="katex"><span class="katex-mathml"><math>
                                            <semantics>
                                                <mrow>
                                                    <mi>O</mi>
                                                    <mo>(</mo>
                                                    <mn>1</mn>
                                                    <mo>)</mo>
                                                </mrow>
                                                <annotation encoding="application/x-tex">O(1)</annotation>
                                            </semantics>
                                        </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord">1</span><span class="mclose">)</span></span></span></span> time for other checks, otherwise, it will take <span class="katex"><span class="katex-mathml"><math>
                                            <semantics>
                                                <mrow>
                                                    <mi>O</mi>
                                                    <mo>(</mo>
                                                    <mi>S</mi>
                                                    <mo>+</mo>
                                                    <mi>P</mi>
                                                    <mo>)</mo>
                                                </mrow>
                                                <annotation encoding="application/x-tex">O(S + P)</annotation>
                                            </semantics>
                                        </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span> time to create a substring <code>s[1:]</code> and a substring <code>p[1:]</code>. Here, let's assume the worst-case scenario where most of the non-memoized recursive calls require <span class="katex"><span class="katex-mathml"><math>
                                            <semantics>
                                                <mrow>
                                                    <mi>O</mi>
                                                    <mo>(</mo>
                                                    <mi>S</mi>
                                                    <mo>+</mo>
                                                    <mi>P</mi>
                                                    <mo>)</mo>
                                                </mrow>
                                                <annotation encoding="application/x-tex">O(S + P)</annotation>
                                            </semantics>
                                        </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span> time.</p>
                        </li>
                        <li>
                            <p>Then we will cache our result, which takes <span class="katex"><span class="katex-mathml"><math>
                                            <semantics>
                                                <mrow>
                                                    <mi>O</mi>
                                                    <mo>(</mo>
                                                    <mn>1</mn>
                                                    <mo>)</mo>
                                                </mrow>
                                                <annotation encoding="application/x-tex">O(1)</annotation>
                                            </semantics>
                                        </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord">1</span><span class="mclose">)</span></span></span></span> time since the hash for tuple <code>(s, p)</code> was already created when we checked if the result for <code>(s, p)</code> is already cached.</p>
                        </li>
                    </ol>
                    <p>So in total, we spend <span class="katex"><span class="katex-mathml"><math>
                                    <semantics>
                                        <mrow>
                                            <mi>O</mi>
                                            <mo>(</mo>
                                            <mn>2</mn>
                                            <mo>⋅</mo>
                                            <mo>(</mo>
                                            <mi>S</mi>
                                            <mo>+</mo>
                                            <mi>P</mi>
                                            <mo>)</mo>
                                            <mo>)</mo>
                                            <mo>=</mo>
                                            <mi>O</mi>
                                            <mo>(</mo>
                                            <mi>S</mi>
                                            <mo>+</mo>
                                            <mi>P</mi>
                                            <mo>)</mo>
                                        </mrow>
                                        <annotation encoding="application/x-tex">O(2 \cdot (S + P)) = O(S + P)</annotation>
                                    </semantics>
                                </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord">2</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span><span class="mclose">)</span><span class="mspace" style="margin-right:0.2777777777777778em;"></span><span class="mrel">=</span><span class="mspace" style="margin-right:0.2777777777777778em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span> time on every non-memoized call (<span class="katex"><span class="katex-mathml"><math>
                                    <semantics>
                                        <mrow>
                                            <mi>S</mi>
                                            <mo>+</mo>
                                            <mi>P</mi>
                                        </mrow>
                                        <annotation encoding="application/x-tex">S + P</annotation>
                                    </semantics>
                                </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.76666em;vertical-align:-0.08333em;"></span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span></span></span></span> for creating a hash and <span class="katex"><span class="katex-mathml"><math>
                                    <semantics>
                                        <mrow>
                                            <mi>S</mi>
                                            <mo>+</mo>
                                            <mi>P</mi>
                                        </mrow>
                                        <annotation encoding="application/x-tex">S + P</annotation>
                                    </semantics>
                                </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.76666em;vertical-align:-0.08333em;"></span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span></span></span></span> for creating substrings). We can only have as many non-memoized calls as there are combinations of <code>s</code> and <code>p</code>. Therefore, in the worst case, we can have <span class="katex"><span class="katex-mathml"><math>
                                    <semantics>
                                        <mrow>
                                            <mi>S</mi>
                                            <mo>⋅</mo>
                                            <mi>P</mi>
                                        </mrow>
                                        <annotation encoding="application/x-tex">S \cdot P</annotation>
                                    </semantics>
                                </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span></span></span></span> non-memoized calls. This gives us a total time spent on non-memoized calls of <span class="katex"><span class="katex-mathml"><math>
                                    <semantics>
                                        <mrow>
                                            <mi>O</mi>
                                            <mo>(</mo>
                                            <mi>S</mi>
                                            <mo>⋅</mo>
                                            <mi>P</mi>
                                            <mo>⋅</mo>
                                            <mo>(</mo>
                                            <mi>S</mi>
                                            <mo>+</mo>
                                            <mi>P</mi>
                                            <mo>)</mo>
                                            <mo>)</mo>
                                        </mrow>
                                        <annotation encoding="application/x-tex">O(S \cdot P \cdot (S + P))</annotation>
                                    </semantics>
                                </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span><span class="mclose">)</span></span></span></span>.</p>
                </li>
                <li>
                    <p>As for the memoized calls, for each non-memoized call, we can make at most 2 additional calls to <code>helper</code>. This means that there will be at most <span class="katex"><span class="katex-mathml"><math>
                                    <semantics>
                                        <mrow>
                                            <mi>S</mi>
                                            <mo>⋅</mo>
                                            <mi>P</mi>
                                        </mrow>
                                        <annotation encoding="application/x-tex">S \cdot P</annotation>
                                    </semantics>
                                </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span></span></span></span> memoized calls. Each memoized call takes <span class="katex"><span class="katex-mathml"><math>
                                    <semantics>
                                        <mrow>
                                            <mi>O</mi>
                                            <mo>(</mo>
                                            <mi>S</mi>
                                            <mo>+</mo>
                                            <mi>P</mi>
                                            <mo>)</mo>
                                        </mrow>
                                        <annotation encoding="application/x-tex">O(S + P)</annotation>
                                    </semantics>
                                </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span> time to create the hash for <code>(s, p)</code> and <span class="katex"><span class="katex-mathml"><math>
                                    <semantics>
                                        <mrow>
                                            <mi>O</mi>
                                            <mo>(</mo>
                                            <mn>1</mn>
                                            <mo>)</mo>
                                        </mrow>
                                        <annotation encoding="application/x-tex">O(1)</annotation>
                                    </semantics>
                                </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord">1</span><span class="mclose">)</span></span></span></span> time to get the cached result. So the total time spent on memoized calls is <span class="katex"><span class="katex-mathml"><math>
                                    <semantics>
                                        <mrow>
                                            <mi>O</mi>
                                            <mo>(</mo>
                                            <mi>S</mi>
                                            <mo>⋅</mo>
                                            <mi>P</mi>
                                            <mo>⋅</mo>
                                            <mo>(</mo>
                                            <mi>S</mi>
                                            <mo>+</mo>
                                            <mi>P</mi>
                                            <mo>)</mo>
                                            <mo>)</mo>
                                        </mrow>
                                        <annotation encoding="application/x-tex">O(S \cdot P \cdot (S + P))</annotation>
                                    </semantics>
                                </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span><span class="mclose">)</span></span></span></span> which is a loose upper bound.</p>
                </li>
                <li>
                    <p>Adding all 3 time complexities together we get: <span class="katex"><span class="katex-mathml"><math>
                                    <semantics>
                                        <mrow>
                                            <mi>O</mi>
                                            <mo>(</mo>
                                            <mi>P</mi>
                                            <mo>+</mo>
                                            <mn>2</mn>
                                            <mo>⋅</mo>
                                            <mi>S</mi>
                                            <mo>⋅</mo>
                                            <mi>P</mi>
                                            <mo>⋅</mo>
                                            <mo>(</mo>
                                            <mi>S</mi>
                                            <mo>+</mo>
                                            <mi>P</mi>
                                            <mo>)</mo>
                                            <mo>)</mo>
                                            <mo>=</mo>
                                            <mi>O</mi>
                                            <mo>(</mo>
                                            <mi>S</mi>
                                            <mo>⋅</mo>
                                            <mi>P</mi>
                                            <mo>⋅</mo>
                                            <mo>(</mo>
                                            <mi>S</mi>
                                            <mo>+</mo>
                                            <mi>P</mi>
                                            <mo>)</mo>
                                            <mo>)</mo>
                                        </mrow>
                                        <annotation encoding="application/x-tex">O(P + 2 \cdot S \cdot P \cdot (S + P)) = O(S \cdot P \cdot (S + P))</annotation>
                                    </semantics>
                                </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:0.64444em;vertical-align:0em;"></span><span class="mord">2</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span><span class="mclose">)</span><span class="mspace" style="margin-right:0.2777777777777778em;"></span><span class="mrel">=</span><span class="mspace" style="margin-right:0.2777777777777778em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span><span class="mclose">)</span></span></span></span>.</p>
                    <blockquote>
                        <p>Note: This approach can be optimized by using two pointers to track the current position on <code>s</code> and <code>p</code> instead of passing substrings of <code>s</code> and <code>p</code> as arguments. To improve readability, this was not implemented here, however, doing so will reduce the time complexity to <span class="katex"><span class="katex-mathml"><math>
                                        <semantics>
                                            <mrow>
                                                <mi>O</mi>
                                                <mo>(</mo>
                                                <mi>S</mi>
                                                <mo>⋅</mo>
                                                <mi>P</mi>
                                                <mo>)</mo>
                                            </mrow>
                                            <annotation encoding="application/x-tex">O(S \cdot P)</annotation>
                                        </semantics>
                                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span> since hashing two integers takes <span class="katex"><span class="katex-mathml"><math>
                                        <semantics>
                                            <mrow>
                                                <mi>O</mi>
                                                <mo>(</mo>
                                                <mn>1</mn>
                                                <mo>)</mo>
                                            </mrow>
                                            <annotation encoding="application/x-tex">O(1)</annotation>
                                        </semantics>
                                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord">1</span><span class="mclose">)</span></span></span></span> time and each recursive call to <code>helper</code> would no longer require creating new substrings which takes linear time. Thus the total time complexity is <span class="katex"><span class="katex-mathml"><math>
                                        <semantics>
                                            <mrow>
                                                <mi>O</mi>
                                                <mo>(</mo>
                                                <mn>1</mn>
                                                <mo>)</mo>
                                            </mrow>
                                            <annotation encoding="application/x-tex">O(1)</annotation>
                                        </semantics>
                                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord">1</span><span class="mclose">)</span></span></span></span> per call for a maximum of <span class="katex"><span class="katex-mathml"><math>
                                        <semantics>
                                            <mrow>
                                                <mi>S</mi>
                                                <mo>⋅</mo>
                                                <mi>P</mi>
                                            </mrow>
                                            <annotation encoding="application/x-tex">S \cdot P</annotation>
                                        </semantics>
                                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span></span></span></span> non-memoized calls and <span class="katex"><span class="katex-mathml"><math>
                                        <semantics>
                                            <mrow>
                                                <mi>S</mi>
                                                <mo>⋅</mo>
                                                <mi>P</mi>
                                            </mrow>
                                            <annotation encoding="application/x-tex">S \cdot P</annotation>
                                        </semantics>
                                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span></span></span></span> memoized calls.</p>
                    </blockquote>
                </li>
            </ul>
        </li>
        <li>
            <p>Space complexity: <span class="katex"><span class="katex-mathml"><math>
                            <semantics>
                                <mrow>
                                    <mi>O</mi>
                                    <mo>(</mo>
                                    <mi>S</mi>
                                    <mo>⋅</mo>
                                    <mi>P</mi>
                                    <mo>)</mo>
                                </mrow>
                                <annotation encoding="application/x-tex">O(S \cdot P)</annotation>
                            </semantics>
                        </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span>. Creating a new string <code>p</code> requires <span class="katex"><span class="katex-mathml"><math>
                            <semantics>
                                <mrow>
                                    <mi>O</mi>
                                    <mo>(</mo>
                                    <mi>P</mi>
                                    <mo>)</mo>
                                </mrow>
                                <annotation encoding="application/x-tex">O(P)</annotation>
                            </semantics>
                        </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span> space. The recursion call stack may exceed <code>max(S, P)</code> in cases such as <code>(s, p)</code> = <code>(aaab, *a*b)</code>, however, it is bounded by <span class="katex"><span class="katex-mathml"><math>
                            <semantics>
                                <mrow>
                                    <mi>O</mi>
                                    <mo>(</mo>
                                    <mi>S</mi>
                                    <mo>+</mo>
                                    <mi>P</mi>
                                    <mo>)</mo>
                                </mrow>
                                <annotation encoding="application/x-tex">O(S + P)</annotation>
                            </semantics>
                        </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span>. Lastly, the hashmap requires <span class="katex"><span class="katex-mathml"><math>
                            <semantics>
                                <mrow>
                                    <mi>O</mi>
                                    <mo>(</mo>
                                    <mi>S</mi>
                                    <mo>⋅</mo>
                                    <mi>P</mi>
                                    <mo>)</mo>
                                </mrow>
                                <annotation encoding="application/x-tex">O(S \cdot P)</annotation>
                            </semantics>
                        </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span> space to memoize the result of each call to <code>helper</code>.
                <br>
                <br>
            </p>
        </li>
    </ul>
    <hr>
    <h4 id="approach-2-dynamic-programming">Approach 2: Dynamic Programming</h4>
    <p><strong>Intuition</strong></p>
    <p>Recursion approach above shows how painful the large recursion depth could be,
        so let's try something more iterative.</p>
    <p>Memoization from the first approach gives an idea to try a dynamic programming.
        The problem is very similar with <a href="https://leetcode.com/problems/edit-distance/solution/">Edit Distance problem</a>,
        so let's use exactly the same approach here.</p>
    <p>The idea would be to reduce the problem to simple ones.
        For example, there is a string <code>adcebdk</code> and pattern <code>*a*b?k</code>,
        and we want to compute if there is a match for them: <code>D = True/False</code>.
        One could notice that it seems to be more simple for short strings and patterns
        and so it would be logical to relate a match <code>D[p_len][s_len]</code> with the lengths <code>p_len</code>
        and <code>s_len</code> of input pattern and string correspondingly.</p>
    <p>Let's go further and introduce a match <code>D[p_idx][s_idx]</code>
        which is a match between the first <code>p_idx</code> characters of the pattern
        and the first <code>s_idx</code> characters of the string.</p>
    <p><img src="../Figures/44/dp_match2_fixed.png" alt="pic"></p>
    <p>It turns out that one could compute <code>D[p_idx][s_idx]</code>, knowing
        a match without the last characters <code>D[p_idx - 1][s_idx - 1]</code>.</p>
    <p>If the last characters are the same or pattern character is '?', then</p>
    <blockquote>
        <p><span class="katex"><span class="katex-mathml"><math>
                        <semantics>
                            <mrow>
                                <mi>D</mi>
                                <mo>[</mo>
                                <msub>
                                    <mi>p</mi>
                                    <mrow>
                                        <mi>i</mi>
                                        <mi>d</mi>
                                        <mi>x</mi>
                                    </mrow>
                                </msub>
                                <mo>]</mo>
                                <mo>[</mo>
                                <msub>
                                    <mi>s</mi>
                                    <mrow>
                                        <mi>i</mi>
                                        <mi>d</mi>
                                        <mi>x</mi>
                                    </mrow>
                                </msub>
                                <mo>]</mo>
                                <mo>=</mo>
                                <mi>D</mi>
                                <mo>[</mo>
                                <msub>
                                    <mi>p</mi>
                                    <mrow>
                                        <mi>i</mi>
                                        <mi>d</mi>
                                        <mi>x</mi>
                                    </mrow>
                                </msub>
                                <mo>−</mo>
                                <mn>1</mn>
                                <mo>]</mo>
                                <mo>[</mo>
                                <msub>
                                    <mi>s</mi>
                                    <mrow>
                                        <mi>i</mi>
                                        <mi>d</mi>
                                        <mi>x</mi>
                                    </mrow>
                                </msub>
                                <mo>−</mo>
                                <mn>1</mn>
                                <mo>]</mo>
                                <mspace width="2em"></mspace>
                                <mo>(</mo>
                                <mn>1</mn>
                                <mo>)</mo>
                            </mrow>
                            <annotation encoding="application/x-tex">D[p_{idx}][s_{idx}] = D[p_{idx} - 1][s_{idx} - 1] \qquad (1)</annotation>
                        </semantics>
                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">D</span><span class="mopen">[</span><span class="mord"><span class="mord mathdefault">p</span><span class="msupsub"><span class="vlist-t vlist-t2"><span class="vlist-r"><span class="vlist" style="height:0.33610799999999996em;"><span style="top:-2.5500000000000003em;margin-left:0em;margin-right:0.05em;"><span class="pstrut" style="height:2.7em;"></span><span class="sizing reset-size6 size3 mtight"><span class="mord mtight"><span class="mord mathdefault mtight">i</span><span class="mord mathdefault mtight">d</span><span class="mord mathdefault mtight">x</span></span></span></span></span><span class="vlist-s">​</span></span><span class="vlist-r"><span class="vlist" style="height:0.15em;"><span></span></span></span></span></span></span><span class="mclose">]</span><span class="mopen">[</span><span class="mord"><span class="mord mathdefault">s</span><span class="msupsub"><span class="vlist-t vlist-t2"><span class="vlist-r"><span class="vlist" style="height:0.33610799999999996em;"><span style="top:-2.5500000000000003em;margin-left:0em;margin-right:0.05em;"><span class="pstrut" style="height:2.7em;"></span><span class="sizing reset-size6 size3 mtight"><span class="mord mtight"><span class="mord mathdefault mtight">i</span><span class="mord mathdefault mtight">d</span><span class="mord mathdefault mtight">x</span></span></span></span></span><span class="vlist-s">​</span></span><span class="vlist-r"><span class="vlist" style="height:0.15em;"><span></span></span></span></span></span></span><span class="mclose">]</span><span class="mspace" style="margin-right:0.2777777777777778em;"></span><span class="mrel">=</span><span class="mspace" style="margin-right:0.2777777777777778em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">D</span><span class="mopen">[</span><span class="mord"><span class="mord mathdefault">p</span><span class="msupsub"><span class="vlist-t vlist-t2"><span class="vlist-r"><span class="vlist" style="height:0.33610799999999996em;"><span style="top:-2.5500000000000003em;margin-left:0em;margin-right:0.05em;"><span class="pstrut" style="height:2.7em;"></span><span class="sizing reset-size6 size3 mtight"><span class="mord mtight"><span class="mord mathdefault mtight">i</span><span class="mord mathdefault mtight">d</span><span class="mord mathdefault mtight">x</span></span></span></span></span><span class="vlist-s">​</span></span><span class="vlist-r"><span class="vlist" style="height:0.15em;"><span></span></span></span></span></span></span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">−</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord">1</span><span class="mclose">]</span><span class="mopen">[</span><span class="mord"><span class="mord mathdefault">s</span><span class="msupsub"><span class="vlist-t vlist-t2"><span class="vlist-r"><span class="vlist" style="height:0.33610799999999996em;"><span style="top:-2.5500000000000003em;margin-left:0em;margin-right:0.05em;"><span class="pstrut" style="height:2.7em;"></span><span class="sizing reset-size6 size3 mtight"><span class="mord mtight"><span class="mord mathdefault mtight">i</span><span class="mord mathdefault mtight">d</span><span class="mord mathdefault mtight">x</span></span></span></span></span><span class="vlist-s">​</span></span><span class="vlist-r"><span class="vlist" style="height:0.15em;"><span></span></span></span></span></span></span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">−</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord">1</span><span class="mclose">]</span><span class="mspace" style="margin-right:2em;"></span><span class="mopen">(</span><span class="mord">1</span><span class="mclose">)</span></span></span></span></p>
    </blockquote>
    <p><img src="../Figures/44/word_match3.png" alt="pic"></p>
    <p>If the pattern character is '*' and there was a match on the previous step
        <code>D[p_idx - 1][s_idx - 1] = True</code>, then
    </p>
    <ul>
        <li>
            <p>The star at the end of pattern still results in a match.</p>
        </li>
        <li>
            <p>The star could match as many characters as you wish.</p>
        </li>
    </ul>
    <blockquote>
        <p><span class="katex"><span class="katex-mathml"><math>
                        <semantics>
                            <mrow>
                                <mi>D</mi>
                                <mo>[</mo>
                                <msub>
                                    <mi>p</mi>
                                    <mrow>
                                        <mi>i</mi>
                                        <mi>d</mi>
                                        <mi>x</mi>
                                    </mrow>
                                </msub>
                                <mo>−</mo>
                                <mn>1</mn>
                                <mo>]</mo>
                                <mo>[</mo>
                                <mi>i</mi>
                                <mo>]</mo>
                                <mo>=</mo>
                                <mtext>True</mtext>
                                <mo separator="true">,</mo>
                                <mi>i</mi>
                                <mo>≥</mo>
                                <msub>
                                    <mi>s</mi>
                                    <mrow>
                                        <mi>i</mi>
                                        <mi>d</mi>
                                        <mi>x</mi>
                                    </mrow>
                                </msub>
                                <mo>−</mo>
                                <mn>1</mn>
                                <mspace width="2em"></mspace>
                                <mo>(</mo>
                                <mn>2</mn>
                                <mo>)</mo>
                            </mrow>
                            <annotation encoding="application/x-tex">D[p_{idx} - 1][i] = \textrm{True}, i \ge s_{idx} - 1 \qquad(2)</annotation>
                        </semantics>
                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">D</span><span class="mopen">[</span><span class="mord"><span class="mord mathdefault">p</span><span class="msupsub"><span class="vlist-t vlist-t2"><span class="vlist-r"><span class="vlist" style="height:0.33610799999999996em;"><span style="top:-2.5500000000000003em;margin-left:0em;margin-right:0.05em;"><span class="pstrut" style="height:2.7em;"></span><span class="sizing reset-size6 size3 mtight"><span class="mord mtight"><span class="mord mathdefault mtight">i</span><span class="mord mathdefault mtight">d</span><span class="mord mathdefault mtight">x</span></span></span></span></span><span class="vlist-s">​</span></span><span class="vlist-r"><span class="vlist" style="height:0.15em;"><span></span></span></span></span></span></span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">−</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord">1</span><span class="mclose">]</span><span class="mopen">[</span><span class="mord mathdefault">i</span><span class="mclose">]</span><span class="mspace" style="margin-right:0.2777777777777778em;"></span><span class="mrel">=</span><span class="mspace" style="margin-right:0.2777777777777778em;"></span></span><span class="base"><span class="strut" style="height:0.8777699999999999em;vertical-align:-0.19444em;"></span><span class="mord text"><span class="mord textrm">True</span></span><span class="mpunct">,</span><span class="mspace" style="margin-right:0.16666666666666666em;"></span><span class="mord mathdefault">i</span><span class="mspace" style="margin-right:0.2777777777777778em;"></span><span class="mrel">≥</span><span class="mspace" style="margin-right:0.2777777777777778em;"></span></span><span class="base"><span class="strut" style="height:0.73333em;vertical-align:-0.15em;"></span><span class="mord"><span class="mord mathdefault">s</span><span class="msupsub"><span class="vlist-t vlist-t2"><span class="vlist-r"><span class="vlist" style="height:0.33610799999999996em;"><span style="top:-2.5500000000000003em;margin-left:0em;margin-right:0.05em;"><span class="pstrut" style="height:2.7em;"></span><span class="sizing reset-size6 size3 mtight"><span class="mord mtight"><span class="mord mathdefault mtight">i</span><span class="mord mathdefault mtight">d</span><span class="mord mathdefault mtight">x</span></span></span></span></span><span class="vlist-s">​</span></span><span class="vlist-r"><span class="vlist" style="height:0.15em;"><span></span></span></span></span></span></span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">−</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord">1</span><span class="mspace" style="margin-right:2em;"></span><span class="mopen">(</span><span class="mord">2</span><span class="mclose">)</span></span></span></span></p>
    </blockquote>
    <p>So each step of the computation would be done based on the previous ones,
        as follows:</p>
    <p><img src="../Figures/44/if_match.png" alt="pic"></p>
    <p><img src="../Figures/44/dpstar.png" alt="pic"></p>
    <p><strong>Algorithm</strong></p>
    <ul>
        <li>
            <p>Start from the table <code>D</code> filled with <code>False</code> everywhere but <code>D[0][0] = True</code>.</p>
        </li>
        <li>
            <p>Apply rules (1) and (2) in a loop and return <code>D[p_len][s_len]</code> as an answer.</p>
        </li>
    </ul>
    <p><img src="../Figures/44/fixed.png" alt="pic"></p>
    <p><strong>Implementation</strong></p>
    <iframe src="https://leetcode.com/playground/KwWWkXwr/shared" frameborder="0" width="100%" height="500" name="KwWWkXwr"></iframe>
    <p><strong>Complexity Analysis</strong></p>
    <ul>
        <li>Time complexity: <span class="katex"><span class="katex-mathml"><math>
                        <semantics>
                            <mrow>
                                <mi>O</mi>
                                <mo>(</mo>
                                <mi>S</mi>
                                <mo>⋅</mo>
                                <mi>P</mi>
                                <mo>)</mo>
                            </mrow>
                            <annotation encoding="application/x-tex">O(S \cdot P)</annotation>
                        </semantics>
                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span> where <span class="katex"><span class="katex-mathml"><math>
                        <semantics>
                            <mrow>
                                <mi>S</mi>
                            </mrow>
                            <annotation encoding="application/x-tex">S</annotation>
                        </semantics>
                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span></span></span></span> and <span class="katex"><span class="katex-mathml"><math>
                        <semantics>
                            <mrow>
                                <mi>P</mi>
                            </mrow>
                            <annotation encoding="application/x-tex">P</annotation>
                        </semantics>
                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span></span></span></span> are lengths of
            the input string and the pattern respectively.</li>
        <li>Space complexity: <span class="katex"><span class="katex-mathml"><math>
                        <semantics>
                            <mrow>
                                <mi>O</mi>
                                <mo>(</mo>
                                <mi>S</mi>
                                <mo>⋅</mo>
                                <mi>P</mi>
                                <mo>)</mo>
                            </mrow>
                            <annotation encoding="application/x-tex">O(S \cdot P)</annotation>
                        </semantics>
                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span> to store the matrix.
            <br>
            <br>
        </li>
    </ul>
    <hr>
    <h4 id="approach-3-backtracking">Approach 3: Backtracking</h4>
    <p><strong>Intuition</strong></p>
    <p>Complexity <span class="katex"><span class="katex-mathml"><math>
                    <semantics>
                        <mrow>
                            <mi>O</mi>
                            <mo>(</mo>
                            <mi>S</mi>
                            <mo>⋅</mo>
                            <mi>P</mi>
                            <mo>)</mo>
                        </mrow>
                        <annotation encoding="application/x-tex">O(S \cdot P)</annotation>
                    </semantics>
                </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span> is much better than <span class="katex"><span class="katex-mathml"><math>
                    <semantics>
                        <mrow>
                            <mi>O</mi>
                            <mo>(</mo>
                            <mi>S</mi>
                            <mo>⋅</mo>
                            <mi>P</mi>
                            <mo>⋅</mo>
                            <mo>(</mo>
                            <mi>S</mi>
                            <mo>+</mo>
                            <mi>P</mi>
                            <mo>)</mo>
                            <mo>)</mo>
                        </mrow>
                        <annotation encoding="application/x-tex">O(S \cdot P \cdot (S + P))</annotation>
                    </semantics>
                </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">+</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span><span class="mclose">)</span></span></span></span>,
        but still could be improved. There is no need to compute the entire matrix,
        i.e., to check all the possibilities for each star:</p>
    <ul>
        <li>Star matches zero characters.</li>
        <li>Star matches one character.</li>
        <li>Star matches two characters.</li>
    </ul>
    <p>...</p>
    <ul>
        <li>Star matches all remaining characters.</li>
    </ul>
    <p>Let's just pick up the first opportunity "matches zero characters" and proceed further.
        If this assumption would lead in "no match" situation, then <em>backtrack</em>: come back
        to the previous star, assume now that it matches one more character (one) and
        proceed again. Again "no match" situation?
        <em>Backtrack again</em>: come back to the previous star,
        and assume now that it matches one more character (two), etc.
    </p>
    <p><img src="../Figures/44/backtrack.png" alt="pic"></p>
    <p><strong>Algorithm</strong></p>
    <p>Here is the algorithm.</p>
    <ul>
        <li>
            <p>Let's use two pointers here: <code>s_idx</code> to iterate over the string, and <code>p_idx</code> to
                iterate over the pattern. While <code>s_idx &lt; s_len</code>:</p>
            <ul>
                <li>
                    <p>If there are still characters in the pattern (<code>p_idx &lt; p_len</code>) and
                        the characters under the pointers match
                        (<code>p[p_idx] == s[s_idx]</code> or <code>p[p_idx] == '?'</code>),
                        then move forward by increasing both pointers.</p>
                </li>
                <li>
                    <p>Otherwise, if there are still characters in the pattern (<code>p_idx &lt; p_len</code>), and
                        <code>p[p_idx] == '*'</code>, then first check "match zero characters" situation, i.e.,
                        increase only pattern pointer <code>p_idx++</code>.
                        Write down for a possible backtrack the star position in <code>star_idx</code> variable,
                        and the current string pointer in <code>s_tmp_idx</code> variable.
                    </p>
                </li>
                <li>
                    <p>Else if there is "no match" situation:
                        the pattern is used up <code>p_idx &lt; p_len</code>
                        or the characters under the pointers doesn't match.</p>
                    <ul>
                        <li>
                            <p>If there was no stars in the pattern, i.e., no <code>star_idx</code>, return <code>False</code>.</p>
                        </li>
                        <li>
                            <p>If there was a star, then backtrack: set pattern pointer
                                just after the last star <code>p_idx = star_idx + 1</code>, and string
                                pointer <code>s_idx = s_tmp_idx + 1</code>, i.e., assume that this time the star
                                matches <em>one more character</em>. Save the current string pointer
                                for the possible backtrack <code>s_tmp_idx = s_idx</code>.</p>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
        <li>
            <p>Return <code>True</code> if all remaining characters in the pattern are stars.</p>
        </li>
    </ul>
    <p><strong>Implementation</strong></p>
    <iframe src="https://leetcode.com/playground/K3Pd2N7F/shared" frameborder="0" width="100%" height="500" name="K3Pd2N7F"></iframe>
    <p><strong>Complexity Analysis</strong></p>
    <ul>
        <li>Time complexity: <span class="katex"><span class="katex-mathml"><math>
                        <semantics>
                            <mrow>
                                <mi>O</mi>
                                <mo>(</mo>
                                <mi>min</mi>
                                <mo>⁡</mo>
                                <mo>(</mo>
                                <mi>S</mi>
                                <mo separator="true">,</mo>
                                <mi>P</mi>
                                <mo>)</mo>
                                <mo>)</mo>
                            </mrow>
                            <annotation encoding="application/x-tex">O(\min(S, P))</annotation>
                        </semantics>
                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mop">min</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mpunct">,</span><span class="mspace" style="margin-right:0.16666666666666666em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span><span class="mclose">)</span></span></span></span> for the best case and
            better than <span class="katex"><span class="katex-mathml"><math>
                        <semantics>
                            <mrow>
                                <mi>O</mi>
                                <mo>(</mo>
                                <mi>S</mi>
                                <mi>log</mi>
                                <mo>⁡</mo>
                                <mi>P</mi>
                                <mo>)</mo>
                            </mrow>
                            <annotation encoding="application/x-tex">O(S \log P)</annotation>
                        </semantics>
                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.16666666666666666em;"></span><span class="mop">lo<span style="margin-right:0.01389em;">g</span></span><span class="mspace" style="margin-right:0.16666666666666666em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span> for the average case, where <span class="katex"><span class="katex-mathml"><math>
                        <semantics>
                            <mrow>
                                <mi>S</mi>
                            </mrow>
                            <annotation encoding="application/x-tex">S</annotation>
                        </semantics>
                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span></span></span></span> and <span class="katex"><span class="katex-mathml"><math>
                        <semantics>
                            <mrow>
                                <mi>P</mi>
                            </mrow>
                            <annotation encoding="application/x-tex">P</annotation>
                        </semantics>
                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:0.68333em;vertical-align:0em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span></span></span></span> are lengths of
            the input string and the pattern correspondingly.
            Please refer to <a href="https://arxiv.org/pdf/1407.0950.pdf" rel="ugc">this article</a> for detailed proof. However, in the worst-case scenario, this algorithm requires <span class="katex"><span class="katex-mathml"><math>
                        <semantics>
                            <mrow>
                                <mi>O</mi>
                                <mo>(</mo>
                                <mi>S</mi>
                                <mo>⋅</mo>
                                <mi>P</mi>
                                <mo>)</mo>
                            </mrow>
                            <annotation encoding="application/x-tex">O(S \cdot P)</annotation>
                        </semantics>
                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord mathdefault" style="margin-right:0.05764em;">S</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span><span class="mbin">⋅</span><span class="mspace" style="margin-right:0.2222222222222222em;"></span></span><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.13889em;">P</span><span class="mclose">)</span></span></span></span> time.</li>
        <li>Space complexity: <span class="katex"><span class="katex-mathml"><math>
                        <semantics>
                            <mrow>
                                <mi>O</mi>
                                <mo>(</mo>
                                <mn>1</mn>
                                <mo>)</mo>
                            </mrow>
                            <annotation encoding="application/x-tex">O(1)</annotation>
                        </semantics>
                    </math></span><span class="katex-html" aria-hidden="true"><span class="base"><span class="strut" style="height:1em;vertical-align:-0.25em;"></span><span class="mord mathdefault" style="margin-right:0.02778em;">O</span><span class="mopen">(</span><span class="mord">1</span><span class="mclose">)</span></span></span></span> since it's a constant space solution.
            <br>
            <br>
        </li>
    </ul>
    <hr>
    <h4 id="further-reading">Further reading</h4>
    <p>There are a lot of search-related questions around this problem
        which could pop up during the interview.
        To prepare, you could read about <a href="https://en.wikipedia.org/wiki/String-searching_algorithm" rel="ugc">string searching algorithm</a>
        and <a href="https://en.wikipedia.org/wiki/Knuth%E2%80%93Morris%E2%80%93Pratt_algorithm" rel="ugc">KMP algorithm</a>.</p>
</div>