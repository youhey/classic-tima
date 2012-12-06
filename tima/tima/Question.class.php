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
 * フォームの設問要素を操作するためのクラス
 * 
 * @package  tima
 * @version  SVN: $Id: Question.class.php 36 2007-10-05 11:35:00Z do_ikare $
 */
class Question
{

    /**
     * アクション・コントローラ
     * 
     * @var    Action
     * @access public
     */
    var $action = null;

    /**
     * 設問クラスを読み込むローダー
     * 
     * @var    ClassLoader
     * @access public
     */
    var $questionLoader = null;

    /**
     * HTMLを生成するビルダー
     * 
     * @var    HTML|CHTML
     * @access public
     */
    var $builder = null;

    /**
     * 設問要素
     * 
     * @var    array
     * @access private
     */
    var $_elements = array();

    /**
     * エラー・メッセージ
     * 
     * @var    array
     * @access private
     */
    var $_errors = array();

    /**
     * エラー・メッセージの雛形
     * 
     * @var    array
     * @access private
     */
    var $_errorMessages = array();

    /**
     * コンストラクタ
     * 
     * @param  Action $action  アクション・コントローラ
     * @param  string $app_dir アプリケーションの配置パス
     * @access public
     */
    function Question(&$action, $app_dir)
    {
        $this->action         = &$action;
        $this->questionLoader = &new ClassLoader;

        if ($this->action->userAgent->isMobile()) {
            $this->builder = &new CHtml;
        } else {
            $this->builder = &new Html;
        }

        $this->questionLoader->setParents('Question');
        $this->questionLoader->setIncludePath(
            ROOT_PATH . PATH_SEPARATOR . $app_dir);
    }

    /**
     * 新たな設問要素を生成して管理下に登録
     * 
     * @param  string $name   設問要素の名前
     * @param  string $type   生成する設問クラスの名前
     * @param  array  $params 設問の属性
     * @return boolean
     * @access public
     */
    function register($name, $type, $params)
    {
        $class_name = $this->questionLoader->load($type);
        if ($class_name === '') {
            trigger_error(
                "Question '${name}' type not found: '${type}'", 
                E_USER_WARNING);
            return false;
        }

        // 誤って大文字が含まれた場合にエラーメッセージ補完に失敗するので
        $name = strtolower($name);

        // 要素を生成
        $element = &new $class_name($name, $this);
        foreach ($params as $varkey => $varvalue) {
            switch ($varkey) {
            case 'default' :
                // デフォルト値を登録
                if (is_scalar($varvalue) && is_scalar($element->value)) {
                    $element->value = $varvalue;
                } elseif (is_array($varvalue) && is_array($element->value)) {
                    $element->value = Utility::merge($element->value, $varvalue);
                } elseif ($varvalue !== null) {
                    trigger_error(
                        "Unable to set the '${name}' default: Disagreement of type", 
                        E_USER_WARNING);
                    return false;
                }
                break;
            case 'required' :
                // 必須／任意を登録
                $element->required = ($varvalue === true);
                break;
            default : 
                if ($varvalue !== null) {
                    $element->$varkey = $varvalue;
                }
                break;
            }
        }
        $element->initialize();
        $this->_elements[$name] = &$element;

        return true;
    }

    /**
     * 登録されている設問要素を削除
     * 
     * @param  string $element 設問要素の名前
     * @return void
     * @access public
     */
    function remove($element)
    {
        if (isset($this->_elements[$element])) {
            unset($this->_elements[$element]);
        }
    }

    /**
     * 設問要素の回答を消去して初期化
     * - 設問の消去（erase()メソッド）と初期化（initialize()メソッド）を実行
     * - 正常な動作のためには上記2つのメソッドが的確に実装されている必要あり
     * 
     * @param  string $element 設問要素の名前
     * @return void
     * @access public
     */
    function erase($element)
    {
        if (isset($this->_elements[$element])) {
            $this->_elements[$element]->erase();
            $this->_elements[$element]->initialize();
        }
    }

