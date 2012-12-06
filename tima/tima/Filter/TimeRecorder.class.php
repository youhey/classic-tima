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
 * デバックのための時間測定フィルタ
 * 
 * @package    tima
 * @subpackage tima_Filter
 * @version    SVN: $Id: TimeRecorder.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Filter_TimeRecorder
{

    /**
     * 計測開始時間
     *
     * @var    float
     * @access private
     */
    var $_beginingMTime;

    /**
     * 計測開始
     *
     * @params Front $front
     * @return void
     * @access public
     */
    function before(&$front)
    {
        $this->_beginingMTime = $this->_getMicroTime();

        $logger = &$front->getLogger();
        $logger->trace(__CLASS__ . "の前処理を実行");
    }

    /**
     * 計測終了
     *
     * @params Front $front
     * @return void
     * @access public
     */
    function after(&$front)
    {
        $mtime = (string)$this->_stopWatch();

        $logger = &$front->getLogger();
        $logger->trace(__CLASS__ . "の後処理を実行");

        $logger->debug('[execute time] ' . $mtime . ' sec');
    }

    /**
     * 開始時間からの差分マイクロ秒を返却
     *
     * @params void
     * @return float
     * @access private
     */
    function _stopWatch()
    {
        return round(($this->_getMicroTime() - $this->_beginingMTime), 4);
    }

    /**
     * マイクロ秒までのタイムスタンプを返却
     *
     * @params void
     * @return float
     * @access private
     */
    function _getMicroTime()
    {
        list($usec, $sec) = explode(' ', microtime());

        return ((float) $usec + (float) $sec);
    }
}
