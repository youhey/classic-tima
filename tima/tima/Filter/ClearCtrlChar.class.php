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
 * リクエストに含まれる制御文字を除去するフィルタ
 * 
 * @package    tima
 * @subpackage tima_Filter
 * @version    SVN: $Id: ClearCtrlChar.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Filter_ClearCtrlChar
{

    /**
     * リクエストに含まれる制御文字を除去して上書き
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
        foreach ($request->getAll() as $varkey => $varvalue) {
            if (is_array($varvalue)) {
                array_walk($varvalue, array(&$this, 'bridgeRecursive'));
            } else {
                $varvalue = Utility::to('EraseCtrlChar', $varvalue);
            }
            $request->set($varkey, $varvalue);
        }
    }

    /**
     * 配列を「array_walk()」関数で処理するためのコールバック関数
     * - 変換モジュールの「EraseCtrlChar」機能で値を変換
     * - 値は参照で受け取って値を直接上書き
     * - PHP5の「array_walk_recursive()」関数互換の動作を果たすよう再帰処理
     *
     * @params mixed $varvalue
     * @params mixed $varkey
     * @return void
     * @access public
     */
    function bridgeRecursive(&$varvalue, $varkey)
    {
        if (is_array($varvalue)) {
            array_walk($varvalue, array(&$this, __FUNCTION__));
        } else {
            $varvalue = Utility::to('EraseCtrlChar', $varvalue);
        }
    }
}
