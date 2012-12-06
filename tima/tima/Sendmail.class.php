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
 * �᡼���������饹
 * 
 * ���������ˡ�sendmail�ץ��ޥ�ɤ����
 * ��ǽ��ɬ�׺����
 * 
 * @package  tima
 * @version  SVN: $Id: Sendmail.class.php 19 2007-09-07 07:51:47Z do_ikare $
 */
class Sendmail
{

    /**
     * ��̾
     * 
     * @var    string
     * @access private
     */
    var $_subject = '';

    /**
     * �᡼����ʸ
     * 
     * @var    string
     * @access private
     */
    var $_body = '';

    /**
     * ������
     * 
     * @var    array
     * @access private
     */
    var $_from = array('email'=>'', 'name'=>'');

    /**
     * ������
     * 
     * @var    array
     * @access private
     */
    var $_to = array('email'=>'', 'name'=>'');

    /**
     * �ֿ���
     * 
     * @var    array
     * @access private
     */
    var $_replyTo = array('email'=>'', 'name'=>'');

    /**
     * ���顼�����
     * 
     * @var    array
     * @access private
     */
    var $_returnPath = array('email'=>'', 'name'=>'');

    /**
     * �᡼��ʸ��������
     * 
     * @var    string
     * @access private
     */
    var $_emailEncoding = 'iso-2022-jp';

    /**
     * ����ʸ��������
     * 
     * @var    string
     * @access private
     */
    var $_internalEncoding = 'EUC-JP';

    /**
     * �᡼����ʸ�Υ��󥳡�������
     * 
     * @var    string
     * @access private
     */
    var $_transferEncoding = '7bit';

    /**
     * �إå��β��ԥ�����
     * 
     * @var    string
     * @access private
     */
    var $_linefeed = 'CRLF';

    /**
     * ���ԥ����ɡ�CR��
     * 
     * @var    string
     * @access private
     */
    var $_cr = "\x0D";

    /**
     * ���ԥ����ɡ�LF��
     * 
     * @var    string
     * @access private
     */
    var $_lf = "\x0A";

    /**
     * ���ԥ����ɡ�CRLF��
     * 
     * @var    string
     * @access private
     */
    var $_crlf = "\x0D\x0A";

    /**
     * ���󥹥ȥ饯��
     *
     * @param  string $email_encoding
     * @param  string $internal_encoding
     * @param  string $transfer_encoding
     * @param  string $linefeed
     * @access Public
     */
    function Sendmail($email_encoding = 'iso-2022-jp', 
                      $internal_encoding = 'EUC-JP', 
                      $transfer_encoding = '7bit', 
                      $linefeed = "CRLF")
    {
        $this->_emailEncoding    = $email_encoding;
        $this->_internalEncoding = $internal_encoding;
        $this->_linefeed         = $linefeed;
    }

    /**
     * ��̾����Ͽ
     *
     * @param  string $subject
     * @return void
     * @access public
     */
    function setSubject($subject)
    {
        $this->_subject = $subject;
    }

    /**
     * �᡼����ʸ����Ͽ
     *
     * @param  string $body
     * @return void
     * @access public
     */
    function setBody($body)
    {
        $this->_body = $this->_formatBodyText($body);
    }

    /**
     * ����������Ͽ
     *
     * @param  string $email
     * @param  string $name
     * @return void
     * @access public
     */
    function setFrom($email, $name = '')
    {
        $this->_from = array('email' => $email, 'name' => $name);
    }

    /**
     * ���������Ͽ
     *
     * @param  string $email
     * @param  string $name
     * @return void
     * @access public
     */
    function setTo($email, $name = '')
    {
        $this->_to = array('email' => $email, 'name' => $name);
    }

    /**
     * �ֿ������Ͽ
     *
     * @param  string $email
     * @param  string $name
     * @return void
     * @access public
     */
    function setReplyTo($email, $name = '')
    {
        $this->_replyTo = array('email' => $email, 'name' => $name);
    }

    /**
     * ���顼����������Ͽ
     *
     * @param  string $email
     * @param  string $name
     * @return void
     * @access public
     */
    function setReturnPath($email, $name = '')
    {
        $this->_returnPath = array('email' => $email, 'name' => $name);
    }

    /**
     * �᡼�������
     *
     * @param  string $command
     * @return boolean
     * @access public
     */
    function send($command = '/usr/sbin/sendmail')
    {
        $header = $this->_buildHeader();
        if ($header === false) {
            return false;
        }

        $sendmail = 
            popen(
                sprintf(
                    '%s -t -f %s', 
                    escapeshellcmd($command), 
                    escapeshellarg(
                        ($this->_returnPath['email'] !== '') ? 
                            $this->_returnPath['email'] : $this->_from['email'])), 
                'w');
        if ($sendmail === false) {
            trigger_error("Unable to open the 'sendmail command'", E_USER_WARNING);
            return false;
        }
        $separator = ($this->_linefeed === 'CRLF') ? $this->_crlf : $this->_lf;
        fputs($sendmail, implode($separator, $header));
        fputs($sendmail, $separator . $separator);
        fputs(
            $sendmail, 
            str_replace(
                $this->_cr, 
                $this->_lf, 
                str_replace(
                    $this->_crlf, 
                    $this->_lf, 
                    $this->_body)));
        $resultant = pclose($sendmail);

        if (version_compare(phpversion(), '4.2.3') === -1) {
            $resultant = ($resultant >> 8 & 0xFF);
        }
        if ($resultant !== 0) {
            trigger_error("Unable to send the Email - ${resultant}", E_USER_WARNING);

            return false;
        }

        return true;
    }

