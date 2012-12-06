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
 * ログ管理クラス
 * - ロギング => ファイル書き出し
 * 
 * @package    tima
 * @subpackage tima_Logger
 * @version    SVN: $Id: File.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Logger_File extends Logger
{

    /**
     * ログ記録ファイル
     * 
     * @var    string
     * @access private
     */
    var $_logFile = '';

    /**
     * ロガーの初期化処理
     * 
     * @param  array $option
     * @return void
     * @access public
     */
    function initialize($option)
    {
        if (!isset($option['file_name'])) {
            trigger_error('Log file name not exists', E_USER_WARNING);
            return;
        }

        $dir = isset($option['dir']) ? $option['dir'] : dirname(__FILE__);
        $this->_logFile = $dir . DS . $option['file_name'];
    }

    /**
     * ログ・メッセージの出力
     * 
     * @param  integer $level   エラーレベル
     * @param  string  $message メッセージ
     * @param  string  $file    呼び出し元のファイル名
     * @param  string  $line    呼び出し元の行番号
     * @return void
     * @access public
     */
    function logging($level, $message, $file, $line)
    {
        if ($level >= $this->getMask()) {
            error_log(
                $this->formatMessage($level, $message, $file, $line), 
                3, $this->_logFile);
        }
    }
}
