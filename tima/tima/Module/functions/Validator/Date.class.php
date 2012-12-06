<?php

/**
 * The tiny modules for web application
 * - PHP versions 4 -
 * 
 * @category  web application framework
 * @package   tima
 * @author    IKEDA Youhey <youhey.ikeda@gmail.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright 2007 IKEDA Youhey
 *     Licensed under the Apache License, Version 2.0 (the "License"); 
 *     you may not use this file except in compliance with the License. 
 *     You may obtain a copy of the License at 
 *         http://www.apache.org/licenses/LICENSE-2.0 
 *     Unless required by applicable law or agreed to in writing, software 
 *     distributed under the License is distributed on an "AS IS" BASIS, 
 *     WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. 
 *     See the License for the specific language governing permissions and 
 *     limitations under the License.
 * @version  1.0.0
 */

/* 日付パターンの正規表現 */
define(
    'VALIDATE_DATE_FORMAT', 
    '(?:' . 
        '(\d{2}|\d{4})\/(\d{1,2})\/(\d{1,2})|' . 
        '(\d{2}|\d{4})\.(\d{1,2})\.(\d{1,2})|' . 
        '(\d{2}|\d{4})-(\d{1,2})-(\d{1,2})|' . 
        '(\d{2}|\d{4}) (\d{1,2}) (\d{1,2})|' . 
        '(\d{2}|\d{4})(\d{2})(\d{2})|' . 
        '(\d{2}|\d{4})年(\d{1,2})月(\d{1,2})日' . 
        ')');

/**
 * 文字列が日付として正しいかを検証
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: Date.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Validator_Date extends Validator_AbstractValidator
{

    /**
     * 文字列が日付として正しいかを検証
     * 
     * 日付として評価する書式は以下
     * - YYYY-MM-DD
     * - YYYY/MM/DD
     * - YYYY.MM.DD
     * - YYYY年MM月DD日
     * - YY-MM-DD
     * - YY/MM/DD
     * - YY.MM.DD
     * - YY年MM月DD日
     * - YYYYMMDD
     * - YYMMDD
     * 
     * <del>年の値が100未満なら上位の桁が欠損と判断して補正</del>
     * - <del> 1〜49 => +2000</del>
     * - <del>50〜99 => +1900</del>
     * - 検証として不正確なのでこの処理は中止
     * 
     * 引数「$params」の値で動作を制御
     * - 許容する年の最小値（デフォルトは「1900年」）
     * - 許容する年の最大値（デフォルトは「2038年」）
     * 
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        $min = 1900;
        $max = 2038;
        if (($param = array_shift($params)) !== null) {
            $min = (int)$param;
        }
        if (($param = array_shift($params)) !== null) {
            $max = (int)$param;
        }

        // 日付情報を年月日に分解
        preg_match('/^' . VALIDATE_DATE_FORMAT . '$/iD', $attribute, $match);
        $date = array();
        for ($i = 1, $n = count($match); $i < $n; ++ $i) {
            if ($match[$i] === '') {
                continue;
            }
            $date[] = $match[$i];
        }
        $year  = (int)array_shift($date);
        $month = (int)array_shift($date);
        $day   = (int)array_shift($date);

        // 年数の補完処理は中止
        // if (($year > 0) && ($year < 100)) {
        //      $year += (($year > 50) ? 1900 : 2000);
        // }

        return 
            (checkdate($month, $day, $year) && ($year >= $min) && ($year <= $max));
    }
}
