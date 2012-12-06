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
 * フロント・コントローラ
 * 
 * @package  tima
 * @version  SVN: $Id: Front.class.php 37 2007-10-12 06:51:54Z do_ikare $
 */
class Front
{

    /**
     * テンプレート・ファイルの拡張子
     * 
     * @var    string
     * @access protected
     */
    var $templateExt = '.html';

    // /**
    //  * アクション・コントローラの配置情報
    //  * 
    //  * @var    mixed
    //  * @access protected
    //  */
    // var $actionLocation = null;

    /**
     * 実行プロセス
     * 
     * @var    Process
     * @access private
     */
    var $_process = null;

    /**
     * ビュー
     * 
     * @var    View
     * @access private
     */
    var $_view = null;

    /**
     * リクエスト
     * 
     * @var    Request
     * @access private
     */
    var $_request = null;

    /**
     * セッション
     * 
     * @var    session
     * @access private
     */
    var $_session = null;

    /**
     * レスポンス
     * 
     * @var    Response
     * @access private
     */
    var $_response = null;

    /**
     * ユーザ・エージェント
     * 
     * @var    UserAgent
     * @access private
     */
    var $_userAgent = null;

    /**
     * フィルタ
     * 
     * @var    array
     * @access private
     */
    var $_filters = array();

    /**
     * 設定
     * 
     * @var    Config
     * @access private
     */
    var $_config = null;

    /**
     * ロガー
     * 
     * @var    Logger
     * @access private
     */
    var $_log = null;

    /**
     * 稼動開始時間
     * 
     * @var    DateMicrotimeAccessor
     * @access private
     */
    var $_beginning = null;

    /**
     * コンストラクタ
     * - 例外が発生したら異常終了
     *  - ロガー・クラスが存在しない
     *   - エラーログ：Logger 'ロガーの名前' not found
     *   - レスポンス：500 Internal Server Error
     * 
     * @param  void
     * @access public
     */
    function Front()
    {
        $this->_beginning = &new DateMicrotimeAccessor;
        $this->_process   = &new Process;
        $this->_request   = &new Request;
        $this->_response  = &new Response;
        $this->_config    = &new Config;
        $this->_userAgent = &new UserAgent;

        // 設定ファイルの読み込み
        $root_etc = ROOT_PATH . DS . 'etc';
        $app_etc  = $this->getAppDir() . DS . 'etc';
        $this->_config->setEtcDir($root_etc);
        if ($root_etc !== $app_etc) {
            $this->_config->setEtcDir($app_etc);
        }
        $reading_result = $this->_config->readConfig('all');
        if (!$reading_result) {
            trigger_error(
                "Unable to read the configuration 'all-ini.php'", 
                E_USER_WARNING);
        }

        // コントローラ固有の設定ファイルの読み込み
        $this->_config->readConfig(get_class($this));

        // セッションを準備
        $this->_session = 
            &new Session(
                '__Front', 
                $this->_config->get('session_name', 'env'), 
                $this->_config->get('session_lifetime', 'env'));

        // ビューを準備
        $basedir = $this->getAppDir() . DS . 'templates';
        $device  = $this->_userAgent->isMobile() ? 'mobile' : 'pc';
        $this->_view = 
            &new Smarty4View(
                $this->getInternalEncoding(), $this->getContentsEncoding(), 
                Utility::merge(
                    $this->_config->getByNamespace('template'), 
                    array(
                        'template_dir' => $basedir . DS . 'template' . DS . $device, 
                        'compile_dir'  => $basedir . DS . 'templates_c')));
        $this->_view->registerObject('Request', $this->_request);
        $this->_view->registerObject('UserAgent', $this->_userAgent);
        $this->_view->registerObject('Date', $this->_beginning);

        // ログを準備
        $logger_class = $this->_config->get('logger_class', 'env');
        if (($logger_class === null) || !class_exists($logger_class)) {
            header('HTTP/1.0 500 Internal Server Error');
            trigger_error("Logger '${logger_class}' not found", E_USER_ERROR);
            exit;
        }
        $this->_log = 
            &new $logger_class(
                $this->_config->get('log_level', 'env'), 
                $this->_config->get('log_option', 'env'));
    }

    /**
     * コントローラを初期化（スーパークラスでは空）
     * 
     * 継承クラスで初期化に対する処理をオーバーライド
     * この初期化処理はタスク実行の一番最初に呼び出される
     * 
     * @param  void
     * @return void
     * @access public
     */
    function initialize() {}

