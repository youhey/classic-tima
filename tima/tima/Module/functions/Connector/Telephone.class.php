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
 * @version    SVN: $Id: Telephone.class.php 38 2007-10-16 06:43:01Z do_ikare $
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
        $area  = isset($attribute['area'])  ? $attribute['area']  : '';
        $city  = isset($attribute['city'])  ? $attribute['city']  : '';
        $local = isset($attribute['local']) ? $attribute['local'] : '';

        $telephone = '';
        if (($area !== '') && ($city !== '') && ($local !== '')) {
            $telephone = "${area}-${city}-${local}";
        }

        return $telephone;
    }
}
