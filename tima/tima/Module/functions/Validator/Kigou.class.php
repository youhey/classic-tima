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

/* EUC-JP���󥳡��ɤ����ѵ���ʸ����Υޥ���Х�������ɽ�� */
define('VALIDATE_MB_FULL_SYMBOL_EUC', "(?:[\xA1\xA2-\xA1\xFE]|[\xA2\xA1-\xA2\xFE])");

/**
 * ʸ�������ѵ���Τߤǹ�������Ƥ��뤫�򸡾�
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: Kigou.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Validator_Kigou extends Validator_AbstractValidator
{

    /**
     * ʸ�������ѵ���Τߤǹ�������Ƥ��뤫�򸡾�
     * 
     * ʣ���Ԥ��б��ʥѥ����󽤾��Ҥ�"m"�ǤϤʤ����Ժ����
     * 
     * ������$params�פ��ͤ�ư�������
     * - ʸ�����ʸ�����󥳡��ǥ��󥰡�EUC-JP�ʳ��ξ���ɬ�ס�
     * 
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     * @see    Utility::isMatch()
     */
    function doFunction($attribute, $params)
    {
        $mb_charset = 'EUC-JP';
        if (($param = array_shift($params)) !== null) {
            $mb_charset = (string)$param;
        }
        if ($mb_charset !== 'EUC-JP') {
            $attribute = mb_convert_encoding($attribute, 'EUC-JP', $mb_charset);
        }

        return 
            Utility::isMatch(
                '^' . VALIDATE_MB_FULL_SYMBOL_EUC . '+$', 
                str_replace(array("\r\n", "\n", "\r"), '', $attribute), 
                'EUC-JP');
    }
}
