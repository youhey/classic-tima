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

/* バッグ環境の動作請求 */
define('DEBUG', false);

/* パス区切り文字のショートカット */
define('DS', DIRECTORY_SEPARATOR);

/* 動作環境の基底ディレクトリ */
define('ROOT_DIR', 'tima');
/* 動作環境の基底パス */
define('ROOT_PATH', dirname(__FILE__) . DS . ROOT_DIR);

/* ログ・レベル：TRACE */
define('TW_LOG_TRACE', 1);
/* ログ・レベル：DEBUG */
define('TW_LOG_DEBUG', 2);
/* ログ・レベル：INFO */
define('TW_LOG_INFO', 3);
/* ログ・レベル：NOTICE */
define('TW_LOG_NOTICE', 4);
/* ログ・レベル：WARN */
define('TW_LOG_WARN', 5);
/* ログ・レベル：ERROR */
define('TW_LOG_ERROR', 6);
/* ログ・レベル：FATAL */
define('TW_LOG_FATAL', 7);

/* 日時単位：秒 */
define('SECOND',  1);
/* 日時単位：分 */
define('MINUTE', 60 * SECOND);
/* 日時単位：時 */
define('HOUR',   60 * MINUTE);
/* 日時単位：日 */
define('DAY',    24 * HOUR);
/* 日時単位：週 */
define('WEEK',    7 * DAY);
/* 日時単位：月（31日換算） */
define('MONTH',  31 * DAY);
/* 日時単位：年 */
define('YEAR',  365 * DAY);

/* ISO標準の日時書式（YYYY-MM-DD HH:MM:SS） */
define('DATE_FORMAT_ISO', 'Y-m-d H:i:s');
/* 日時のタイムスタンプ書式（YYYYMMDDHHMMSS） */
define('DATE_FORMAT_TIMESTAMP', 'YmdHis');
/* 日時書式を日付だけに省略（YYYY-MM-DD） */
define('DATE_FORMAT_SIMPLEDATE', 'Y-m-d');
/* UNIXタイムスタンプ */
define('DATE_FORMAT_UNIXTIME', 'U');

/* 要素型：整数 */
define('VAR_TYPE_INT', 1);
/* 要素型：実数 */
define('VAR_TYPE_FLOAT', 2);
/* 要素型：文字列 */
define('VAR_TYPE_STRING', 3);
/* 要素型：日時 */
define('VAR_TYPE_DATETIME', 4);
/* 要素型：真偽値 */
define('VAR_TYPE_BOOLEAN', 5);

/* フォーム型：1行テキスト */
define('FORM_TYPE_TEXT', 1);
/* フォーム型：パスワード */
define('FORM_TYPE_PASTWORD', 2);
/* フォーム型：複数行テキスト */
define('FORM_TYPE_TEXTAREA', 3);
/* フォーム型：セレクト */
define('FORM_TYPE_SELECT', 4);
/* フォーム型：レヂオ */
define('FORM_TYPE_RADIO', 5);
/* フォーム型：チェックボックス */
define('FORM_TYPE_CHECKBOX', 6);
/* フォーム型：送信ボタン */
define('FORM_TYPE_SUBMIT', 7);
/* フォーム型：ボタン */
define('FORM_TYPE_BUTTON', 8);
/* フォーム型：隠し要素 */
define('FORM_TYPE_HIDDEN', 9);

/**
 * @use PEAR
 * 
 * OS環境に関する定数のみ使用
 */
@include_once 'PEAR.php';

/* @use Controller */
require_once 
    ROOT_PATH . DS . 'Front.class.php';
/* @use Action */
require_once 
    ROOT_PATH . DS . 'Action.class.php';
/* @use Process */
require_once 
    ROOT_PATH . DS . 'Process.class.php';
/* @use Config */
require_once 
    ROOT_PATH . DS . 'Config.class.php';
/* @use ClassLoader */
require_once 
    ROOT_PATH . DS . 'ClassLoader.class.php';
/* @use Request */
require_once 
    ROOT_PATH . DS . 'Request.class.php';
/* @use Request */
require_once 
    ROOT_PATH . DS . 'UserAgent.class.php';
/* @use Response */
require_once 
    ROOT_PATH . DS . 'Response.class.php';
/* @use Session */
require_once 
    ROOT_PATH . DS . 'Session.class.php';
/* @use Question */
require_once 
    ROOT_PATH . DS . 'Question.class.php';
/* @use Sendmail */
require_once 
    ROOT_PATH . DS . 'Sendmail.class.php';
/* @use Module */
require_once 
    ROOT_PATH . DS . 'Module.class.php';

/* @use Utility */
require_once 
    ROOT_PATH . DS . 'Utility.class.php';
/* @use Html */
require_once 
    ROOT_PATH . DS . 'Html.class.php';
/* @use CHtml */
require_once 
    ROOT_PATH . DS . 'CHtml.class.php';

/* @use Logger */
require_once 
    ROOT_PATH . DS . 'Logger.class.php';
/* @use Logger_File */
require_once 
    ROOT_PATH . DS . 'Logger' . DS . 'File.class.php';
/* @use Logger_File */
require_once 
    ROOT_PATH . DS . 'Logger' . DS . 'DailyFile.class.php';
/* @use Logger_Display */
require_once 
    ROOT_PATH . DS . 'Logger' . DS . 'Display.class.php';

/* @use View */
require_once 
    ROOT_PATH . DS . 'View.class.php';
/* @use Smarty4View */
require_once 
    ROOT_PATH . DS . 'Smarty4View.class.php';

/* @use DateAccessor */
require_once 
    ROOT_PATH . DS . 'DateAccessor.class.php';
/* @use DateTimeAccessor */
require_once 
    ROOT_PATH . DS . 'DateTimeAccessor.class.php';
/* @use DateMicrotimeAccessor */
require_once 
    ROOT_PATH . DS . 'DateMicrotimeAccessor.class.php';
/* @use DateController */
require_once 
    ROOT_PATH . DS . 'DateController.class.php';

/* @use SingletonDB */
require_once 
    ROOT_PATH . DS . 'SingletonDB.class.php';

/* @use Question_Common */
require_once 
    ROOT_PATH . DS . 'Question' . DS . 'Common.class.php';
