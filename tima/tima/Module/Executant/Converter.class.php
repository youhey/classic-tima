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

/* @use Module_Executant_AbstractExecutant */
require_once 
    dirname(__FILE__) . DS . 'AbstractExecutant.class.php';

/**
 * ʸ������Ѵ�����⥸�塼��
 * 
 * @package    tima
 * @subpackage tima_Converter
 * @version    SVN: $Id: Converter.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Module_Executant_Converter extends Module_Executant_AbstractExecutant
{

    /**
     * �⥸�塼���̾��
     * 
     * @var    string
     * @access protected
     */
    var $moduleName = 'Converter';

    /**
     * ����С������Ѵ�������̤��ֵ�
     * 
     * @param  string  $converter_name ����С���̾
     * @param  string  $attributes     �Ѵ�������
     * @return string
     * @access public
     * @see    Module_Executant_AbstractExecutant::execute()
     */
    function to($converter_name, $attributes)
    {
        return 
            $this->execute($converter_name, (string)$attributes);
    }

    /**
     * ľ�����Ѵ��������ͤ˱ƶ���Ϳ�������򸡺�
     * 
     * @param  string  $converter_name ����С���̾
     * @return boolean
     * @access public
     * @see    Module_Executant_AbstractExecutant::factory()
     * @see    Converter_AbstractConverter::isConverted()
     */
    function isConverted($converter_name)
    {
        $converted = false;

        $function = &$this->factory($converter_name);
        if (($function !== null) && method_exists($function, 'isConverted')) {
            $converted = $function->isConverted();
        }

        return 
            $converted;
    }
}
