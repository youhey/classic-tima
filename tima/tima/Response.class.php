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
 * リクエストに対する応答をサポートするためのクラス
 * 
 * HTTPヘッダを一元管理するために独立した応答クラスを定義
 * クッキも使用するようであれば、このクラスまたは継承クラスに機能を実装する
 * 
 * @package  tima
 * @version  SVN: $Id: Response.class.php 37 2007-10-12 06:51:54Z do_ikare $
 */
class Response
{

    /**
     * HTTPヘッダ
     * 
     * @var    array
     * @access private
     */
    var $_headerFields = array();

    /**
     * ボディパート
     * 
     * @var    string
     * @access private
     */
    var $_contents = '';

    /**
     * テンプレートのデータ・モデル
     * 
     * @var    array
     * @access private
     */
    var $_dataModel = array();

    /**
     * HTTPプロトコル・バージョン
     * 
     * @var    string
     * @access private
     */
    var $_protocol = 'HTTP/1.0';

    /**
     * HTTPヘッダの結果ステータス（コード）
     * 
     * @var    string
     * @access private
     */
    var $_statusCode = '200';

    /**
     * HTTPヘッダの結果ステータス（テキスト）
     * 
     * @var    string
     * @access private
     */
    var $_statusText = 'OK';

    /**
     * HTTPヘッダの結果ステータス定義一覧
     * 
     * @var    array
     * @access private
     */
    var $_statusTextList = array(
            '100' => 'Continue',
            '101' => 'Switching Protocols',
            '200' => 'OK',
            '201' => 'Created',
            '202' => 'Accepted',
            '203' => 'Non-Authoritative Information',
            '204' => 'No Content',
            '205' => 'Reset Content',
            '206' => 'Partial Content',
            '300' => 'Multiple Choices',
            '301' => 'Moved Permanently',
            '302' => 'Found',
            '303' => 'See Other',
            '304' => 'Not Modified',
            '305' => 'Use Proxy',
            '306' => '(Unused)',
            '307' => 'Temporary Redirect',
            '400' => 'Bad Request',
            '401' => 'Unauthorized',
            '402' => 'Payment Required',
            '403' => 'Forbidden',
            '404' => 'Not Found',
            '405' => 'Method Not Allowed',
            '406' => 'Not Acceptable',
            '407' => 'Proxy Authentication Required',
            '408' => 'Request Timeout',
            '409' => 'Conflict',
            '410' => 'Gone',
            '411' => 'Length Required',
            '412' => 'Precondition Failed',
            '413' => 'Request Entity Too Large',
            '414' => 'Request-URI Too Long',
            '415' => 'Unsupported Media Type',
            '416' => 'Requested Range Not Satisfiable',
            '417' => 'Expectation Failed',
            '500' => 'Internal Server Error',
            '501' => 'Not Implemented',
            '502' => 'Bad Gateway',
            '503' => 'Service Unavailable',
            '504' => 'Gateway Timeout',
            '505' => 'HTTP Version Not Supported',
        );

    /**
     * コンストラクタ
     * 
     * @access public
     */
    function Response()
    {
        if (isset($_SERVER['SERVER_PROTOCOL']) &&
            preg_match('/^(HTTP\/\d\.\d)$/', 
                       $_SERVER['SERVER_PROTOCOL'], $matches)) {
            $this->_protocol = $matches[1];
        }
    }

    /**
     * HTTPヘッダの結果ステータスを登録
     * 
     * @param  string      $status_code
     * @param  string|null $status_text
     * @return void
     * @access public
     */
    function setStatus($status_code, $status_text = null)
    {
        if (!isset($this->_statusTextList[$status_code])) {
            $this->_statusTextList[$status_code] = '';
            if ($status_text !== null) {
                $this->_statusTextList[$status_code] = $status_text;
            }
        }

        if ($status_text === null) {
            $status_text = $this->_statusTextList[$status_code];
        }

        $this->_statusCode = $status_code;
        $this->_statusText = $status_text;
    }

    /**
     * ステータスを返却
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getStatus()
    {
        return "{$this->_protocol} {$this->_statusCode} {$this->_statusText}";
    }

    /**
     * HTTPヘッダに「Content-Type」を登録
     *
     * @var    string $type
     * @var    string $charset
     * @return void
     * @access public
     */
    function setContentType($type, $charset = '')
    {
        $value = $type;

        if ($charset !== '') {
            $value .= '; charset=' . mb_preferred_mime_name($charset);
        }

        $this->setHeader('Content-Type', $value);
    }

