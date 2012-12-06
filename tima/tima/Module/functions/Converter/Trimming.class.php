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
 * 文字列の前後に存在するスペースを削除
 * 
 * @package    tima
 * @subpackage tima_Converter
 * @version    SVN: $Id: Trimming.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Converter_Trimming extends Converter_AbstractConverter
{

    /**
     * 文字列の前後に存在するスペースを削除
     * 
     * 前後から取り除く文字
     * - 制御文字（ASCII 0 〜 ASCII 31）
     * - 半角スペース（ASCII 32）
     * - 全角スペース（0xA10xA1）
     *
     * @param  string $attribute
     * @return string
     * @access protected
     */
    function doFunction($attribute)
    {
        // trim($attribute, "\x00..\x20");
        // trim($attribute, "\xA1\xA1");
        $pattern = array('/^(\xA1\xA1|[\x00-\x20])+/', '/(\xA1\xA1|[\x00-\x20])+$/');
        $result  = preg_replace($pattern, '', $attribute);

        return $result;
    }
}
