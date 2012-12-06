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
 * 機能を汎用的に細分化したモジュール・クラス
 * 
 * @package  tima
 * @version  SVN: $Id: Module.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Module
{

    /**
     * 部品化した機能を返却するファクトリー・メソッド
     * 
     * @param  string  $module_name モジュール名
     * @return Module_Executant
     * @access public
     */
    function &factory($module_name)
    {
        static $class_loader;
        if (!isset($class_loader)) {
            $class_loader = &new ClassLoader();
            $class_loader->setParents('Module', 'Executant');
            $class_loader->setIncludePath(dirname(__FILE__));
        }

        $module_class = null;

        $class_name   = $class_loader->load($module_name);
        if ($class_name !== '') {
            $module_class = &new $class_name;
        }

        return $module_class;
    }
}
