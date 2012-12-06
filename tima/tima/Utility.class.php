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
 * �桼�ƥ���ƥ��������饹
 * 
 * @package  tima
 * @version  SVN: $Id: Utility.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Utility
{

    /**
     * [��������С���ˡ]��ʸ�����[�ѥ����뵭ˡ]���Ѵ�
     * - [�ѥ����뵭ˡ] ʣ������Ƭ����ʸ���ǽ񤭻Ϥ��
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
     * [�����뵭ˡ][�ѥ����뵭ˡ]��ʸ�����[��������С���ˡ]��ʸ����ˤ����ֵ�
     * - [�����뵭ˡ] ʣ������Ƭ�򡢾�ʸ���ǽ񤭻Ϥ��
     *  - camelCase
     * - (xxx2xxx|xxx4xxx)��ñ��ξ�ά��to, for�ˤȤ��ƻȤäƤ����礢��
     *  - ���ư�β�ǽ���Ϥ��뤬ñ��֤Ρ�2|4�ˤ�ñ��Ȥ��ư���
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
     * ʣ���������ޡ�������
     * 
     * array_merge()�ؿ��������Ǥ��ʤ�¿����������˥ޡ�������
     * array_merge_recursive()�ؿ��Ȥΰ㤤��Ʊ�쥭�����ͤ��񤭤���
     * 
     * CakePHP��Set::merge()�򻲹�
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
     * �ޥ���Х���ʸ��������ɽ���˰��פ��뤫�򸡾�
     * - ����ɽ�����ץ����
     *  - i����ʸ�����羮��̵��
     *       ���ѱ�ʸ�����Ф��Ƥ�̵�ط�
     *  - x����ĥ����ɽ���⡼��
     *       ���Ԥ䥹�ڡ�����̵�뤵�졢"#" �ǻϤޤ�Ԥϥ����ȤȤ��ư���
     *  - s�����Ϥ�ñ��ιԤ��鹽�����줿�ƥ����ȤȤߤʤ�
     *       "^" �� "$" �ΰ��������줾��Хåե��λ�ü�Ƚ�ü��
     *  - m������ "\n" �� "." �˥ޥå�
     *  - p��POSIX ��ɤ�����ɽ���⡼�ɤ����
     *       "m" �� "s" ��Ʊ���˻��ꤷ�����֤�Ʊ��
     *  - e���ִ�����ʸ�����ͭ���ʥ��ơ��ȥ��ȤȤߤʤ���ɾ�����Ƥ��η�̤��ִ�
     * 
     * @param  string      $pattern   ����ɽ���ѥ�����
     * @param  string      $attribute ���ڤ���ʸ����
     * @param  string|null $encoding  ʸ�����󥳡��ǥ���
     * @param  string|null $option    ����ɽ�����ץ����
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
     * ����С������ͤ��Ѵ�
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
     * �Х�ǡ������ͤ򸡾�
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
     * ���ͥ������ͤ���
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
     * ����󥸥���ͤ�ʬ��
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
