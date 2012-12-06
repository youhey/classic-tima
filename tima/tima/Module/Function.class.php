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
 * �⥸�塼��ε�ǽ
 * 
 * @package  tima
 * @version  SVN: $Id: Function.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Module_Function
{

    /**
     * �¹��о�
     * 
     * @var    string
     * @access protected
     */
    var $attribute;

    /**
     * �¹Է��
     * 
     * @var    string
     * @access protected
     */
    var $result;

    /**
     * �¹ԥ��ץ����
     * 
     * @var    mixed
     * @access protected
     */
    var $option;

    /**
     * �⥸�塼������
     * 
     * @var    Module_Executant_AbstractExecutant
     * @access protected
     */
    var $module;

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  Module_Executant_AbstractExecutant $module �⥸�塼������
     * @access public 
     */
    function Module_Function(&$module)
    {
        $this->module = &$module;
    }

    /**
     * ��ǽ��¹�
     *  - �⥸�塼�뵡ǽ���饹�Ǽ�������������ƤӽФ�
     *  - ��ʬ�εۼ��Ƚ����ν��沽
     * 
     * @param  mixed      $attribute �о���
     * @param  array|null $params    ���ץ����
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
     * �⥸�塼�뵡ǽ����ݥ᥽�å�
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
     * ��ǽ��ͭ����̵�������
     * - �����ԲĤȤ����郎����С������ֵѤ�����������
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
