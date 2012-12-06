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
 * 電話番号（文字列）を分割して配列を生成
 * 
 * @package    tima
 * @subpackage tima_Arranger
 * @version    SVN: $Id: Telephone.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Arranger_Telephone extends Arranger_AbstractArranger
{

    /**
     * 電話番号の文字列を分割して配列で返却
     * 
     * 引数「$params」の値で動作を制御
     * - 市外局番のインデックス（指定がなければ「pref」）
     * - 市外局番のインデックス（指定がなければ「city」）
     * - 局番のインデックス（指定がなければ「local」）
     * - 分割する文字列（指定がなければ「-」）
     * 
     * @param  string     $attribute
     * @param  array|null $params
     * @return array
     * @access protected 
     */
    function doFunction($attribute, $params)
    {
        $pref_key  = 'pref';
        $city_key  = 'city';
        $local_key = 'local';
        $separator = '-';
        if (($param  = array_shift($params)) !== null) {
            $pref_key = (string)$param;
        }
        if (($param  = array_shift($params)) !== null) {
            $city_key = (string)$param;
        }
        if (($param  = array_shift($params)) !== null) {
            $local_key = (string)$param;
        }
        if (($param  = array_shift($params)) !== null) {
            $separator = (string)$param;
        }

        $matches = mb_split($separator, $attribute, 3);

        return 
            array(
                    $pref_key  => trim((string)array_shift($matches)), 
                    $city_key  => trim((string)array_shift($matches)), 
                    $local_key => trim((string)array_shift($matches)), 
                );
    }
}
