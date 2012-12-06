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
 * CHTML生成クラス
 * 
 * @package  tima
 * @version  SVN: $Id: CHtml.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class CHtml extends HTML
{

    /**
     * INPUT/Radioの子要素を区切る文字のデフォルト地
     * 
     * @var    string
     * @access public
     */
    var $radiosSeparator = '<br />';

    /**
     * INPUT/Checkboxの子要素を区切る文字のデフォルト地
     * 
     * @var    string
     * @access public
     */
    var $checkboxSeparator = '<br />';

    /**
     * INPUT/Text「SIZE」属性のデフォルト値
     * 
     * @var    integer
     * @access public
     */
    var $inputSize = 14;

    /**
     * INPUT/Text「MAXLENGTH」属性のデフォルト値
     * 
     * @var    integer
     * @access public
     */
    var $inputMaxlength = 64;

    /**
     * TEXTARE「COLS」属性のデフォルト値
     * 
     * @var    integer
     * @access public
     */
    var $textareaCols = 0;

    /**
     * TEXTARE「ROWS」属性のデフォルト値
     * 
     * @var    integer
     * @access public
     */
    var $textareaRows = 0;

    /**
     * チェック選択構造のHTMLを返却
     * 
     * @param  string  $name 
     * @param  string  $option
     * @param  array   $checked
     * @param  string  $separator
     * @param  array   $attribute
     * @return string
     * @access public
     * @static
     */
    function checkboxes($name, $option, $checked = array(), 
                        $separator = null, $attribute = array())
    {
        if ($separator === null) {
            $separator = $this->checkboxSeparator;
        }
        if (!is_array($checked)) {
            $checked = (array)$checked;
        }

        $html = array();
        foreach ($option as $varkey => $varvalue) {
            $html[] =
                $this->inputCheckbox(
                    $name, $varvalue, in_array($varvalue, $checked), $attribute) . 
                $this->escape($varkey);
        }

        return 
            implode($separator, $html);
    }

    /**
     * レヂオ選択構造のHTMLを返却
     * 
     * @param  string  $name 
     * @param  string  $option
     * @param  string  $checked
     * @param  string  $separator
     * @param  array   $attribute
     * @return string
     * @access public
     * @static
     */
    function radios($name, $option, $checked = '', 
                    $separator = null, $attribute = array())
    {
        if ($separator === null) {
            $separator = $this->checkboxSeparator;
        }

        $html = array();
        foreach ($option as $varkey => $varvalue) {
            $html[] =
                $this->inputRadio(
                    $name, $varvalue, ($checked === $varvalue), $attribute) . 
                $this->escape($varkey);
        }

        return 
            implode($separator, $html);
    }

    /**
     * 単一行テキストのINPUT要素を返却
     * 
     * @param  string       $name
     * @param  string       $value
     * @param  integer|null $size
     * @param  integer|null $maxlength
     * @param  string|null  $ime
     * @param  array        $attribute
     * @return string
     * @access public
     * @static
     */
    function inputText($name, $value, $size = null, $maxlength = null, $ime = null, 
                       $attribute = array())
    {
        if (($size === null) || ($size > $this->inputSize)) {
            $size = $this->inputSize;
        }
        if (($maxlength === null) || ($maxlength > $this->inputMaxlength)) {
            $maxlength = $this->inputMaxlength;
        }

        $params = array(
                'value'     => $value, 
                'size'      => $size, 
                'maxlength' => $maxlength, 
            );

        switch ($ime) {
        case 1 : 
            $params['istyle'] = '1';
            break;
        case 2 : 
            $params['istyle'] = '2';
            break;
        case 3 : 
            $params['istyle'] = '3';
            break;
        case 4 : 
            $params['istyle'] = '4';
            break;
        }

        return 
            $this->input('text', $name, Utility::merge($params, $attribute));
    }

    /**
     * パスワードのINPUT要素を返却
     * 
     * @param  string $name
     * @param  string $value
     * @param  integer|null $size
     * @param  integer|null $maxlength
     * @param  array  $attribute
     * @return string
     * @access public
     * @static
     */
    function inputPassword($name, $value, $size = null, $maxlength = null, 
                           $attribute = array())
    {
        if (($size === null) || ($size > $this->inputSize)) {
            $size = $this->inputSize;
        }
        if (($maxlength === null) || ($maxlength > $this->inputMaxlength)) {
            $maxlength = $this->inputMaxlength;
        }

        $params = array(
                'value'     => $value, 
                'size'      => $size, 
                'maxlength' => $maxlength, 
            );

        return 
            $this->input('password', $name, Utility::merge($params, $attribute));
    }


    /**
     * チェックボックスのINPUT要素を返却
     * 
     * @param  string  $name 
     * @param  string  $value
     * @param  boolean $checked
     * @param  array   $attribute
     * @return string
     * @access public
     * @static
     */
    function inputCheckbox($name, $value, $checked = false, $attribute = array())
    {
        $params = array('value' => $value);

        if ($checked === true) {
            $params['checked'] = 'checked';
        }

        return 
            $this->input(
                'checkbox', $name . '[]', Utility::merge($params, $attribute));
    }

    /**
     * レヂオのINPUT要素を返却
     * 
     * @param  string  $name 
     * @param  string  $value
     * @param  boolean $checked
     * @param  array   $attribute
     * @return string
     * @access public
     * @static
     */
    function inputRadio($name, $value, $checked = false, $attribute = array())
    {
        $params = array('value' => $value);

        if ($checked === true) {
            $params['checked'] = 'checked';
        }

        return 
            $this->input('radio', $name, Utility::merge($params, $attribute));
    }

    /**
     * TEXTAREA要素を返却
     * 
     * @param  string       $name
     * @param  string       $value
     * @param  integer|null $cols
     * @param  integer|null $rows
     * @param  string|null  $ime
     * @param  array        $attribute
     * @return string
     * @access public
     * @static
     */
    function textarea($name, $value, $cols = null, $rows = null, $ime = null, 
                      $attribute = array())
    {
        if (($cols === null) || ($cols > $this->textareaCols)) {
            $cols = $this->textareaCols;
        }
        if (($rows === null) || ($rows > $this->textareaRows)) {
            $rows = $this->textareaRows;
        }

        $params = array('name'  => $name);
        if ($cols > 0) {
            $params['cols'] = $cols;
        }
        if ($rows > 0) {
            $params['rows'] = $rows;
        }

        switch ($ime) {
        case 1 : 
            $params['istyle'] = '1';
            break;
        case 2 : 
            $params['istyle'] = '2';
            break;
        case 3 : 
            $params['istyle'] = '3';
            break;
        case 4 : 
            $params['istyle'] = '4';
            break;
        }

        return 
            $this->blockTag(
                'textarea', $this->escape($value), 
                Utility::merge($params, $attribute));
    }

    /**
     * OPTION要素のタグを返却
     * 
     * @param  string       $label
     * @param  string|array $value
     * @params array        $selected
     * @param  array        $attribute
     * @return string
     * @access public
     * @static
     */
    function option($label, $value, $selected = array(), $attribute = array())
    {
        if (is_array($value)) {
            $buf = '';
            foreach ($value as $varkey => $varvalue) {
                $buf .= $this->option($varkey, $varvalue, $selected, $attribute);
            }

            return $buf;
        }

        $params = array('value' => $value);
        if (!is_array($selected)) {
            $checked = (array)$selected;
        }
        if (in_array($value, $selected)) {
            $params['selected'] = 'selected';
        }

        return 
            $this->openTag(
                'option', Utility::merge($params, $attribute)) . 
            $this->escape($label);
    }

    /**
     * 要素の属性を返却
     * 
     * @param  array  $params
     * @return string
     * @access public
     * @static
     */
    function attribute($params = array())
    {
        static $excludes = array(
                'id', 
                'class', 
                'style', 
            );

        $attribute = array();
        foreach ($params as $varkey => $varvalue) {
            if (in_array($varkey, $excludes)) {
                continue;
            }
            // if ($varvalue === '') {
            //     continue;
            // }

            if (is_array($varvalue)) {
                $varvalue = implode(' ', $varvalue);
            }

            $attribute[] = 
                sprintf('%s="%s"', $this->escape($varkey), $this->escape($varvalue));
        }

        return 
            implode(' ', $attribute);
    }
}
