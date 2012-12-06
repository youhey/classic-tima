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
 * ��̾����
 * 
 * @package    tima
 * @subpackage tima_Connector
 * @version    SVN: $Id: Name.class.php 38 2007-10-16 06:43:01Z do_ikare $
 */
class Connector_Name extends Connector_AbstractConnector
{

    /**
     * ��̾��Ϣ��������礷��ʸ������ֵ�
     * 
     * ������$params�פ��ͤ�ư�������
     * - ʸ����ν񼰡�sprintf()�ؿ��Υե����ޥåȡ�
     * - �ͤ��ʤ����Ρֻ��פΥǥե�����͡ʻ��꤬�ʤ�����䴰���ʤ�)
     * - �ͤ��ʤ����Ρ�ʬ�פΥǥե�����͡ʻ��꤬�ʤ�����䴰���ʤ�)
     * - �ͤ��ʤ����Ρ��áפΥǥե�����͡ʻ��꤬�ʤ�����䴰���ʤ�)
     * 
     * @param  array      $attribute
     * @param  array|null $params
     * @return string
     * @access protected
     */
    function doFunction($attribute, $params)
    {
        $format = '%1$s��%2$s';
        if (($param = array_shift($params)) !== null) {
            $format = $param;
        }

        $familyname = isset($attribute['family']) ? $attribute['family'] : '';
        $firstname  = isset($attribute['first'])  ? $attribute['first']  : '';

        $fullname   = '';
        if (($familyname !== '') && ($firstname !== '')) {
            $fullname = sprintf($format, $familyname, $firstname);
        }

        return $fullname;
    }
}
