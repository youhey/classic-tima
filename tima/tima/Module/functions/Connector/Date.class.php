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
 * 年月日を結合
 * 
 * @package    tima
 * @subpackage tima_Connector
 * @version    SVN: $Id: Date.class.php 38 2007-10-16 06:43:01Z do_ikare $
 */
class Connector_Date extends Connector_AbstractConnector
{

    /**
     * 日付の連動配列を結合して文字列で返却
     * 
     * 配列を書式化した日付文字列に結合する
     * 日付情報に不足があればデフォルト値で補完する
     * 
     * 引数「$params」の値で動作を制御
     * - 文字列の書式（sprintf()関数のフォーマット）
     * 
     * @param  array      $attribute
     * @param  array|null $params
     * @return string
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        $format     = '%1$s年%2$s月%3$s日';
        if (($param = array_shift($params)) !== null) {
            $format = $param;
        }

        $year  = isset($attribute['year'])  ? $attribute['year']  : '';
        $month = isset($attribute['month']) ? $attribute['month'] : '';
        $day   = isset($attribute['day'])   ? $attribute['day']   : '';

        $date = '';
        if (($year !== '') && ($month !== '') && ($day !== '')) {
            $date = sprintf($format, $year, $month, $day);
        }

        return $date;
    }
}
