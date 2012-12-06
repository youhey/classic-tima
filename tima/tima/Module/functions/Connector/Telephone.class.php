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
 * 電話番号を結合
 * 
 * @package    tima
 * @subpackage tima_Connector
 * @version    SVN: $Id: Telephone.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Connector_Telephone extends Connector_AbstractConnector
{

    /**
     * 電話番号の連想配列を結合して文字列で返却
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

        $format = '%1$s-%2$s-%3$s';
        if (($param = array_shift($params)) !== null) {
            $format = (string)$param;
        }

        $specific = false;
        $area     = '';
        $city     = '';
        $local    = '';
        foreach (array_keys($attribute) as $varkey) {
            switch (strtolower($varkey)) {
            case 'area' : 
            case 'pref' : 
            case 'prefecture' : 
                $specific = true;
                $area     = trim((string)$attribute[$varkey]);
                break;
            case 'city' : 
                $specific = true;
                $city     = trim((string)$attribute[$varkey]);
                break;
            case 'local' : 
                $specific = true;
                $local    = trim((string)$attribute[$varkey]);
                break;
            }
        }
        if ($specific !== true) {
            if (($varvalue = array_shift($attribute)) !== null) {
                $area = trim((string)$varvalue);
            }
            if (($varvalue = array_shift($attribute)) !== null) {
                $city = trim((string)$varvalue);
            }
            if (($varvalue = array_shift($attribute)) !== null) {
                $local = trim((string)$varvalue);
            }
        }

        $telephone = '';
        if (($area !== '') && ($city !== '') && ($local !== '')) {
            $telephone = sprintf($format, $area, $city, $local);
        }

        return $telephone;
    }
}
