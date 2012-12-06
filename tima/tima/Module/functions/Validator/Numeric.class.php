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

/* ����ʸ���������ɽ�� */
define('VALIDATE_NUMERIC', "(?:[+-]?(?:(?:\d{1,3},)+\d{3}|\d+)(?:\.\d*)?)");

/**
 * ʸ���󤬿��ͤȤ���ɾ���Ǥ��뤫�򸡾�
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: Numeric.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Validator_Numeric extends Validator_AbstractValidator
{

    /**
     * ʸ���󤬿��ͤȤ���ɾ���Ǥ��뤫�򸡾�
     * 
     * PHP�����ͤȤ���ɾ���Ǥ����ͤ˽��
     * - �������������
     * - �񼰲����줿������1,234,567��
     * - ��ư�����������������
     * - 8�ʿ���0123��
     * - 16�ʿ���0xFF��
     * - �ؿ����Ŀ�����+0123.45e6��
     *
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        if (preg_match('/^' . VALIDATE_NUMERIC . '$/', $attribute)) {
            $attribute = str_replace(',', '', $attribute);
        }

        return 
            is_numeric($attribute);
    }
}
