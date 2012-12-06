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
 * 用意された機能をモジュールに通知するクラス
 * 
 * 用意された機能の定義状況を確認してファイルをインクルード
 * 
 * 機能はモジュール単位でディレクトリを配置する
 * <pre>
 * Validatorモジュールの「Email」機能なら、
 * Validator/Email.class.php => class Module_Validator_Email
 * </pre>
 * 
 * @package  tima
 * @version  SVN: $Id: FunctionLoader.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Module_FunctionLoader
{

    /**
     * 機能の名前を全て返却
     * 
     * @param  string  $module_name モジュール名
     * @return array
     * @access public
     * @see    ClassLoader::load()
     * @see    Module_FunctionLoader::getFunctionNames()
     */
    function factory($module_name)
    {
        $class_loader = &new ClassLoader;
        $class_loader->setParents($module_name);
        $class_loader->setIncludePath(dirname(__FILE__) . DS . 'functions');

        $function_class_names = array();
        foreach (Module_FunctionLoader::getFunctionNames($module_name) 
        as $function_name) {
            $class_name = $class_loader->load($function_name);
            if ($class_name === '') {
                continue;
            }
            $function_class_names[] = $class_name;
        }

        return $function_class_names;
    }

    /**
     * モジュールに用意された機能の名前を全て配列で返却
     * 
     * @param  string  $module_name モジュール名
     * @return array 
     * @access public
     */
    function getFunctionNames($module_name)
    {
        $func_names = array();

        if (is_string($module_name) && ($module_name !== '')) {
            $module_dir = dirname(__FILE__) . DS . 'functions' . DS . $module_name;
            if (is_dir($module_dir) && (($dh = opendir($module_dir)) !== false)) {
                while (($file = readdir($dh)) !== false) {
                    if (($file === '.') || ($file === '..')) {
                        continue;
                    }
                    if (preg_match('/^(?:[a-z0-9_-]+)(?:\.class\.php)$/i', $file)) {
                        $func_names[] = basename($file, '.class.php');
                    }
                }
                closedir($dh);
            }
        }

        return $func_names;
    }
}
