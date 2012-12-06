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
 * クライアントのリクエスト情報を操作
 * 
 * @package  tima
 * @version  SVN: $Id: Request.class.php 37 2007-10-12 06:51:54Z do_ikare $
 * @todo     URI|URL|pathが曖昧なので明確に整する
 * @todo     ファイルのアップロードに対応する
 */
class Request
{

    /**
     * リクエスト値
     * 
     * @var    array
     * @access private
     */
    var $_parameters = array();

    /**
     * コンストラクタ
     * - 指定メソッドのリクエスト変数のみを有効にする
     *  - g: $_GET
     *  - p: $_POST
     * - 有効にするメソッドは配列で複数指定可能
     * - メソッドが省略された場合にはリクエストのメソッドを対象にする
     * 
     * @param  array $method メソッド名
     * @access public
     */
    function Request($method = null)
    {
        if ($method === null) {
            $method = array();
                if ($this->isPost()) $method = array('p');
            elseif ($this->isGet())  $method = array('g');
        }
        if (!is_array($method)) {
            $method = (array)$method;
        }

        foreach ($method as $section) {
            switch ($section) {
            case 'g' : $this->_fetchGetParameters();
                break;
            case 'p' : $this->_fetchPostParameters();
                break;
            }
        }
    }

    /**
     * リクエスト変数の値を返却
     * 
     * @param  string $varkey
     * @return mixed
     * @access public
     */
    function get($varkey)
    {
        $varvalue = 
            isset($this->_parameters[$varkey]) ? $this->_parameters[$varkey] : null;

        return $varvalue;
    }

    /**
     * リクエスト変数の値を上書き
     * 
     * @param  string $varkey
     * @param  mixed  $varvalue
     * @return void
     * @access public
     */
    function set($varkey, $varvalue)
    {
        if ($this->exists($varkey)) {
            $this->_parameters[$varkey] = $varvalue;
        } else {
            trigger_error("Nonexistent request::${varkey}", E_USER_NOTICE);
        }
    }

    /**
     * 指定された変数名のリクエストが存在するかを検査
     * - 値がnullなら「存在しない」と評価する
     *  - 値にnullを登録する＝存在を消去したと判断
     * 
     * @param  string $varkey
     * @return boolean
     * @access public
     */
    function exists($varkey)
    {
        return 
            isset($this->_parameters[$varkey]);
    }

    /**
     * 全てのリクエストを配列で返却
     * 
     * @return array
     * @access public
     */
    function getAll()
    {
        return $this->_parameters;
    }

    /**
     * リクエストの変数名を全て返却
     * 
     * @return array
     * @access public
     */
    function getNames()
    {
        return 
            array_keys($this->_parameters);
    }

    /**
     * 特定メソッドのリクエストを返却
     * - PHP本来のリクエスト変数に直接アクセス
     *  - フレームワークのフィルタリングなどに影響されない生データ
     * - 引数で読み込むメソッドを指定可能
     *  - g: $_GET
     *  - p: $_POST
     *  - c: $_COOKIE
     *  - q: $_SERVER['QUERY_STRING']
     * - メソッド別に同名の変数が存在すれば配列順で上書き
     *  - デフォルトでは「GET→POST」の順序で評価
     * 
     * @param  string $varkey
     * @param  array  $order
     * @return mixed
     * @access public
     */
    function getAcceptRequest($varkey, $order = array('g', 'p'))
    {
        $varvalue = null;

        if (!is_array($order)) $order = (array)$order;

        foreach ($order as $method) {
            // 値がnullなら存在しないものと判断する
            switch ($method) {
            case 'g' : if (isset($_GET[$varkey])) $varvalue = $_GET[$varkey];
                break;
            case 'p' : if (isset($_POST[$varkey])) $varvalue = $_POST[$varkey];
                break;
            case 'c' : if (isset($_COOKIE[$varkey])) $varvalue = $_COOKIE[$varkey];
                break;
            case 'q' : 
                $query_string = $this->getEnv('QUERY_STRING');
                if ($query_string !== null) {
                    parse_str($query_string, $params);
                    if (isset($params[$varkey])) $varvalue = $params[$varkey];
                }
                break;
            }
        }

        return $varvalue;
    }

