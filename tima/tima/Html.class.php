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
 * HTML生成クラス
 * 
 * @package  tima
 * @version  SVN: $Id: Html.class.php 28 2007-09-11 11:13:03Z do_ikare $
 */
class Html
{

    /**
     * INPUT/Radioの子要素を区切る文字のデフォルト地
     * 
     * @var    string
     * @access public
     */
    var $radiosSeparator = '　';

    /**
     * INPUT/Checkboxの子要素を区切る文字のデフォルト地
     * 
     * @var    string
     * @access public
     */
    var $checkboxSeparator = '　';

    /**
     * INPUT/Text「SIZE」属性のデフォルト値
     * 
     * @var    integer
     * @access public
     */
    var $inputSize = 50;

    /**
     * INPUT/Text「MAXLENGTH」属性のデフォルト値
     * 
     * @var    integer
     * @access public
     */
    var $inputMaxlength = 100;

    /**
     * TEXTARE「COLS」属性のデフォルト値
     * 
     * @var    integer
     * @access public
     */
    var $textareaCols = 70;

    /**
     * TEXTARE「ROWS」属性のデフォルト値
     * 
     * @var    integer
     * @access public
     */
    var $textareaRows = 8;

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

        $html  = array();
        $index = 0;
        foreach ($option as $varkey => $varvalue) {
            $id     = sprintf('%s_%03d', $name, ++$index);
            $html[] =
                $this->inputCheckbox(
                    $name, $varvalue, in_array($varvalue, $checked, true), 
                    Utility::merge(array('id' => $id), $attribute)) . 
                $this->blockTag(
                    'label', $this->escape($varkey), array('for' => $id));
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
            $separator = $this->radiosSeparator;
        }

        $html  = array();
        $index = 0;
        foreach ($option as $varkey => $varvalue) {
            $id     = sprintf('%s_%03d', $name, ++$index);
            $html[] =
                $this->inputRadio(
                    $name, $varvalue, ($checked === $varvalue), 
                    Utility::merge(array('id' => $id), $attribute)) . 
                $this->blockTag(
                    'label', $this->escape($varkey), array('for' => $id));
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
        if ($size === null) {
            $size = $this->inputSize;
        }
        if ($maxlength === null) {
            $maxlength = $this->inputMaxlength;
        }
        if (isset($attribute['class']) && !is_array($attribute['class'])) {
            $attribute['class'] = array($attribute['class']);
        }

        $params = array(
                'value'     => $value, 
                'id'        => $name, 
                'size'      => $size, 
                'maxlength' => $maxlength, 
                'class'     => array('input_text'), 
            );
        switch ($ime) {
        case 1 : 
        case 2 : 
            $params['style'] = array('ime-mode: active;');
            break;
        case 3 : 
        case 4 : 
            $params['style'] = array('ime-mode: inactive;');
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
        if ($size === null) {
            $size = $this->inputSize;
        }
        if ($maxlength === null) {
            $maxlength = $this->inputMaxlength;
        }
        if (isset($attribute['class']) && !is_array($attribute['class'])) {
            $attribute['class'] = array($attribute['class']);
        }

        $params = array(
                'value'     => $value, 
                'id'        => $name, 
                'size'      => $size, 
                'maxlength' => $maxlength, 
                'class'     => array('input_password'), 
            );

        return 
            $this->input('password', $name, Utility::merge($params, $attribute));
    }

    /**
     * 隠し入力のINPUT要素を返却
     * 
     * @param  string $name
     * @param  string $value
     * @param  array  $attribute
     * @return string
     * @access public
     * @static
     */
    function inputHidden($name, $value, $attribute = array())
    {
        return 
            $this->input(
                'hidden', 
                $name, 
                Utility::merge(array('value' => $value), $attribute));
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
        $params = array(
                'value' => $value, 
                'class' => array('input_checkbox'), 
            );
        if ($checked === true) {
            $params['checked'] = 'checked';
        }
        if (isset($attribute['class']) && !is_array($attribute['class'])) {
            $attribute['class'] = array($attribute['class']);
        }

        return 
            $this->input(
                'checkbox', "${name}[]", Utility::merge($params, $attribute));
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
        $params = array(
                'value' => $value, 
                'class' => array('input_radio'), 
            );
        if ($checked === true) {
            $params['checked'] = 'checked';
        }
        if (isset($attribute['class']) && !is_array($attribute['class'])) {
            $attribute['class'] = array($attribute['class']);
        }

        return 
            $this->input('radio', $name, Utility::merge($params, $attribute));
    }

    /**
     * INPUT要素を返却
     * 
     * @param  string $name
     * @param  string $type
     * @param  array  $attribute
     * @return string
     * @access public
     * @static
     */
    function input($type, $name, $attribute = array())
    {
        return 
            $this->singleTag(
                'input', 
                Utility::merge(array('type'=>$type, 'name'=>$name), $attribute));
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
        if ($cols === null) {
            $cols = $this->textareaCols;
        }
        if ($rows === null) {
            $rows = $this->textareaRows;
        }
        if (isset($attribute['class']) && !is_array($attribute['class'])) {
            $attribute['class'] = array($attribute['class']);
        }

        $params = array(
                'name'  => $name, 
                'id'    => $name, 
                'cols'  => $cols, 
                'rows'  => $rows, 
                'class' => array('textarea'), 
            );
        switch ($ime) {
        case 1 : 
        case 2 : 
            $params['style'] = array('ime-mode: active;');
            break;
        case 3 : 
        case 4 : 
            $params['style'] = array('ime-mode: inactive;');
            break;
        }

        return 
            $this->blockTag(
                'textarea', $this->escape($value), 
                Utility::merge($params, $attribute));
    }

    /**
     * 単一選択のSELECT要素を返却
     * 
     * @param  string  $name
     * @param  array   $option
     * @param  string  $selected
     * @param  integer $size
     * @param  array   $attribute
     * @return string
     * @access public
     * @static
     */
    function select($name, $option, $selected = '', $size = 1, 
                    $attribute = array())
    {
        $option_html = '';
        foreach ($option as $varkey => $varvalue) {
            $option_html .= $this->option($varkey, $varvalue, array($selected));
        }

        return 
            $this->blockTag(
                'select', $option_html, 
                Utility::merge(
                    array('name' => $name, 'id' => $name, 'size' => $size), 
                    $attribute));
    }

    /**
     * 複数選択のSELECT要素を返却
     * 
     * @param  string  $name
     * @param  array   $option
     * @param  array   $selected
     * @param  integer $size
     * @param  array   $attribute
     * @return string
     * @access public
     * @static
     */
    function multipleSelect($name, $option, $selected = array(), $size = 1, 
                               $attribute = array())
    {
        $option_html = '';

        foreach ($option as $varkey => $varvalue) {
            $option_html .= $this->option($varkey, $varvalue, $selected);
        }

        return 
            $this->blockTag(
                'select', $option_html, 
                Utility::merge(
                    array('name' => "${name}[]", 'id' => $name, 
                          'size' => $size, 'multiple' => 'multiple'), 
                    $attribute));
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

            return 
                $this->blockTag(
                    'optgroup', $buf, array('label' => $label));
        }

        $params = array('value' => $value);
        if (!is_array($selected)) {
            $checked = (array)$selected;
        }
        if (in_array($value, $selected, true)) {
            $params['selected'] = 'selected';
        }

        return 
            $this->blockTag(
                'option', $this->escape($label), 
                Utility::merge($params, $attribute));
    }

