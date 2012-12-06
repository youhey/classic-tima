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

/**
 * 日時と評価可能な文字列を固定の日付書式「YYYY-MM-DD」に変換
 * 
 * @package    tima
 * @subpackage tima_Converter
 * @version    SVN: $Id: Date.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Converter_Date extends Converter_AbstractConverter
{

    /**
     * 日時と評価可能な文字列を固定の日付書式「YYYY-MM-DD」に変換
     * 
     * 日付としての評価する文字列の例
     * - YYYY-MM-DD => 2007-01-05
     * - YY-MM-DD => 07-01-05
     * - YYYY-M-D => 2007-1-5
     * - YY-M-D => 07-1-5
     * - YYYY/MM/DD => 2007/01/05
     * - YY/MM/DD => 07/01/05
     * - YYYY/M/D => 2007/1/5
     * - YY/M/D => 07/1/5
     * - YYYY.MM.DD => 2007.01.05
     * - YY.MM.DD => 07.01.05
     * - YYYY.M.D => 2007.1.5
     * - YY.M.D => 07.1.5
     * - YYYYMMDD => 20070105
     * 
     * 文字列が日時として評価できなければ固定で「1970-01-01」を返却
     * 年数が省略されて二桁の場合には以下のように評価
     * - 年の値が50以下 => 2000年代と評価して「2000」を加算
     * - 年の値が50以上 => 1900年代と評価して「1900」を加算
     * 
     * @param  string $attribute
     * @return string
     * @access protected
     */
    function doFunction($attribute)
    {
        $hyphen_regex  = '!^(\d{2,4})-(\d{1,2})-(\d{1,2})$!';
        $slash_regex   = '!^(\d{2,4})/(\d{1,2})/(\d{1,2})$!';
        $dot_regex     = '!^(\d{2,4})\.(\d{1,2})\.(\d{1,2})$!';
        $connect_regex = '!^(\d{4})(\d{2})(\d{2})$!';

        switch (true) {
        case (bool) preg_match($hyphen_regex, $attribute, $matches) : 
        case (bool) preg_match($slash_regex, $attribute, $matches) : 
        case (bool) preg_match($dot_regex, $attribute, $matches) : 
        case (bool) preg_match($connect_regex, $attribute, $matches) : 
            $year  = (int) $matches[1];
            $month = (int) $matches[2];
            $day   = (int) $matches[3];
            break;
        default : 
            return '1970-01-01';
        }

        if ($year < 100) {
            $year += (($year < 50) ? 2000 : 1900);
        }

        return 
            sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
