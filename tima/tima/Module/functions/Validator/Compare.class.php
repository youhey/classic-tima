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
 * 文字列が比較対象と一致するかを検証
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: Compare.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Validator_Compare extends Validator_AbstractValidator
{

    /**
     * 文字列が比較対象と一致するかを検証
     * 
     * 文字列の比較は大文字・小文字を区別し
     * 比較対象に複数の候補があれば、いずれかと一致すれば真
     * 比較対象がゼロであれば、一致を検証できないので偽
     * 
     * 引数「$params」の値で動作を制御
     * - 比較する文字列
     * 
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        $comparativist = array();
        if (is_array($params) && !empty($params)) {
            $comparativist = $params;
        }

        $concord = false;
        foreach ($comparativist as $comparison) {
            if (!is_string($comparison)) {
                continue;
            }
            if (strcmp($attribute, $comparison) === 0) {
                $concord = true;
                break;
            }
        }

        return $concord;
    }
}
