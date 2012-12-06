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
 * 設問要素の基底クラス
 * 
 * @package    tima
 * @subpackage tima_Question
 * @version    SVN: $Id: Common.class.php 9 2007-09-05 02:48:37Z do_ikare $
 */
class Question_Common
{

    /**
     * 名前
     * 
     * @var    string
     * @access public
     */
    var $name = '';

    /**
     * 外部操作のための偽名
     * 外部操作で必要としなければ「$name」と同一
     * 
     * @var    string
     * @access public
     */
    var $alias = '';

    /**
     * 項目の名前
     * 
     * @var    string
     * @access public
     */
    var $label = '';

    /**
     * 値
     * 
     * @var    mixed
     * @access public
     */
    var $value = null;

    /**
     * 必須／任意
     * 
     * @var    boolean
     * @access public
     */
    var $required = false;

    /**
     * 操作クラス
     * 
     * @var    Question
     * @access protected
     */
    var $handler = null;

    /**
     * リクエスト（ショートカット用）
     * 
     * @var    Request
     * @access protected
     */
    var $request = null;

    /**
     * セッション（ショートカット用）
     * 
     * @var    Session
     * @access protected
     */
    var $session = null;

    /**
     * コンストラクタ
     * 
     * @param  string   $name    設問要素の名前
     * @param  Question $handler 操作クラス
     * @access public
     */
    function Question_Common($name, &$handler)
    {
        $this->name    = $name;
        $this->alias   = $name;
        $this->handler = &$handler;
        $this->request = &$this->handler->action->request;
        $this->session = &$this->handler->action->session;
    }

    /**
     * 初期化
     * 
     * @param  void
     * @return void
     * @access public
     */
    function initialize()
    {
        $this->handler->clearError($this->alias);

        $prev_state = $this->session->getFlash($this->name);
        if ($prev_state !== null) {
            $this->set($prev_state);
        }

        $request = $this->request->get($this->name);
        if ($request !== null) {
            $this->set($request);
        }
    }

    /**
     * 回答を登録
     * 
     * @param  mixed $request リクエスト
     * @return void
     * @access public
     */
    function set($request)
    {
        $this->value = $request;
        $this->session->setFlash($this->name, $this->value);
    }

    /**
     * 回答を消去
     * 
     * @param  void
     * @return void
     * @access public
     */
    function erase()
    {
        // フラッシュ変数を消去のため取得して捨てる
        $flash_value = $this->session->getFlash($this->name);

        $defaults    = get_class_vars(get_class($this));
        $this->value = $defaults['value'];
    }

    /**
     * 値を文字列で返却
     * 
     * @param  void
     * @return string
     * @access public
     */
    function toText()
    {
        return (string)$this->value;
    }

    /**
     * 設問のHTMLを返却
     * 
     * 継承したクラスで処理を実装
     * 
     * @param  void
     * @return string
     * @access public
     * @abstract
     */
    function toHtml() {}

    /**
     * 値を検証
     * 
     * 継承したクラスで処理を実装
     * 
     * @param  void
     * @return void
     * @access public
     * @abstract
     */
    function validate() {}

    /**
     * 必須条件を充たしているかを検証
     * - 値（メンバ関数「toText()」の戻り値）が存在しているかを検証
     * 
     * @param  void
     * @return string
     * @access public
     */
    function checkRequired()
    {
        $resultant = true;

        if ($this->required === true) {
            $validator = 'Required';
            $resultant = Utility::is($validator, $this->toText());
            if ($resultant === false) {
                $this->handler->setError(
                    $this->alias, 
                    $this->handler->expectError($this->alias, $validator));
            }
        }

        return $resultant;
    }
}