    /**
     * �إå�������
     *
     * @param  void
     * @return array|false
     * @access private
     */
    function _buildHeader()
    {
        $header = array();

        // ��������
        $header[] = $this->_expectHeader('Date', date('r'));
        // $header[] = $this->_expectHeader('Date', date('j M Y G:i:s O'));

        // ��å�����ID
        if (preg_match('/^[^@]+@(.+)$/i', $this->_from['email'], $match)) {
            // md5(uniqid(rand(), true)) => uniqid(date('YmdHis.'))
            $header[] = 
                $this->_expectHeader(
                    'Message-ID', 
                    sprintf('<%s@%s>', uniqid(date('YmdHis.')), $match[1]));
        }

        // ������
        if ($this->_from['email'] === '') {
            trigger_error(
                "Unable to build the header - 'From' not exist", E_USER_WARNING);
            return false;
        }
        $header[] = 
            $this->_expectHeader('From', $this->_formatMailAddress($this->_from));

        // �ֿ���
        if ($this->_replyTo['email'] !== '') {
            $header[] = 
                $this->_expectHeader(
                    'Reply-To', $this->_formatMailAddress($this->_replyTo));
        }

        // ���顼�����
        if ($this->_returnPath['email'] !== '') {
            $header[] = 
                $this->_expectHeader(
                    'Return-Path', $this->_returnPath);
        }

        // ������
        if ($this->_to['email'] === '') {
            trigger_error(
                "Unable to build the header - 'To' not exist", E_USER_WARNING);
            return false;
        }
        $header[] = 
            $this->_expectHeader('To', $this->_formatMailAddress($this->_to));

        // MIME�С������
        $header[] = $this->_expectHeader('MIME-Version', '1.0');

        // ��̾
        $header[] = 
            $this->_expectHeader('Subject', $this->_encodeMIME($this->_subject));

        // ���󥳡��ɾ���
        $header[] = 
            $this->_expectHeader(
                'Content-Type', 
                'text/plain; charset=' . 
                    strtolower(mb_preferred_mime_name($this->_emailEncoding)));
        $header[] = $this->_expectHeader(
            'Content-Transfer-Encoding', $this->_transferEncoding);

        return $header;
    }

    /**
     * �إå����Ǥ���Ͽ
     * 
     * @param  string $category
     * @param  string $element
     * @return void
     * @access private
     */
    function _expectHeader($category, $element)
    {
        return 
            str_replace(
                array($this->_cr, $this->_lf), 
                '', 
                "${category}: ${element}");
    }

    /**
     * MIME�����˥��󥳡���
     * 
     * @param  string $attribute
     * @return string
     * @access private
     */
    function _encodeMIME($attribute)
    {
        return 
            '=?' . mb_preferred_mime_name($this->_emailEncoding) . '?B?' . 
            base64_encode(
                str_replace(
                    array($this->_cr, $this->_lf), 
                    '', 
                    $this->_convertEncoding($attribute))) . 
            '?=';
    }

    /**
     * �إå��Υ᡼�륢�ɥ쥹������
     * - ̾��������С�̾�� <�᡼�륢�ɥ쥹>�פν񼰤�
     *  - ̾����MIME base64�����ǥ��󥳡���
     * - ̾�����ʤ���С֥᡼�륢�ɥ쥹�פΤߤ�
     * 
     * @param  string $body
     * @return string
     * @access private
     */
    function _formatMailAddress($email_address)
    {
        $formatted = 
            ($email_address['name'] !== '') ? 
                sprintf(
                    '%s <%s>', 
                    $this->_encodeMIME($email_address['name']), 
                    $email_address['email']) : 
                $email_address['email'];

        return $formatted;
    }

    /**
     * �᡼����ʸ������
     * - ���פʶ������ʲ��Ԥ����
     * 
     * @param  string $body
     * @return string
     * @access private
     */
    function _formatBodyText($body)
    {
        return 
            preg_replace(
                '/(\n{2,})/', "\n\n", 
                preg_replace(
                    array('/^([\t ]+)/m', '/([\t ]+)$/m'), '', 
                    str_replace(
                        array("\r\n", "\n\r", "\r"), "\n", 
                        $this->_convertEncoding($body))));
    }

    /**
     * ����ʸ�������ɤ���᡼���ʸ�������ɤ��Ѵ�
     * 
     * @param  string $attribute
     * @return string
     * @access private
     */
    function _convertEncoding($attribute)
    {
        return 
            mb_convert_encoding(
                $attribute, $this->_emailEncoding, $this->_internalEncoding);
    }
}
