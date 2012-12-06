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

/* ���ʤʥ᡼�륢�ɥ쥹������ɽ�� */
define(
    'VALIDATE_EMAIL_STRICT', 
    '(?:[.0-9a-zA-Z_+-]+@(?:[0-9a-zA-Z-]+\.)+[0-9a-zA-Z]{2,4})');

/* �ˤ䤫�ʥ᡼�륢�ɥ쥹������ɽ�� */
define(
    'VALIDATE_EMAIL_EASY', 
    '(?:[*+!.&#$|\'\\%\/0-9a-zA-Z^_`{}=?~:-]+' . 
    '@(?:[0-9a-zA-Z-]+\.)+[0-9a-zA-Z]{2,4})');

/* �ȥåץ�٥롦�ɥᥤ�����ꤷ�����ʤʥ᡼�륢�ɥ쥹������ɽ�� */
define(
    'VALIDATE_EMAIL_DOMAIN', 
    '(?:[.0-9a-zA-Z_+-]+@(?:[0-9a-zA-Z-]+\.)+)' . VALIDATE_TOP_LV_DOMAIN);

/**
 * ʸ���󤬥᡼�륢�ɥ쥹�Ȥ������������򸡾�
 * 
 * @package    tima
 * @subpackage tima_Validator
 * @version    SVN: $Id: EMail.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Validator_EMail extends Validator_AbstractValidator
{

    /**
     * ʸ���󤬥᡼�륢�ɥ쥹�Ȥ������������򸡾�
     * 
     * ������$params�פ��ͤ�ư�������
     * - ���ڤθ��ʤ���٥�ʻ��꤬�ʤ���С�easy�ס�
     *  - easy => RFC822�˽�򤷤ʤ������Υ᡼�륢�ɥ쥹�����
     *  - strict => RFC822�˽�򤷤��᡼�륢�ɥ쥹�Τߵ���
     *  - domain => �ȥåץ�٥�ɥᥤ��ޤǸ���
     * 
     * @param  string     $attribute
     * @param  array|null $params
     * @return boolean
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        $validate_level = 'easy';
        if (($param = array_shift($params)) !== null) {
            $validate_level = (string)$param;
        }

        switch (strtolower($validate_level)) {
        case 'strict' : 
            $email_regex = VALIDATE_EMAIL_STRICT;
            break;
        case 'domain' : 
            $email_regex = VALIDATE_EMAIL_DOMAIN;
            break;
        case 'easy' : 
            $email_regex = VALIDATE_EMAIL_EASY;
            break;
        default : 
            return false;
        }

        return 
            (bool)preg_match('/^' . $email_regex . '$/', $attribute);
    }
}
