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
 * モジュールの機能
 * 
 * @package  tima
 * @version  SVN: $Id: Function.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Module_Function
{

    /**
     * 実行対象
     * 
     * @var    string
     * @access protected
     */
    var $attribute;

    /**
     * 実行結果
     * 
     * @var    string
     * @access protected
     */
    var $result;

    /**
     * 実行オプション
     * 
     * @var    mixed
     * @access protected
     */
    var $option;

    /**
     * モジュール本体
     * 
     * @var    Module_Executant_AbstractExecutant
     * @access protected
     */
    var $module;

    /**
     * コンストラクタ
     * 
     * @param  Module_Executant_AbstractExecutant $module モジュール本体
     * @access public 
     */
    function Module_Function(&$module)
    {
        $this->module = &$module;
    }

    /**
     * 機能を実行
     *  - モジュール機能クラスで実装した処理を呼び出す
     *  - 差分の吸収と処理の集中化
     * 
     * @param  mixed      $attribute 対象値
     * @param  array|null $params    オプション
     * @return mixed
     * @access public 
     * @final
     */
    function execute($attributes, $params)
    {
        $this->attribute = $attributes;
        $this->option    = $params;
        $this->result    = $this->doFunction($this->attribute, $this->option);

        return $this->result;
    }

    /**
     * モジュール機能の抽象メソッド
     * 
     * @param  void
     * @return mixed
     * @access protected
     * @abstract
     */
    function doFunction()
    {
        return null;
    }

    /**
     * 機能の有効／無効を応答
     * - 使用不可とする条件があれば、偽を返却する処理を実装
     * 
     * @param  void
     * @return boolean
     * @access public
     */
    function isEnabled()
    {
        return true;
    }
}
