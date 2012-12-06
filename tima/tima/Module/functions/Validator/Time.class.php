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

/* 時間パターンの正規表現 */
define(
    'VALIDATE_TIME_FORMAT', 
    '(?:(\d{1,2}):(\d{1,2}):(\d{1,2})|' . 
       '(\d{1,2}):(\d{1,2})|' . 
       '(\d{1,2})時(\d{1,2})分(\d{1,2})秒|' . 
       '(\d{1,2})時(\d{1,2})分)');

/**
 * 文字列が時間として正しいかを検証
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: Time.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Validator_Time extends Validator_AbstractValidator
{

    /**
     * 文字列が時間として正しいかを検証
     * 
     * 時間として評価する書式は以下
     * - hh:mm:ss
     * - hh:mm
     * - hh時mm分ss秒
     * - hh時mm分
     * 
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        // 時間情報を分解
        preg_match('/^' . VALIDATE_TIME_FORMAT . '$/iD', $attribute, $match);
        $time = array();
        for ($i = 1, $n = count($match); $i < $n; ++ $i) {
            if ($match[$i] === '') {
                continue;
            }
            $time[] = $match[$i];
        }
        $hour = array_shift($time);
        $min  = array_shift($time);
        $sec  = array_shift($time);

        return (
            ($hour !== null) && ($hour >= 0) && ($hour <= 24) && 
            ($min !== null) && ($min >= 0) && ($min <= 60) && 
            (($sec === null) || (($sec  >= 0) && ($sec  <= 60))));
    }
}
