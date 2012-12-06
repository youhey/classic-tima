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

/* @use Module_FunctionLoader */
require_once 
    dirname(dirname(__FILE__)) . DS . 'FunctionLoader.class.php';

/* エラー・コード：モジュール機能の生成に失敗 */
define('MODULE_EXECUTANT_ERROR_FUNCTION_CREATE', 303);

/**
 * モジュールの抽象クラス
 * 
 * モジュールはこのクラスを継承して機能の実行処理を実装する
 * モジュールの名前さえ定義されていれば一応モジュールとして動作
 * 
 * 本来の意図からは蛇足ながら、文字エンコーディング情報を内包
 * ※たいていの処理が日本語のマルチバイトの挙動と切り離せないので
 * 
 * @package    tima
 * @version    SVN: $Id: AbstractExecutant.class.php 6 2007-08-17 08:46:57Z do_ikare $
 * @abstract
 */
class Module_Executant_AbstractExecutant
{

    /**
     * モジュールの名前
     * 
     * @var    string
     * @access protected
     */
    var $moduleName = '';

    /**
     * 内部文字エンコーディング
     * 
     * @var    string|null
     * @access protected
     */
    var $internalEncoding = null;

    /**
     * モジュール機能のクラス名
     * 
     * @var    array
     * @access private
     */
    var $_functionNames = array();

    /**
     * モジュール機能のインスタンス
     * 
     * @var    array
     * @access private
     */
    var $_functions  = array();

    /**
     * コンストラクタ
     * 
     * @param  void
     * @access public
     */
    function Module_Executant_AbstractExecutant()
    {
        $module_prefix = $this->moduleName . '_';
        $prefix_length = strlen($module_prefix);

        foreach (Module_FunctionLoader::factory($this->moduleName) as $class_name) {
            if (strpos($class_name, $module_prefix) !== 0) {
                continue;
            }
            $function_name = strtolower(substr($class_name, $prefix_length));
            $this->_functionNames[$function_name] = $class_name;
        }
    }

    /**
     * モジュールの機能を実行
     * 
     * @param  string     $function_name モジュール機能名
     * @param  mixed      $attributes    実行対象の値
     * @param  array|null $params        実行オプション
     * @return mixed 実行結果
     * @access protected
     * @see    Module_Executant_AbstractExecutant::factory()
     * @final
     */
    function execute($function_name, $attributes, $params = null)
    {
        $resultant = null;

        $function = &$this->factory($function_name);
        if ($function !== null) {
            $resultant = $function->execute($attributes, $params);
        }

        return $resultant;
    }

    /**
     * モジュールの機能を返却
     * 
     * @param  string $function_name モジュール機能名
     * @return Module_Function
     * @access public
     * @see    Module_Executant_AbstractExecutant::exists()
     */
    function &factory($function_name)
    {
        $function = null;

        if (is_string($function_name) && ($function_name !== '')) {
            if ($this->exists($function_name)) {
                $subset_name = strtolower($function_name);
                if (!array_key_exists($subset_name, $this->_functions)) {
                    $this->_functions[$subset_name] = 
                        &new $this->_functionNames[$subset_name]($this);
                }
                $function = &$this->_functions[$subset_name];
            }
        }

        return $function;
    }

    /**
     * モジュール機能が使用可能かを応答
     * 
     * @param  string  $function_name モジュール機能名
     * @return boolean
     * @final
     * @access public
     */
    function exists($function_name)
    {
        return 
            (is_string($function_name) && 
             ($function_name !== '') && 
             array_key_exists(strtolower($function_name), $this->_functionNames));
    }

    /**
     * モジュール機能名の一覧を返却
     * 
     * @param  void
     * @return array  モジュール機能名の一覧
     * @final
     * @access public
     */
    function names()
    {
        return 
            array_keys($this->_functionNames);
    }

    /**
     * 内部文字エンコーディングを返却
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getInternalEncoding()
    {
        if (!isset($this->internalEncoding)) {
            $this->internalEncoding = mb_internal_encoding();
        }

        return $this->internalEncoding;
    }

    /**
     * 内部文字エンコーディングを登録
     * 
     * @param  string  $encoding
     * @return void
     * @access public
     */
    function setInternalEncoding($encoding)
    {
        $this->internalEncoding = $encoding;
    }
}
