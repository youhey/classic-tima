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
 * �ӥ塼
 * 
 * @package  tima
 * @version  SVN: $Id: View.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class View
{

    /**
     * �ƥ�ץ졼�ȡ����󥸥�
     * 
     * @var    string
     * @access protected
     */
    var $engine = null;

    /**
     * ������ʸ�����󥳡��ǥ���
     * 
     * @var    string
     * @access protected
     */
    var $internalEncoding = '';

    /**
     * ���Ϥ�ʸ�����󥳡��ǥ���
     * 
     * @var    string
     * @access protected
     */
    var $contentsEncoding = '';

    /**
     * ���󥹥ȥ饯��
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
     * �ӥ塼�������ʥ����ѡ����饹�Ǥ϶���
     * �Ѿ����饹�ǽ����򥪡��С��饤��
     * 
     * @param  array $options
     * @return void
     * @access public
     * @abstract
     */
    function initialize($option = array()) {}

    /**
     * �ƥ�ץ졼�Ȥ�ɾ���ʥ����ѡ����饹�Ǥ϶���
     * �Ѿ����饹�ǽ����򥪡��С��饤��
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
     * �ƥ�ץ졼�ȡ����󥸥�˥��֥������Ȥ���Ͽ
     * 
     * @param  string $varkey
     * @param  object $object
     * @return void
     * @access public
     * @abstract
     */
    function registerObject($varkey, &$object) {}

    /**
     * �ƥ�ץ졼�Ȥ�¸�ߤ��뤫�򸡾ڡʥ����ѡ����饹�Ǥ϶���
     * �Ѿ����饹�ǽ����򥪡��С��饤��
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
     * ������ʸ�����󥳡��ǥ��󥰤���Ͽ
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
     * ���Ϥ�ʸ�����󥳡��ǥ��󥰤���Ͽ
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
     * �ƥ�ץ졼�ȡ����󥸥���ֵ�
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
