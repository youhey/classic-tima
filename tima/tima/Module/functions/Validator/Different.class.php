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
 * ʸ��������оݤȰ��פ��ʤ����򸡾�
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: Different.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Validator_Different extends Validator_AbstractValidator
{

    /**
     * ʸ��������оݤȰ��פ��ʤ����򸡾�
     * 
     * ʸ�������Ӥ���ʸ������ʸ������̤�
     * ����оݤ�ʣ���θ��䤬����С������԰��פʤ�п�
     * ����оݤ�����Ǥ���С��԰��פ򸡾ڤǤ��ʤ��Τǵ�
     * 
     * ������$params�פ��ͤ�ư�������
     * - ��Ӥ���ʸ����
     * 
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        $comparativist = array();
        if (is_array($params) && !empty($params)) {
            $comparativist = $params;
        }

        $discord = false;
        foreach ($comparativist as $comparison) {
            if (!is_string($comparison)) {
                continue;
            }
            if (strcmp($attribute, $comparison) === 0) {
                $discord = false;
                break;
            }
            $discord = true;
        }

        return $discord;
    }
}
