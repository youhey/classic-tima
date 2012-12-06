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
 * ͹���ֹ��ʸ����ˤ�ʬ�䤷�����������
 * 
 * @package    tima
 * @subpackage tima_Arranger
 * @version    SVN: $Id: Postcode.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Arranger_Postcode extends Arranger_AbstractArranger
{

    /**
     * ͹���ֹ��ʸ�����ʬ�䤷��������ֵ�
     * 
     * ������$params�פ��ͤ�ư�������
     * - ��3��Υ���ǥå����ʻ��꤬�ʤ���С�head�ס�
     * - ��4��Υ���ǥå����ʻ��꤬�ʤ���С�foot�ס�
     * - ʬ�䤹��ʸ����ʻ��꤬�ʤ���С�-�ס�
     * 
     * @param  string     $attribute
     * @param  array|null $params
     * @return array
     * @access protected 
     */
    function doFunction($attribute, $params)
    {
        $head_key  = 'head';
        $foot_key  = 'foot';
        $separator = '-';
        if (($param  = array_shift($params)) !== null) {
            $head_key = (string) $param;
        }
        if (($param  = array_shift($params)) !== null) {
            $foot_key = (string) $param;
        }
        if (($param  = array_shift($params)) !== null) {
            $separator = (string) $param;
        }

        $matches = mb_split($separator, $attribute, 2);

        return 
            array(
                    $head_key => trim((string)array_shift($matches)), 
                    $foot_key => trim((string)array_shift($matches)), 
                );
    }
}
