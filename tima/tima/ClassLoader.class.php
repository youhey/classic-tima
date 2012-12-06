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
 * ���饹��ưŪ�˥��ɤ���
 * 
 * - ���饹̾��������������ǧ-
 *  - ���饹��̤���ɤǤ���Хե�����򥤥󥯥롼��
 * - ���饹�ϥѥå�����ñ�̤ǿƻҴط����θ
 *  - �ƻҴط��ι����ϡ�PEAR��Ū�ʷ���������
 *   - PEAR::DB => DB_Pgsql = DB/Pgsql.php
 *   - PEAR::Net_UserAgent_Mobile => Net_UserAgent_Mobile = Net/UserAgent/Mobile.php
 * <code>
 * $loader = new ClassLoader('DB', 'php');
 * 
 *  // ��������С�ʸ���� 'DB_Pgsql' ���ֵ�
 * $classname = $loader->load('Pgsql');
 * if ($classname === '') {
 *     die('error');
 * }
 * $pgsql = new $classname();
 * </code>
 * 
 * @package  tima
 * @version  SVN: $Id: ClassLoader.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class ClassLoader
{

    /**
     * ���饹��¤�Υ١����ˤʤ�����̾��
     * - ���饹�ϥѥå�������˥ǥ��쥯�ȥ�ʬ��
     * - ���֡��ѥå������Ͽƥ��饹̾����Ƭ��ˤ��
     *  - PEAR::DB_Pgsql => DB_Pgsql
     * 
     * @var    array
     * @access private
     */
    var $_classParents = array();

    /**
     * �ե������ĥ��
     * 
     * @var    string
     * @access private
     */
    var $_fileExt = '';

    /**
     * ���󥯥롼�ɡ��ѥ��θ����ϰ�
     * 
     * @var    string|null
     * @access private
     */
    var $_includePath = null;

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  string      $parent   ���饹��¤�η���̾��
     * @param  string      $file_ext ���饹�ե�����γ�ĥ��̾
     * @param  string|null $fix_path ���󥯥롼�ɡ��ѥ��θ����ϰ�
     * @access public
     */
    function ClassLoader($parents = null, $file_ext = '.class.php', $fix_path = null)
    {
        if ($parents !== null) {
            $this->setParents($parents);
        }
        $this->setFileExt($file_ext);
        $this->setIncludePath($fix_path);
    }

    /**
     * ���饹���ɤ߹���
     * 
     * - ���饹�����Ѳ�ǽ�Ǥ���Х��饹��̾�����ֵ�
     *  - ���饹��̾����������������ǧ
     *  - �ƻҴط���ưŪ��ɾ�����륯�饹�ϥե�͡������
     * - ���饹�����ѤǤ��ʤ���ж�ʸ������ֵ�
     * 
     * @param  string $class ���󥯥롼�ɤ��륯�饹��̾��
     * @return string ���饹̾�ʥ��饹��̤����ʤ��ʸ�����
     * @access public 
     */
    function load($class)
    {
        $classname = '';

        if (!is_string($class) || ($class === '')) {
            return '';
        }

        // ���饹̾�ξ�ά��ʬ���䴰
        $parents = '';
        if (count($this->_classParents) > 0) {
            $parents = implode('_', $this->_classParents) . '_';
        }
        $classname = $parents . $class;

        // ̤����ʤ����
        if (!class_exists($classname)) {
            // ���󥯥롼�ɤ��ϰϤ����
            $this->_fixPath($this->_includePath);

            $directory = '';
            if (count($this->_classParents) > 0) {
                $directory = implode(DS, $this->_classParents) . DS;
            }
            $file_path = $directory . $class . $this->_fileExt;

            if (!$this->_existsFile($file_path)) {
                $classname = '';
            } else {
                $resultant = (include_once $file_path);
                if (($resultant === false) || !class_exists($classname)) {
                    $classname = '';
                }
            }

            // ���󥯥롼�ɤ��ϰϤ򸵤��᤹
            $this->_fixPath();
        }

        return $classname;
    }

    /**
     * ���ꤵ�줿���饹��̾������ե����뤬¸�ߤ��뤫�򸡺�
     * 
     * @param  string  $class ���饹̾
     * @return boolean
     * @access public 
     */
    function isReadable($class)
    {
        $this->_fixPath($this->_includePath);

        $directory = '';
        if (count($this->_classParents) > 0) {
            $directory = implode(DS, $this->_classParents) . DS;
        }
        $resultant = $this->_existsFile($directory . $class . $this->_fileExt);

        $this->_fixPath();

        return $resultant;
    }

    /**
     * ���饹��¤�Υ١����Ȥʤ�������Ͽ
     * 
     * - setParents( String parentName [, String parentName [, ... ] ] );
     * - ���饹̾��ǥ��쥯�ȥ깽¤��PEAR������̿̾����Ƥ�������
     *  - PEAR::DB_Pgsql => DB/Pgsql.php => setParents('DB')
     *  - PEAR::Net_UserAgent_Mobile 
     *        => Net/UserAgent/Mobile.php
     *        => setParents('Net', 'UserAgent')
     * 
     * @param  string  �١��������饹��̾��
     * @return void
     * @access public
     */
    function setParents()
    {
        $class_names = array();
        if (func_num_args() > 0) {
            foreach(func_get_args() as $arg) {
                if ($arg === '') {
                    continue;
                }
                $class_names[] = $arg;
            }
        }
        $this->_classParents = $class_names;
    }

    /**
     * ���󥯥롼�ɤ���ե�����γ�ĥ�Ҥ���Ͽ
     * 
     * @param  string $file_ext �ե������ĥ��
     * @return void
     * @access public
     */
    function setFileExt($file_ext)
    {
        $this->_fileExt = (string)$file_ext;
    }

    /**
     * ���󥯥롼�ɤ�������ϰϤ���Ͽ
     * 
     * @param  string|null $fix_path ���ꤹ���ϰ�
     * @return void
     * @access public
     */
    function setIncludePath($fix_path)
    {
        $this->_includePath = (is_null($fix_path) ? null : (string)$fix_path);
    }

    /**
     * �ե����뤬¸�ߤ��뤫�򸡾�
     * - PHP�δĶ������include_path�פΥѥ��⸡��
     * 
     * @param  string  $file_path
     * @return boolean
     * @access private
     */
    function _existsFile($file_path)
    {
        if ($this->_isAbsolutePath($file_path)) {
            return file_exists($file_path);
        }

        foreach (explode(PATH_SEPARATOR, ini_get('include_path')) as $include_path) {
            if (file_exists($include_path . DS . $file_path)) {
                return true;
            }
        }
        return false;
    }

    /**
     * �ե�����Υѥ������Хѥ����򸡾�
     * 
     * @param  string  $file_path
     * @return boolean
     * @access private
     */
    function _isAbsolutePath($file_path)
    {
        if (defined('OS_WINDOWS') && OS_WINDOWS) {
            if (!preg_match('/^[a-z]:/i', $file_path)) {
                return false;
            }
            return ($file_path{2} === DS);
        }

        return ($file_path{0} === DS);
    }

    /**
     * PHP��ư��Ķ������include_path�פ��ѹ�
     * - ���ֺǽ�μ¹����˥��ꥸ�ʥ�������ͤ򵭲�
     * - ��������ά�����Х��ꥸ�ʥ������
     * 
     * @param  string|null $reference
     * @return void
     * @access private
     */
    function _fixPath($reference = null)
    {
        static $original;
        if (!isset($original)) {
            $original = ini_get('include_path');
        }

        ini_set('include_path', isset($reference) ? $reference : $original);
    }

}
