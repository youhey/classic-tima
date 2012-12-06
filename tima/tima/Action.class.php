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
 * ��������󡦥���ȥ���Υ����ѡ����饹
 * 
 * - �ץ����Υե�������
 * - ���дؿ��ǥ������������
 *  - ���������ǥꥯ�����ȤΥ��å���¹�
 *  - ����������ư�������Ǥ�����дؿ�
 *   - entry���������
 *   - execute���¹Խ���
 *   - exit��������
 *   - validate�����ڽ���
 * 
 * @package  tima
 * @version  SVN: $Id: Action.class.php 35 2007-09-28 02:03:08Z do_ikare $
 */
class Action
{
    /**
     * �ǥե���ȡ�����������̾��
     * 
     * @var    string
     * @access protected
     */
    var $defaultAction = 'default';

    /**
     * ����
     * 
     * @var    Question
     * @access protected
     */
    var $question = null;

    /**
     * �ꥯ������
     * 
     * @var    Request
     * @access protected
     */
    var $request = null;

    /**
     * �쥹�ݥ�
     * 
     * @var    Response
     * @access protected
     */
    var $response = null;

    /**
     * �桼���������������
     * 
     * @var    UserAgent
     * @access protected
     */
    var $userAgent = null;

    /**
     * ���å����
     * 
     * @var    Session
     * @access protected
     */
    var $session = null;

    /**
     * �ե��ȡ�����ȥ���
     * 
     * @var    Front
     * @access protected
     */
    var $front = null;

    /**
     * ����
     * 
     * @var    Logger
     * @access protected
     */
    var $logger = null;

    /**
     * ���Ѥ����ǥ롦���饹
     * 
     * @var    array
     * @access protected
     */
    var $useModels = array();

    /**
     * �������ʤ���protected�˥����ѿ����ѿ�̾
     * - �ݸ���������ѿ����ѿ�̾������
     * - ����ʼ��ʤ�protected�ʤ������������ʬ����Ť餤���׸�Ƥ
     * 
     * @var    array
     * @access protected
     */
    var $protectedVarNames = array(
            'defaultAction', 
            'question', 
            'request', 
            'response', 
            'userAgent', 
            'session', 
            'logger', 
            'front', 
            'useModels', 
        );

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  Front $front �ե��ȡ�����ȥ���
     * @access public
     */
    function Action(&$front)
    {
        $this->session   = &new Session($this->getName());
        $this->front     = &$front;
        $this->request   = &$front->getRequest();
        $this->response  = &$front->getResponse();
        $this->userAgent = &$front->getUserAgent();
        $this->beginning = &$front->getBeginning();
        $this->logger    = &$front->getLogger();
        $this->question  = &new Question($this, $front->getAppDir());

        if (!empty($this->useModels)) {
            $class_loader = &new ClassLoader;
            $class_loader->setParents('Model');
            $class_loader->setIncludePath($this->front->getAppDir());
            foreach ($this->useModels as $model) {
                $class_loader->load($model);
            }
        }
    }

    /**
     * ����ȥ��������
     * 
     * @param  void
     * @return void
     * @access public
     */
    function initialize() {}

    /**
     * ����ȥ���μ¹Ը��¤��ֵ�
     * - �����ѡ����饹�ǤϾ�˿�
     * - �Ѿ����饹�Ǿ��˱������������֤��褦�����С��饤��
     * 
     * @param  void
     * @return boolean
     * @access public
     */
    function enable()
    {
        return true;
    }

    /**
     * ����������������Ƥ��뤫�򸡺�
     * 
     * @param  string  $action ����������̾��
     * @return boolean
     * @access public
     */
    function isActionExists($action)
    {
        return 
            method_exists($this, 'execute' . $action);
    }

    /**
     * ����������¹�
     * 
     * @param  string $action_name
     * @return string
     * @access public
     */
    function invoke($action_name)
    {
        // ��ư���륢��������̾�������
        $this->ACTIVE_ACTION = $action_name;

        // �������
        $entry_method = 'entry' . $action_name;
        if (method_exists($this, $entry_method)) {
            call_user_func(array(&$this, $entry_method));
        }

        // �¹Խ���
        $execute_method = 'execute' . $action_name;
        if (!method_exists($this, $execute_method)) {
            trigger_error(
                "Unable to execute the action '${action_name}'", E_USER_WARNING);
        } else {
            $reply = call_user_func(array(&$this, $execute_method));
        }

        // �ƥ�ץ졼�ȤΥǡ�������ǥ����Ͽ
        foreach ($this->getProperties() as $varkey => $varvalue) {
            $this->response->setDataModel($varkey, $varvalue);
        }
        $this->response->setDataModel('form', $this->question->toArray());

        // ������
        $exit_method = 'exit' . $action_name;
        if (method_exists($this, $exit_method)) {
            call_user_func(array(&$this, $exit_method));
        }

        return $reply;
    }

    /**
     * �ǥե���ȡ�����������̾�����ֵ�
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     * ��������Ĥ��������ѿ���������ֵ�
     * 
     * @param  void
     * @return array
     * @access public
     */
    function getProperties()
    {
        $public_properties = array();
        foreach (array_keys(get_object_vars($this)) as $varkey) {
            if ((strpos($varkey, '_') === 0) || in_array($varkey, $this->protectedVarNames)) {
                continue;
            }
            $public_properties[$varkey] = $this->$varkey;
        }

        return $public_properties;
    }

    /**
     * ��������󡦥���ȥ���ʼ��ʡˤ�̾�����ֵ�
     * - ̾���ϼºݤΥ��饹̾�˴ط��ʤ�ɬ����ʸ���ˤʤ�ޤ�
     *  - PHP4/5�ɤ���Ǥ�Ʊ���ͤ��ֵѤ��뤿���
     * 
     * @param  void
     * @return string
     * @access protected
     * @static string $ctrl_name
     */
    function getName()
    {
        static $ctrl_name;
        if (!isset($ctrl_name)) {
            $ctrl_name = 
                preg_match('/^(?:Action_)([\w]+)$/i', get_class($this), $match) ?
                    $match[1] : get_class($this);
        }

        // PHP4/5�κ���ۼ����뤿��strtolower()�ؿ��Ǿ�ʸ�����Ѵ�
        return 
            strtolower($ctrl_name);
    }

    /**
     * ����������ư�����˵�Ͽ
     * - �귿�������ǥ�å�������Ͽ
     * 
     * @param  string $action
     * @param  string $message
     * @return void
     * @access protected
     * @static UserAgent $ua
     * @see    Logger::info()
     */
    function logAction($action, $message = '')
    {
        static $ua;
        if (!isset($ua)) {
            $ua = &$this->front->getUserAgent();
        }

        $device = $ua->isMobile() ? 'Mobile' : 'PC';

        $this->logger->info(
            sprintf(
                '[%s] %s / Ctrl:%s?%s Device:%s', 
                $action, 
                $message, 
                $this->getName(), 
                $this->session->getId(), 
                $device));
    }
}
