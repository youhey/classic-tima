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
 * �����ե����������ͤ������ͤȤ��ƻȤ�����Υ��饹
 * 
 * - PHP�ե�����Υ��󥯥롼�ɷ�̤����
 *  - ���󥯥롼�ɤ���PHP�ե����뤬�Ǹ��return������
 *  - ����������Ѥ���PHP�ե������������������ֵѤ���
 * - �ե�����̾�ϡ�(�������)-ini.php��
 *  - section��Front => Front-ini.php
 *  - PHP4��PHP5�ǤΥ��饹̾����ʸ������ʸ�����θ�������ƾ�ʸ��
 *   - section : FooBar => foobar-ini.php
 * - ����ե�����Ǥ�returnʸ��������ֵѤ��뤳�Ȥ������ʤ�
 *  - ����ե�����������Ѥ����롿����ͤ����곰
 *        => �տޤ��ʤ���ư����̿Ū�ʥ��顼�β�ǽ������
 *  - �������İ��Ǥ��ʤ��ʤ�ΤǤ����ϸ���
 * 
 * @package  tima
 * @version  SVN: $Id: Config.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Config
{

    /**
     * ��������
     * 
     * @var    array
     * @access private
     */
    var $_parameters = array();

    /**
     * ����ե���������֥ǥ��쥯�ȥ�
     * 
     * @var    array
     * @access private
     */
    var $_etcDir = array();

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  void
     * @access public
     */
    function Config() {}

    /**
     * �����ͤ��ֵ�
     *
     * @param  string $configkey
     * @return mixed
     * @access public
     */
    function get($configkey, $namespace = 'anonymous')
    {
        if (!is_string($configkey) || !is_string($namespace)) {
            return null;
        }
        if (!isset($this->_parameters[$namespace][$configkey])) {
            return null;
        }

        return $this->_parameters[$namespace][$configkey];
    }

    /**
     * ̾�����֤�¸�ߤ������Ƥ�������ֵ�
     * 
     * @param  string  $name      
     * @param  string  $namespace ̾������
     * @return array
     * @access public
     * @todo ��������
     */
    function getByNamespace($namespace)
    {
        if (!is_string($namespace) || ($namespace === '')) {
            return array();
        }
        if (!isset($this->_parameters[$namespace])) {
            return array();
        }

        return $this->_parameters[$namespace];
    }

    /**
     * �����ͤ˿������ͤ���
     *
     * @param  string  $configkey ���ꥭ��
     * @param  string  $varvalue  ������
     * @param  string  $namespace ̾������
     * @return void
     * @access public
     */
    function set($configkey, $varvalue, $namespace = 'anonymous')
    {
        if (!is_string($configkey) || !is_string($namespace) || 
            ($configkey === '') || ($namespace === '')) {
            trigger_error('Wrong parameter for setting configuration.');
            return;
        }

        if (!isset($this->_parameters[$namespace])) {
            $this->_parameters[$namespace] = array();
        }

        $this->_parameters[$namespace][$configkey] = $varvalue;
    }

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  string $added_etc_dir
     * @return void
     * @access public
     */
    function setEtcDir($added_etc_dir)
    {
        $this->_etcDir[] = $added_etc_dir;
    }

    /**
     * �ե������������줿������ɤ߹���
     *
     * @param  string  $section ����̾
     * @return boolean 
     * @access public
     */
    function readConfig($section)
    {
        $file_name = $this->_getFileName($section);

        // ����ե����뤬¸�ߤ��ʤ���е����ֵѤ��ƽ�λ
        if (is_null($file_name)) {
            return false;
        }

        $config = @include $file_name;

        // ����ե�����Υ��󥯥롼�ɷ�̤�������ʤ���а۾ｪλ
        if (!isset($config) || !is_array($config)) {
            header('HTTP/1.1 500 Internal Server Error');
            trigger_error(
                "Unable to read the configuration '${section}-ini.php'.", 
                E_USER_ERROR);
            exit;
        }

        // ���󥯥롼�ɷ�̤��ݻ����������ȿ��
        foreach ($config as $varvalue) {
            // ������ͭ����ʸ���󤬤ʤ����̵��
            if (!isset($varvalue['key']) || 
                !is_string($varvalue['key']) || ($varvalue['key'] === '')) {
                trigger_error(
                    "Wrong definition for setting configuration in '${file_name}'", 
                    E_USER_WARNING);
                continue;
            }
            // �����͡�NULL�פ������ȿ�Ǥ���ʤ���̵���
            if (!isset($varvalue['value'])) {
                continue;
            }

            $configvar = array($varvalue['key'], $varvalue['value']);
            if (isset($varvalue['space'])) {
                $configvar[] = $varvalue['space'];
            }
            call_user_func_array(array(&$this, 'set'), $configvar);
        }

        return true;
    }

    /**
     * ����̾���б������ե�����̾���ֵ�
     * �б���������ե����뤬�ʤ���Х̥���ֵ�
     * 
     * @param  string $section
     * @return string|null
     * @access private
     */
    function _getFileName($section)
    {
        $config_file = null;

        foreach ($this->_etcDir as $dir) {
            $file = $dir . DS . strtolower($section) . '-ini.php';
            if (!is_file($file) && !is_readable($file)) {
                continue;
            }
            $config_file = $file;
            break;
        }

        return $config_file;
    }
}
