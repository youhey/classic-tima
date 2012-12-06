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
 * Ⱦ�Ѥ�ʸ�������Ѥ��Ѵ�
 * 
 * @package    tima
 * @subpackage tima_Converter
 * @version    SVN: $Id: FullAll.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Converter_FullAll extends Converter_AbstractConverter
{

    /**
     * Ⱦ�Ѥ�ʸ�������Ѥ��Ѵ�
     * 
     * ���Ѥ���mb_convert_kana()�ؿ��Υ��ץ����
     * - A��Ⱦ�ѱѿ��� => ���ѱѿ���
     *      ����"�ס�'�ס�\�ס�~�פ�����ʲ����ϰϤ����Ѥ���Ⱦ�Ѥ��Ѵ�
     *      0020:   !   # $ % &   ( ) * + , - . /
     *      0030: 0 1 2 3 4 5 6 7 8 9 : ; < = > ?
     *      0040: @ A B C D E F G H I J K L M N O
     *      0050: P Q R S T U V W X Y Z [   ] ^ _
     *      0060: ` a b c d e f g h i j k l m n o
     *      0070: p q r s t u v w x y z { | }
     * - S��Ⱦ�ѥ��ڡ��� => ���ѥ��ڡ���
     * - K��Ⱦ�ѥ������� => ���ѥ�������
     * - V�������դ���ʸ�����ʸ����
     *
     * @param  string $attribute
     * @return string
     * @access protected
     */
    function doFunction($attribute)
    {
        return 
            mb_convert_kana(
                $attribute, 
                'ASKV', 
                $this->module->getInternalEncoding());
    }
}
