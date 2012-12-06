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
 * �ե�������������Ǥ����뤿��Υ��饹
 * 
 * @package  tima
 * @version  SVN: $Id: Question.class.php 36 2007-10-05 11:35:00Z do_ikare $
 */
class Question
{

    /**
     * ��������󡦥���ȥ���
     * 
     * @var    Action
     * @access public
     */
    var $action = null;

    /**
     * ���䥯�饹���ɤ߹��������
     * 
     * @var    ClassLoader
     * @access public
     */
    var $questionLoader = null;

    /**
     * HTML����������ӥ����
     * 
     * @var    HTML|CHTML
     * @access public
     */
    var $builder = null;

    /**
     * ��������
     * 
     * @var    array
     * @access private
     */
    var $_elements = array();

    /**
     * ���顼����å�����
     * 
     * @var    array
     * @access private
     */
    var $_errors = array();

    /**
     * ���顼����å������ο���
     * 
     * @var    array
     * @access private
     */
    var $_errorMessages = array();

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  Action $action  ��������󡦥���ȥ���
     * @param  string $app_dir ���ץꥱ�����������֥ѥ�
     * @access public
     */
    function Question(&$action, $app_dir)
    {
        $this->action         = &$action;
        $this->questionLoader = &new ClassLoader;

        if ($this->action->userAgent->isMobile()) {
            $this->builder = &new CHtml;
        } else {
            $this->builder = &new Html;
        }

        $this->questionLoader->setParents('Question');
        $this->questionLoader->setIncludePath(
            ROOT_PATH . PATH_SEPARATOR . $app_dir);
    }

    /**
     * �������������Ǥ��������ƴ���������Ͽ
     * 
     * @param  string $name   �������Ǥ�̾��
     * @param  string $type   �����������䥯�饹��̾��
     * @param  array  $params �����°��
     * @return boolean
     * @access public
     */
    function register($name, $type, $params)
    {
        $class_name = $this->questionLoader->load($type);
        if ($class_name === '') {
            trigger_error(
                "Question '${name}' type not found: '${type}'", 
                E_USER_WARNING);
            return false;
        }

        // ��ä���ʸ�����ޤޤ줿���˥��顼��å������䴰�˼��Ԥ���Τ�
        $name = strtolower($name);

        // ���Ǥ�����
        $element = &new $class_name($name, $this);
        foreach ($params as $varkey => $varvalue) {
            switch ($varkey) {
            case 'default' :
                // �ǥե�����ͤ���Ͽ
                if (is_scalar($varvalue) && is_scalar($element->value)) {
                    $element->value = $varvalue;
                } elseif (is_array($varvalue) && is_array($element->value)) {
                    $element->value = Utility::merge($element->value, $varvalue);
                } elseif ($varvalue !== null) {
                    trigger_error(
                        "Unable to set the '${name}' default: Disagreement of type", 
                        E_USER_WARNING);
                    return false;
                }
                break;
            case 'required' :
                // ɬ�ܡ�Ǥ�դ���Ͽ
                $element->required = ($varvalue === true);
                break;
            default : 
                if ($varvalue !== null) {
                    $element->$varkey = $varvalue;
                }
                break;
            }
        }
        $element->initialize();
        $this->_elements[$name] = &$element;

        return true;
    }

    /**
     * ��Ͽ����Ƥ����������Ǥ���
     * 
     * @param  string $element �������Ǥ�̾��
     * @return void
     * @access public
     */
    function remove($element)
    {
        if (isset($this->_elements[$element])) {
            unset($this->_elements[$element]);
        }
    }

    /**
     * �������Ǥβ�����õ�ƽ����
     * - ����ξõ��erase()�᥽�åɡˤȽ������initialize()�᥽�åɡˤ�¹�
     * - �����ư��Τ���ˤϾ嵭2�ĤΥ᥽�åɤ�Ū�Τ˼�������Ƥ���ɬ�פ���
     * 
     * @param  string $element �������Ǥ�̾��
     * @return void
     * @access public
     */
    function erase($element)
    {
        if (isset($this->_elements[$element])) {
            $this->_elements[$element]->erase();
            $this->_elements[$element]->initialize();
        }
    }