    /**
     * フロント・コントローラを実行
     * - 実行計画から新規タスクを生成してプロセスを実行
     * - 例外が発生したら異常終了
     *  - ルーティングに失敗｜実行計画が不正
     *   - エラーログ：Unable to dispatch process in mapping
     *   - レスポンス：500 Internal Server Error
     * 
     * @param  void
     * @return void
     * @access public
     * @see    Front::route()
     * @see    Front::dispatch()
     * @see    Front::process()
     * @see    Front::_applyFiltersBeforeMethod()
     * @see    Front::_applyFiltersAfterMethod()
     */
    function start($mapping = null)
    {
        // フロント・コントローラを初期化
        $this->initialize();

        // 事前フィルタ
        $this->_applyFiltersBeforeMethod();

        // リクエストをルーティング→実行計画を作成
        if ($mapping === null) {
            $mapping = $this->route();
        }
        if (!is_array($mapping) || !isset($mapping['path'])) {
            header('HTTP/1.0 500 Internal Server Error');
            trigger_error('Unable to dispatch process in mapping', E_USER_ERROR);
            exit;
        }
        if (!array_key_exists('method', $mapping)) {
            $mapping['method'] = null;
        }

        // 実行計画→タスクを作成→プロセスに登録
        $this->dispatch($mapping['path'], $mapping['method']);

        // アクション・コントローラの設定ファイルを読み込む
        $this->_config->readConfig($mapping['path']);

        // アクションを実行
        $this->process();

        // 事後フィルタ
        $this->_applyFiltersAfterMethod();

        // 結果を出力
        $this->_response->flush();
    }

    /**
     * リダイレクト
     * - レスポンスにリダイレクトのためのヘッダ情報を登録
     * - レスポンスにリダイレクトのためのコンテンツを登録
     *  - このメソッドからは直接リダイレクト処理は発生しない
     *  - 既存のレスポンス情報をリダイレクトの情報に変更するだけ
     *   - メソッドの呼び出し元でレスポンス内容が書き換えられればリダイレクトは無効
     *   - 既存のレスポンス情報はリダイレクト情報で書き換えられて失う
     * - デフォルトのステータスコードは「302 Moved Temporarily」
     *  - RFC2616の仕様では302ステータスコードではリクエストの発行条件を変えられない
     *   - POSTのリクエストをGETでリダイレクトすることはできない
     *   - 一般的なユーザエージェントでは302ステータスコードでも期待通りに動作する
     *    - POSTのリクエストをGETでリダイレクトしても警告など発生しない
     *  - 本来はCGIなどでのリダイレクトには「303 See Other」を使用するべき
     *   - ユーザエージェントによっては対応していないものがある
     *   - 対応しているユーザエージェントでもバグ情報を見かける
     *  - 一般的なユーザエージェントとの互換性を考慮して302ステータスコードに
     *  - 将来的に警告など出すユーザエージェントが出てきたら再考
     * 
     * @param  string     $link
     * @param  array|null $params
     * @param  string     $status_code
     * @return void
     * @access public
     */
    function redirect($link, $params = array(), $status_code = '302')
    {
        $url = htmlentities($link, ENT_QUOTES, $this->getHttpCharSet());

        if (is_array($params) && (count($params) > 0)) {
            $query = array();
            foreach ($params as $varkey => $varvalue) {
                $query[] = urlencode($varkey) . '=' . urlencode($varvalue);
            }
            $url .= '?' . implode('&', $query);
        }

        $this->_response->clearHeader();
        $this->_response->setStatus($status_code);
        $this->_response->setHeader('Location', $url);

        $this->_response->setContents(
            "<html><head>\n" . 
            "<meta http-equiv=\"refresh\" content=\"0;url=${url}\" />\n" . 
            "</head></html>\n");
    }

    /**
     * 動作中コントローラの任意のアクションにリダイレクト
     * - パラメータに任意アクションの指定情報を登録
     * - セッションが開始されていれば、パラメータにセッション情報を追加
     * 
     * @param  string     $action
     * @param  array|null $params
     * @return void
     * @access public
     * @see    Front::redirect()
     */
    function redirectAction($action, $params = array())
    {
        // アクション情報
        $query = array(
                $this->_config->get('action_key', 'env') => $action, 
            );

        // セッション情報
        if ($this->_session->isStarted()) {
            $use_cookie = $this->_config->get('use_cookie', 'session');
            if ($this->_userAgent->isMobile() || ($use_cookie !== true)) {
                $query[$this->_session->getSessionName()] = $this->_session->getId();
            }
        }

        $this->redirect($this->_request->getUrl(), Utility::merge($query, $params));
    }

