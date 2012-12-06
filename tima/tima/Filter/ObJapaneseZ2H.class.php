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
 * 出力の「全角カタカナ」を「半角カタカナ」に変換
 * 
 * @package    tima
 * @subpackage tima_Filter
 * @version    SVN: $Id: ObJapaneseZ2H.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Filter_ObJapaneseZ2H
{
    /**
     * コンテンツの文字エンコーディング
     * 
     * @var    string
     * @access private
     */
    var $_encoding = '';

    /**
     * 出力の「全角カタカナ」を「半角カタカナ」に変換
     *
     * @params Front $front
     * @return void
     * @access public
     */
    function before(&$front)
    {
        $logger = &$front->getLogger();
        $logger->trace(__CLASS__ . "の前処理を実行");

        $this->_encoding = $front->getContentsEncoding();
        ob_start(array(&$this, 'invokeBuffer'));
    }

    /**
     * 出力バッファリング処理のコールバック関数
     *
     * @params string $buffer
     * @return string
     * @access public
     */
    function invokeBuffer($buffer)
    {
         return mb_convert_kana($buffer, 'k', $this->_encoding);
    }
}
