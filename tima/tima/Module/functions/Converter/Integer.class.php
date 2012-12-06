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
 * ʸ����ο����Ȥ���ɾ���Ǥ���ʸ����������������
 * 
 * @package    tima
 * @subpackage tima_Converter
 * @version    SVN: $Id: Integer.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Converter_Integer extends Converter_AbstractConverter
{

    /**
     * ʸ����ο����Ȥ���ɾ���Ǥ���ʸ����������������
     * 
     * �����Ȥ���ɾ���Ǥ���ʸ�������Ƥ��Ѵ�����褦��ߤ�
     * ɾ���Ǥ��ʤ�ʸ��������ơ�0�פ��Ѵ�
     * 
     * �Ѵ���
     * - 123 => 123
     * - 123.9 => 123
     * - -123.9 => -124
     * - 123.5.0 => 0
     * - 0x123 => 291
     * - abc => 0
     * - 123a => 0
     * - a123 => 0
     * - ������ => 123
     * - 1,230 => 1230
     * - 1,23 => 0
     * - +1230 => 1230
     * - -1230 => -1230
     * 
     * ���Ѥ���mb_convert_kana()�ؿ��Υ��ץ����
     * - a�����ѱѿ��� => Ⱦ�ѱѿ���
     *      ����"�ס�'�ס�\�ס�~�פ�����ʲ����ϰϤ����Ѥ���Ⱦ�Ѥ��Ѵ�
     *      0020:   !   # $ % &   ( ) * + , - . /
     *      0030: 0 1 2 3 4 5 6 7 8 9 : ; < = > ?
     *      0040: @ A B C D E F G H I J K L M N O
     *      0050: P Q R S T U V W X Y Z [   ] ^ _
     *      0060: ` a b c d e f g h i j k l m n o
     *      0070: p q r s t u v w x y z { | }
     * - s�����ѥ��ڡ��� => Ⱦ�ѥ��ڡ���
     *
     * @param  string $attribute
     * @return string
     * @access protected
     */
    function doFunction($attribute)
    {
        $number = 
            mb_convert_kana($attribute, 'as', $this->module->getInternalEncoding());
        if (preg_match('/^[\+-]?([0-9]{1,3},)+[0-9]{3}(\.[0-9]*)?$/', $number)) {
            $number = str_replace(',', '', $number);
        }
        if (!is_numeric($number)) {
            $number = 0;
        }

        return 
            (string)floor($number + 0.0);
    }
}
