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
 * �������Ǥδ��쥯�饹
 * 
 * @package    tima
 * @subpackage tima_Question
 * @version    SVN: $Id: Common.class.php 9 2007-09-05 02:48:37Z do_ikare $
 */
class Question_Common
{

    /**
     * ̾��
     * 
     * @var    string
     * @access public
     */
    var $name = '';

    /**
     * �������Τ���ε�̾
     * ��������ɬ�פȤ��ʤ���С�$name�פ�Ʊ��
     * 
     * @var    string
     * @access public
     */
    var $alias = '';

    /**
     * ���ܤ�̾��
     * 
     * @var    string
     * @access public
     */
    var $label = '';

    /**
     * ��
     * 
     * @var    mixed
     * @access public
     */
    var $value = null;

    /**
     * ɬ�ܡ�Ǥ��
     * 
     * @var    boolean
     * @access public
     */
    var $required = false;

    /**
     * ���饹
     * 
     * @var    Question
     * @access protected
     */
    var $handler = null;

    /**
     * �ꥯ�����ȡʥ��硼�ȥ��å��ѡ�
     * 
     * @var    Request
     * @access protected
     */
    var $request = null;

    /**
     * ���å����ʥ��硼�ȥ��å��ѡ�
     * 
     * @var    Session
     * @access protected
     */
    var $session = null;

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  string   $name    �������Ǥ�̾��
     * @param  Question $handler ���饹
     * @access public
     */
    function Question_Common($name, &$handler)
    {
        $this->name    = $name;
        $this->alias   = $name;
        $this->handler = &$handler;
        $this->request = &$this->handler->action->request;
        $this->session = &$this->handler->action->session;
    }

    /**
     * �����
     * 
     * @param  void
     * @return void
     * @access public
     */
    function initialize()
    {
        $this->handler->clearError($this->alias);

        $prev_state = $this->session->getFlash($this->name);
        if ($prev_state !== null) {
            $this->set($prev_state);
        }

        $request = $this->request->get($this->name);
        if ($request !== null) {
            $this->set($request);
        }
    }

    /**
     * ��������Ͽ
     * 
     * @param  mixed $request �ꥯ������
     * @return void
     * @access public
     */
    function set($request)
    {
        $this->value = $request;
        $this->session->setFlash($this->name, $this->value);
    }

    /**
     * ������õ�
     * 
     * @param  void
     * @return void
     * @access public
     */
    function erase()
    {
        // �ե�å����ѿ���õ�Τ���������ƼΤƤ�
        $flash_value = $this->session->getFlash($this->name);

        $defaults    = get_class_vars(get_class($this));
        $this->value = $defaults['value'];
    }

    /**
     * �ͤ�ʸ������ֵ�
     * 
     * @param  void
     * @return string
     * @access public
     */
    function toText()
    {
        return (string)$this->value;
    }

    /**
     * �����HTML���ֵ�
     * 
     * �Ѿ��������饹�ǽ��������
     * 
     * @param  void
     * @return string
     * @access public
     * @abstract
     */
    function toHtml() {}

    /**
     * �ͤ򸡾�
     * 
     * �Ѿ��������饹�ǽ��������
     * 
     * @param  void
     * @return void
     * @access public
     * @abstract
     */
    function validate() {}

    /**
     * ɬ�ܾ��򽼤����Ƥ��뤫�򸡾�
     * - �͡ʥ��дؿ���toText()�פ�����͡ˤ�¸�ߤ��Ƥ��뤫�򸡾�
     * 
     * @param  void
     * @return string
     * @access public
     */
    function checkRequired()
    {
        $resultant = true;

        if ($this->required === true) {
            $validator = 'Required';
            $resultant = Utility::is($validator, $this->toText());
            if ($resultant === false) {
                $this->handler->setError(
                    $this->alias, 
                    $this->handler->expectError($this->alias, $validator));
            }
        }

        return $resultant;
    }
}
