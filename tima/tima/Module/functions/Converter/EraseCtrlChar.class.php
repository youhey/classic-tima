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
 * ���Ԥ��������ʸ��������
 * 
 * @package    tima
 * @subpackage tima_Converter
 * @version    SVN: $Id: EraseCtrlChar.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Converter_EraseCtrlChar extends Converter_AbstractConverter
{

    /**
     * ʸ����������¸�ߤ��륹�ڡ�������
     * 
     * ���夫�������ʸ��
     * - ����ʸ����ASCII 0 �� ASCII 31��
     *  - ����ʸ����ASCII 10��LF/NL�פϽ�����
     *  - ����ʸ����ASCII 13��CR�פϽ�����
     * - DEL��ASCII 127��
     *
     * @param  string $attribute
     * @return string
     * @access protected
     */
    function doFunction($attribute)
    {
        static $ctrl_char;
        if (!isset($ctrl_char)) {
            // �ʲ���ʸ�������ɤϥ��ե�JIS�Ǥ�EUC-JP�Ǥ�
            // �Ի����ΰ�ʤΤǥ��󥰥�Х��Ȥ��Ѵ��Ǥ�ƶ��ʤ��Ϥ�
            $ctrl_char = array(
                    "\x00", // NUL
                    "\x01", // SOH
                    "\x02", // STX
                    "\x03", // ETX
                    "\x04", // EOT
                    "\x05", // ENQ
                    "\x06", // ACK
                    "\x07", // BEL
                    "\x08", // BS
                    "\x09", // HT
                    // "\x0A", // LF/NL
                    "\x0B", // VT
                    "\x0C", // FF
                    // "x\0D", // CR
                    "\x0E", // SO
                    "\x0F", // SI
                    "\x10", // DLE
                    "\x11", // DC1
                    "\x12", // DC2
                    "\x13", // DC3
                    "\x14", // DC4
                    "\x15", // NAK
                    "\x16", // SYN
                    "\x17", // ETB
                    "\x18", // CAN
                    "\x19", // EM
                    "\x1A", // SUB
                    "\x1B", // ESC
                    "\x1C", // FS
                    "\x1D", // GS
                    "\x1E", // RS
                    "\x1F", // US
                    "\x7F", // DEL
                );
        }

        return 
            str_replace($ctrl_char, '', $attribute);
    }
}
