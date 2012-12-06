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
 * ビュー
 * 
 * @package  tima
 * @version  SVN: $Id: View.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class View
{

    /**
     * テンプレート・エンジン
     * 
     * @var    string
     * @access protected
     */
    var $engine = null;

    /**
     * 内部の文字エンコーディング
     * 
     * @var    string
     * @access protected
     */
    var $internalEncoding = '';

    /**
     * 出力の文字エンコーディング
     * 
     * @var    string
     * @access protected
     */
    var $contentsEncoding = '';

    /**
     * コンストラクタ
     * 
     * @param  void
     * @access public
     */
    function View($internal_encoding, $contents_encoding, $option)
    {
        $this->setInternalEncoding($internal_encoding);
        $this->setContentsEncoding($contents_encoding);
        $this->initialize($option);
    }

    /**
     * ビューを初期化（スーパークラスでは空）
     * 継承クラスで処理をオーバーライド
     * 
     * @param  array $options
     * @return void
     * @access public
     * @abstract
     */
    function initialize($option = array()) {}

    /**
     * テンプレートを評価（スーパークラスでは空）
     * 継承クラスで処理をオーバーライド
     * 
     * @param  string $template
     * @param  array  $data_model
     * @return string
     * @access public
     */
    function render($template, $data_model)
    {
        return null;
    }

    /**
     * テンプレート・エンジンにオブジェクトを登録
     * 
     * @param  string $varkey
     * @param  object $object
     * @return void
     * @access public
     * @abstract
     */
    function registerObject($varkey, &$object) {}

    /**
     * テンプレートが存在するかを検証（スーパークラスでは空）
     * 継承クラスで処理をオーバーライド
     * 
     * @param  string $template
     * @return boolean
     * @access public
     * @abstract
     */
    function isTemplateExists($template)
    {
        return false;
    }

    /**
     * 内部の文字エンコーディングを登録
     * 
     * @param  string $encoding
     * @return void
     * @access public
     */
    function setInternalEncoding($encoding)
    {
        $this->internalEncoding = $encoding;
    }

    /**
     * 出力の文字エンコーディングを登録
     * 
     * @param  string $encoding
     * @return void
     * @access public
     */
    function setContentsEncoding($encoding)
    {
        $this->contentsEncoding = $encoding;
    }

    /**
     * テンプレート・エンジンを返却
     * 
     * @param  void
     * @return object|null
     * @access public
     */
    function &getEngine()
    {
        return $this->engine;
    }
}