    /**
     * 要素の単一タグを返却
     * 
     * @param  string $element
     * @param  array  $attribute
     * @return string
     * @access public
     * @static
     */
    function singleTag($element, $attribute = array())
    {
        $addon = $this->attribute($attribute);
        if ($addon !== '') { 
            $addon= ' ' . $addon;
        }

        return 
            '<' . $this->escape($element) . $addon . ' />';
    }

    /**
     * 要素のブロックを返却
     * 
     * @param  string $element
     * @param  array  $attribute
     * @return string
     * @access public
     * @static
     */
    function blockTag($element, $text, $attribute = array())
    {
        return 
            $this->openTag($element, $attribute) . 
            $text . 
            $this->endTag($element);
    }

    /**
     * 要素の開始タグを返却
     * 
     * @param  string $element
     * @param  array  $attribute
     * @return string
     * @access public
     * @static
     */
    function openTag($element, $attribute = array())
    {
        $addon = $this->attribute($attribute);
        if ($addon !== '') { 
            $addon= ' ' . $addon;
        }

        return 
            '<' . $this->escape($element) . $addon . '>';
    }

    /**
     * 要素の終了タグを返却
     * 
     * @param  array  $element
     * @return string
     * @access public
     * @static
     */
    function endTag($element)
    {
        return 
            '</' . $this->escape($element) . '>';
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
        $attribute = array();
        foreach ($params as $varkey => $varvalue) {
            if (is_array($varvalue)) {
                $varvalue = implode(' ', $varvalue);
            }
            if (($varkey === 'id') || ($varkey === 'for')) {
                $varvalue = preg_replace('/\[(.+)\]/', '_$1', $varvalue);
            }
            $attribute[] = 
                sprintf('%s="%s"', $this->escape($varkey), $this->escape($varvalue));
        }

        return 
            implode(' ', $attribute);
    }

    /**
     * 特殊な文字をHTMLエンティティに変換して無効化
     * 
     * @param  string $attribute
     * @return string
     * @access public
     * @static
     */
    function escape($attribute)
    {
        return 
            str_replace(
                array('%%%EntityRef__%%%','%%%__EntityRef%%%'), 
                array('&',';'), 
                htmlspecialchars(
                    preg_replace(
                        '/&(#?[\w]+);/', '%%%EntityRef__%%%$1%%%__EntityRef%%%', 
                        $attribute), ENT_QUOTES));
    }
}
