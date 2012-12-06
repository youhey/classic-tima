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
 * セッションを開始・終了するためのフィルタ
 * 
 * @package    tima
 * @subpackage tima_Filter
 * @version    SVN: $Id: SessionGateway.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Filter_SessionGateway
{

    /**
     * セッションの開始／終了処理
     * 
     * @params Front $front
     * @return void
     * @access public
     */
    function before(&$front)
    {
        $ua = &$front->getUserAgent();

        if ($ua->isMobile()) {
            ini_set('session.use_cookies', false);
            ini_set('session.use_only_cookies', false);
        } else {
            $config     = &$front->getConfig();
            $use_cookie = $config->get('use_cookie', 'session');

            if ($use_cookie === true) {
                ini_set('session.use_cookies', true);
                ini_set('session.use_only_cookies', true);

                $cookie_domain = $config->get('cookie_domain', 'session');
                $cookie_secure = $config->get('cookie_secure', 'session');
                $cookie_path   = $config->get('cookie_path', 'session');

                if ($cookie_domain !== null) {
                    ini_set('cookie_domain', $cookie_domain);
                }
                if ($cookie_secure !== null) {
                    ini_set('session.cookie_secure', $cookie_secure);
                }
                if ($cookie_path !== null) {
                    ini_set('session.cookie_path', $cookie_path);
                }
            } else {
                ini_set('session.use_cookies', false);
                ini_set('session.use_only_cookies', false);
            }
        }

        $session = &$front->getSession();
        $logger  = &$front->getLogger();

        $session->start();

        $logger->debug('セッションを開始');
        $logger->trace(__CLASS__ . "の前処理を実行");
    }

    /**
     * アプリケーションの終了状況を記録
     *
     * @param  Front $front コントローラ・クラス
     * @return void
     * @access public
     */
    function after(&$front)
    {
        $logger = &$front->getLogger();
        $logger->trace(__CLASS__ . "の後処理を実行");

        $session = &$front->getSession();

        if ($session->isStarted()) {
            $session->stop();
            $logger->debug('セッションを停止');
        }
    }
}
