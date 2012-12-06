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
 * 長さの不定な配列を結合
 * 
 * @package    tima
 * @subpackage tima_Connector
 * @version    SVN: $Id: Separate.class.php 40 2007-10-16 06:51:38Z do_ikare $
 */
class Connector_Separate extends Connector_AbstractConnector
{

    /**
     * 長さ不定の配列を結合して文字列で返却
     * - 値と値の結合子として指定された文字列を使用する
     * - 引数「$params」の値で動作を制御
     *  - 区切りの結合子（デフォルトカンマ「,」）
     * - Separateは他の連結処理と違い汎用の多目的な用途を想定するので、
     *   値をtrimしてしまうのは副作用が強すぎる
     *  - ("   1234", "   abcd", "    ....") => "1234,abcd,...."
     *  - ("-a-\n-b-\n-c-\n", "-d-\n-e-\n")  => "-a-\n-b-\n-c-,-d-\n-e-"
     *  - 改行を前後に入れる／空白でインデントなどできない
     *    ただし改行／空白のみの値は連結すべきでないのでif文の評価でtrim
     *   - ("   1234", "   abcd", "    ....") => "    1234,    abcd,    ...."
     *   - ("-a-\n-b-\n-c-\n", "-d-\n-e-\n")  => "-a-\n-b-\n-c-\n,-d-\n-e-\n"
     *   - ("aaa", "  ", "bbb", "\n", "ccc")  => "aaa,bbb,ccc"
     * 
     * @param  array      $attribute
     * @param  array|null $params
     * @return string
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        $separate = ',';
        if (($param = array_shift($params)) !== null) {
            $separate = $param;
        }

        $values = array();
        foreach (array_values($attribute) as $value) {
            if (is_array($value)) {
                $value = $this->module->zip('separate', $value, $separate);
            }
            if (trim($value) === '') {
                continue;
            }
            $values[] = $value;
        }

        return 
            implode($separate, $values);
    }
}
