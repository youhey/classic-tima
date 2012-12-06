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
 * 文字列が数値として評価でき、かつ値が範囲内であるかを検証
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: Range.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Validator_Range extends Validator_AbstractValidator
{

    /**
     * 文字列が数値として評価でき、かつ値が範囲内であるかを検証
     *
     * PHPの型変換の挙動を考慮して、型の変更はキャストではなく計算結果から求める
     * - (float)"0x12" => 0
     * - ("0x12" + 0.0) => 18.0
     * 
     * 引数「$params」の値で動作を制御
     * - 最小値（指定がなければ「0」）
     * - 最大値（指定がなければ「0」）
     * 
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     * @see    Module_Executant_Validator::is()
     * @see    Validator_Numeric::doFunction()
     */
    function doFunction($attribute, $params)
    {
        $min = 0.0;
        $max = 0.0;
        if (($param = array_shift($params)) !== null) {
            $min = ($param + 0.0);
        }
        if (($param = array_shift($params)) !== null) {
            $max = ($param + 0.0);
        }

        $result = false;

        if ($this->module->is('numeric', $attribute)) {
            $value  = ($attribute + 0.0);
            $result = (($value >= $min) && ($value <= $max));
        }

        return $result;
    }
}
