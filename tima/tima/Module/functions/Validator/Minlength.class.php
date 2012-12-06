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
 * 文字列の長さが下限値値を下回らないかを検証
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: Minlength.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Validator_Minlength extends Validator_AbstractValidator
{

    /**
     * 文字列の長さが下限値値を下回らないかを検証
     *
     * 引数「$params」の値で動作を制御
     * - 文字列長の下限値（指定がなければ「0」）
     *
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        $length = 0;
        if (($param = array_shift($params)) !== null) {
            $length = (int)$param;
        }

        return 
            (mb_strlen(
                str_replace(array("\r\n", "\n", "\r"), '', $attribute), 
                $this->module->getInternalEncoding()) >= $length);
    }
}
