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
 * ����ͤȤ��������������ư��Ķ�������ˤʤ�ޤ�
 * <code>
 * return array(
 *     array(
 *         'key'   => '���ꤹ�����̾', 
 *         'value' => '���ꤹ����', 
 *         'space' => '�����̾������', 
 *       ), 
 *     array(
 *         'key'   => 'config_name', 
 *         'value' => 'config_value', 
 *         'space' => 'name_space', 
 *       ), 
 *   );
 * </code>
 * 
 * ����ե�����ϥ��󥯥롼�ɤ�������ͤ�ɾ�����ޤ�
 * �ֵѰʳ���ư��ˤ�������Ѥϡ��տޤ��ʤ�ư��θ����ˤʤ�ޤ�
 * 
 * - ����ե���������Ƥϰʲ��ν���Ǿ�񤭤���ޤ�
 *  - all-ini.php => ���Ƥζ�������
 *  - <front>-ini.php => �ե��ȡ�����ȥ��������ʥ���ȥ���̾��Ƚ�ǡ�
 *  - <action>-ini.php => ��������󡦥���ȥ��������ʥ���ȥ���̾��Ƚ�ǡ�
 * - ����ե�����ϼ���ˤ�ä��ɤ߹��ॿ���ߥ󥰤��㤤�ޤ�
 *  - all-ini.php�θƤӽФ��ϥե��ȡ�����ȥ���ν����
 *  - <front>-ini.php�θƤӽФ��ϥե��ȡ�����ȥ���ν����
 *  - <action>-ini.php�ϥե��ȡ�����ȥ���μ¹Ի�
 * 
 * ���Υե����������ϴ��ܤν���ͤʤΤǸ�§�Ȥ��ƽ񤭴����ʤ�
 * �����ͤ��ѹ��ϳƥ���ȥ����ͭ������Ǿ�񤹤�
 */

return
    array(
            // �ꥯ�����Ȥ�����դ��륢�������Υ���̾
            array(
                    'key'   => 'action_key', 
                    'value' => 'a', 
                    'space' => 'env', 
                ), 
            // ���å����̾
            array(
                    'key'   => 'session_name', 
                    'value' => 's', 
                    'space' => 'env', 
                ), 
            // �����Υ��饹̾
            array(
                    'key'   => 'logger_class', 
                    'value' => 'Logger', 
                    'space' => 'env', 
                ), 

            // ����ȥ꡼�ݥ���Ȥˤʤ�HTTP�Ķ��Υ롼�ȥѥ��ʥ�����̾�ʹߤΥ롼��������
            array(
                    'key'   => 'http_root_path', 
                    'value' => '/', 
                    'space' => 'env', 
                ), 
            // ����ȥ꡼�ݥ���Ȥˤʤ�HTTPS�Ķ��Υ롼�ȥѥ�
            // �롼�ȥѥ���HTTP��HTTPS�ǰۤʤ���ˤΤ�ͭ��
            array(
                    'key'   => 'https_root_path', 
                    'value' => '/', 
                    'space' => 'env', 
                ), 

            // ��å��󥰤������٥������
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
            // �����Υ��ץ������
            array(
                    'key'   => 'log_option', 
                    'value' => array(), 
                    'space' => 'env', 
                ), 
        );