    /**
     * レンダリング
     * - 例外が発生したら異常終了
     *  - テンプレート・ファイルが存在しない
     *   - エラーログ：Template 'テンプレート・ファイル' not found
     *   - レスポンス：500 Internal Server Error'
     * 
     * @param  string $attribute
     * @return void
     * @access public
     * @see    View::isTemplateExists()
     * @see    View::render()
     * @see    Response::getDataModel()
     * @see    Response::setContents()
     */
    function render($attribute)
    {
        $template = $attribute . $this->templateExt;

        if (!$this->_view->isTemplateExists($template)) {
            header('HTTP/1.0 500 Internal Server Error');
            trigger_error("Template '${attribute}' not found", E_USER_ERROR);
            exit;
        }

        $this->_response->setContents(
            $this->_view->render($template, $this->_response->getDataModel()));
    }

    /**
     * フィルタを登録
     * - 例外が発生したら異常終了
     *  - フィルタが読み込めない
     *   - エラーログ：Filter 'フィルタの名前' not found
     *   - レスポンス：500 Internal Server Error
     * 
     * @param  string $filter
     * @return void
     * @access public
     * @see    ClassLoader::load()
     */
    function setFilter($filter)
    {
        static $class_loader;
        if (!isset($class_loader)) {
            $class_loader = &new ClassLoader;
            $class_loader->setParents('Filter');
            $class_loader->setIncludePath(ROOT_PATH);
        }

        $class_name = $class_loader->load($filter);
        if ($class_name === '') {
            header('HTTP/1.0 500 Internal Server Error');
            trigger_error("Filter '${filter}' not found", E_USER_ERROR);
            exit;
        }

        $this->_filters[] = &new $class_name;
    }

    /**
     * アプリケーションを配置したディレクトリの絶対パスを返却
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getAppDir()
    {
        $app_dir = '';

        $varvalue = defined('APP_DIR') ? APP_DIR : ROOT_PATH;
        if (OS_WINDOWS) {
            if (preg_match('/^[a-z]:/i', $varvalue) && ($varvalue{2} === DS)) {
                $app_dir = $varvalue;
            }
        } elseif ($varvalue{0} === DS) {
            $app_dir = $varvalue;
        }
        if ($app_dir === '') {
            $app_dir = realpath(ROOT_PATH . DS . $varvalue);
        }

        return $app_dir;
    }

    /**
     * 内部文字エンコーディングを返却
     * 
     * 内部文字エンコーディングの設定の有無によって動作変化
     * - 設定あり：設定された値を返却
     * - 設定なし：PHPの動作環境を設定に登録 => 登録した値を返却
     *  - 動作環境からの推測にはmb_internal_encoding()関数を使用
     *  - 前後の設定変更によっては整合性がなくなる場合もあり
     * 
     * @param  void
     * @return string|null
     * @access public
     */
    function getInternalEncoding()
    {
        $internal_encoding = $this->_config->get('internal_encoding', 'env');
        if ($internal_encoding === null) {
            $internal_encoding = mb_internal_encoding();
        }

        return $internal_encoding;
    }

    /**
     * 出力の文字エンコーディングを返却
     * 
     * 設定の有無によって動作変化
     * - 設定あり：設定された値を返却
     * - 設定なし：PHPの動作環境を設定に登録 => 登録した値を返却
     *  - 動作環境からの推測にはmb_http_output()関数を使用
     *  - 前後で設定が変動すると整合性がなくなる場合もあり
     * 
     * @param  void
     * @return string|null
     * @access public
     */
    function getContentsEncoding()
    {
        $http_encoding = $this->_config->get('contents_encoding', 'env');
        if ($http_encoding === null) {
            $http_encoding = mb_http_output();
        }

        return $http_encoding;
    }

    /**
     * 出力するHTMLのキャラクターセットを返却
     * 
     * @param  void
     * @return string|null
     * @access public
     * @see    Front::getContentsEncoding()
     */
    function getHttpCharSet()
    {
        return 
            mb_preferred_mime_name($this->getContentsEncoding());
    }

    /**
     * リクエストを返却
     * 
     * @param  void
     * @return Request
     * @access public
     */
    function &getRequest()
    {
        return $this->_request;
    }

