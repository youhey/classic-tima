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
 * プログラムのセッション情報を操作
 * 
 * - 抽象的な操作によるセッション制御
 * - セッションデータに名前空間をもたせる
 *  - 多プログラム間でのセッションの共存
 *  - 多プログラム間での個データの保護
 * - セッションの活動状態の操作
 * - セッションの活動状態の把握
 * 
 * @package  tima
 * @version  SVN: $Id: Session.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Session
{

    /**
     * 名前空間
     * 
     * @var    string
     * @access private
     */
    var $_namespace = '';

    /**
     * クッキの有効時間（秒）
     * 
     * @var    integer
     * @access private
     */
    var $_lifetime = 0;

    /**
     * セッションのクッキ使用有無
     * 
     * @var    boolean
     * @access private
     */
    var $_useCookies = true;

    /**
     * コンストラクタ
     * 
     * @param  string  $namespace    名前空間
     * @param  string  $session_name セッション名
     * @param  boolean $lifetime     クッキの有効時間（秒数）
     * @access public
     */
    function Session($namespace = 'noname', $session_name = null, $lifetime = null)
    {
        $this->_namespace = $namespace;

        if ($this->isStarted()) {
            // セッションが開始されていれば、名前空間の初期化
            if (!isset($_SESSION['__sc'][$this->_namespace])) {
               $_SESSION['__sc'][$this->_namespace] = array();
            }
        } else {
            // セッションが開始前ならば初期化情報を更新

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
     * セッションIDを返却
     *
     * @param  void
     * @return string  セッションID
     * @access public
     */
    function getId()
    {
        return session_id();
    }

    /**
     * セッションIDを新たに設定
     * 
     * セッションの保存方法は「ファイル」固定であることを前提とするので、
     * セッションIDは[a-zA-Z0-9]の範囲内のランダムな文字列を期待
     * 
     * @param  string  $id セッションID
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
     * セッション名を返却
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
     * セッションを開始
     * 
     * 二重の呼び出しまたは外部でのセッションの開始を許さない
     * ここで開始したものが唯一の始動となる
     * 
     * セッションIDの固定化を最小限に抑制するよう一定条件でセッションを再生成
     * - 未使用のセッション（初めて開始されるセッション）
     * - 1時間以上経過したセッション
     * - 100分の1の確率
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

        // ステータス更新
        $this->_status('started', true);
        $this->_status('readable', true);
        $this->_status('writable', true);

        // 環境準備
        if (!isset($_SESSION['__sc'])) {
            $_SESSION['__sc'] = array();
        }
        if (!isset($_SESSION['__sc'][$this->_namespace])) {
            $_SESSION['__sc'][$this->_namespace] = array();
        }

        // 条件により再生成
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
     * セッションを破棄
     * 
     * - セッションが開始されいなければ異常終了します
     * - ロック状況は無視します
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
        // session_destroy() はファイルを削除するだけでメモリの開放まではしないため
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
     * セッションを再生
     *
     * - セッションが開始されいなければ異常終了します
     * - ロック状況は無視します
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

        // セッション情報を一時的に退避してから消去
        $tmp = array();
        foreach ($_SESSION['__sc'] as $varkey => $varvalue) {
            if (strpos($varkey, '__') === 0) {
                continue;
            }
            $tmp[$varkey] = $varvalue;
        }
        $_SESSION = array();

        // destroy() => start() の方が直接的で効率よいかと思ったら
        // CookieのセッションIDのセットがうまくいかないようなので変更
        $old_session_id = session_id();
        session_regenerate_id();

        // 不要となったセッション・ファイルを削除
        $old_session_file = 
            session_save_path() . DIRECTORY_SEPARATOR . 'sess_' . $old_session_id;
        if (file_exists($old_session_file)) {
            @unlink($old_session_file);
        }

        $this->_status('started', true);
        $this->_status('readable', true);
        $this->_status('writable', true);

        // セッション情報を復元
        $_SESSION['__sc'] = $tmp;

        return true;
    }

    /**
     * セッション値を登録
     *
     * @param  string  $varkey   キー
     * @param  mixed   $varvalue 値
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
     * セッション一時保存領域に値を登録
     * 
     * @param  string  $varkey   キー
     * @param  mixed   $varvalue 値
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
     * セッション値を返却
     *
     * @param  string  $varkey キー
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
     * セッション値を返却
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
     * セッション一時保存領域の値を返却
     * 
     * セッション一時保存領域は一回きりの使いきりなので、
     * 保存されていた値は返却と同時に抹消
     * 
     * @param  string  $varkey キー
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
     * セッション値を削除
     *
     * @param  string  $varkey キー
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
     * 指定したキーのセッション値が存在するかを検証
     *
     * @param  string  $varkey キー
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
     * 名前空間のセッションを破壊
     * 
     * 名前空間が所有する全ての値を空の配列で上書きします
     * 他の名前空間には影響しません
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
     * セッションが活動しているかを検査
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
     * セッションが開始しているかを検査
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
     * 書き込みをロック
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
     * セッションに対する入出力を終了
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
     * 状態を管理
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
