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
 * 文字列をコールバック関数で検証
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: Callback.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Validator_Callback extends Validator_AbstractValidator
{

    /**
     * 文字列をコールバック関数で検証
     * 
     * 引数「$params」の値で動作を制御
     * - コールバック関数名
     *  - 指定されなければ偽を返却
     *  - 未定義の場合は偽を返却
     *  - コールバック関数で引数「$attribute」を評価
     *  - 評価結果が真の場合のみ真を返却
     *  - 論理値の真でなければ（整数「1」など）偽を返却
     *  - 副作用などは考慮しない
     * 
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        $function_name = '';
        if (($param = array_shift($params)) !== null) {
            $function_name = (string) $param;
        }
        if (($function_name === '') || !function_exists($function_name)) {
            return false;
        }

        return 
            (call_user_func($function_name, $attribute) === true);
    }
}
