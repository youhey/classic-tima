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

/* @use Module_Function */
require_once 
    dirname(dirname(dirname(__FILE__))) . DS . 'Function.class.php';

/**
 * 変換モジュールの機能（抽象）クラス
 * 
 * @package    tima
 * @subpackage tima_Converter
 * @version    SVN: $Id: AbstractConverter.class.php 6 2007-08-17 08:46:57Z do_ikare $
 * @abstract
 */
class Converter_AbstractConverter extends Module_Function
{

    /**
     * 変換処理が結果に影響を与えたかを検査
     * 
     * @param  void
     * @return boolean
     * @access public
     */
    function isConverted()
    {
        return 
            ($this->attribute !== $this->result);
    }
}
