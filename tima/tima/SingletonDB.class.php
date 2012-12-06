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
 * Singleton DB
 * 
 * @package  tima
 * @version  SVN: $Id: SingletonDB.class.php 37 2007-10-12 06:51:54Z do_ikare $
 */
class SingletonDB
{

    /**
     * DB
     * 
     * @var    DB
     * @access private
     */
    var $_db = null;

    /**
     * コンストラクタ
     * 
     * @param  string $dsn
     * @param  array  $options
     * @return void
     * @access protected
     */
    function SingletonDB($dsn, $options)
    {
        /* @use DB */
        require_once 'DB.php';

        $this->_db =& DB::connect($dsn, $options);

        if (PEAR::isError($this->_db)) {
            header('HTTP/1.1 500 Internal Server Error');
            trigger_error(
                'Unable to connect the DB: ' . $front->db->getMessage(), 
                E_USER_ERROR);
            exit;
        }

        $this->_db->setFetchMode(DB_FETCHMODE_ASSOC);
    }

    /**
     * Singletonメソッド
     * 
     * @param  mixed $dsn
     * @return DB
     * @access public
     */
    function &getInstance($dsn = null, $options = array())
    {
        static $_self;

        if ($dsn !== null) $_self = new SingletonDB($dsn, $options);

        $instance = null;
        if (isset($_self)) $instance = &$_self->getDB();

        return $instance;
    }

    /**
     * DBを返却
     * 
     * @param  void
     * @return DB
     * @access protected
     */
    function &getDB()
    {
        return $this->_db;
    }
}