    /**
     * HTTPヘッダに「Cache-Control」を登録
     *
     * @var    string $name
     * @var    string $value
     * @return void
     * @access public
     */
    function setCacheControl($name, $value = null)
    {
        $cache_control   = $this->getHeader('Cache-Control');
        $current_headers = array();
        if (isset($cache_control)) {
            foreach (preg_split('/\s*,\s*/', $cache_control) as $buf) {
                $buf = explode('=', $buf);
                $current_headers[$buf[0]] = isset($buf[1]) ? $buf[1] : null;
            }
        }
        $current_headers[strtr(strtolower($name), '_', '-')] = $value;

        $headers = array();
        foreach ($current_headers as $varname => $varvalue) {
            $headers[] = $varname . (isset($varvalue) ? '='.$varvalue : '');
        }

        $this->setHeader('Cache-Control', implode(', ', $headers));
    }

    /**
     * HTTPヘッダのメッセージを追加
     * 既存の値がある場合には第三引数の「$replace」で挙動変化
     * - $replace == true: 置き換え
     * - $replace == false: 追加
     * 
     * @param  string  $name
     * @param  string  $value
     * @param  boolean $replace
     * @return void
     * @access public
     */
    function setHeader($name, $value, $replace = true)
    {
        $field_name = $this->_normalizeFieldName($name);

        if (!$replace) {
            $current = $this->getHeader($field_name);
            $value   = (($current !== '') ? $current . ', ' : '') . $value;
        }

        $this->_headerFields[$field_name] = $value;
    }

    /**
     * HTTPヘッダのメッセージを返却
     * 
     * @param  string $name
     * @return string
     * @access public
     */
    function getHeader($name)
    {
        $field_name = $this->_normalizeFieldName($name);

        $field_value = '';
        if (isset($this->_headerFields[$field_name])) {
            $field_value = $this->_headerFields[$field_name];
        }

        return $field_value;
    }

    /**
     * HTTPヘッダのメッセージが登録されているか検査
     * 
     * @param  string $varkey
     * @return boolean
     * @access public
     */
    function isHeaderExists($varkey)
    {
        return 
            isset($this->_headerFields[$varkey]);
    }

    /**
     * 登録されているHTTPヘッダのメッセージを全消去
     * 
     * @param  void
     * @return void
     * @access public
     */
    function clearHeader()
    {
        $this->_headerFields = array();
    }

    /**
     * テンプレートのデータ・モデルを登録
     * 
     * @param  string $varkey
     * @param  mixed  $varvalue
     * @return void
     * @access public
     */
    function setDataModel($varkey, $varvalue)
    {
        $this->_dataModel[$varkey] = $varvalue;
    }

    /**
     * テンプレートのデータ・モデルを返却
     * 
     * @param  void
     * @return array
     * @access public
     */
    function getDataModel()
    {
        return $this->_dataModel;
    }

    /**
     * ボディパートを登録
     * 
     * @param  string $content
     * @return void
     * @access public
     */
    function setContents($content)
    {
        $this->_contents = $content;
    }

    /**
     * HTTPヘッダとボディパートを送信
     * 
     * @param  void
     * @return void
     * @access public
     */
    function flush()
    {
        if (headers_sent($filename, $linenum)) {
            // ヘッダ送信後なのでエラーステータスは送信できない
            trigger_error(
                'Unable to send HTTP header, ' . 
                    "sent to the browser in ${filename}/${linenum}", 
                E_USER_WARNING);
        } else {
            header($this->getStatus());
            foreach ($this->_headerFields as $varname => $varvalue) {
                header($varname . ': ' . $varvalue);
            }
        }
        $this->clearHeader();

        print $this->_contents;

        $this->_contents = '';
    }

    /**
     * HTTPヘッダーフィールドの名前として正しい書式に変換する
     *
     * @param  string $name
     * @return string 
     * @access private
     */
    function _normalizeFieldName($name)
    {
        return 
            preg_replace(
                '/\-(.)/e', 
                "'-'.strtoupper('\\1')", 
                strtr(ucfirst(strtolower($name)), '_', '-'));
    }
}
