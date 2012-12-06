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
 * DBクラスの接続を開始／終了するフィルタ
 * - 接続のリソースはフロント・コントローラの「db」というプロパティに代入
 * 
 * @package    tima
 * @subpackage tima_Filter
 * @version    SVN: $Id: DBConnection.class.php 35 2007-09-28 02:03:08Z do_ikare $
 */
class Filter_DBConnection
{

    /**
     * DBクラスの接続を開始
     *
     * @params Front $front
     * @return void
     * @access public
     */
    function before(&$front)
    {
        $config  = &$front->getConfig();

        $dsn = $config->get('dsn', 'db');
        if ($dsn === null) {
            $db_type = $config->get('dbtype', 'db');
            $db_host = $config->get('host', 'db');
            $db_port = $config->get('port', 'db');
            $db_name = $config->get('dbname', 'db');
            $db_user = $config->get('user', 'db');
            $db_pass = $config->get('password', 'db');

            $dsn = array('phptype' => $db_type);
            if ($db_user !== null) {
                $dsn['username'] = $db_user;
            }
            if ($db_pass !== null) {
                $dsn['password'] = $db_pass;
            }
            if ($db_host !== null) {
                $dsn['hostspec'] = $db_host;
            }
            if ($db_port !== null) {
                $dsn['port'] = $db_port;
            }
            if ($db_name !== null) {
                $dsn['database'] = $db_name;
            }
        }

        $db = &SingletonDB::getInstance($dsn);

        $logger = &$front->getLogger();
        $logger->trace(__CLASS__ . "の前処理を実行");
    }

    /**
     * DBクラスの接続を終了
     *
     * @params Front $front
     * @return void
     * @access public
     */
    function after(&$front)
    {
        $db = &SingletonDB::getInstance();
        if (isset($db->connection)) {
            $db->rollback();
            $db->disconnect();
        }

        $logger = &$front->getLogger();
        $logger->trace(__CLASS__ . "の後処理を実行");
    }
}
