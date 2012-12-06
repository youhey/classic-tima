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
 * @version  SVN: $Id: Request.class.php 6 2007-08-17 08:46:57Z do_ikare $
 * @todo     URI|URL|pathが曖昧に使い分けられているのを整理したい
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
     * 
     * リクエスト変数は引数で指定されたメソッドだけが有効になる
     * 引数が省略された場合にはリクエスト・メソッドのみを対象にする
     * 
     * - g: $_GET
     * - p: $_POST
     * - c: $_COOKIE
     * - q: $_SERVER['QUERY_STRING']
     *  - URLエンコードされたままのクエリ文字列
     * 
     * @param  array $method メソッド名
     * @access public
     */
    function Request($method = null)
    {
        if ($method === null) {
            switch (true) {
            case $this->isPost() : 
                $method = array('p');
                break;
            case $this->isGet() : 
                $method = array('g');
                break;
           default : 
                $method = array();
                break;
            }
        }
       if (!is_array($method)) {
           $method = (array)$method;
       }

        foreach ($method as $section) {
            switch ($section) {
            case 'g' : 
                foreach ($_GET as $varkey => $varvalue) {
                    if (strrpos($varkey, '_x') === (strlen($varkey) - 2)) {
                        $varvalue = array('x' => $varvalue);
                    } elseif (strrpos($varkey, '_y') === (strlen($varkey) - 2)) {
                        $varvalue = array('y' => $varvalue);
                    }
                    $this->set($varkey, $varvalue);
                }
                break;
            case 'p' : 
                foreach ($_POST as $varkey => $varvalue) {
                    if (strrpos($varkey, '_x') === (strlen($varkey) - 2)) {
                        $varvalue = array('x' => $varvalue);
                    } elseif (strrpos($varkey, '_y') === (strlen($varkey) - 2)) {
                        $varvalue = array('y' => $varvalue);
                    }
                    $this->set($varkey, $varvalue);
                }
                break;
            case 'c' : 
                foreach ($_COOKIE as $varkey => $varvalue) {
                    $this->set($varkey, $varvalue);
                }
                break;
            case 'q' : 
                if (!isset($_SERVER['QUERY_STRING'])) {
                    break;
                }
                foreach (explode('&', $_SERVER['QUERY_STRING']) as $query) {
                    $query    = split('=', $query);
                    $varkey   = $query[0];
                    $varvalue = (isset($query[1]) ? $query[1] : null);
                    if ($varkey === '') {
                        continue;
                    }
                    $this->set($varkey, $varvalue);
                }
                break;
            }
        }
    }

    /**
     * キーのリクエスト値を登録
     * 
     * 配列が登録されていれば、値のマージを試みる
     * 1. GETの変数「$b」が配列「array(1,2,3)」
     * 2. POSTの変数「$b」が文字列「hoge」
     * 3. セットする順序は「GET→POST」
     * 4. 登録後の値は「array(1,2,3,hoge)」
     * 
     * @param  string $varkey   リクエストキー
     * @param  mixed  $varvalue リクエスト値
     * @return void
     * @access public
     */
    function set($varkey, $varvalue)
    {
        // if (isset($this->_parameters[$varkey]) && 
        //     is_array($this->_parameters[$varkey])) {
        //     $varvalue = Utility::merge(
        //         $this->_parameters[$varkey], (array)$varvalue);
        // }
        $this->_parameters[$varkey] = $varvalue;
    }

    /**
     * キーのリクエスト値を返却
     * 
     * @param  string $varkey リクエストキー
     * @return mixed
     * @access public
     */
    function get($varkey)
    {
        if (!is_string($varkey) || ($varkey === '')) {
            return null;
        }
        if (!isset($this->_parameters[$varkey])) {
            return null;
        }

        return $this->_parameters[$varkey];
    }

    /**
     * 全てのリクエスト値を配列で返却
     * 
     * @param  void
     * @return array
     * @access public
     */
    function getAll()
    {
        return $this->_parameters;
    }

    /**
     * 指定したメソッドの指定変数名のリクエスト値を返却
     * 
     * - リクエスト・メソッド以外のリクエスト値に直接アクセス
     * - 対象のリクエスト値はメソッド名で評価順を指定することができる
     *  - デフォルトでは「GET→POST」の順に評価
     * 
     * @param  string $varkey リクエストキー
     * @param  array  $order  メソッドの名前
     * @return mixed
     * @access public
     */
    function getAcceptRequest($varkey, $order = array('g', 'p'))
    {
        $varvalue = null;

        if (!is_array($order)) {
            $order = array();
        }
        foreach ($order as $method) {
            // 値がnullなら存在しないものと判断する
            switch ($method) {
            case 'g' : 
                if (isset($_GET[$varkey])) {
                    $varvalue = $_GET[$varkey];
                }
                break;
            case 'p' : 
                if (isset($_POST[$varkey])) {
                    $varvalue = $_POST[$varkey];
                }
                break;
            case 'c' : 
                if (isset($_COOKIE[$varkey])) {
                    $varvalue = $_COOKIE[$varkey];
                }
                break;
            case 'f' : 
                if (isset($_FILES[$varkey])) {
                    $varvalue = $_FILES[$varkey];
                }
                break;
            case 'q' : 
                if (!isset($_SERVER['QUERY_STRING'])) {
                    break;
                }
                foreach (explode('&', $_SERVER['QUERY_STRING']) as $query) {
                    $query       = split('=', $query);
                    $query_name  = $query[0];
                    $query_value = (isset($query[1]) ? $query[1] : null);
                    if ($query_name === $varkey) {
                        if (isset($query_value)) {
                            $varvalue = $query_value;
                        }
                        break;
                    }
                }
                break;
            }
        }

        return $varvalue;
    }

    /**
     * 環境変数のPATH_INFOを連想配列で返却
     * 
     * - /example.php/foo/1/bar/2/hoge/3 => array('foo'=>1, 'bar'=>2, 'hoge'=>3)
     * 
     * @param  void
     * @return array
     * @access public
     */
    function getPathInfo()
    {
        $request = $this->f('PATH_INFO');
        if ($request === null) {
            $request = $this->getEnv('ORIG_PATH_INFO');
        }
        if ($request === null) {
            $request = '';
        }
        $query     = explode('/', trim($request, '/'));
        $path_info = array();
        for ($i = 0; $i < count($query); $i += 2) {
            $path_info[$query[$i]] = 
                (isset($query[$i + 1]) ? $query[$i + 1] : null);
        }

        return $path_info;
    }

    /**
     * 指定された変数名のリクエストが存在するかを検査
     * 
     * @param  string $varkey リクエストキー
     * @return boolean
     * @access public
     */
    function exists($varkey)
    {
        // 値がnullのリクエストは「存在しない」と評価する
        // 値にnullを登録するという行為は存在を消去するものと考える
        return 
            isset($this->_parameters[$varkey]);
    }

    /**
     * リクエストの変数名を全て返却
     * 
     * @param  void
     * @return array
     * @access public
     */
    function getNames()
    {
        return 
            array_keys($this->_parameters);
    }

    /**
     * 環境変数の値を返却
     * - 制御文字を全て除去するため、値が影響を受ける可能性あり
     * 
     * @param  string $varkey
     * @return string|null
     * @static string $ctrl_cahr
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
     * @param  void
     * @return string
     * @access public
     */
    function getHost()
    {
        $host = $this->getEnv('HTTP_X_FORWARDED_HOST');
        if ($host === null) {
            $host = $this->getEnv('HTTP_HOST');
        }
        if ($host === null) {
            $host = '';
        }

        return $host;
    }

    /**
     * リソースのホスト・サーバの名前を返却
     * - サーバに設定されている名前
     *  - Apacheであれば「ServerName」ディレクティブの設定値
     *  - バーチャルホストではサーバの設定によっては
     *    Request::getHost()の値と異なる可能性あり
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getServerName()
    {
        $server_name = $this->getEnv('HTTP_X_FORWARDED_SERVER');
        if ($server_name === null) {
            $server_name = $this->getEnv('SERVER_NAME');
        }
        if ($server_name === null) {
            $server_name = '';
        }

        return $server_name;
    }

    /**
     * リファラーを返却
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getReferer()
    {
        $referer = $this->getEnv('HTTP_REFERER');
        if ($referer === null) {
            $referer = '';
        }

        return $referer;
    }

    /**
     * 実行しているスクリプトのドキュメントルートからのパスを返却
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getScriptName()
    {
        $script_name = $this->getEnv('SCRIPT_NAME');
        if ($script_name === null) {
            $script_name = $this->getEnv('ORIG_SCRIPT_NAME');
        }
        if ($script_name === null) {
            $script_name = '';
        }

        return $script_name;
    }

    /**
     * 実行している（mainとなっている）スクリプトのパスを返却
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getMainPath()
    {
        $script_filename = $this->getEnv('SCRIPT_FILENAME');
        if ($script_filename === null) {
            $script_filename = $this->getEnv('ORIG_SCRIPT_FILENAME');
        }
        if ($script_filename === null) {
            $script_filename = '';
        }

        return $script_filename;
    }

    /**
     * リソースのスキームを返却
     * - http:
     * - https:
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getScheme()
    {
        $scheme = ($this->isSecure() ? 'https:' : 'http:');

        return $scheme;
    }

    /**
     * リソースのスキームとホストの名前を返却
     * - http://example.com
     * - https://example.com
     * - http://example.com:8080
     * 
     * @param  void
     * @return string
     * @access public
     * @todo 優先的に「HTTP_HOST」の値を使用するので、
     *       ポート番号が使われていると「http://example.com:8080:8080」
     *       という値を返却する可能性があるので要修正
     */
    function getHostUri()
    {
        $host_name = $this->getHost();
        if ($host_name === '') {
            $host_name = $this->getServerName();
        }
        $host_uri = $this->getScheme() . '//' . $host_name;
        $std_port = ($this->isSecure() ? '443' : '80');
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
     * @param  void
     * @return string
     * @access public
     */
    function getPathUri()
    {
        $path = $this->getEnv('REQUEST_URI');
        if ($path === null) {
            $path = '';
        }
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
     * @param  void
     * @return string
     * @access public
     */
    function getUrl()
    {
        $url = $this->getHostUri() . $this->getPathUri();

        return $url;
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
     * @param  void
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
     * @param  void
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
     * @param  void
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
     * @param  void
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
     * @param  void
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
     * @param  void
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
     * @param  void
     * @return boolean
     * @access public
     */
    function isSecure()
    {
        return (
            ($this->getEnv('HTTPS') === 'on') || 
            ($this->getEnv('HTTP_X_FORWARDED_PROTO') === 'https'));
    }
}