    /**
     * 環境変数「PATH_INFO」を連想配列で返却
     * - /example.php/foo/1/bar/2/hoge/3 => array('foo'=>1, 'bar'=>2, 'hoge'=>3)
     * 
     * @param  void
     * @return array
     * @access public
     */
    function getPathInfo()
    {
        $request = $this->getEnv('PATH_INFO');
        if ($request === null) $request = $this->getEnv('ORIG_PATH_INFO');
        if ($request === null) $request = '';

        $query     = explode('/', trim($request, '/'));
        $path_info = array();
        for ($i = 0; $i < count($query); $i += 2) {
            $path_info[$query[$i]] = isset($query[$i + 1]) ? $query[$i + 1] : null;
        }

        return $path_info;
    }

    /**
     * 環境変数を返却
     * - 制御文字を全て除去するため、値が影響を受ける可能性あり
     * 
     * @param  string $varkey
     * @return string|null
     * @access public
     */
    function getEnv($varkey)
    {
        $varvalue = 
            isset($_SERVER[$varkey]) ? 
                Utility::to('EraseCtrlChar', $_SERVER[$varkey]) : null;

        return $varvalue;
    }

    /**
     * リクエストされたホストサーバの名前を返却
     * - リクエストヘッダの「host」の値
     *  - HTTP/1.0では存在しない
     *  - HTTP/1.1でもリクエストによっては存在しない
     *  - リクエストにポート番号があればポート番号を含む
     * 
     * @return string
     * @access public
     */
    function getHost()
    {
        $host = $this->getEnv('HTTP_X_FORWARDED_HOST');
        if ($host === null) $host = $this->getEnv('HTTP_HOST');
        if ($host === null) $host = '';

        return $host;
    }

    /**
     * リソースのホスト・サーバの名前を返却
     * - サーバに設定されている名前
     *  - Apacheであれば「ServerName」ディレクティブの設定値
     *  - バーチャルホストではサーバの設定によっては
     *    Request::getHost()の値と異なる可能性あり
     * 
     * @return string
     * @access public
     */
    function getServerName()
    {
        $server_name = $this->getEnv('HTTP_X_FORWARDED_SERVER');
        if ($server_name === null) $server_name = $this->getEnv('SERVER_NAME');
        if ($server_name === null) $server_name = '';

        return $server_name;
    }

    /**
     * リファラーを返却
     * 
     * @return string
     * @access public
     */
    function getReferer()
    {
        $referer = $this->getEnv('HTTP_REFERER');
        if ($referer === null) $referer = '';

        return $referer;
    }

    /**
     * 実行しているスクリプトのドキュメントルートからのパスを返却
     * 
     * @return string
     * @access public
     */
    function getScriptName()
    {
        $script_name = $this->getEnv('SCRIPT_NAME');
        if ($script_name === null) $script_name = $this->getEnv('ORIG_SCRIPT_NAME');
        if ($script_name === null) $script_name = '';

        return $script_name;
    }

    /**
     * 実行している（mainとなっている）スクリプトのパスを返却
     * 
     * @return string
     * @access public
     */
    function getMainPath()
    {
        $script_filename = $this->getEnv('SCRIPT_FILENAME');
        if ($script_filename === null) 
            $script_filename = $this->getEnv('ORIG_SCRIPT_FILENAME');
        if ($script_filename === null) $script_filename = '';

        return $script_filename;
    }

    /**
     * リソースのスキームを返却
     * - http:
     * - https:
     * 
     * @return string
     * @access public
     */
    function getScheme()
    {
        $scheme = $this->isSecure() ? 'https:' : 'http:';

        return $scheme;
    }

    /**
     * リソースのスキームとホストの名前を返却
     * - http://example.com
     * - https://example.com
     * - http://example.com:8080
     * 
     * @return string
     * @access public
     * @todo 優先的に「HTTP_HOST」の値を使用するので、
     *       ポート番号が使われていると「http://example.com:8080:8080」
     *       という値を返却する可能性があるので要修正
     */
    function getHostUri()
    {
        $host_name = $this->getHost();
        if ($host_name === '') $host_name = $this->getServerName();

        $host_uri = $this->getScheme() . '//' . $host_name;
        $std_port = $this->isSecure() ? '443' : '80';
        $port_no  = $this->getEnv('SERVER_PORT');
        if (isset($port_no) && ($port_no !== '') && ($port_no !== $std_port)) {
            $host_uri .= ':' . $port_no;
        }

        return $host_uri;
    }

