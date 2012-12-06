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

/* 数字文字列の正規表現 */
define('VALIDATE_NUMERIC', "(?:[+-]?(?:(?:\d{1,3},)+\d{3}|\d+)(?:\.\d*)?)");

/**
 * 文字列が数値として評価できるかを検証
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: Numeric.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Validator_Numeric extends Validator_AbstractValidator
{

    /**
     * 文字列が数値として評価できるかを検証
     * 
     * PHPが数値として評価できる値に準拠
     * - 整数（正／負）
     * - 書式化された数字（1,234,567）
     * - 浮動小数点数（正／負）
     * - 8進数（0123）
     * - 16進数（0xFF）
     * - 指数をもつ数字（+0123.45e6）
     *
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        if (preg_match('/^' . VALIDATE_NUMERIC . '$/', $attribute)) {
            $attribute = str_replace(',', '', $attribute);
        }

        return 
            is_numeric($attribute);
    }
}
