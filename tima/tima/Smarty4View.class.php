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
 * �ƥ�ץ졼�ȡ����󥸥�ˡ�Smarty�פ���Ѥ���ӥ塼
 * 
 * @package  tima
 * @version  SVN: $Id: Smarty4View.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Smarty4View extends View
{

    /**
     * �ӥ塼������
     * 
     * @param  array $option
     * @return void
     * @access public
     */
    function initialize($option = array())
    {
        if (!class_exists('Smarty')) {
            header('HTTP/1.0 500 Internal Server Error');
            trigger_error('Smarty not found', E_USER_ERROR);
            exit;
        }

        $this->engine = new Smarty;

        foreach ($option as $varkey => $varvalue) {
            if ($varvalue === null) {
                continue;
            }
            $this->engine->$varkey = $varvalue;
        }

        $this->_registerFilters();
    }

    /**
     * �ƥ�ץ졼�Ȥ�ɾ��
     * 
     * @param  string $template
     * @param  array  $data_model
     * @return string
     * @access public
     */
    function render($template, $data_model)
    {
        $this->engine->assign($data_model);

        return $this->engine->fetch($template);
    }

    /**
     * �ƥ�ץ졼�Ȥ�¸�ߤ��뤫�򸡾�
     * 
     * @param  string $template
     * @return boolean
     * @access public
     */
    function isTemplateExists($template)
    {
        return $this->engine->template_exists($template);
    }

    /**
     * �ƥ�ץ졼�ȡ����󥸥�˥��֥������Ȥ���Ͽ
     * 
     * @param  string $varkey
     * @param  object $object
     * @return void
     * @access public
     */
    function registerObject($varkey, &$object)
    {
        // $this->engine->register_object($varkey, $object);
        $this->engine->assign_by_ref($varkey, $object);
    }

    /**
     * �ƥ�ץ졼�ȡ��������Υ���ѥ�����Ф���ץ�ե��륿
     * 
     * - ʸ�����󥳡��ɤ�ֽ��Ϣ������פ��Ѵ�
     *  - �ƥ�ץ졼�ȤϽ���ʸ�����󥳡��ɤǵ��Ҥ���Ƥ���Ȥ�������
     * 
     * @param  string $source
     * @param  Smarty $smarty
     * @return string
     * @access public
     */
    function prefilterCompile($source, &$smarty)
    {
        return 
            mb_convert_encoding(
                $source, $this->internalEncoding, $this->contentsEncoding);
    }

    /**
     * ����ѥ����̤��Ф���ݥ��ȥե��륿
     * 
     * - ʸ�����󥳡��ɤ�ֽ��Ϣ������פ��Ѵ�
     *  - �ƥ�ץ졼�ȤϽ���ʸ�����󥳡��ɤǵ��Ҥ���Ƥ���Ȥ�������
     * 
     * @param  string $source
     * @param  Smarty $smarty
     * @return string
     * @access public
     */
    function postfilterCompile($source, &$smarty)
    {
        return 
            mb_convert_encoding(
                $source, $this->contentsEncoding, $this->internalEncoding);
    }

    /**
     * Smarty�˥ե��륿����Ͽ
     * 
     * @param  void
     * @return void
     * @access private
     */
    function _registerFilters()
    {
        if ($this->internalEncoding === $this->contentsEncoding) {
            return;
        }

        $this->engine->register_prefilter(array(&$this, 'prefilterCompile'));
        $this->engine->register_outputfilter(array(&$this, 'postfilterCompile'));
    }
}
