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
 * リクエストの値をシンプルな文字列型にキャストするフィルタ
 * 
 * @package    tima
 * @subpackage tima_Filter
 * @version    SVN: $Id: RealString.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Filter_RealString
{

    /**
     * リクエストの値を文字列型にキャスト
     *
     * @params Front $front
     * @return void
     * @access public
     */
    function before(&$front)
    {
        $logger = &$front->getLogger();
        $logger->trace(__CLASS__ . "の前処理を実行");

        $request = &$front->getRequest();
        foreach ($request->getAll() as $varname => $varvalue) {
            $request->set($varname, $this->_convert2String($varvalue));
        }
    }

    /**
     * 値を全て文字列型にキャストする
     * 
     * キャストするのは末端の値だけで配列の構造などは保持する
     * またマジック・クォートが有効ならstripslashes()関数でバックスラッシュ除去する
     *
     * @params mixed $attribute
     * @return string|array
     * @access public
     */
    function _convert2String($attribute)
    {
        static $magic_quotes_gpc;
        if (!isset($magic_quotes_gpc)) {
            $magic_quotes_gpc = (bool)get_magic_quotes_gpc();
        }

        if (!is_array($attribute)) {
            $real_string = (string)$attribute;

            if ($magic_quotes_gpc) {
                $real_string = stripslashes($real_string);
            }

            return $real_string;
        }

        return array_map(array(&$this, __FUNCTION__), $attribute);
    }
}