    /**
     * �������ˤ������Ƥ��������Ǥ�Ϣ��������ֵ�
     * - �ֵѤ���Ϣ������ϼ��פ�°���Τ߸���
     *  - string  label    => �������Ǥι���̾
     *  - string  html     => �����HTML
     *  - string  text     => ������ʸ����
     *  - mixed   value    => �����Υꥯ��������
     *   - �ͤγ�Ǽ��ˡ������˵��Τ�ɬ���������Υꥯ�������ͤǤϤʤ�
     *  - string  error    => ��Ͽ���줿���顼����å�����
     *   - ʣ���Υ��顼����Ͽ����Ƥ���кǸ����Ͽ���줿���顼����å�����
     *   - ���顼����Ͽ����Ƥ��ʤ���ж�ʸ����
     *  - boolean required => ɬ�ܡ�Ǥ��
     *   - ɬ�� => true
     *   - Ǥ�� => false
     * 
     * @param  void
     * @return array
     * @access public
     */
    function toArray()
    {
        $elements = array();

        foreach ($this->_elements as $name => $element) {
            $elements[$name] = array(
                    'label'    => $element->label, 
                    'html'     => $element->toHtml(), 
                    'text'     => $element->toText(), 
                    'value'    => $element->value, 
                    'error'    => $this->getError($name), 
                    'required' => $element->required, 
                );
        }

        return $elements;
    }

    /**
     * �������Ǥβ�����ʸ������ֵ�
     * 
     * @param  string $element �������Ǥ�̾��
     * @return string
     * @access public
     */
    function toText($element)
    {
        $text = '';

        if ($this->exists($element)) {
            $text = $this->_elements[$element]->toText();
        }

        return $text;
    }

    /**
     * �������ˤ������Ƥ��������Ǥǲ������������򸡾�
     * 
     * @param  void
     * @return void
     * @access public
     */
    function validate()
    {
        foreach ($this->_elements as $element) {
            if ($element->required === true) {
                // ɬ�ܹ��ܤ��ͤ�ɬ�ܾ��򽼤����Ƥ��뤫����
                // ���ڷ�̤����ʤ餹�Ǥ˥��顼�ʤΤǸ�ν����Ͼ�ά
                if (!$element->checkRequired()) {
                    continue;
                }
            } else {
                // Ǥ�չ��ܤ��ͤ�¸�ߤ��ʤ���и��ڤ�����
                if (!$this->isNotNull($element->name)) {
                    continue;
                }
            }

            $element->validate();
        }
    }

    /**
     * �������Ǥ˥��顼����å���������Ͽ
     * 
     * @param  string $element �������Ǥ�̾��
     * @param  string $message ���顼����å�����
     * @return void
     * @access public
     */
    function setError($element, $message)
    {
        if (!isset($this->_errors[$element])) {
            $this->_errors[$element] = array();
        }

        $this->_errors[$element][] = $message;
    }

    /**
     * �������Ǥ���Ͽ���줿���顼����å��������ֵ�
     * 
     * @param  string $element �������Ǥ�̾��
     * @return string
     * @access public
     */
    function getError($element)
    {
        $error = 
            (isset($this->_errors[$element]) && 
                (count($this->_errors[$element]) > 0)) ? 
                    end($this->_errors[$element]) : '';

        return $error;
    }

    /**
     * �����������ƤΥ��顼����å��������ֵ�
     * 
     * @param  void
     * @return array
     * @access public
     */
    function getAllError()
    {
        $error = array();

        foreach (array_keys($this->_elements) as $name) {
            $element_error = $this->getError($name);
            if ($element_error !== '') {
                $error[$name] = $element_error;
            }
        }

        return $error;
    }

    /**
     * �������Ǥ���Ͽ����Ƥ��륨�顼����å�������õ�
     * 
     * @param  string $element �������Ǥ�̾��
     * @return void
     * @access public
     */
    function clearError($element)
    {
        $this->_errors[$element] = array();
    }

    /**
     * �������Ǥ˥��顼����å���������Ͽ����Ƥ��뤫�򸡾�
     * 
     * @param  string $element �������Ǥ�̾��
     * @return boolean
     * @access public
     * @see    Question::getError()
     */
    function isError($element)
    {
        return 
            ($this->getError($element) !== '');
    }

    /**
     * �������˥��顼����å���������Ͽ����Ƥ��뤫�򸡾�
     * 
     * @param  void
     * @return boolean
     * @access public
     * @see    Question::getAllError()
     */
    function hasError()
    {
        return 
            (count($this->getAllError()) > 0);
    }

    /**
     * ���дؿ���expectError()�פΤ���Υ��顼����å������ο�������Ͽ
     * - ����Ŭ�����ϡ�action.validation.element�פȤ�������
     *  - action     => ��������󡦥���ȥ����̾��
     *  - validation => �Х�ǡ�������̾��
     *  - element    => �������Ǥ�̾��
     * - ��������Ȥ���ˤϡ�*�פΥ磻��ɥ����ɤ����
     * 
     * @param  string $replacement Ŭ�����
     * @param  string $message     ���顼����å������ο���
     * @return string
     * @access public
     * @see    Question::expectError()
     */
    function setErrorMessages($replacement, $message)
    {
        $this->_errorMessages[$replacement] = $message;
    }

