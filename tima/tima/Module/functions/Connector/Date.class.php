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
 * ǯ��������
 * 
 * @package    tima
 * @subpackage tima_Connector
 * @version    SVN: $Id: Date.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Connector_Date extends Connector_AbstractConnector
{

    /**
     * ���դ�Ϣư������礷��ʸ������ֵ�
     * 
     * �����񼰲���������ʸ����˷�礹��
     * ���վ������­������Хǥե�����ͤ��䴰����
     * 
     * ������$params�פ��ͤ�ư�������
     * - ʸ����ν񼰡�sprintf()�ؿ��Υե����ޥåȡ�
     * - �ͤ���­�������˥ǥե�����ͤ��䴰�򤹤뤫
     * - ǯ�Υǥե�����͡ʻ��꤬�ʤ���С�1970��)
     * - ��Υǥե�����͡ʻ��꤬�ʤ���С�1��)
     * - ���Υǥե�����͡ʻ��꤬�ʤ���С�1��)
     * 
     * @param  array      $attribute
     * @param  array|null $params
     * @return string
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        $format     = '%1$sǯ%2$s��%3$s��';
        $supplement = false;
        $year       = '1970';
        $month      = '1';
        $day        = '1';
        if (($param = array_shift($params)) !== null) {
            $format = (string)$param;
        }
        if (($param = array_shift($params)) !== null) {
            $supplement = (bool)$param;
        }
        if (($param = array_shift($params)) !== null) {
            $year = (int)$param;
        }
        if (($param = array_shift($params)) !== null) {
            $month = (int)$param;
        }
        if (($param = array_shift($params)) !== null) {
            $day = (int)$param;
        }
        if ($supplement !== true) {
            $year  = '';
            $month = '';
            $day   = '';
        }

        $specific = false;
        foreach (array_keys($attribute) as $varkey) {
            switch (strtolower($varkey)) {
            case 'year' : 
                $specific = true;
                $year     = trim((string)$attribute[$varkey]);
                break;
            case 'month' : 
                $specific = true;
                $month    = trim((string)$attribute[$varkey]);
                break;
            case 'day' : 
                $specific = true;
                $day      = trim((string)$attribute[$varkey]);
                break;
            }
        }
        if ($specific !== true) {
            if (($varvalue = array_shift($attribute)) !== null) {
                $year = trim((string)$varvalue);
            }
            if (($varvalue = array_shift($attribute)) !== null) {
                $month = trim((string)$varvalue);
            }
            if (($varvalue = array_shift($attribute)) !== null) {
                $day = trim((string)$varvalue);
            }
        }

        $date = '';
        if (($year !== '') && ($month !== '') && ($day !== '')) {
            $date = sprintf($format, $year, $month, $day);
        }

        return $date;
    }
}
