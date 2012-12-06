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
 * アクション・コントローラのスーパークラス
 * 
 * - プログラムのフローを制御
 * - メンバ関数でアクションを定義
 *  - アクションでリクエストのロジックを実行
 *  - アクションの動作を定義できるメンバ関数
 *   - entry：入場処理
 *   - execute：実行処理
 *   - exit：退場処理
 *   - validate：検証処理
 * 
 * @package  tima
 * @version  SVN: $Id: Action.class.php 43 2007-10-16 11:25:23Z do_ikare $
 */
class Action
{
    /**
     * デフォルト・アクションの名前
     * 
     * @var    string
     * @access protected
     */
    var $defaultAction = 'default';

    /**
     * 設問
     * 
     * @var    Question
     * @access protected
     */
    var $question = null;

    /**
     * リクエスト
     * 
     * @var    Request
     * @access protected
     */
    var $request = null;

    /**
     * レスポンス
     * 
     * @var    Response
     * @access protected
     */
    var $response = null;

    /**
     * ユーザ・エージェント
     * 
     * @var    UserAgent
     * @access protected
     */
    var $userAgent = null;

    /**
     * セッション
     * 
     * @var    Session
     * @access protected
     */
    var $session = null;

    /**
     * フロント・コントローラ
     * 
     * @var    Front
     * @access protected
     */
    var $front = null;

    /**
     * ロガー
     * 
     * @var    Logger
     * @access protected
     */
    var $logger = null;

    /**
     * 公開しない（protected）メンバ変数の変数名
     * - 保護したいメンバ変数の変数名を羅列
     * - こんな手段でprotectedなあたりダサいし分かりづらい、要検討
     * 
     * @var    array
     * @access protected
     */
    var $protectedVarNames = array(
            'defaultAction', 
            'question', 
            'request', 
            'response', 
            'userAgent', 
            'session', 
            'logger', 
            'front', 
        );

    /**
     * コンストラクタ
     * 
     * @param  Front $front フロント・コントローラ
     * @access public
     */
    function Action(&$front)
    {
        $this->session   = &new Session($this->getName());
        $this->front     = &$front;
        $this->request   = &$front->getRequest();
        $this->response  = &$front->getResponse();
        $this->userAgent = &$front->getUserAgent();
        $this->beginning = &$front->getBeginning();
        $this->logger    = &$front->getLogger();
        $this->question  = &new Question($this, $front->getAppDir());
    }

    /**
     * コントローラを初期化
     * 
     * @param  void
     * @return void
     * @access public
     */
    function initialize() {}

    /**
     * コントローラの実行権限を返却
     * - スーパークラスでは常に真
     * - 継承クラスで条件に応じた応答を返すようオーバーライド
     * 
     * @param  void
     * @return boolean
     * @access public
     */
    function enable()
    {
        return true;
    }

    /**
     * モデル・クラスを返却
     * 
     * @param  string $model
     * @return string
     * @access public
     * @static ClassLoader $class_loader
     */
    function getModel($model)
    {
        static $class_loader;
        if (!isset($class_loader)) {
            $class_loader = &new ClassLoader;
            $class_loader->setParents('Model');
            $class_loader->setIncludePath($this->front->getAppDir());
        }

        return 
            $class_loader->load($model);
    }

    /**
     * モデル・クラスのインスタンスを生成
     * 
     * @param  string $model
     * @params mixed  $args
     * @return Object
     * @access public
     */
    function &useModel($model, $args = null)
    {
        $class_name = $this->getModel($model);
        if ($class_name === '') {
            header('HTTP/1.0 500 Internal Server Error');
            trigger_error("Model '${model}' not found", E_USER_ERROR);
            exit;
        }

        $args = array();
        for ($i = 1, $n = func_num_args(); $i < $n; ++$i) {
            $args[] = func_get_arg($i);
        }
        $args = implode(',', $args);

        $result = @eval("\$obj = &new ${class_name}(${args});");
        if ($result === false) {
            header('HTTP/1.0 500 Internal Server Error');
            trigger_error("Unable to new the Model '${model}'", E_USER_ERROR);
            exit;
        }

        return $obj;
    }

    /**
     * アクションが定義されているかを検査
     * 
     * @param  string  $action アクションの名前
     * @return boolean
     * @access public
     */
    function isActionExists($action)
    {
        return 
            method_exists($this, 'execute' . $action);
    }

    /**
     * アクションを実行
     * 
     * @param  string $action_name
     * @return string
     * @access public
     */
    function invoke($action_name)
    {
        // 活動するアクションの名前を定義
        $this->ACTIVE_ACTION = $action_name;

        // 入場処理
        $entry_method = 'entry' . $action_name;
        if (method_exists($this, $entry_method)) {
            call_user_func(array(&$this, $entry_method));
        }

        // 実行処理
        $execute_method = 'execute' . $action_name;
        if (!method_exists($this, $execute_method)) {
            trigger_error(
                "Unable to execute the action '${action_name}'", E_USER_WARNING);
        } else {
            $reply = call_user_func(array(&$this, $execute_method));
        }

        // テンプレートのデータ・モデルを登録
        foreach ($this->getProperties() as $varkey => $varvalue) {
            $this->response->setDataModel($varkey, $varvalue);
        }
        $this->response->setDataModel('form', $this->question->toArray());

        // 退場処理
        $exit_method = 'exit' . $action_name;
        if (method_exists($this, $exit_method)) {
            call_user_func(array(&$this, $exit_method));
        }

        return $reply;
    }

    /**
     * デフォルト・アクションの名前を返却
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     * 公開を許可したメンバ変数を配列で返却
     * 
     * @param  void
     * @return array
     * @access public
     */
    function getProperties()
    {
        $public_properties = array();
        foreach (array_keys(get_object_vars($this)) as $varkey) {
            if ((strpos($varkey, '_') === 0) || in_array($varkey, $this->protectedVarNames)) {
                continue;
            }
            $public_properties[$varkey] = $this->$varkey;
        }

        return $public_properties;
    }

    /**
     * アクション・コントローラ（自己）の名前を返却
     * - 名前は実際のクラス名に関係なく必ず小文字になります
     *  - PHP4/5どちらでも同じ値を返却するために
     * 
     * @param  void
     * @return string
     * @access protected
     * @static string $ctrl_name
     */
    function getName()
    {
        static $ctrl_name;
        if (!isset($ctrl_name)) {
            $ctrl_name = 
                preg_match('/^(?:Action_)([\w]+)$/i', get_class($this), $match) ?
                    $match[1] : get_class($this);
        }

        // PHP4/5の差を吸収するためstrtolower()関数で小文字に変換
        return 
            strtolower($ctrl_name);
    }

    /**
     * アクションの動作をログに記録
     * - 定型ログ形式でメッセージを記録
     * 
     * @param  string $action
     * @param  string $message
     * @return void
     * @access protected
     * @static UserAgent $ua
     * @see    Logger::info()
     */
    function logAction($action, $message = '')
    {
        static $ua;
        if (!isset($ua)) {
            $ua = &$this->front->getUserAgent();
        }

        $device = $ua->isMobile() ? 'Mobile' : 'PC';

        $this->logger->info(
            sprintf(
                '[%s] %s / Ctrl:%s?%s Device:%s', 
                $action, 
                $message, 
                $this->getName(), 
                $this->session->getId(), 
                $device));
    }
}
