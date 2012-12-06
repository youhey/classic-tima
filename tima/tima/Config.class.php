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
 * 外部ファイルの定義値を設定値として使うためのクラス
 * 
 * - PHPファイルのインクルード結果を使用
 *  - インクルードしたPHPファイルが最後にreturnする値
 *  - 外部設定に用いるPHPファイルは末尾で配列を返却する
 * - ファイル名は「(設定種別)-ini.php」
 *  - section：Front => Front-ini.php
 *  - PHP4／PHP5でのクラス名の大文字・小文字を考慮して全て小文字
 *   - section : FooBar => foobar-ini.php
 * - 設定ファイルではreturn文で配列を返却することしかしない
 *  - 設定ファイルに副作用がある／戻り値が想定外
 *        => 意図しない挙動／致命的なエラーの可能性あり
 *  - 処理を把握できなくなるのでそれらは厳禁
 * 
 * @package  tima
 * @version  SVN: $Id: Config.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Config
{

    /**
     * 設定内容
     * 
     * @var    array
     * @access private
     */
    var $_parameters = array();

    /**
     * 設定ファイルの配置ディレクトリ
     * 
     * @var    array
     * @access private
     */
    var $_etcDir = array();

    /**
     * コンストラクタ
     * 
     * @param  void
     * @access public
     */
    function Config() {}

    /**
     * 設定値を返却
     *
     * @param  string $configkey
     * @return mixed
     * @access public
     */
    function get($configkey, $namespace = 'anonymous')
    {
        if (!is_string($configkey) || !is_string($namespace)) {
            return null;
        }
        if (!isset($this->_parameters[$namespace][$configkey])) {
            return null;
        }

        return $this->_parameters[$namespace][$configkey];
    }

    /**
     * 名前空間に存在する全ての設定を返却
     * 
     * @param  string  $name      
     * @param  string  $namespace 名前空間
     * @return array
     * @access public
     * @todo 実装する
     */
    function getByNamespace($namespace)
    {
        if (!is_string($namespace) || ($namespace === '')) {
            return array();
        }
        if (!isset($this->_parameters[$namespace])) {
            return array();
        }

        return $this->_parameters[$namespace];
    }

    /**
     * 設定値に新しい値を上書き
     *
     * @param  string  $configkey 設定キー
     * @param  string  $varvalue  設定値
     * @param  string  $namespace 名前空間
     * @return void
     * @access public
     */
    function set($configkey, $varvalue, $namespace = 'anonymous')
    {
        if (!is_string($configkey) || !is_string($namespace) || 
            ($configkey === '') || ($namespace === '')) {
            trigger_error('Wrong parameter for setting configuration.');
            return;
        }

        if (!isset($this->_parameters[$namespace])) {
            $this->_parameters[$namespace] = array();
        }

        $this->_parameters[$namespace][$configkey] = $varvalue;
    }

    /**
     * コンストラクタ
     * 
     * @param  string $added_etc_dir
     * @return void
     * @access public
     */
    function setEtcDir($added_etc_dir)
    {
        $this->_etcDir[] = $added_etc_dir;
    }

    /**
     * ファイルで定義された設定を読み込む
     *
     * @param  string  $section 設定名
     * @return boolean 
     * @access public
     */
    function readConfig($section)
    {
        $file_name = $this->_getFileName($section);

        // 設定ファイルが存在しなければ偽を返却して終了
        if (is_null($file_name)) {
            return false;
        }

        $config = @include $file_name;

        // 設定ファイルのインクルード結果を受け取れなければ異常終了
        if (!isset($config) || !is_array($config)) {
            header('HTTP/1.1 500 Internal Server Error');
            trigger_error(
                "Unable to read the configuration '${section}-ini.php'.", 
                E_USER_ERROR);
            exit;
        }

        // インクルード結果を保持する設定に反映
        foreach ($config as $varvalue) {
            // キーに有効な文字列がなければ無視
            if (!isset($varvalue['key']) || 
                !is_string($varvalue['key']) || ($varvalue['key'] === '')) {
                trigger_error(
                    "Wrong definition for setting configuration in '${file_name}'", 
                    E_USER_WARNING);
                continue;
            }
            // 設定値「NULL」は設定に反映されない（無視）
            if (!isset($varvalue['value'])) {
                continue;
            }

            $configvar = array($varvalue['key'], $varvalue['value']);
            if (isset($varvalue['space'])) {
                $configvar[] = $varvalue['space'];
            }
            call_user_func_array(array(&$this, 'set'), $configvar);
        }

        return true;
    }

    /**
     * 設定名に対応したファイル名を返却
     * 対応する設定ファイルがなければヌルを返却
     * 
     * @param  string $section
     * @return string|null
     * @access private
     */
    function _getFileName($section)
    {
        $config_file = null;

        foreach ($this->_etcDir as $dir) {
            $file = $dir . DS . strtolower($section) . '-ini.php';
            if (!is_file($file) && !is_readable($file)) {
                continue;
            }
            $config_file = $file;
            break;
        }

        return $config_file;
    }
}
