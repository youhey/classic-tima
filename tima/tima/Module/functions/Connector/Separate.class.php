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
 * Ĺ����������������
 * 
 * @package    tima
 * @subpackage tima_Connector
 * @version    SVN: $Id: Separate.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Connector_Separate extends Connector_AbstractConnector
{

    /**
     * Ĺ�������������礷��ʸ������ֵ�
     * 
     * �ͤ��ͤη��ҤȤ��ƻ��ꤵ�줿ʸ�������Ѥ���
     * 
     * ���ڤ�η��Ҥϥǥե���Ȥ��ȥ����
     * �����˻Ȥ����ѥ졼��ʸ�������������ǻ���
     * ������$params�פ��ͤ�ư�������
     * - Ϣ�뤹��ʸ����ʻ��꤬�ʤ���Х���ޡ�,�ס�
     * 
     * @param  array      $attribute
     * @param  array|null $params
     * @return string
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        $separate = ',';
        if (($param = array_shift($params)) !== null) {
            $separate = (string) $param;
        }

        $values = array();

        foreach (array_values($attribute) as $value) {
            // [����ν���]
            // $value = trim((string) $value);
            // if ($value === '') {
            //     continue;
            // }
            // Separate��¾��Ϣ������Ȱ㤤���Ѥ�¿��Ū�����Ӥ����ꤹ��Τǡ�
            // �ͤ�trim���Ƥ��ޤ��Τ������Ѥ���������
            // --------------------------------------------------
            // ("   1234", "   abcd", "    ....") => "1234,abcd,...."
            // ("-a-\n-b-\n-c-\n", "-d-\n-e-\n")  => "-a-\n-b-\n-c-,-d-\n-e-"
            // --------------------------------------------------
            // ���Ԥ����������롿����ǥ���ǥ�ȤʤɤǤ��ʤ�
            // ���������ԡ�����Τߤ��ͤ�Ϣ�뤹�٤��Ǥʤ��Τ�ifʸ��ɾ����trim
            // --------------------------------------------------
            // ("   1234", "   abcd", "    ....") => "    1234,    abcd,    ...."
            // ("-a-\n-b-\n-c-\n", "-d-\n-e-\n")  => "-a-\n-b-\n-c-\n,-d-\n-e-\n"
            // ("aaa", "  ", "bbb", "\n", "ccc")  => "aaa,bbb,ccc"
            // --------------------------------------------------
            $value = (string)$value;
            if (trim($value) === '') {
                continue;
            }
            $values[] = $value;
        }

        return implode($separate, $values);
    }
}
