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
 * 時間の連動配列を結合して文字列で返却
 * 
 * @package    tima
 * @subpackage tima_Connector
 * @version    SVN: $Id: Time.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Connector_Time extends Connector_AbstractConnector
{

    /**
     * 時間の連動配列を結合して文字列で返却
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
        $format = '%1$s時%2$s分%3$s秒';
        if (($param = array_shift($params)) !== null) {
            $format = (string)$param;
        }

        $specific = false;
        $hour     = '00';
        $min      = '00';
        $sec      = '00';
        foreach (array_keys($attribute) as $varkey) {
            switch (strtolower($varkey)) {
            case 'hour' : 
            case 'hr' : 
                $specific = true;
                $hour     = trim((string)$attribute[$varkey]);
                break;
            case 'minute' : 
            case 'min' : 
                $specific = true;
                $min      = trim((string)$attribute[$varkey]);
                break;
            case 'second' : 
            case 'sec' : 
                $specific = true;
                $sec      = trim((string)$attribute[$varkey]);
                break;
            }
        }
        if ($specific !== true) {
            foreach (array('hour', 'min', 'sec') as $varkey) {
                if (($varvalue = array_shift($attribute)) !== null) {
                    $$varkey = trim((string)$varvalue);
                }
            }
        }

        $time = '';
        if (($hour !== '') && ($min !== '') && ($sec !== '')) {
            $time = sprintf($format, $hour, $min, $sec);
        }

        return $time;
    }
}
