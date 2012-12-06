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
 * �ꥯ�����Ȥ�ʸ�����󥳡��ǥ��󥰤��Ѵ�����ե��륿
 * 
 * @package    tima
 * @subpackage tima_Filter
 * @version    SVN: $Id: RequestEncoding.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class Filter_RequestEncoding
{

    /**
     * �ꥯ�����Ȥ�ʸ�����󥳡��ǥ��󥰤�����ʸ�����󥳡��ǥ��󥰤��Ѵ�
     *
     * @params Front $front
     * @return void
     * @access public
     */
    function before(&$front)
    {
        $logger = &$front->getLogger();
        $logger->trace(__CLASS__ . "����������¹�");

        $request   = &$front->getRequest();
        $attribute = $request->getAll();

        // �Դ�����ʸ���ˤ��XSS���ȼ����ؤ��б��Ȥ��ơ�
        // �����ȳ�����ʸ�������ɤ˴ط��ʤ��¹ԡ�UTF-8 => UTF-8 �Ǥ�¹ԡ�
        mb_convert_variables(
            $front->getInternalEncoding(), $front->getContentsEncoding(), 
            $attribute);

        foreach ($attribute as $varkey => $varvalue) {
            $request->set($varkey, $varvalue);
        }

        $logger->debug('�ꥯ�����Ȥ�ʸ������󥳡��ǥ��󥰤��Ѵ�');
    }
}
