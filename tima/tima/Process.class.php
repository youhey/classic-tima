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
 * �¹ԥץ���
 * 
 * @package  tima
 * @version  SVN: $Id: Process.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Process
{

    /**
     * ��������󡦥�����
     * 
     * @var    action
     * @access private
     */
    var $_task = null;

    /**
     * �¹Ԥ��륢��������̾��
     * 
     * @var    string
     * @access private
     */
    var $_action = '';

    /**
     * ��������󡦥���ȥ���Υѥ�
     * 
     * @var    string
     * @access private
     */
    var $_path = '';

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  void
     * @access public
     */
    function Process() {}

    /**
     * ����������Ͽ
     * 
     * @param  Action $task   ��������󡦥���ȥ���
     * @param  string $action �¹Ԥ��륢��������̾��
     * @param  array  $path   ��������󡦥���ȥ��顦���饹�Υѥ�
     * @return void
     * @access public
     * @see    Action::enabled()
     * @see    Action::getDefaultAction()
     * @see    Action::isActionExists()
     */
    function set(&$task, $action, $path)
    {
        $this->_task   = &$task;
        $this->_action = $action;
        $this->_path   = $path;
    }

    /**
     * ��������¹�
     * 
     *  - ��������󡦥���ȥ��餫��¹Ը��¤������ʤ��ä�
     *   - <del>���顼����Forbidden executing action-controller '����ȥ���̾'</del>
     *   - �쥹�ݥ󥹡�403 Forbidden
     *  - ��������󡦥���ȥ���˥��������¸�ߤ��ʤ�
     *   - ���顼����Action '����ȥ���̾::���������̾' not found
     *   - �쥹�ݥ󥹡�404 Not Found
     * 
     * @param  void
     * @return string|null
     * @access public
     */
    function invoke()
    {
        $this->_task->initialize();

        // ����������̾�����ʤ���Хǥե�����ͤ򥻥å�
        if ($this->_action === null) {
            $this->_action = $this->_task->getDefaultAction();
        }

        if (!$this->_task->isActionExists($this->_action)) {
            header('HTTP/1.0 400 Bad Request');
            // �ץ����Υ��顼�ǤϤʤ��Τ�PHP�Υ��顼��ȯ�������ʤ��٤�����
            // ����ʽ����η�̤Ȥ��ƥ���ȥ���¦�ǥ���Ͽ�����롩
            trigger_error(
                sprintf(
                    "Action '%s::%s' not found", 
                    $this->_task->getName(), $this->_action), 
                E_USER_ERROR);
            exit;
        }

        if (!$this->_task->enable()) {
            header('HTTP/1.0 403 Forbidden');
            // �����Ȥ��Ƥ�����ϤʤΤ�PHP�Υ��顼�ϵ�Ͽ���ʤ�
            // ���ε�Ͽ�ʤ�ɬ�פǤ���Х���ȥ���¦��Ǥ����
            // trigger_error(
            //     sprintf(
            //         "Forbidden executing action-controller '%s'", 
            //         $this->_task->getName()), 
            //     E_USER_ERROR);
            exit;
        }

        $resultant = $this->_task->invoke($this->_action);
        if (($resultant === null) || ($resultant === 'success')) {
            $resultant = $this->_action;
        }

        return $this->_formatResult($resultant);
    }

    /**
     * �¹Է�̤Υѥ���ƹ���
     * 
     * @param  string $resultant ��̥ѥ�
     * @param  array  $path      ��������󡦥���ȥ��顦���饹�Υѥ�
     * @return string|null
     * @access public
     */
    function _formatResult($resultant)
    {
        if (!is_string($resultant) || ($resultant === '')) {
            return '';
        }

        // ��̥ѥ���̿��ҡ�::�פ��ޤޤ�Ƥ���кƹ�����ɬ�פʤ�
        if (strpos($resultant, '::') !== false) {
            return $resultant;
        }

        // ��̥ѥ�����Ƭ����/�פʤ顢�������Υ���󥻥���Ū��Ƚ��
        // �������˥ѥ������л��ꤹ��ʤ�ƥ�ץ졼�Ȥ������ͤ�����
        $resultant = str_replace('/', DS, $resultant);
        if ((strpos($resultant, DS) === 0)) {
            return substr($resultant, 1);
        }

        // ��̥ѥ���ƹ���
        $action_path = $this->_path['ctrl'];
        if ($this->_path['dir'] !== '') {
            if (strrpos($this->_path['dir'], DS) !== strlen($this->_path['dir'])) {
                $this->_path['dir'] = $this->_path['dir'] . DS;
            }
            $action_path = $this->_path['dir'] . $action_path;
        }

        return $action_path . DS . $resultant;
    }
}
