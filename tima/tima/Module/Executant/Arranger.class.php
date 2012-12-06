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

/* @use Module_Executant_AbstractExecutant */
require_once 
    dirname(__FILE__) . DS . 'AbstractExecutant.class.php';

/**
 * 文字列を分割して配列を生成するモジュール
 * 
 * @package    tima
 * @subpackage tima_Arranger
 * @version    SVN: $Id: Arranger.class.php 39 2007-10-16 06:43:35Z do_ikare $
 */
class Module_Executant_Arranger extends Module_Executant_AbstractExecutant
{

    /**
     * モジュールの名前
     * 
     * @var    string
     * @access protected
     */
    var $moduleName = 'Arranger';

    /**
     * アレンジャで分割した結果を返却
     * 
     * 三番目以降を可変引数として一つの配列にして実行オプションとする
     * - zip('a', 123) => array()
     * - zip('a', 123, 'ABC') => array('ABC')
     * - zip('a', 123, 'ABC', 'XYZ', 987) => array('ABC', 'XYZ', 987)
     * 
     * @param  string  $Arranger_name  アレンジャ名
     * @param  string  $attributes     分割する文字列
     * @return array
     * @access public
     * @see    Module_Executant_AbstractExecutant::execute()
     */
    function cut($arranger_name, $attributes)
    {
        $params = array();
        for ($i = 2, $n = func_num_args(); $i < $n; ++$i) {
            $arg = func_get_arg($i);
            if (is_array($arg)) {
                $params = Utility::merge($params, $arg);
            } else {
                $params[] = $arg;
            }
        }

        return
            $this->execute($arranger_name, $attributes, $params);
    }
}
