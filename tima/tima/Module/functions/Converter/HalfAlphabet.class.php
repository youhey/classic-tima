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
 * ���ѥ���ե��٥åȤ�ʸ����Ⱦ�ѥ���ե��٥åȤ��Ѵ�
 * 
 * @package    tima
 * @subpackage tima_Converter
 * @version    SVN: $Id: HalfAlphabet.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Converter_HalfAlphabet extends Converter_AbstractConverter
{

    /**
     *  ���ѥ���ե��٥åȤ�ʸ����Ⱦ�ѥ���ե��٥åȤ��Ѵ�
     * 
     * ���Ѥ���mb_convert_kana()�ؿ��Υ��ץ����
     * - r�����ѱѻ� => Ⱦ�ѱѻ�
     * 
     * @param  string $attribute
     * @return string
     * @access protected
     */
    function doFunction($attribute)
    {
        return 
            mb_convert_kana($attribute, 'r', $this->module->getInternalEncoding());
    }
}
