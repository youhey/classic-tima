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
 * 文字列の数式を評価して計算結果（整数|実数）を算出
 * 
 * @package    tima
 * @subpackage tima_Converter
 * @version    SVN: $Id: Math.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Converter_Math extends Converter_AbstractConverter
{

    /**
     * 文字列の数式を評価して計算結果（整数|実数）を算出
     * 
     * 変換例
     * - ( 100 - 50 ) => 50
     * - ( (2 * 15 - 50) * 2.5 ) => -50
     * - ( (1<<10) * 10 ) => 10240
     * 
     * @param  string $attribute
     * @return string
     * @access protected
     */
    function doFunction($attribute)
    {
        $formula = preg_replace('/([^+\-*=\/\(\)\d\^<>&|\.]*)/', '', $attribute);
        if (empty($formula)) {
            $formula = '0';
        }
        $result = (string)@eval('return ' . $formula . ';');
        if ($result === '') {
            $result = '0';
        }

        return $result;
    }
}
