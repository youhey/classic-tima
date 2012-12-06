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
 * 携帯電話からのアクセスを識別するためのユーザ・エージェント判定クラス
 * 
 * - PEAR::Net_UserAgent_Mobileに依存
 *  - ユーザ・エージェントの判定に使用
 *  - 同クラスが使用できなければ判定処理を省略
 *   - エラーなど発生させずに携帯の判定を行わないだけ
 *   - 同クラスを使用しない判定結果は常に非携帯環境（≒PC環境）
 * 
 * @package  tima
 * @version  SVN: $Id: UserAgent.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class UserAgent
{

    /**
     * 端末名（PC、DoCoMo、SoftBank、Vodafone、etc...）
     * 
     * @var    string
     * @access public
     */
    var $name = 'unknow';

    /**
     * 端末モデルの名前（P502、J-DN02、etc...）
     * 
     * @var    string
     * @access public
     */
    var $model = 'unknow';

    /**
     * ユーザ・エージェント処理クラス
     * 
     * @var    integer
     * @access private
     */
    var $_ua = UA_MOBILE_CARRIER_UNKNOW;

    /**
     * コンストラクタ
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
     * ユーザ・エージェントが「日本の携帯電話」のものであるかを検証
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
     * ユーザ・エージェントが「DoCoMo」のものであるかを検証
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
     * ユーザ・エージェントが「SoftBank」のものであるかを検証
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
     * ユーザ・エージェントが「EZweb」のものであるかを検証
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
     * 端末名を返却
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
     * 端末モデルの名前を返却
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
