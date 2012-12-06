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

/* ���顼�������ɡ��⥸�塼�뵡ǽ�������˼��� */
define('MODULE_EXECUTANT_ERROR_FUNCTION_CREATE', 303);

/**
 * �⥸�塼�����ݥ��饹
 * 
 * �⥸�塼��Ϥ��Υ��饹��Ѿ����Ƶ�ǽ�μ¹Խ������������
 * �⥸�塼���̾�������������Ƥ���а���⥸�塼��Ȥ���ư��
 * 
 * ����ΰտޤ���ϼ�­�ʤ��顢ʸ�����󥳡��ǥ��󥰾��������
 * �������Ƥ��ν��������ܸ�Υޥ���Х��Ȥε�ư���ڤ�Υ���ʤ��Τ�
 * 
 * @package    tima
 * @version    SVN: $Id: AbstractExecutant.class.php 6 2007-08-17 08:46:57Z do_ikare $
 * @abstract
 */
class Module_Executant_AbstractExecutant
{

    /**
     * �⥸�塼���̾��
     * 
     * @var    string
     * @access protected
     */
    var $moduleName = '';

    /**
     * ����ʸ�����󥳡��ǥ���
     * 
     * @var    string|null
     * @access protected
     */
    var $internalEncoding = null;

    /**
     * �⥸�塼�뵡ǽ�Υ��饹̾
     * 
     * @var    array
     * @access private
     */
    var $_functionNames = array();

    /**
     * �⥸�塼�뵡ǽ�Υ��󥹥���
     * 
     * @var    array
     * @access private
     */
    var $_functions  = array();

    /**
     * ���󥹥ȥ饯��
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
     * �⥸�塼��ε�ǽ��¹�
     * 
     * @param  string     $function_name �⥸�塼�뵡ǽ̾
     * @param  mixed      $attributes    �¹��оݤ���
     * @param  array|null $params        �¹ԥ��ץ����
     * @return mixed �¹Է��
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
     * �⥸�塼��ε�ǽ���ֵ�
     * 
     * @param  string $function_name �⥸�塼�뵡ǽ̾
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
     * �⥸�塼�뵡ǽ�����Ѳ�ǽ�������
     * 
     * @param  string  $function_name �⥸�塼�뵡ǽ̾
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
     * �⥸�塼�뵡ǽ̾�ΰ������ֵ�
     * 
     * @param  void
     * @return array  �⥸�塼�뵡ǽ̾�ΰ���
     * @final
     * @access public
     */
    function names()
    {
        return 
            array_keys($this->_functionNames);
    }

    /**
     * ����ʸ�����󥳡��ǥ��󥰤��ֵ�
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
     * ����ʸ�����󥳡��ǥ��󥰤���Ͽ
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
