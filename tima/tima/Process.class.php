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
 * 実行プロセス
 * 
 * @package  tima
 * @version  SVN: $Id: Process.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Process
{

    /**
     * アクション・タスク
     * 
     * @var    action
     * @access private
     */
    var $_task = null;

    /**
     * 実行するアクションの名前
     * 
     * @var    string
     * @access private
     */
    var $_action = '';

    /**
     * アクション・コントローラのパス
     * 
     * @var    string
     * @access private
     */
    var $_path = '';

    /**
     * コンストラクタ
     * 
     * @param  void
     * @access public
     */
    function Process() {}

    /**
     * タスクを登録
     * 
     * @param  Action $task   アクション・コントローラ
     * @param  string $action 実行するアクションの名前
     * @param  array  $path   アクション・コントローラ・クラスのパス
     * @return void
     * @access public
     * @see    Action::enabled()
     * @see    Action::getDefaultAction()
     * @see    Action::isActionExists()
     */
    function set(&$task, $action, $path)
    {
        $this->_task   = &$task;
        $this->_action = $action;
        $this->_path   = $path;
    }

    /**
     * タスクを実行
     * 
     *  - アクション・コントローラから実行権限が得られなかった
     *   - <del>エラーログ：Forbidden executing action-controller 'コントローラ名'</del>
     *   - レスポンス：403 Forbidden
     *  - アクション・コントローラにアクションが存在しない
     *   - エラーログ：Action 'コントローラ名::アクション名' not found
     *   - レスポンス：404 Not Found
     * 
     * @param  void
     * @return string|null
     * @access public
     */
    function invoke()
    {
        $this->_task->initialize();

        // アクションの名前がなければデフォルト値をセット
        if ($this->_action === null) {
            $this->_action = $this->_task->getDefaultAction();
        }

        if (!$this->_task->isActionExists($this->_action)) {
            header('HTTP/1.0 400 Bad Request');
            // プログラムのエラーではないのでPHPのエラーを発生させないべきか？
            // 正常な処理の結果としてコントローラ側でログを記録させる？
            trigger_error(
                sprintf(
                    "Action '%s::%s' not found", 
                    $this->_task->getName(), $this->_action), 
                E_USER_ERROR);
            exit;
        }

        if (!$this->_task->enable()) {
            header('HTTP/1.0 403 Forbidden');
            // 処理としては正常系なのでPHPのエラーは記録しない
            // ログの記録など必要であればコントローラ側に任せる
            // trigger_error(
            //     sprintf(
            //         "Forbidden executing action-controller '%s'", 
            //         $this->_task->getName()), 
            //     E_USER_ERROR);
            exit;
        }

        $resultant = $this->_task->invoke($this->_action);
        if (($resultant === null) || ($resultant === 'success')) {
            $resultant = $this->_action;
        }

        return $this->_formatResult($resultant);
    }

    /**
     * 実行結果のパスを再構成
     * 
     * @param  string $resultant 結果パス
     * @param  array  $path      アクション・コントローラ・クラスのパス
     * @return string|null
     * @access public
     */
    function _formatResult($resultant)
    {
        if (!is_string($resultant) || ($resultant === '')) {
            return '';
        }

        // 結果パスに命令子「::」が含まれていれば再構成の必要なし
        if (strpos($resultant, '::') !== false) {
            return $resultant;
        }

        // 結果パスの先頭が「/」なら、↑処理のキャンセル目的と判断
        // ※本当にパスを絶対指定するならテンプレートの設定値を操作する
        $resultant = str_replace('/', DS, $resultant);
        if ((strpos($resultant, DS) === 0)) {
            return substr($resultant, 1);
        }

        // 結果パスを再構成
        $action_path = $this->_path['ctrl'];
        if ($this->_path['dir'] !== '') {
            if (strrpos($this->_path['dir'], DS) !== strlen($this->_path['dir'])) {
                $this->_path['dir'] = $this->_path['dir'] . DS;
            }
            $action_path = $this->_path['dir'] . $action_path;
        }

        return $action_path . DS . $resultant;
    }
}
