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
 * ������ɾ����ǽ��ʸ������������ս񼰡�YYYY-MM-DD�פ��Ѵ�
 * 
 * @package    tima
 * @subpackage tima_Converter
 * @version    SVN: $Id: Date.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Converter_Date extends Converter_AbstractConverter
{

    /**
     * ������ɾ����ǽ��ʸ������������ս񼰡�YYYY-MM-DD�פ��Ѵ�
     * 
     * ���դȤ��Ƥ�ɾ������ʸ�������
     * - YYYY-MM-DD => 2007-01-05
     * - YY-MM-DD => 07-01-05
     * - YYYY-M-D => 2007-1-5
     * - YY-M-D => 07-1-5
     * - YYYY/MM/DD => 2007/01/05
     * - YY/MM/DD => 07/01/05
     * - YYYY/M/D => 2007/1/5
     * - YY/M/D => 07/1/5
     * - YYYY.MM.DD => 2007.01.05
     * - YY.MM.DD => 07.01.05
     * - YYYY.M.D => 2007.1.5
     * - YY.M.D => 07.1.5
     * - YYYYMMDD => 20070105
     * 
     * ʸ���������Ȥ���ɾ���Ǥ��ʤ���и���ǡ�1970-01-01�פ��ֵ�
     * ǯ������ά��������ξ��ˤϰʲ��Τ褦��ɾ��
     * - ǯ���ͤ�50�ʲ� => 2000ǯ���ɾ�����ơ�2000�פ�û�
     * - ǯ���ͤ�50�ʾ� => 1900ǯ���ɾ�����ơ�1900�פ�û�
     * 
     * @param  string $attribute
     * @return string
     * @access protected
     */
    function doFunction($attribute)
    {
        $hyphen_regex  = '!^(\d{2,4})-(\d{1,2})-(\d{1,2})$!';
        $slash_regex   = '!^(\d{2,4})/(\d{1,2})/(\d{1,2})$!';
        $dot_regex     = '!^(\d{2,4})\.(\d{1,2})\.(\d{1,2})$!';
        $connect_regex = '!^(\d{4})(\d{2})(\d{2})$!';

        switch (true) {
        case (bool) preg_match($hyphen_regex, $attribute, $matches) : 
        case (bool) preg_match($slash_regex, $attribute, $matches) : 
        case (bool) preg_match($dot_regex, $attribute, $matches) : 
        case (bool) preg_match($connect_regex, $attribute, $matches) : 
            $year  = (int) $matches[1];
            $month = (int) $matches[2];
            $day   = (int) $matches[3];
            break;
        default : 
            return '1970-01-01';
        }

        if ($year < 100) {
            $year += (($year < 50) ? 2000 : 1900);
        }

        return 
            sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
