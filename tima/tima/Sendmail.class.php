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
 * メール送信クラス
 * 
 * 送信処理に「sendmail」コマンドを使用
 * 機能は必要最低限
 * 
 * @package  tima
 * @version  SVN: $Id: Sendmail.class.php 19 2007-09-07 07:51:47Z do_ikare $
 */
class Sendmail
{

    /**
     * 件名
     * 
     * @var    string
     * @access private
     */
    var $_subject = '';

    /**
     * メール本文
     * 
     * @var    string
     * @access private
     */
    var $_body = '';

    /**
     * 送信元
     * 
     * @var    array
     * @access private
     */
    var $_from = array('email'=>'', 'name'=>'');

    /**
     * 送信先
     * 
     * @var    array
     * @access private
     */
    var $_to = array('email'=>'', 'name'=>'');

    /**
     * 返信先
     * 
     * @var    array
     * @access private
     */
    var $_replyTo = array('email'=>'', 'name'=>'');

    /**
     * エラー戻り先
     * 
     * @var    array
     * @access private
     */
    var $_returnPath = array('email'=>'', 'name'=>'');

    /**
     * メール文字コード
     * 
     * @var    string
     * @access private
     */
    var $_emailEncoding = 'iso-2022-jp';

    /**
     * 内部文字コード
     * 
     * @var    string
     * @access private
     */
    var $_internalEncoding = 'EUC-JP';

    /**
     * メール本文のエンコード方式
     * 
     * @var    string
     * @access private
     */
    var $_transferEncoding = '7bit';

    /**
     * ヘッダの改行コード
     * 
     * @var    string
     * @access private
     */
    var $_linefeed = 'CRLF';

    /**
     * 改行コード「CR」
     * 
     * @var    string
     * @access private
     */
    var $_cr = "\x0D";

    /**
     * 改行コード「LF」
     * 
     * @var    string
     * @access private
     */
    var $_lf = "\x0A";

    /**
     * 改行コード「CRLF」
     * 
     * @var    string
     * @access private
     */
    var $_crlf = "\x0D\x0A";

    /**
     * コンストラクタ
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
     * 件名を登録
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
     * メール本文を登録
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
     * 送信元を登録
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
     * 送信先を登録
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
     * 返信先を登録
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
     * エラーの戻り先を登録
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
     * メールを送信
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
     * ヘッダを生成
     *
     * @param  void
     * @return array|false
     * @access private
     */
    function _buildHeader()
    {
        $header = array();

        // 送信日時
        $header[] = $this->_expectHeader('Date', date('r'));
        // $header[] = $this->_expectHeader('Date', date('j M Y G:i:s O'));

        // メッセージID
        if (preg_match('/^[^@]+@(.+)$/i', $this->_from['email'], $match)) {
            // md5(uniqid(rand(), true)) => uniqid(date('YmdHis.'))
            $header[] = 
                $this->_expectHeader(
                    'Message-ID', 
                    sprintf('<%s@%s>', uniqid(date('YmdHis.')), $match[1]));
        }

        // 送信元
        if ($this->_from['email'] === '') {
            trigger_error(
                "Unable to build the header - 'From' not exist", E_USER_WARNING);
            return false;
        }
        $header[] = 
            $this->_expectHeader('From', $this->_formatMailAddress($this->_from));

        // 返信先
        if ($this->_replyTo['email'] !== '') {
            $header[] = 
                $this->_expectHeader(
                    'Reply-To', $this->_formatMailAddress($this->_replyTo));
        }

        // エラー戻り先
        if ($this->_returnPath['email'] !== '') {
            $header[] = 
                $this->_expectHeader(
                    'Return-Path', $this->_returnPath);
        }

        // 送信先
        if ($this->_to['email'] === '') {
            trigger_error(
                "Unable to build the header - 'To' not exist", E_USER_WARNING);
            return false;
        }
        $header[] = 
            $this->_expectHeader('To', $this->_formatMailAddress($this->_to));

        // MIMEバージョン
        $header[] = $this->_expectHeader('MIME-Version', '1.0');

        // 件名
        $header[] = 
            $this->_expectHeader('Subject', $this->_encodeMIME($this->_subject));

        // エンコード情報
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
     * ヘッダ要素を登録
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
     * MIME形式にエンコード
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
     * ヘッダのメールアドレスを整形
     * - 名前があれば「名前 <メールアドレス>」の書式に
     *  - 名前はMIME base64方式でエンコード
     * - 名前がなければ「メールアドレス」のみに
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
     * メール本文を整形
     * - 不要な空白や過剰な改行を除去
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
     * 内部文字コードからメールの文字コードに変換
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
