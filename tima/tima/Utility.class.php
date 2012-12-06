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
 * ユーティリティー・クラス
 * 
 * @package  tima
 * @version  SVN: $Id: Utility.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Utility
{

    /**
     * [アンダーバー記法]の文字列を[パスカル記法]に変換
     * - [パスカル記法] 複合語の先頭を、大文字で書き始める
     *  - CamelCase
     * 
     * @param  string $name
     * @return string
     * @access public
     */
    function camelize($name)
    {
        return 
            str_replace(' ', '', 
                ucwords(
                    preg_replace('[^a-z0-9 ]', '', 
                         str_replace('_', ' ', strtolower($name)))));
    }

    /**
     * [キャメル記法][パスカル記法]の文字列を[アンダーバー記法]の文字列にして返却
     * - [キャメル記法] 複合語の先頭を、小文字で書き始める
     *  - camelCase
     * - (xxx2xxx|xxx4xxx)は単語の省略（to, for）として使っている場合あり
     *  - 誤作動の可能性はあるが単語間の（2|4）は単語として扱う
     *  - Select4Update    >> select_4_update
     *  - convert2Katakana >> convert_2_katakana
     *  - FinalFantasy2    >> final_fantasy2
     *  - P902iS           >> p902i_s
     * 
     * @param  string $word
     * @return string
     * @access private
     */
    function decamelize($word)
    {
        $replace_pattern = array(
                '/([A-Z]+)([A-Z][a-z])/' => '\\1_\\2',
                '/([a-z])(2|4)([A-Z])/'  => '\\1_\\2_\\3', 
                '/([a-z0-9])([A-Z])/'    => '\\1_\\2', 
            );

        return 
            strtolower(
                preg_replace(
                    array_keys($replace_pattern), 
                    array_values($replace_pattern), 
                    $word));
    }

    /**
     * 複数の配列をマージする
     * 
     * array_merge()関数が処理できない多重配列を自然にマージする
     * array_merge_recursive()関数との違いは同一キーで値を上書きする
     * 
     * CakePHPのSet::merge()を参考
     * 
     * $a = array('User'=>array('name'=>'ikeda', 'age'=>'26'));
     * $b = array('User'=>array('gender'=>'man','age'=>'27'));
     * Utility::merge($a, $b) == array(1) {
     *   ['User'] => array(3) {
     *     'name'   => ikeda
     *     'age'    => 27
     *     'gender' => man
     *   }
     * }
     * 
     * @param  array $array
     * @param  array [...]
     * @return array
     * @access public
     */
    function merge($array)
    {
        $args   = func_get_args();

        $result = (array)current($args);
        while (($arg = next($args)) !== false) {
            foreach ((array)$arg as $varkey => $varvalue)     {
                if(is_array($varvalue) && 
                   isset($result[$varkey]) && is_array($result[$varkey])) {
                    $result[$varkey] = Utility::merge($result[$varkey], $varvalue);
                } elseif(is_int($varkey)) {
                    $result[] = $varvalue;
                } else {
                    $result[$varkey] = $varvalue;
                }
            }
        }

        return $result;
    }

    /**
     * マルチバイト文字列が正規表現に一致するかを検証
     * - 正規表現オプション
     *  - i：英文字の大小を無視
     *       全角英文字に対しては無関係
     *  - x：拡張正規表現モード
     *       改行やスペースは無視され、"#" で始まる行はコメントとして扱う
     *  - s：入力を単一の行から構成されたテキストとみなす
     *       "^" や "$" の扱いがそれぞれバッファの始端と終端に
     *  - m：改行 "\n" を "." にマッチ
     *  - p：POSIX もどき正規表現モードを指定
     *       "m" と "s" を同時に指定した状態と同一
     *  - e：置換する文字列を有効なステートメントとみなし、評価してその結果と置換
     * 
     * @param  string      $pattern   正規表現パターン
     * @param  string      $attribute 検証する文字列
     * @param  string|null $encoding  文字エンコーディング
     * @param  string|null $option    正規表現オプション
     * @return boolean
     * @access public
     */
    function isMatch($pattern, $attribute, $encoding = null, $option = null)
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        if ($option === null) {
            $option = mb_regex_set_options();
        }
        mb_regex_encoding($encoding);

        return 
            mb_ereg_match($pattern, $attribute, $option);
    }

    /**
     * コンバータで値を変換
     * 
     * @param  string|array $converter
     * @param  string       $attributes
     * @return string
     * @access public
     * @static Module_Executant_Converter $module
     */
    function to($converter, $attributes)
    {
        static $module;
        if (!isset($module)) {
            $module = &Module::factory('Converter');
        }

        if (!is_array($converter)) {
            $converter = array($converter);
        }
        $result = $attributes;
        foreach ($converter as $function_name) {
            $result = $module->to($function_name, $result);
        }

        return $result;
    }

    /**
     * バリデータで値を検証
     * 
     * @param  string  $validation
     * @param  string  $attributes
     * @param  mixed   $params
     * @return boolean
     * @access public
     * @static Module_Executant_Validator $module
     */
    function is($validator, $attributes, $params = null)
    {
        static $module;
        if (!isset($module)) {
            $module = &Module::factory('Validator');
        }

        $args = func_get_args();

        return 
            call_user_func_array(array(&$module, 'is'), $args);
    }

    /**
     * コネクタで値を結合
     * 
     * @param  string  $connector
     * @param  array   $attributes
     * @param  mixed   $params
     * @return string
     * @access public
     * @static Module_Executant_Connector $module
     */
    function zip($connector, $attributes, $params = null)
    {
        static $module;
        if (!isset($module)) {
            $module = &Module::factory('Connector');
        }

        $args = func_get_args();

        return 
            call_user_func_array(array(&$module, 'zip'), $args);
    }

    /**
     * アレンジャで値を分割
     * 
     * @param  string  $arranger
     * @param  string  $attributes
     * @param  array   $params
     * @return array
     * @access public
     * @static Module_Executant_Arranger $module
     */
    function cut($arranger, $attributes, $params = array())
    {
        static $module;
        if (!isset($module)) {
            $module = &Module::factory('Arranger');
        }

        $args = func_get_args();

        return 
            call_user_func_array(array(&$module, 'cut'), $args);
    }
}
