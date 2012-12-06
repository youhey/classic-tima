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
 * @version    SVN: $Id: StandardLF.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Filter_StandardLF
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
            $request->set($varname, $this->_standardizeLF($varvalue));
        }
    }

    /**
     * 改行文字を「\n」に統一する
     *
     * @params mixed $attribute
     * @return string|array
     * @access public
     */
    function _standardizeLF($attribute)
    {

        if (!is_array($attribute)) {
            return
                str_replace(
                    array("\r\n", "\n\r", "\r", "\n"), 
                    "\n", 
                    $attribute);
        }

        return array_map(array(&$this, __FUNCTION__), $attribute);
    }
}
