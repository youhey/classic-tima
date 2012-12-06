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
 * �ꥯ�����Ȥ˴ޤޤ������ʸ��������ե��륿
 * 
 * @package    tima
 * @subpackage tima_Filter
 * @version    SVN: $Id: ClearCtrlChar.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Filter_ClearCtrlChar
{

    /**
     * �ꥯ�����Ȥ˴ޤޤ������ʸ�������ƾ��
     *
     * @params Front $front
     * @return void
     * @access public
     */
    function before(&$front)
    {
        $logger = &$front->getLogger();
        $logger->trace(__CLASS__ . "����������¹�");

        $request = &$front->getRequest();
        foreach ($request->getAll() as $varkey => $varvalue) {
            if (is_array($varvalue)) {
                array_walk($varvalue, array(&$this, 'bridgeRecursive'));
            } else {
                $varvalue = Utility::to('EraseCtrlChar', $varvalue);
            }
            $request->set($varkey, $varvalue);
        }
    }

    /**
     * ������array_walk()�״ؿ��ǽ������뤿��Υ�����Хå��ؿ�
     * - �Ѵ��⥸�塼��Ρ�EraseCtrlChar�׵�ǽ���ͤ��Ѵ�
     * - �ͤϻ��ȤǼ�����ä��ͤ�ľ�ܾ��
     * - PHP5�Ρ�array_walk_recursive()�״ؿ��ߴ���ư���̤����褦�Ƶ�����
     *
     * @params mixed $varvalue
     * @params mixed $varkey
     * @return void
     * @access public
     */
    function bridgeRecursive(&$varvalue, $varkey)
    {
        if (is_array($varvalue)) {
            array_walk($varvalue, array(&$this, __FUNCTION__));
        } else {
            $varvalue = Utility::to('EraseCtrlChar', $varvalue);
        }
    }
}
