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

/* EUC-JP限定：全角空白と「ー」を含むカタカナのマルチバイト正規表現 */
define('VALIDATE_MB_KATAKANA_EUC', "(?:[\xA5\xA1-\xA5\xF6]|\xA1\xBC|\xA1\xA1)");

/**
 * 文字列がカタカナのみで構成されているかを検証
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: Katakana.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Validator_Katakana extends Validator_AbstractValidator
{

    /**
     * 文字列がカタカナのみで構成されているかを検証
     * 
     * 厳密にはカタカナではないが「ー」を許容
     * 運用上の都合から全角空白を許容
     * 複数行に対応（パターン修飾子の"m"ではなく改行削除）
     * 
     * 引数「$params」の値で動作を制御
     * - 文字列の文字エンコーディング（EUC-JP以外の場合に必要）
     * 
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     * @see    Utility::isMatch()
     */
    function doFunction($attribute, $params)
    {
        $mb_charset = 'EUC-JP';
        if (($param = array_shift($params)) !== null) {
            $mb_charset = (string)$param;
        }
        if ($mb_charset !== 'EUC-JP') {
            $attribute = mb_convert_encoding($attribute, 'EUC-JP', $mb_charset);
        }

        return 
            Utility::isMatch(
                '^' . VALIDATE_MB_KATAKANA_EUC . '+$', 
                str_replace(array("\r\n", "\n", "\r"), '', $attribute), 
                'EUC-JP');
    }
}