    /**
     * セッションを返却
     * 
     * @param  void
     * @return Session
     * @access public
     */
    function &getSession()
    {
        return $this->_session;
    }

    /**
     * レスポンスを返却
     * 
     * @param  void
     * @return Response
     * @access public
     */
    function &getResponse()
    {
        return $this->_response;
    }

    /**
     * ユーザ・エージェントを返却
     * 
     * @param  void
     * @return Session
     * @access public
     */
    function &getUserAgent()
    {
        return $this->_userAgent;
    }

    /**
     * プロセスを返却
     * 
     * @param  void
     * @return Process
     * @access public
     */
    function &getProcess()
    {
        return $this->_process;
    }

    /**
     * テンプレート・エンジンを返却
     * 
     * @param  void
     * @return object|null
     * @access public
     */
    function &getTemplateEngine()
    {
        return $this->_view->getEngine();
    }

    /**
     * 設定を返却
     * 
     * @param  void
     * @return Config
     * @access public
     */
    function &getConfig()
    {
        return $this->_config;
    }

    /**
     * ロガーを返却
     * 
     * @param  void
     * @return Logger
     * @access public
     */
    function &getLogger()
    {
        return $this->_log;
    }

    /**
     * 稼動開始時刻を返却
     * 
     * @param  void
     * @return DateMicrotimeAccessor
     * @access public
     */
    function &getBeginning()
    {
        return $this->_beginning;
    }

    /**
     * リクエストのURIとパラメータから実行計画を作成
     * - リクエストURIからアクション・コントローラのパスを決定
     *  - /foo/bar/hoge.php => test_html_foo_bar_hoge
     * - リクエスト値から実行アクション名を決定
     *  - GET::action::bar => bar
     * - web_root_pathで設定されたパスを経路の先頭から除外
     *  - '/'：/test/html/foo/bar/hoge.php => test_html_foo_bar_hoge
     *  - '/test/html/'：/test/html/foo/bar/hoge.php => foo_bar_hoge
     * - ドット（.）以降をファイル拡張子と判断して削除
     *  - /foo/bar/hoge => foo_bar_hoge
     *  - /foo/bar/hoge.php => foo_bar_hoge
     *  - /foo/bar/hoge.html => foo_bar_hoge
     * - ファイル名が省略されたアクセスは「index」として処理
     * 
     * @param  void
     * @return array
     * @access protected
     */
    function route()
    {
        $rootpath = 
            $this->_config->get(
                $this->_request->isSecure() ? 'https_root_path' : 'http_root_path', 
                'env');
        if (($rootpath === null) || !is_string($rootpath) || ($rootpath === '')) {
            $rootpath = '/';
        }
        if ($rootpath{0} !== '/') {
            $rootpath = '/' . $rootpath;
        }

        $pathuri  = $this->_request->getPathUri();

        if (strpos($pathuri, $rootpath) === 0) {
            $start   = strlen($rootpath);
            $pathuri = substr($pathuri, $start, (strlen($pathuri) - $start));
        }
        if (strpos($pathuri, '.') !== false) {
            $pathuri = substr($pathuri, 0, strpos($pathuri, '.'));
        }

        if (($pathuri === '') || ($pathuri === false)) {
            $pathuri = 'index';
        } else {
            $i = strlen($pathuri) - 1;
            if ($pathuri{$i} === '/') {
                $pathuri .= 'index';
            }
        }

        return 
            array(
                    'path' => 
                        preg_replace(
                            '/[^a-z0-9_]/i', '', 
                            str_replace(
                                array('/', '-'), '_', 
                                trim(strtolower($pathuri), '/'))), 
                    'method' => 
                        $this->_request->getAcceptRequest(
                            $this->_config->get('action_key', 'env'), 
                            array('p', 'g')), 
                );
    }

