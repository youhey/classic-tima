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
 * 郵便番号を結合
 * 
 * @package    tima
 * @subpackage tima_Connector
 * @version    SVN: $Id: Postcode.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Connector_Postcode extends Connector_AbstractConnector
{

    /**
     * 郵便番号の連想配列を結合して文字列で返却
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
        // $format = '〒%1$s-%2$s';
        $format = '%1$s-%2$s';
        if (($param  = array_shift($params)) !== null) {
            $format = (string)$param;
        }

        $specific = false;
        $first    = '';
        $last     = '';
        foreach (array_keys($attribute) as $varkey) {
            switch (strtolower($varkey)) {
            case 'first' : 
                $specific = true;
                $first    = trim((string)$attribute[$varkey]);
                break;
            case 'last' : 
                $specific = true;
                $last     = trim((string)$attribute[$varkey]);
                break;
            }
        }
        if ($specific !== true) {
            if (($varvalue = array_shift($attribute)) !== null) {
                $first = trim((string)$varvalue);
            }
            if (($varvalue = array_shift($attribute)) !== null) {
                $last = trim((string)$varvalue);
            }
        }

        $postcode = '';
        if (($first !== '') && ($last !== '')) {
            $postcode = sprintf($format, $first, $last);
        }

        return $postcode;
    }
}
