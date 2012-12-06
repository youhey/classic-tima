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

/* �Хå��Ķ���ư������ */
define('DEBUG', false);

/* �ѥ����ڤ�ʸ���Υ��硼�ȥ��å� */
define('DS', DIRECTORY_SEPARATOR);

/* ư��Ķ��δ���ǥ��쥯�ȥ� */
define('ROOT_DIR', 'tima');
/* ư��Ķ��δ���ѥ� */
define('ROOT_PATH', dirname(__FILE__) . DS . ROOT_DIR);

/* ������٥롧TRACE */
define('TW_LOG_TRACE', 1);
/* ������٥롧DEBUG */
define('TW_LOG_DEBUG', 2);
/* ������٥롧INFO */
define('TW_LOG_INFO', 3);
/* ������٥롧NOTICE */
define('TW_LOG_NOTICE', 4);
/* ������٥롧WARN */
define('TW_LOG_WARN', 5);
/* ������٥롧ERROR */
define('TW_LOG_ERROR', 6);
/* ������٥롧FATAL */
define('TW_LOG_FATAL', 7);

/* ����ñ�̡��� */
define('SECOND',  1);
/* ����ñ�̡�ʬ */
define('MINUTE', 60 * SECOND);
/* ����ñ�̡��� */
define('HOUR',   60 * MINUTE);
/* ����ñ�̡��� */
define('DAY',    24 * HOUR);
/* ����ñ�̡��� */
define('WEEK',    7 * DAY);
/* ����ñ�̡����31�������� */
define('MONTH',  31 * DAY);
/* ����ñ�̡�ǯ */
define('YEAR',  365 * DAY);

/* ISOɸ��������񼰡�YYYY-MM-DD HH:MM:SS�� */
define('DATE_FORMAT_ISO', 'Y-m-d H:i:s');
/* �����Υ����ॹ����׽񼰡�YYYYMMDDHHMMSS�� */
define('DATE_FORMAT_TIMESTAMP', 'YmdHis');
/* �����񼰤����դ����˾�ά��YYYY-MM-DD�� */
define('DATE_FORMAT_SIMPLEDATE', 'Y-m-d');
/* UNIX�����ॹ����� */
define('DATE_FORMAT_UNIXTIME', 'U');

/* ���Ƿ������� */
define('VAR_TYPE_INT', 1);
/* ���Ƿ����¿� */
define('VAR_TYPE_FLOAT', 2);
/* ���Ƿ���ʸ���� */
define('VAR_TYPE_STRING', 3);
/* ���Ƿ������� */
define('VAR_TYPE_DATETIME', 4);
/* ���Ƿ��������� */
define('VAR_TYPE_BOOLEAN', 5);

/* �ե����෿��1�ԥƥ����� */
define('FORM_TYPE_TEXT', 1);
/* �ե����෿���ѥ���� */
define('FORM_TYPE_PASTWORD', 2);
/* �ե����෿��ʣ���ԥƥ����� */
define('FORM_TYPE_TEXTAREA', 3);
/* �ե����෿�����쥯�� */
define('FORM_TYPE_SELECT', 4);
/* �ե����෿����¥� */
define('FORM_TYPE_RADIO', 5);
/* �ե����෿�������å��ܥå��� */
define('FORM_TYPE_CHECKBOX', 6);
/* �ե����෿�������ܥ��� */
define('FORM_TYPE_SUBMIT', 7);
/* �ե����෿���ܥ��� */
define('FORM_TYPE_BUTTON', 8);
/* �ե����෿���������� */
define('FORM_TYPE_HIDDEN', 9);

/**
 * @use PEAR
 * 
 * OS�Ķ��˴ؤ�������Τ߻���
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