    /**
     * 実行計画からタスクを生成してプロセスに登録
     * - 例外が発生したら異常終了
     *  - リクエストに対応したアクション・コントローラが存在しない
     *   - エラーログ：Controller 'コントローラ名' not found
     *   - レスポンス：404 Not Found
     *  - アクション・コントローラのロードに失敗
     *   - エラーログ："Unable to use the Controller 'コントローラ名'
     *   - レスポンス：404 Not Found
     * 
     * @param  string $ctrlpath アクション・コントローラのパス
     * @param  string $action   実行するアクション
     * @return void
     * @access protected
     * @see    Front::_findCtrl()
     * @see    ClassLoader::load()
     */
    function dispatch($ctrlpath, $action)
    {
        $class_loader = &new ClassLoader;
        $class_loader->setParents('Action');
        $class_loader->setIncludePath($this->getAppDir());

        $discovery = $this->_findCtrl($ctrlpath, $class_loader);
        // $this->actionLocation = $this->_findCtrl($ctrlpath, $class_loader);
        if ($discovery === null) {
            header('HTTP/1.0 404 Not Found');
            trigger_error("Action-controller '${ctrlpath}' not found", E_USER_ERROR);
            exit;
        }

        $action_class = $class_loader->load($discovery['ctrl']);
        // $action_class = $class_loader->load($this->actionLocation['ctrl']);
        if ($action_class === '') {
            header('HTTP/1.0 404 Not Found');
            trigger_error(
                "Unable to load the action-controller '${ctrlpath}'", 
                E_USER_ERROR);
            exit;
        }

        $task = &new $action_class($this);
        $this->_process->set($task, $action, $discovery);
        // $this->_process->set($task, $action, $this->actionLocation);
    }

    /**
     * プロセスのタスクを実行して実行結果に対応
     * 
     * @param  void
     * @return void
     * @access protected
     * @see    Process::invoke()
     * @see    Front::redirectAction()
     * @see    Front::redirect()
     * @see    Front::render()
     */
    function process()
    {
        // プロセスを実行
        $reply = $this->_process->invoke();

        // 実行結果を評価
        switch (true) {
        case preg_match('/^action::(.+)$/i', $reply, $match) : 
            $this->redirectAction($match[1]);
            break;
        case preg_match('/^redirect::(.+)$/i', $reply, $match) : 
            $this->redirect($match[1]);
            break;
        case ($reply !== '') : 
            $this->render($reply);
            break;
        }
    }

    /**
     * 実行前フィルタ
     * 
     * @param  void
     * @return void
     * @access private
     */
    function _applyFiltersBeforeMethod()
    {
        for ($i = 0, $n = count($this->_filters); $i < $n; ++$i) {
            if (!method_exists($this->_filters[$i], 'before')) {
                continue;
            }
            $this->_filters[$i]->before($this);
        }
    }

    /**
     * 実行後フィルタ
     * 
     * @param  void
     * @return void
     * @access private
     */
    function _applyFiltersAfterMethod()
    {
        for ($i = (count($this->_filters) - 1); $i >= 0; --$i) {
            if (!method_exists($this->_filters[$i], 'after')) {
                continue;
            }
            $this->_filters[$i]->after($this);
        }
    }

    /**
     * アクション・コントローラのパスからクラス名を検索
     * - パス「foo_bar_hoge_moge_abc」から探すファイル
     *  1. __action__/FooBarHogeMogeAbc.class.php
     *  2. __action__/foo/BarHogeMogeAbc.class.php
     *  3. __action__/foo_bar/HogeMogeAbc.class.php
     *  4. __action__/foo_bar_hoge/MogeAbc.class.php
     *  5. __action__/foo_bar_hoge_moge/Abc.class.php
     *  6. 失敗（nullを返却）
     * 
     * @param  string      $ctrlpath
     * @param  ClassLoader $class_loader
     * @return array|null
     * @access private
     * @see    Utility::camelize()
     * @see    ClassLoader::isReadable()
     */
    function _findCtrl($ctrlpath, &$class_loader)
    {
        $ctrl_class = null;
        $classname  = Utility::camelize($ctrlpath);

        if ($class_loader->isReadable($classname)) {
            // パス＝クラス名
            $ctrl_class = array(
                    'dir'  => '', 
                    'ctrl' => $classname, 
                );
        } else {
            // サブディレクトリあり
            // --------------------------------------------------
            // パスの区切り（_）を先頭から渡り歩く
            $subname  = $ctrlpath;
            $routelen = strlen($ctrlpath);
            while ($subname = strstr($subname, '_')) {
                $sublen  = strlen($subname);
                $subname = substr($subname, 1, $sublen - 1);
                $dirname = substr($ctrlpath, 0, $routelen - $sublen);

                $class_loader->setParents('Action', $dirname);
                $classname = Utility::camelize($subname);

                if ($class_loader->isReadable($classname)) {
                    $ctrl_class = array(
                            'dir'  => $dirname, 
                            'ctrl' => $classname, 
                        );
                    break;
                }
            }
        }

        return $ctrl_class;
    }
}
