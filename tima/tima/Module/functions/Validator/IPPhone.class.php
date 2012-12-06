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

/* 一般的なIPフォンの番号の正規表現 */
define('VALIDATE_IP_PHONE_JP', "(?:050-(?:\d{3}-\d{5}|\d{4}-\d{4}))");

/**
 * 文字列が一般的なIPフォンの番号のとして正しいかを検証
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: IPPhone.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Validator_IPPhone extends Validator_AbstractValidator
{

    /**
     * 文字列が一般的なIPフォンの番号のとして正しいかを検証
     * 
     * IPフォンの番号の形式は日本国内の番号に限定
     * 
     * チェックする電話番号の種類
     * - IPフォン (050)
     *
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        return 
            (bool)preg_match('/^'. VALIDATE_IP_PHONE_JP . '$/', $attribute);
    }
}