    /**
     * �������Ǥ�̾���ȥХ�ǡ�������̾������
     * Ŭ���ʥ��顼����å���������Ͽ����Ƥ��������������
     * - ��Ŭ�ʿ����򸡺��������Ū������
     *  - ������Ŭ���������󥭡��Ȥ���Ϣ������ˤʤäƤ���
     *   - ���󥭡� => action.validation.element
     *    - action     => ��������󡦥���ȥ����̾��
     *    - validation => �Х�ǡ�������̾��
     *    - element    => �������Ǥ�̾��
     *  - ̾����������Ȥ�����ˤϡ�*�פ�磻��ɥ����ɤȤ���ɾ��
     *  - ��������ϡ�element > action > validation�פ�ɾ����ǰʲ��ΤȤ���
     *   1. action.validation.element
     *   2. action.*.element
     *   3. *.validation.element
     *   4. *.*.element
     *   5. action.validation.*
     *   6. *.validation.*
     *   7. *.*.*
     * - �����������ޤ줿���ꥭ����ɤ�ѡ���
     *  - ���ꥭ����� => %�ѿ�̾%���ѿ�̾ == �������ǤΥץ�ѥƥ�̾��
     *  - ���ꥭ����ɤΥѡ���
     *   1. preg_replace_callback()�ؿ������ꥭ����ɡ�%[\w]+%�פ򸡺�
     *   2. ������Хå���ƿ̾�ؿ����������ǤΥץ�ѥƥ����ͤ�Ȳ�
     *    - �������Ǥ����ꥭ����ɤ��ѿ�̾��Ʊ̾�Υץ�ѥƥ����ʤ�����ǧ
     *     - �ץ�ѥƥ���¸�ߤ����ʸ����˥��㥹�Ȥ����ִ�
     *     - �ץ�ѥƥ���¸�ߤ��ʤ���ж�ʸ������ִ�
     * - �ѡ���������̤򥨥顼����å������Ȥ����ֵ�
     * 
     * @param  string $element    �������Ǥ�̾��
     * @param  string $validation ���ڤ�̾��
     * @return string
     * @access public
     */
    function expectError($element, $validation)
    {
        $message = '';

        // ��Ŭ�ʥ��顼����å������ο����򸡺�
        $action     = strtolower($this->action->getName());
        $validation = strtolower($validation);
        $element    = strtolower($element);
        foreach (
            array(
                    '%1$s.%2$s.%3$s', 
                    '%1$s.*.%3$s', 
                    '*.%2$s.%3$s', 
                    '*.*.%3$s', 
                    '%1$s.%2$s.*', 
                    '*.%2$s.*', 
                    '*.*.*', 
                ) as $replacement) {
            $varkey = sprintf($replacement, $action, $validation, $element);
            if (!isset($this->_errorMessages[$varkey])) {
                continue;
            }

            $message = $this->_errorMessages[$varkey];
            break;
        }

        if ($message === '') {
            trigger_error(
                "Unable to expect error '${name}::${validation}'", E_USER_WARNING);
        }

        if (isset($this->_elements[$element])) {
            // ���������ꥭ����ɤ�ѡ���
            // �������Ǥ򥰥��Х��ѿ��ǻ��Ȥ���ƿ̾�ؿ�����ǻ���
            $GLOBALS['__QUESTION_ELEMENT'] = &$this->_elements[$element];
            $message = 
                preg_replace_callback(
                    '/%([\w]+)%/', 
                    create_function(
                        '$matches', 
                        '$key = $matches[1];' . 
                            '$msg = ' . 
                            'isset($GLOBALS["__QUESTION_ELEMENT"]->$key) ? ' . 
                            '(string)$GLOBALS["__QUESTION_ELEMENT"]->$key : "";' . 
                            'return $msg;'), 
                    $message);
            unset($GLOBALS['__QUESTION_ELEMENT']);

            $this->action->logAction(
                'QuestionFailure', 
                get_class($this->_elements[$element]) . ':' . $validation);
        }

        return $message;
    }

    /**
     * �������Ǥβ��������Ǥʤ����Ȥ򸡾�
     * 
     * @param  string $element �������Ǥ�̾��
     * @return boolean
     * @access public
     */
    function isNotNull($element)
    {
        return (
            isset($this->_elements[$element]) && 
            ($this->_elements[$element]->toText() !== ''));
    }

    /**
     * �������Ǥ���������¸�ߤ��Ƥ��뤫�򸡾�
     * 
     * @param  string $element �������Ǥ�̾��
     * @return boolean
     * @access public
     */
    function exists($element)
    {
        return 
            isset($this->_elements[$element]);
    }

    /**
     * �������Ǥ��ֵ�
     * 
     * @param  string $element �������Ǥ�̾��
     * @return Question_Common
     * @access public
     */
    function &getElement($element)
    {
        $obj = null;
        if (isset($this->_elements[$element])) {
            $obj = &$this->_elements[$element];
        }

        return $obj;
    }
}
