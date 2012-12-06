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
 * リクエストに含まれるShifJIS外字を除去するフィルタ
 * 
 * @package    tima
 * @subpackage tima_Filter
 * @version    SVN: $Id: SjisGaiji.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Filter_SjisGaiji
{

    /**
     * リクエストに含まれる「Shif_JIS」エンコーディングの外字を「〓」に置換
     *
     * @params Front $front
     * @return void
     * @access public
     * @see    Converter_SjisGaiji2Geta::doFunction()
     */
    function before(&$front)
    {
        $logger = &$front->getLogger();
        $logger->trace(__CLASS__ . "の前処理を実行");

        if ($front->getHttpCharSet() === 'Shift_JIS') {
            $request   = &$front->getRequest();

            foreach ($request->getAll() as $varname => $varvalue) {
                if (is_array($varvalue)) {
                    array_walk($varvalue, array(&$this, 'bridgeRecursive'));
                } else {
                    $varvalue = Utility::to('SjisGaiji2Geta', $varvalue);
                }
                $request->set($varname, $varvalue);
            }

            $logger->debug('リクエストのShift_JIS外字を〓に変換');
        }
    }

    /**
     * 配列を「array_walk()」関数で処理するためのコールバック関数
     * - 変換モジュールの「SjisGaiji2Get」機能で値を変換
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
            $varvalue = Utility::to('SjisGaiji2Geta', $varvalue);
        }
    }
}
