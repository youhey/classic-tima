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
 * �ץ����Υ��å�����������
 * 
 * - ���Ū�����ˤ�륻�å��������
 * - ���å����ǡ�����̾�����֤�⤿����
 *  - ¿�ץ����֤ǤΥ��å����ζ�¸
 *  - ¿�ץ����֤Ǥθĥǡ������ݸ�
 * - ���å����γ�ư���֤����
 * - ���å����γ�ư���֤��İ�
 * 
 * @package  tima
 * @version  SVN: $Id: Session.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Session
{

    /**
     * ̾������
     * 
     * @var    string
     * @access private
     */
    var $_namespace = '';

    /**
     * ���å���ͭ�����֡��á�
     * 
     * @var    integer
     * @access private
     */
    var $_lifetime = 0;

    /**
     * ���å����Υ��å�����̵ͭ
     * 
     * @var    boolean
     * @access private
     */
    var $_useCookies = true;

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  string  $namespace    ̾������
     * @param  string  $session_name ���å����̾
     * @param  boolean $lifetime     ���å���ͭ�����֡��ÿ���
     * @access public
     */
    function Session($namespace = 'noname', $session_name = null, $lifetime = null)
    {
        $this->_namespace = $namespace;

        if ($this->isStarted()) {
            // ���å���󤬳��Ϥ���Ƥ���С�̾�����֤ν����
            if (!isset($_SESSION['__sc'][$this->_namespace])) {
               $_SESSION['__sc'][$this->_namespace] = array();
            }
        } else {
            // ���å���󤬳������ʤ�н��������򹹿�

            $this->_useCookies = (bool)ini_get('session.use_cookies');

            if (isset($session_name)) {
                if (!is_string($session_name) || ($session_name === '')) {
                    trigger_error(
                        "Unable to construct the 'Session' - " . 
                            "session name demands a non-empty string", 
                        E_USER_WARNING);
                } else {
                    session_name($session_name);
                }
            }

            if (isset($lifetime) && $this->_useCookies) {
                session_set_cookie_params((int)$lifetime);
            }

            session_cache_limiter('private, must-revalidate');
        }
    }

    /**
     * ���å����ID���ֵ�
     *
     * @param  void
     * @return string  ���å����ID
     * @access public
     */
    function getId()
    {
        return session_id();
    }

    /**
     * ���å����ID�򿷤�������
     * 
     * ���å�������¸��ˡ�ϡ֥ե�����׸���Ǥ��뤳�Ȥ�����Ȥ���Τǡ�
     * ���å����ID��[a-zA-Z0-9]���ϰ���Υ������ʸ��������
     * 
     * @param  string  $id ���å����ID
     * @return boolean
     * @access public
     */
    function setId($id)
    {
        if (headers_sent($filename, $linenum)) {
            trigger_error(
                'Unable to set the session-id, ' . 
                    "sent to the browser in $filename/$linenum", 
                E_USER_WARNING);
            return false;
        }
        if (!is_string($id) || ($id === '') || !preg_match('/^[a-z0-9]+$/i', $id)) {
            trigger_error(
                'Unable to set the session-id - ' . 
                    'session-id demands a non-empty string ([a-zA-Z0-9]).', 
                E_USER_WARNING);
            return false;
        }

        session_id($id);

        return true;
    }

    /**
     * ���å����̾���ֵ�
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getSessionName()
    {
        return session_name();
    }

    /**
     * ���å����򳫻�
     * 
     * ��ŤθƤӽФ��ޤ��ϳ����ǤΥ��å����γ��Ϥ�����ʤ�
     * �����ǳ��Ϥ�����Τ�ͣ��λ�ư�Ȥʤ�
     * 
     * ���å����ID�θ��경��Ǿ��¤���������褦������ǥ��å����������
     * - ̤���ѤΥ��å����ʽ��Ƴ��Ϥ���륻�å�����
     * - 1���ְʾ�вᤷ�����å����
     * - 100ʬ��1�γ�Ψ
     *
     * @param  void
     * @return boolean
     * @access public
     */
    function start()
    {
        if (headers_sent($filename, $linenum)) {
            trigger_error(
                'Unable to start the session - ' . 
                    "sent to the browser. in ${filename}/${linenum}", 
                E_USER_WARNING);
            return false;
        }
        if ($this->_status('started') || defined('SID')) {
            trigger_error('Session has already been start.', E_USER_WARNING);
            return false;
        }

        if (session_start() === false) {
            trigger_error('Unable to start the session.', E_USER_WARNING);
            return false;
        }

        // ���ơ���������
        $this->_status('started', true);
        $this->_status('readable', true);
        $this->_status('writable', true);

        // �Ķ�����
        if (!isset($_SESSION['__sc'])) {
            $_SESSION['__sc'] = array();
        }
        if (!isset($_SESSION['__sc'][$this->_namespace])) {
            $_SESSION['__sc'][$this->_namespace] = array();
        }

        // ���ˤ�������
        $nowtime = time();
        $prime   = 
            (isset($_SESSION['__sc']['__started']) ? 
                $_SESSION['__sc']['__started'] : null);
        if (($prime === null) || ($prime < ($nowtime-HOUR)) || (rand(0,100) > 99)) {
            $this->regenerate();

            $_SESSION['__sc']['__started'] = $nowtime;
            $_SESSION['__sc']['__client']  = array(
                    'ip_address' => (isset($_SERVER['REMOTE_ADDR']) ? 
                        $_SERVER['REMOTE_ADDR'] : null),
                    'user_agent' => (isset($_SERVER['HTTP_USER_AGENT']) ? 
                        $_SERVER['HTTP_USER_AGENT'] : null),
                );
        }

        return true;
    }

    /**
     * ���å������˴�
     * 
     * - ���å���󤬳��Ϥ��줤�ʤ���а۾ｪλ���ޤ�
     * - ��å�������̵�뤷�ޤ�
     * 
     * @param  void
     * @return boolean
     * @access public
     */
    function destroy()
    {
        if (!$this->_status('started')) {
            header('HTTP/1.1 500 Internal Server Error');
            trigger_error(
                'Unable to destroy the session - session does not begin.', 
                E_USER_WARNING);
            return false;
        }
        // session_destroy() �ϥե����������������ǥ���γ����ޤǤϤ��ʤ�����
        $_SESSION = array();
        session_destroy();

        $this->_status('started', false);
        $this->_status('readable', false);
        $this->_status('writable', false);

        if ($this->_useCookies) {
            $session_name = session_name();
            if (array_key_exists($session_name, $_COOKIE)) {
                setcookie($session_name, '', 0, '/');
            }
        }

        return true;
    }

    /**
     * ���å��������
     *
     * - ���å���󤬳��Ϥ��줤�ʤ���а۾ｪλ���ޤ�
     * - ��å�������̵�뤷�ޤ�
     * 
     * @param  void
     * @return boolean
     * @access public
     */
    function regenerate()
    {
        if (!$this->_status('started')) {
            header('HTTP/1.1 500 Internal Server Error');
            trigger_error(
                'Unable to regenerate the session - session does not begin.', 
                E_USER_WARNING);
            return false;
        }

        // ���å����������Ū�����򤷤Ƥ���õ�
        $tmp = array();
        foreach ($_SESSION['__sc'] as $varkey => $varvalue) {
            if (strpos($varkey, '__') === 0) {
                continue;
            }
            $tmp[$varkey] = $varvalue;
        }
        $_SESSION = array();

        // destroy() => start() ������ľ��Ū�Ǹ�Ψ�褤���Ȼפä���
        // Cookie�Υ��å����ID�Υ��åȤ����ޤ������ʤ��褦�ʤΤ��ѹ�
        $old_session_id = session_id();
        session_regenerate_id();

        // ���פȤʤä����å���󡦥ե��������
        $old_session_file = 
            session_save_path() . DIRECTORY_SEPARATOR . 'sess_' . $old_session_id;
        if (file_exists($old_session_file)) {
            @unlink($old_session_file);
        }

        $this->_status('started', true);
        $this->_status('readable', true);
        $this->_status('writable', true);

        // ���å������������
        $_SESSION['__sc'] = $tmp;

        return true;
    }

    /**
     * ���å�����ͤ���Ͽ
     *
     * @param  string  $varkey   ����
     * @param  mixed   $varvalue ��
     * @return boolean
     * @access public
     */
    function set($varkey, $varvalue)
    {
        if (!$this->_status('started') || !$this->_status('writable')) {
            return false;
        }

        $_SESSION['__sc'][$this->_namespace][$varkey] = $varvalue;

        return true;
    }

    /**
     * ���å��������¸�ΰ���ͤ���Ͽ
     * 
     * @param  string  $varkey   ����
     * @param  mixed   $varvalue ��
     * @return boolean
     * @access public
     */
    function setFlash($varkey, $varvalue)
    {
        if (!$this->_status('started') || !$this->_status('writable')) {
            return false;
        }

        if (!isset($_SESSION['__sc'][$this->_namespace]['__flash'])) {
            $_SESSION['__sc'][$this->_namespace]['__flash'] = array();
        }
        $_SESSION['__sc'][$this->_namespace]['__flash'][$varkey] = $varvalue;

        return $varvalue;
    }

    /**
     * ���å�����ͤ��ֵ�
     *
     * @param  string  $varkey ����
     * @return mixed
     * @access public
     */
    function get($varkey)
    {
        if (!$this->_status('started') || !$this->_status('readable')) {
            return null;
        }
        if (!isset($_SESSION['__sc'][$this->_namespace][$varkey])) {
            return null;
        }

        return $_SESSION['__sc'][$this->_namespace][$varkey];
    }

    /**
     * ���å�����ͤ��ֵ�
     *
     * @param  void
     * @return array|null
     * @access public
     */
    function getAll()
    {
        if (!$this->_status('started') || !$this->_status('readable')) {
            return null;
        }

        $buf = array();
        foreach ($_SESSION['__sc'][$this->_namespace] as $varkey => $varvalue) {
            if (strpos($varkey, '__') === 0) {
                continue;
            }
            $buf[$varkey] = $varvalue;
        }

        return $buf;
    }

    /**
     * ���å��������¸�ΰ���ͤ��ֵ�
     * 
     * ���å��������¸�ΰ�ϰ�󤭤�λȤ�����ʤΤǡ�
     * ��¸����Ƥ����ͤ��ֵѤ�Ʊ��������
     * 
     * @param  string  $varkey ����
     * @return mixed
     * @access public
     */
    function getFlash($varkey)
    {
        if (!$this->_status('started') || !$this->_status('readable')) {
            return null;
        }
        if (!isset($_SESSION['__sc'][$this->_namespace]['__flash'][$varkey])) {
            return null;
        }

        $varvalue = $_SESSION['__sc'][$this->_namespace]['__flash'][$varkey];
        unset($_SESSION['__sc'][$this->_namespace]['__flash'][$varkey]);

        return $varvalue;
    }

    /**
     * ���å�����ͤ���
     *
     * @param  string  $varkey ����
     * @return boolean
     * @access public
     */
    function remove($varkey)
    {
        if (!$this->_status('started') || !$this->_status('writable')) {
            return false;
        }
        if (!array_key_exists($varkey, $_SESSION['__sc'][$this->_namespace])) {
            return false;
        }

        unset($_SESSION['__sc'][$this->_namespace][$varkey]);

        return true;
    }

    /**
     * ���ꤷ�������Υ��å�����ͤ�¸�ߤ��뤫�򸡾�
     *
     * @param  string  $varkey ����
     * @return boolean
     * @access public
     */
    function exists($varkey)
    {
        if (!$this->_status('started')) {
            return false;
        }

        return array_key_exists($varkey, $_SESSION['__sc'][$this->_namespace]);
    }

    /**
     * ̾�����֤Υ��å������˲�
     * 
     * ̾�����֤���ͭ�������Ƥ��ͤ��������Ǿ�񤭤��ޤ�
     * ¾��̾�����֤ˤϱƶ����ޤ���
     * 
     * @param  void
     * @return boolean
     * @access public
     */
    function clear()
    {
        if (!$this->_status('started')) {
            trigger_error(
                'Unable to clear the session - session does not begin.', 
                E_USER_WARNING);
            return false;
        }

        $_SESSION['__sc'][$this->_namespace] = array();

        return true;
    }

    /**
     * ���å���󤬳�ư���Ƥ��뤫�򸡺�
     * 
     * @param  void
     * @return boolean
     * @access public
     */
    function isSessionExists()
    {
        $session_name = session_name();

        return 
            (($this->_useCookies && isset($_COOKIE[$session_name])) || 
             (isset($_GET[$session_name]) || isset($_POST[$session_name])));
    }

    /**
     * ���å���󤬳��Ϥ��Ƥ��뤫�򸡺�
     * 
     * @param  void
     * @return boolean
     * @access public
     */
    function isStarted()
    {
        return $this->_status('started');
    }

    /**
     * �񤭹��ߤ��å�
     * 
     * @param  void
     * @return void
     * @access public
     */
    function lock()
    {
        $this->_status('writable', false);
    }

    /**
     * ���å������Ф��������Ϥ�λ
     * 
     * @param  void
     * @return void
     * @access public
     */
    function stop()
    {
        $this->_status('writable', false);
        $this->_status('readable', false);
        $this->_status('started', false);
    }

    /**
     * ���֤����
     * 
     * @param  string  $type
     * @param  boolean $modif
     * @return boolean
     * @access private
     * @static array $state
     */
    function _status($type, $modif = null)
    {
        static $state = array();

        if (is_bool($modif)) {
            $state[$type] = $modif;
        }

        return (isset($state[$type]) && $state[$type]);
    }
}