    /**
     * 管理下にある全ての設問要素を連想配列で返却
     * - 返却する連想配列は主要な属性のみ固定
     *  - string  label    => 設問要素の項目名
     *  - string  html     => 設問のHTML
     *  - string  text     => 回答の文字列
     *  - mixed   value    => 回答のリクエスト値
     *   - 値の格納方法は設問に拠るので必ずしも生のリクエスト値ではない
     *  - string  error    => 登録されたエラー・メッセージ
     *   - 複数のエラーが登録されていれば最後に登録されたエラー・メッセージ
     *   - エラーが登録されていなければ空文字列
     *  - boolean required => 必須／任意
     *   - 必須 => true
     *   - 任意 => false
     * 
     * @param  void
     * @return array
     * @access public
     */
    function toArray()
    {
        $elements = array();

        foreach ($this->_elements as $name => $element) {
            $elements[$name] = array(
                    'label'    => $element->label, 
                    'html'     => $element->toHtml(), 
                    'text'     => $element->toText(), 
                    'value'    => $element->value, 
                    'error'    => $this->getError($name), 
                    'required' => $element->required, 
                );
        }

        return $elements;
    }

    /**
     * 設問要素の回答を文字列で返却
     * 
     * @param  string $element 設問要素の名前
     * @return string
     * @access public
     */
    function toText($element)
    {
        $text = '';

        if ($this->exists($element)) {
            $text = $this->_elements[$element]->toText();
        }

        return $text;
    }

    /**
     * 管理下にある全ての設問要素で回答の妥当性を検証
     * 
     * @param  void
     * @return void
     * @access public
     */
    function validate()
    {
        foreach ($this->_elements as $element) {
            if ($element->required === true) {
                // 必須項目は値が必須条件を充たしているか検証
                // 検証結果が偽ならすでにエラーなので後の処理は省略
                if (!$element->checkRequired()) {
                    continue;
                }
            } else {
                // 任意項目で値が存在しなければ検証は不要
                if (!$this->isNotNull($element->name)) {
                    continue;
                }
            }

            $element->validate();
        }
    }

    /**
     * 設問要素にエラー・メッセージを登録
     * 
     * @param  string $element 設問要素の名前
     * @param  string $message エラー・メッセージ
     * @return void
     * @access public
     */
    function setError($element, $message)
    {
        if (!isset($this->_errors[$element])) {
            $this->_errors[$element] = array();
        }

        $this->_errors[$element][] = $message;
    }

    /**
     * 設問要素に登録されたエラー・メッセージを返却
     * 
     * @param  string $element 設問要素の名前
     * @return string
     * @access public
     */
    function getError($element)
    {
        $error = 
            (isset($this->_errors[$element]) && 
                (count($this->_errors[$element]) > 0)) ? 
                    end($this->_errors[$element]) : '';

        return $error;
    }

    /**
     * 管理下の全てのエラー・メッセージを返却
     * 
     * @param  void
     * @return array
     * @access public
     */
    function getAllError()
    {
        $error = array();

        foreach (array_keys($this->_elements) as $name) {
            $element_error = $this->getError($name);
            if ($element_error !== '') {
                $error[$name] = $element_error;
            }
        }

        return $error;
    }

    /**
     * 設問要素に登録されているエラー・メッセージを消去
     * 
     * @param  string $element 設問要素の名前
     * @return void
     * @access public
     */
    function clearError($element)
    {
        $this->_errors[$element] = array();
    }

    /**
     * 設問要素にエラー・メッセージが登録されているかを検証
     * 
     * @param  string $element 設問要素の名前
     * @return boolean
     * @access public
     * @see    Question::getError()
     */
    function isError($element)
    {
        return 
            ($this->getError($element) !== '');
    }

    /**
     * 管理下にエラー・メッセージが登録されているかを検証
     * 
     * @param  void
     * @return boolean
     * @access public
     * @see    Question::getAllError()
     */
    function hasError()
    {
        return 
            (count($this->getAllError()) > 0);
    }

    /**
     * メンバ関数「expectError()」のためのエラー・メッセージの雛形を登録
     * - 雛形適応条件は「action.validation.element」という形式
     *  - action     => アクション・コントローラの名前
     *  - validation => バリデーションの名前
     *  - element    => 設問要素の名前
     * - 条件を不定とするには「*」のワイルドカードを使用
     * 
     * @param  string $replacement 適応条件
     * @param  string $message     エラー・メッセージの雛形
     * @return string
     * @access public
     * @see    Question::expectError()
     */
    function setErrorMessages($replacement, $message)
    {
        $this->_errorMessages[$replacement] = $message;
    }

