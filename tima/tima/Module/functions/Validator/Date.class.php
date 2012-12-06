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

/* ���եѥ����������ɽ�� */
define(
    'VALIDATE_DATE_FORMAT', 
    '(?:' . 
        '(\d{2}|\d{4})\/(\d{1,2})\/(\d{1,2})|' . 
        '(\d{2}|\d{4})\.(\d{1,2})\.(\d{1,2})|' . 
        '(\d{2}|\d{4})-(\d{1,2})-(\d{1,2})|' . 
        '(\d{2}|\d{4}) (\d{1,2}) (\d{1,2})|' . 
        '(\d{2}|\d{4})(\d{2})(\d{2})|' . 
        '(\d{2}|\d{4})ǯ(\d{1,2})��(\d{1,2})��' . 
        ')');

/**
 * ʸ�������դȤ������������򸡾�
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: Date.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Validator_Date extends Validator_AbstractValidator
{

    /**
     * ʸ�������դȤ������������򸡾�
     * 
     * ���դȤ���ɾ������񼰤ϰʲ�
     * - YYYY-MM-DD
     * - YYYY/MM/DD
     * - YYYY.MM.DD
     * - YYYYǯMM��DD��
     * - YY-MM-DD
     * - YY/MM/DD
     * - YY.MM.DD
     * - YYǯMM��DD��
     * - YYYYMMDD
     * - YYMMDD
     * 
     * <del>ǯ���ͤ�100̤���ʤ��̤η夬��»��Ƚ�Ǥ�������</del>
     * - <del> 1��49 => +2000</del>
     * - <del>50��99 => +1900</del>
     * - ���ڤȤ��������ΤʤΤǤ��ν��������
     * 
     * ������$params�פ��ͤ�ư�������
     * - ���Ƥ���ǯ�κǾ��͡ʥǥե���Ȥϡ�1900ǯ�ס�
     * - ���Ƥ���ǯ�κ����͡ʥǥե���Ȥϡ�2038ǯ�ס�
     * 
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        $min = 1900;
        $max = 2038;
        if (($param = array_shift($params)) !== null) {
            $min = (int)$param;
        }
        if (($param = array_shift($params)) !== null) {
            $max = (int)$param;
        }

        // ���վ����ǯ������ʬ��
        preg_match('/^' . VALIDATE_DATE_FORMAT . '$/iD', $attribute, $match);
        $date = array();
        for ($i = 1, $n = count($match); $i < $n; ++ $i) {
            if ($match[$i] === '') {
                continue;
            }
            $date[] = $match[$i];
        }
        $year  = (int)array_shift($date);
        $month = (int)array_shift($date);
        $day   = (int)array_shift($date);

        // ǯ�����䴰���������
        // if (($year > 0) && ($year < 100)) {
        //      $year += (($year > 50) ? 1900 : 2000);
        // }

        return 
            (checkdate($month, $day, $year) && ($year >= $min) && ($year <= $max));
    }
}
