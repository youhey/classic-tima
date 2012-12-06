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
 * クラスを動的にロードする
 * 
 * - クラス名から定義状況を確認-
 *  - クラスが未ロードであればファイルをインクルード
 * - クラスはパッケージ単位で親子関係を考慮
 *  - 親子関係の構成は「PEAR」的な形式を想定
 *   - PEAR::DB => DB_Pgsql = DB/Pgsql.php
 *   - PEAR::Net_UserAgent_Mobile => Net_UserAgent_Mobile = Net/UserAgent/Mobile.php
 * <code>
 * $loader = new ClassLoader('DB', 'php');
 * 
 *  // 成功すれば、文字列 'DB_Pgsql' を返却
 * $classname = $loader->load('Pgsql');
 * if ($classname === '') {
 *     die('error');
 * }
 * $pgsql = new $classname();
 * </code>
 * 
 * @package  tima
 * @version  SVN: $Id: ClassLoader.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class ClassLoader
{

    /**
     * クラス構造のベースになる系譜の名称
     * - クラスはパッケージ毎にディレクトリ分け
     * - サブ・パッケージは親クラス名を前頭句にもつ
     *  - PEAR::DB_Pgsql => DB_Pgsql
     * 
     * @var    array
     * @access private
     */
    var $_classParents = array();

    /**
     * ファイル拡張子
     * 
     * @var    string
     * @access private
     */
    var $_fileExt = '';

    /**
     * インクルード・パスの限定範囲
     * 
     * @var    string|null
     * @access private
     */
    var $_includePath = null;

    /**
     * コンストラクタ
     * 
     * @param  string      $parent   クラス構造の系譜名称
     * @param  string      $file_ext クラスファイルの拡張子名
     * @param  string|null $fix_path インクルード・パスの限定範囲
     * @access public
     */
    function ClassLoader($parents = null, $file_ext = '.class.php', $fix_path = null)
    {
        if ($parents !== null) {
            $this->setParents($parents);
        }
        $this->setFileExt($file_ext);
        $this->setIncludePath($fix_path);
    }

    /**
     * クラスを読み込む
     * 
     * - クラスが使用可能であればクラスの名前を返却
     *  - クラスの名前から定義状況を確認
     *  - 親子関係を動的に評価するクラスはフルネームを解決
     * - クラスが使用できなければ空文字列を返却
     * 
     * @param  string $class インクルードするクラスの名前
     * @return string クラス名（クラスが未定義なら空文字列）
     * @access public 
     */
    function load($class)
    {
        $classname = '';

        if (!is_string($class) || ($class === '')) {
            return '';
        }

        // クラス名の省略部分を補完
        $parents = '';
        if (count($this->_classParents) > 0) {
            $parents = implode('_', $this->_classParents) . '_';
        }
        $classname = $parents . $class;

        // 未定義ならロード
        if (!class_exists($classname)) {
            // インクルードの範囲を限定
            $this->_fixPath($this->_includePath);

            $directory = '';
            if (count($this->_classParents) > 0) {
                $directory = implode(DS, $this->_classParents) . DS;
            }
            $file_path = $directory . $class . $this->_fileExt;

            if (!$this->_existsFile($file_path)) {
                $classname = '';
            } else {
                $resultant = (include_once $file_path);
                if (($resultant === false) || !class_exists($classname)) {
                    $classname = '';
                }
            }

            // インクルードの範囲を元に戻す
            $this->_fixPath();
        }

        return $classname;
    }

    /**
     * 指定されたクラスの名前からファイルが存在するかを検査
     * 
     * @param  string  $class クラス名
     * @return boolean
     * @access public 
     */
    function isReadable($class)
    {
        $this->_fixPath($this->_includePath);

        $directory = '';
        if (count($this->_classParents) > 0) {
            $directory = implode(DS, $this->_classParents) . DS;
        }
        $resultant = $this->_existsFile($directory . $class . $this->_fileExt);

        $this->_fixPath();

        return $resultant;
    }

    /**
     * クラス構造のベースとなる系譜を登録
     * 
     * - setParents( String parentName [, String parentName [, ... ] ] );
     * - クラス名やディレクトリ構造はPEAR形式で命名されている前提
     *  - PEAR::DB_Pgsql => DB/Pgsql.php => setParents('DB')
     *  - PEAR::Net_UserAgent_Mobile 
     *        => Net/UserAgent/Mobile.php
     *        => setParents('Net', 'UserAgent')
     * 
     * @param  string  ベース・クラスの名前
     * @return void
     * @access public
     */
    function setParents()
    {
        $class_names = array();
        if (func_num_args() > 0) {
            foreach(func_get_args() as $arg) {
                if ($arg === '') {
                    continue;
                }
                $class_names[] = $arg;
            }
        }
        $this->_classParents = $class_names;
    }

    /**
     * インクルードするファイルの拡張子を登録
     * 
     * @param  string $file_ext ファイル拡張子
     * @return void
     * @access public
     */
    function setFileExt($file_ext)
    {
        $this->_fileExt = (string)$file_ext;
    }

    /**
     * インクルードする限定範囲を登録
     * 
     * @param  string|null $fix_path 限定する範囲
     * @return void
     * @access public
     */
    function setIncludePath($fix_path)
    {
        $this->_includePath = (is_null($fix_path) ? null : (string)$fix_path);
    }

    /**
     * ファイルが存在するかを検証
     * - PHPの環境設定「include_path」のパスも検索
     * 
     * @param  string  $file_path
     * @return boolean
     * @access private
     */
    function _existsFile($file_path)
    {
        if ($this->_isAbsolutePath($file_path)) {
            return file_exists($file_path);
        }

        foreach (explode(PATH_SEPARATOR, ini_get('include_path')) as $include_path) {
            if (file_exists($include_path . DS . $file_path)) {
                return true;
            }
        }
        return false;
    }

    /**
     * ファイルのパスが絶対パスかを検証
     * 
     * @param  string  $file_path
     * @return boolean
     * @access private
     */
    function _isAbsolutePath($file_path)
    {
        if (defined('OS_WINDOWS') && OS_WINDOWS) {
            if (!preg_match('/^[a-z]:/i', $file_path)) {
                return false;
            }
            return ($file_path{2} === DS);
        }

        return ($file_path{0} === DS);
    }

    /**
     * PHPの動作環境から「include_path」を変更
     * - 一番最初の実行前にオリジナルの設定値を記憶
     * - 引数が省略されればオリジナルに復元
     * 
     * @param  string|null $reference
     * @return void
     * @access private
     */
    function _fixPath($reference = null)
    {
        static $original;
        if (!isset($original)) {
            $original = ini_get('include_path');
        }

        ini_set('include_path', isset($reference) ? $reference : $original);
    }

}
