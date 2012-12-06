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
 * all-ini.php - Initialize for all -
 * 
 * 戻り値として定義した配列が動作環境の設定になります
 * <code>
 * return array(
 *     array(
 *         'key'   => '設定する項目名', 
 *         'value' => '設定する値', 
 *         'space' => '設定の名前空間', 
 *       ), 
 *     array(
 *         'key'   => 'config_name', 
 *         'value' => 'config_value', 
 *         'space' => 'name_space', 
 *       ), 
 *   );
 * </code>
 * 
 * 設定ファイルはインクルードした戻り値を評価します
 * 返却以外の動作による副作用は、意図しない動作の原因になります
 * 
 * - 設定ファイルの内容は以下の順序で上書きされます
 *  - all-ini.php => 全ての共通設定
 *  - <front>-ini.php => フロント・コントローラの設定（コントローラ名で判断）
 *  - <action>-ini.php => アクション・コントローラの設定（コントローラ名で判断）
 * - 設定ファイルは種類によって読み込むタイミングが違います
 *  - all-ini.phpの呼び出しはフロント・コントローラの初期化
 *  - <front>-ini.phpの呼び出しはフロント・コントローラの初期化
 *  - <action>-ini.phpはフロント・コントローラの実行時
 * 
 * このファイルの定義は基本の初期値なので原則として書き換えない
 * 設定値の変更は各コントローラ固有の設定で上書する
 */

return
    array(
            // リクエストを受け付けるアクションのキー名
            array(
                    'key'   => 'action_key', 
                    'value' => 'a', 
                    'space' => 'env', 
                ), 
            // セッション名
            array(
                    'key'   => 'session_name', 
                    'value' => 's', 
                    'space' => 'env', 
                ), 
            // ロガーのクラス名
            array(
                    'key'   => 'logger_class', 
                    'value' => 'Logger', 
                    'space' => 'env', 
                ), 

            // エントリーポイントになるHTTP環境のルートパス（サーバ名以降のルート地点）
            array(
                    'key'   => 'http_root_path', 
                    'value' => '/', 
                    'space' => 'env', 
                ), 
            // エントリーポイントになるHTTPS環境のルートパス
            // ルートパスがHTTPとHTTPSで異なる場合にのみ有効
            array(
                    'key'   => 'https_root_path', 
                    'value' => '/', 
                    'space' => 'env', 
                ), 

            // ロッギングするログレベルの閾値
            // --------------------------------------------------
            // level(1) : TW_LOG_TRACE
            // level(2) : TW_LOG_DEBUG
            // level(3) : TW_LOG_INFO
            // level(4) : TW_LOG_NOTICE
            // level(5) : TW_LOG_WARN
            // level(6) : TW_LOG_ERROR
            // level(7) : TW_LOG_FATAL
            array(
                    'key'   => 'log_level', 
                    'value' => TW_LOG_WARN, 
                    'space' => 'env', 
                ), 
            // ロガーのオプション値
            array(
                    'key'   => 'log_option', 
                    'value' => array(), 
                    'space' => 'env', 
                ), 
        );