    /**
     * 設問要素の名前とバリデーションの名前から
     * 適当なエラー・メッセージを登録されている雛形から生成
     * - 最適な雛形を検索する具体的な内容
     *  - 雛形は適応条件を配列キーとする連想配列になっている
     *   - 配列キー => action.validation.element
     *    - action     => アクション・コントローラの名前
     *    - validation => バリデーションの名前
     *    - element    => 設問要素の名前
     *  - 名前を不特定とする場合には「*」をワイルドカードとして評価
     *  - 検索順序は「element > action > validation」の評価順で以下のとおり
     *   1. action.validation.element
     *   2. action.*.element
     *   3. *.validation.element
     *   4. *.*.element
     *   5. action.validation.*
     *   6. *.validation.*
     *   7. *.*.*
     * - 雛形に埋め込まれた特定キーワードをパース
     *  - 特定キーワード => %変数名%（変数名 == 設問要素のプロパティ名）
     *  - 特定キーワードのパース
     *   1. preg_replace_callback()関数で特定キーワード「%[\w]+%」を検索
     *   2. コールバックの匿名関数で設問要素のプロパティの値を照会
     *    - 設問要素に特定キーワードの変数名と同名のプロパティがないか確認
     *     - プロパティが存在すれば文字列にキャストして置換
     *     - プロパティが存在しなければ空文字列で置換
     * - パースした結果をエラー･メッセージとして返却
     * 
     * @param  string $element    設問要素の名前
     * @param  string $validation 検証の名前
     * @return string
     * @access public
     */
    function expectError($element, $validation)
    {
        $message = '';

        // 最適なエラー・メッセージの雛形を検索
        $action     = strtolower($this->action->getName());
        $validation = strtolower($validation);
        $element    = strtolower($element);
        foreach (
            array(
                    '%1$s.%2$s.%3$s', 
                    '%1$s.*.%3$s', 
                    '*.%2$s.%3$s', 
                    '*.*.%3$s', 
                    '%1$s.%2$s.*', 
                    '*.%2$s.*', 
                    '*.*.*', 
                ) as $replacement) {
            $varkey = sprintf($replacement, $action, $validation, $element);
            if (!isset($this->_errorMessages[$varkey])) {
                continue;
            }

            $message = $this->_errorMessages[$varkey];
            break;
        }

        if ($message === '') {
            trigger_error(
                "Unable to expect error '${name}::${validation}'", E_USER_WARNING);
        }

        if (isset($this->_elements[$element])) {
            // 雛形の特定キーワードをパース
            // 設問要素をグローバル変数で参照して匿名関数の中で使用
            $GLOBALS['__QUESTION_ELEMENT'] = &$this->_elements[$element];
            $message = 
                preg_replace_callback(
                    '/%([\w]+)%/', 
                    create_function(
                        '$matches', 
                        '$key = $matches[1];' . 
                            '$msg = ' . 
                            'isset($GLOBALS["__QUESTION_ELEMENT"]->$key) ? ' . 
                            '(string)$GLOBALS["__QUESTION_ELEMENT"]->$key : "";' . 
                            'return $msg;'), 
                    $message);
            unset($GLOBALS['__QUESTION_ELEMENT']);

            $this->action->logAction(
                'QuestionFailure', 
                get_class($this->_elements[$element]) . ':' . $validation);
        }

        return $message;
    }

    /**
     * 設問要素の回答が空でないことを検証
     * 
     * @param  string $element 設問要素の名前
     * @return boolean
     * @access public
     */
    function isNotNull($element)
    {
        return (
            isset($this->_elements[$element]) && 
            ($this->_elements[$element]->toText() !== ''));
    }

    /**
     * 設問要素が管理下に存在しているかを検証
     * 
     * @param  string $element 設問要素の名前
     * @return boolean
     * @access public
     */
    function exists($element)
    {
        return 
            isset($this->_elements[$element]);
    }

    /**
     * 設問要素を返却
     * 
     * @param  string $element 設問要素の名前
     * @return Question_Common
     * @access public
     */
    function &getElement($element)
    {
        $obj = null;
        if (isset($this->_elements[$element])) {
            $obj = &$this->_elements[$element];
        }

        return $obj;
    }
}
