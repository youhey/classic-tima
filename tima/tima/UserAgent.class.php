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

define('UA_MOBILE_CARRIER_UNKNOW',   0);
define('UA_MOBILE_CARRIER_DOCOMO',   1);
define('UA_MOBILE_CARRIER_SOFTBANK', 2);
define('UA_MOBILE_CARRIER_EZ_WEB',   3);

/**
 * �������ä���Υ����������̤��뤿��Υ桼���������������Ƚ�ꥯ�饹
 * 
 * - PEAR::Net_UserAgent_Mobile�˰�¸
 *  - �桼��������������Ȥ�Ƚ��˻���
 *  - Ʊ���饹�����ѤǤ��ʤ����Ƚ��������ά
 *   - ���顼�ʤ�ȯ���������˷��Ӥ�Ƚ���Ԥ�ʤ�����
 *   - Ʊ���饹����Ѥ��ʤ�Ƚ���̤Ͼ������ӴĶ��ʢ�PC�Ķ���
 * 
 * @package  tima
 * @version  SVN: $Id: UserAgent.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class UserAgent
{

    /**
     * ü��̾��PC��DoCoMo��SoftBank��Vodafone��etc...��
     * 
     * @var    string
     * @access public
     */
    var $name = 'unknow';

    /**
     * ü����ǥ��̾����P502��J-DN02��etc...��
     * 
     * @var    string
     * @access public
     */
    var $model = 'unknow';

    /**
     * �桼��������������Ƚ������饹
     * 
     * @var    integer
     * @access private
     */
    var $_ua = UA_MOBILE_CARRIER_UNKNOW;

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  void
     * @access public
     */
    function UserAgent()
    {
        if (class_exists('Net_UserAgent_Mobile')) {
            $useragent   = &Net_UserAgent_Mobile::factory();
            if (Net_UserAgent_Mobile::isError($useragent)) {
                trigger_error($useragent->getMessage(), E_USER_WARNING);
            } else {
                $this->name = $useragent->getName();
                if (method_exists($useragent, 'getModel')) {
                    $this->model = $useragent->getModel();
                }
                switch (true) {
                case $useragent->isDoCoMo() : 
                    $this->_ua = UA_MOBILE_CARRIER_DOCOMO;
                    break;
                case $useragent->isVodafone() : 
                case $useragent->isJPhone() : 
                    $this->_ua = UA_MOBILE_CARRIER_SOFTBANK;
                    break;
                case $useragent->isEZweb() : 
                case $useragent->isTUKa() : 
                    $this->_ua = UA_MOBILE_CARRIER_EZ_WEB;
                    break;
                }
            }
        }
    }

    /**
     * �桼��������������Ȥ������ܤη������áפΤ�ΤǤ��뤫�򸡾�
     *
     * @param  void
     * @return boolean
     * @access public
     */
    function isMobile()
    {
        return ($this->_ua !== UA_MOBILE_CARRIER_UNKNOW);
    }

    /**
     * �桼��������������Ȥ���DoCoMo�פΤ�ΤǤ��뤫�򸡾�
     *
     * @param  void
     * @return boolean
     * @access public
     */
    function isDoCoMo()
    {
        return ($this->_ua === UA_MOBILE_CARRIER_DOCOMO);
    }

    /**
     * �桼��������������Ȥ���SoftBank�פΤ�ΤǤ��뤫�򸡾�
     *
     * @param  void
     * @return boolean
     * @access public
     */
    function isSoftBank()
    {
        return ($this->_ua === UA_MOBILE_CARRIER_SOFTBANK);
    }

    /**
     * �桼��������������Ȥ���EZweb�פΤ�ΤǤ��뤫�򸡾�
     *
     * @param  void
     * @param  void
     * @return boolean
     * @access public
     * @access public
     */
    function isEZweb()
    {
        return ($this->_ua === UA_MOBILE_CARRIER_EZ_WEB);
    }

    /**
     * ü��̾���ֵ�
     *
     * @param  void
     * @return string
     * @access public
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * ü����ǥ��̾�����ֵ�
     *
     * @param  void
     * @return string
     * @access public
     */
    function getModel()
    {
        return $this->model;
    }
}