    /**
     * リソースのパスを返却
     * - /foo/bar/hoge.html
     * - /foo/bar/
     * 
     * @return string
     * @access public
     */
    function getPathUri()
    {
        $path = $this->getEnv('REQUEST_URI');
        if ($path === null) $path = '';

        if (preg_match('/^(?:http)(?:s)?:\/\//', $path)) {
            $url  = parse_url($path);
            $path = $url['path'];
        }
        if (($i = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $i);
        }

        return $path;
    }

    /**
     * リソースのURLを返却
     * - http://example.com/
     * - https://example.com/foo/bar/
     * - http://example.com:8080/foo/bar/hoge.html
     * 
     * @return string
     * @access public
     */
    function getUrl()
    {
        return 
            $this->getHostUri() . $this->getPathUri();
    }

    /**
     * リクエスト・メソッドを返却
     * - GET
     * - POST
     * - PUT
     * - DELETE
     * - HEAD
     * - これ以外のメソッドは「GET」にくるめる
     *
     * @return string
     * @access public
     */
    function getMethod()
    {
        $method = $this->getEnv('REQUEST_METHOD');
        switch ($method) {
        case 'GET' : 
        case 'POST' : 
        case 'PUT' : 
        case 'DELETE' : 
        case 'HEAD' : 
            break;
        default : 
            $method = 'GET';
        }

        return $method;
    }

    /**
     * リクエストのメソッドが「GET」かを検査
     *
     * @return boolean
     * @access public
     */
    function isGet()
    {
        return 
            ($this->getMethod() === 'GET');
    }

    /**
     * リクエストのメソッドが「POST」かを検査
     *
     * @return boolean
     * @access public
     */
    function isPost()
    {
        return 
            ($this->getMethod() === 'POST');
    }

    /**
     * リクエストのメソッドが「PUT」かを検査
     *
     * @return boolean
     * @access public
     */
    function isPut()
    {
        return 
            ($this->getMethod() === 'PUT');
    }

    /**
     * リクエストのメソッドが「DELETE」かを検査
     *
     * @return boolean
     * @access public
     */
    function isDelete()
    {
        return 
            ($this->getMethod() === 'DELETE');
    }

    /**
     * リクエストのメソッドが「HEAD」かを検査
     *
     * @return boolean
     * @access public
     */
    function isHead()
    {
        return 
            ($this->getMethod() === 'HEAD');
    }

    /**
     * リクエストがHTTPSプロトコルによる暗号通信かを検査
     *
     * @return boolean
     * @access public
     */
    function isSecure()
    {
        return 
            ($this->getEnv('HTTPS') === 'on') || 
            ($this->getEnv('HTTP_X_FORWARDED_PROTO') === 'https');
    }

    /**
     * GETメソッドのリクエスト変数を取り込む
     * 
     * @access private
     * @todo   イメージ・ボタンのクリック値をスマートに取り込む
     *         if (strrpos($varkey, '_x') === (strlen($varkey) - 2))
     *         if (strrpos($varkey, '_y') === (strlen($varkey) - 2))
     */
    function _fetchGetParameters()
    {
        foreach ($_GET as $varkey => $varvalue) {
            $this->_parameters[$varkey] = $varvalue;
        }
    }

    /**
     * POSTメソッドのリクエスト変数を取り込む
     * 
     * @return void
     * @access private
     * @todo   イメージ・ボタンのクリック値をスマートに取り込む
     *         if (strrpos($varkey, '_x') === (strlen($varkey) - 2))
     *         if (strrpos($varkey, '_y') === (strlen($varkey) - 2))
     */
    function _fetchPostParameters()
    {
        foreach ($_POST as $varkey => $varvalue) {
            $this->_parameters[$varkey] = $varvalue;
        }
    }
}
