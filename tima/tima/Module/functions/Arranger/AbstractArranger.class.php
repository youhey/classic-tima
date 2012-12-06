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
 * 分割モジュールの機能（抽象）クラス
 * 
 * @package    tima
 * @subpackage tima_Arranger
 * @version    SVN: $Id: AbstractArranger.class.php 4 2007-06-20 07:16:44Z do_ikare $
 * @abstract
 */
class Arranger_AbstractArranger extends Module_Function {}
