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
 * 文字列の数字として評価できる文字から整数を生成
 * 
 * @package    tima
 * @subpackage tima_Converter
 * @version    SVN: $Id: Integer.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Converter_Integer extends Converter_AbstractConverter
{

    /**
     * 文字列の数字として評価できる文字から整数を生成
     * 
     * 数字として評価できる文字の全てを変換するよう試みる
     * 評価できない文字列は全て「0」に変換
     * 
     * 変換例
     * - 123 => 123
     * - 123.9 => 123
     * - -123.9 => -124
     * - 123.5.0 => 0
     * - 0x123 => 291
     * - abc => 0
     * - 123a => 0
     * - a123 => 0
     * - １２３ => 123
     * - 1,230 => 1230
     * - 1,23 => 0
     * - +1230 => 1230
     * - -1230 => -1230
     * 
     * 使用するmb_convert_kana()関数のオプション
     * - a：全角英数字 => 半角英数字
     *      ※「"」「'」「\」「~」を除く以下の範囲を全角から半角に変換
     *      0020:   !   # $ % &   ( ) * + , - . /
     *      0030: 0 1 2 3 4 5 6 7 8 9 : ; < = > ?
     *      0040: @ A B C D E F G H I J K L M N O
     *      0050: P Q R S T U V W X Y Z [   ] ^ _
     *      0060: ` a b c d e f g h i j k l m n o
     *      0070: p q r s t u v w x y z { | }
     * - s：全角スペース => 半角スペース
     *
     * @param  string $attribute
     * @return string
     * @access protected
     */
    function doFunction($attribute)
    {
        $number = 
            mb_convert_kana($attribute, 'as', $this->module->getInternalEncoding());
        if (preg_match('/^[\+-]?([0-9]{1,3},)+[0-9]{3}(\.[0-9]*)?$/', $number)) {
            $number = str_replace(',', '', $number);
        }
        if (!is_numeric($number)) {
            $number = 0;
        }

        return 
            (string)floor($number + 0.0);
    }
}
