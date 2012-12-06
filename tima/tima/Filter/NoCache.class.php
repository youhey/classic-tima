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
 * キャッシュ無効のヘッダ情報を出力
 * 
 * @package    tima
 * @subpackage tima_Filter
 * @version    SVN: $Id$
 */
class Filter_NoCache
{

    /**
     * キャッシュ無効のヘッダ情報を出力
     *
     * @params Front $front
     * @return void
     * @access public
     */
    function before(&$front)
    {
        $response = &$front->getResponse();

        $response->setHeader('Expires', '-1');
        $response->setHeader('Pragma', 'no-cache');
        $response->setCacheControl('private');
        $response->setCacheControl('max-age', '0');
        $response->setCacheControl('no-store');
        $response->setCacheControl('no-cache');
        $response->setCacheControl('must-revalidate');
        $response->setCacheControl('pre-check', '0');
        $response->setCacheControl('post-check', '0');

        $logger = &$front->getLogger();
        $logger->trace(__CLASS__ . "の前処理を実行");
    }
}
