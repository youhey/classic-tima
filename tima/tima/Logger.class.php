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
 * ���������饹�Υ����ѡ����饹
 * ���󥰽����ϼ������Ƥ��ޤ���
 * 
 * @package  tima
 * @version  SVN: $Id: Logger.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Logger
{

    /**
     * ����ޥ��������٥�
     * 
     * @var    integer
     * @access private
     */
    var $_maskLevel = TW_LOG_NOTICE;

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  integer $level
     * @param  array   $option
     * @access public
     */
    function Logger($level, $option = null)
    {
        switch (true) {
        case $level === TW_LOG_TRACE : 
        case $level === TW_LOG_DEBUG : 
        case $level === TW_LOG_INFO : 
        case $level === TW_LOG_NOTICE : 
        case $level === TW_LOG_WARN : 
        case $level === TW_LOG_ERROR : 
        case $level === TW_LOG_FATAL : 
            $this->_maskLevel = $level;
            break;
        default : 
            trigger_error(
                "Unknown log-level '${level}' in Logger Class", 
                E_USER_WARNING);
            $this->_maskLevel = TW_LOG_NOTICE;
        }

        if (!is_array($option)) {
            $option = array();
        }
        $this->initialize($option);
    }

    /**
     * �����ν��������
     * �Ѿ����饹�ǽ�������Ф�������򥪡��С��饤��
     * 
     * @param  array $option
     * @return void
     * @access public
     */
    function initialize($option) {}

    /**
     * TRACE��٥�Υ�å��������������뤿��Υ��硼�ȥ��å�
     * 
     * @param  string $message ��å�����
     * @param  string $file    �ƤӽФ����Υե�����̾
     * @param  string $line    �ƤӽФ����ι��ֹ�
     * @return void
     * @access public
     */
    function trace($message, $file = null, $line = null)
    {
        $this->logging(TW_LOG_TRACE, $message, $file, $line);
    }

    /**
     * DEBUG��٥�Υ�å��������������뤿��Υ��硼�ȥ��å�
     * 
     * @param  string $message ��å�����
     * @param  string $file    �ƤӽФ����Υե�����̾
     * @param  string $line    �ƤӽФ����ι��ֹ�
     * @return void
     * @access public
     */
    function debug($message, $file = null, $line = null)
    {
        $this->logging(TW_LOG_DEBUG, $message, $file, $line);
    }

    /**
     * INFO��٥�Υ�å��������������뤿��Υ��硼�ȥ��å�
     * 
     * @param  string $message ��å�����
     * @param  string $file    �ƤӽФ����Υե�����̾
     * @param  string $line    �ƤӽФ����ι��ֹ�
     * @return void
     * @access public
     */
    function info($message, $file = null, $line = null)
    {
        $this->logging(TW_LOG_INFO, $message, $file, $line);
    }

    /**
     * INFO��٥�Υ�å��������������뤿��Υ��硼�ȥ��å�
     * 
     * @param  string $message ��å�����
     * @param  string $file    �ƤӽФ����Υե�����̾
     * @param  string $line    �ƤӽФ����ι��ֹ�
     * @return void
     * @access public
     */
    function notice($message, $file = null, $line = null)
    {
        $this->logging(TW_LOG_NOTICE, $message, $file, $line);
    }

    /**
     * WARN��٥�Υ�å��������������뤿��Υ��硼�ȥ��å�
     * 
     * @param  string $message ��å�����
     * @param  string $file    �ƤӽФ����Υե�����̾
     * @param  string $line    �ƤӽФ����ι��ֹ�
     * @return void
     * @access public
     */
    function warn($message, $file = null, $line = null)
    {
        $this->logging(TW_LOG_WARN, $message, $file, $line);
    }

    /**
     * Log error��å��������������뤿��Υ��硼�ȥ��å�
     * 
     * @param  string $message ��å�����
     * @param  string $file    �ƤӽФ����Υե�����̾
     * @param  string $line    �ƤӽФ����ι��ֹ�
     * @return void
     * @access public
     */
    function error($message, $file = null, $line = null)
    {
        $this->logging(TW_LOG_ERROR, $message, $file, $line);
    }

    /**
     * FATAL��٥�Υ�å��������������뤿��Υ��硼�ȥ��å�
     * 
     * @param  string $message ��å�����
     * @param  string $file    �ƤӽФ����Υե�����̾
     * @param  string $line    �ƤӽФ����ι��ֹ�
     * @return void
     * @access public
     */
    function fatal($message, $file = null, $line = null)
    {
        $this->logging(TW_LOG_FATAL, $message, $file, $line);
    }

    /**
     * ������å������ν���
     * 
     * @param  integer $level   ���顼��٥�
     * @param  string  $message ��å�����
     * @param  string  $file    �ƤӽФ����Υե�����̾
     * @param  string  $line    �ƤӽФ����ι��ֹ�
     * @return void
     * @access public
     * @abstract
     */
    function logging($level, $message, $file, $line) {}

    /**
     * ���Υޥ�������٥���ֵ�
     * 
     * @param  void
     * @return integer 
     * @access protected
     */
    function getMask()
    {
        return $this->_maskLevel;
    }

    /**
     * ���顼��٥��ɽ��̾���ֵ�
     * 
     * @param  integer $level ���顼��٥�
     * @return string
     * @access protected
     */
    function getLevel($level)
    {
        $error_class = '';

        switch (true) {
        case $level === TW_LOG_TRACE : 
            $error_class = 'trace';
            break;
        case $level === TW_LOG_DEBUG : 
            $error_class = 'debug';
            break;
        case $level === TW_LOG_INFO : 
            $error_class = 'info';
            break;
        case $level === TW_LOG_NOTICE : 
            $error_class = 'notice';
            break;
        case $level === TW_LOG_WARN : 
            $error_class = 'warning';
            break;
        case $level === TW_LOG_ERROR : 
            $error_class = 'error';
            break;
        case $level === TW_LOG_FATAL : 
            $error_class = 'fatal';
            break;
        }

        return $error_class;
    }

    /**
     * ���顼��å�������ɸ��񼰤��ֵ�
     * 
     * @param  integer $level   ���顼��٥�
     * @param  string  $message ��å�����
     * @param  string  $file    �ƤӽФ����Υե�����̾
     * @param  string  $line    �ƤӽФ����ι��ֹ�
     * @return string
     * @access protected
     */
    function formatMessage($level, $message, $file, $line)
    {
        $release = array();
        if ($file !== null) {
            $release[] = $file;
        }
        if ($line !== null) {
            $release[] = $line;
        }
        $release = implode(' / ', $release);
        if ($release !== '') {
            $message = "${message} (${release})";
        }

        return 
            sprintf("[%s] [%s] %s\n", 
                date("Y-m-d H:i:s"), $this->getLevel($level), $message);
    }
}
