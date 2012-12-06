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
 * 文字列があること（空白文字以外が1文字以上あること）を検証
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: Required.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Validator_Required extends Validator_AbstractValidator
{

    /**
     * 文字列があること（空白文字以外が1文字以上あること）を検証
     * 
     * 文字数をカウントして、結果が一文字以上であれば真を返す
     * 日本語環境を前提に、文字列のカウントにはmb関数を使用
     * 
     * 引数「$params」の値で動作を制御
     * - カウントから除外する文字（デフォルトは改行とタブと半角スペース）
     *  - 除外処理とは別にtrim()関数で前後の空白を取り除きます
     * 
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        $excepts = array("\r\n", "\n", "\r", "\t", ' ');
        if (is_array($params) && !empty($params)) {
            $excepts = $params;
        }

        return 
            (mb_strlen(
                str_replace($excepts, '', trim($attribute)), 
                $this->module->getInternalEncoding()) > 0);
    }
}
