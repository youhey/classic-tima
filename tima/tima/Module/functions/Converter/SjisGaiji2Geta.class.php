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
 * Shift_JIS¤Î³°»ú¤ò¡Ö¢®¡×¤ËÊÑ´¹
 * 
 * @package    tima
 * @subpackage tima_Converter
 * @version    SVN: $Id: SjisGaiji2Geta.class.php 6 2007-08-17 08:46:57Z do_ikare $
 */
class Converter_SjisGaiji2Geta extends Converter_AbstractConverter
{

    /**
     * Shift_JIS¤Î³°»ú¤ò¡Ö¢®¡×¤ËÊÑ´¹
     *
     * @param  string $attribute
     * @return string
     * @access protected
     */
    function doFunction($attribute)
    {
        static $pattern;
        if (!isset($pattern)) {
            $gaiji_ranges = array(
                    array(0x8540,0x857E),array(0x8580,0x859E), //   9¶è
                    array(0x859F,0x85FC),                      //  10¶è
                    array(0x8640,0x867E),array(0x8680,0x869E), //  11¶è
                    array(0x869F,0x86FC),                      //  12¶è
                    array(0x8740,0x877E),array(0x8780,0x879E), //  13¶è-NECÆÃ¼ìµ­¹æ-
                    array(0x879F,0x87FC),                      //  14¶è
                    array(0x8840,0x887E),array(0x8880,0x889E), //  15¶è
                    array(0xEB40,0xEB7E),array(0xEB80,0xEB9E), //  85¶è
                    array(0xEB9F,0xEBFC),                      //  86¶è
                    array(0xEC40,0xEC7E),array(0xEC80,0xEC9E), //  87¶è
                    array(0xEC9F,0xECFC),                      //  88¶è
                    array(0xED40,0xED7E),array(0xED80,0xED9E), //  89¶è-NEC³ÈÄ¥³°»ú
                    array(0xED9F,0xEDFC),                      //  90¶è-NEC³ÈÄ¥³°»ú
                    array(0xEE40,0xEE7E),array(0xEE80,0xEE9E), //  91¶è-NEC³ÈÄ¥³°»ú
                    array(0xEE9F,0xEEFC),                      //  92¶è-NEC³ÈÄ¥³°»ú
                    array(0xEF40,0xEF7E),array(0xEF80,0xEF9E), //  93¶è-NEC³ÈÄ¥³°»ú
                    array(0xEF9F,0xEFFC),                      //  94¶è-NEC³ÈÄ¥³°»ú
                    array(0xF040,0xF07E),array(0xF080,0xF09E), //  95¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF09F,0xF0FC),                      //  96¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF140,0xF17E),array(0xF180,0xF19E), //  97¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF19F,0xF1FC),                      //  98¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF240,0xF27E),array(0xF280,0xF29E), //  99¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF29F,0xF2FC),                      // 100¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF340,0xF37E),array(0xF380,0xF39E), // 101¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF39F,0xF3FC),                      // 102¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF440,0xF47E),array(0xF480,0xF49E), // 103¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF49F,0xF4FC),                      // 104¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF540,0xF57E),array(0xF580,0xF59E), // 105¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF59F,0xF5FC),                      // 106¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF640,0xF67E),array(0xF680,0xF69E), // 107¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF69F,0xF6FC),                      // 108¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF740,0xF77E),array(0xF780,0xF79E), // 109¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF79F,0xF7FC),                      // 110¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF840,0xF87E),array(0xF880,0xF89E), // 111¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF89F,0xF8FC),                      // 112¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF940,0xF97E),array(0xF980,0xF99E), // 113¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xF99F,0xF9FC),                      // 114¶è-¥æ¡¼¥¶ÎÎ°è
                    array(0xFA40,0xFA7E),array(0xFA80,0xFA9E), // 115¶è-IBM³ÈÄ¥´Á»ú
                    array(0xFA9F,0xFAFC),                      // 116¶è-IBM³ÈÄ¥´Á»ú
                    array(0xFB40,0xFB7E),array(0xFB80,0xFB9E), // 117¶è-IBM³ÈÄ¥´Á»ú
                    array(0xFB9F,0xFBFC),                      // 118¶è-IBM³ÈÄ¥´Á»ú
                    array(0xFC40,0xFC7E),array(0xFC80,0xFC9E), // 119¶è-IBM³ÈÄ¥´Á»ú
                    array(0xFC9F,0xFCFC),                      // 120¶è
                );
            $gaiji = array();
            foreach ($gaiji_ranges as $range) {
                $begin = dechex($range[0]);
                $end   = dechex($range[1]);
                // $gaiji[]  = 
                //     '[\x' . substr($begin, 0, 2) . 
                //      '\x' . substr($begin, 2, 2) . '-' . 
                //      '\x' . substr($end, 0, 2) . 
                //      '\x' . substr($end, 2, 2) . ']';

                // PHP4¤Ç16¿Ê¿ô¤ÎÊ¸»ú¥·¡¼¥±¥ó¥¹¤Î¤Þ¤Þ¤Ç¤Ï¥³¥ó¥Ñ¥¤¥ë¤ËÄÌ¤é¤Ê¤«¤Ã¤¿¤Î¤Ç
                // eval()¤Ç¥Ñ¡¼¥¹¤¹¤ë½èÍý¤òÄÉ²Ã
                // ³ÆÈÏ°Ï¤òÁªÂò¤¹¤ëÀµµ¬É½¸½¤À¤Ã¤¿¤Î¤òÁ´¤ÆÈÏ°Ï¤À¤±¤ÇÉ½¸½¤ËÊÑ¹¹
                $gaiji[]  = 
                    eval(
                        'return "' . 
                            '\x'.substr($begin, 0, 2) . '\x'.substr($begin, 2, 2) . 
                            '-' . 
                            '\x'.substr($end, 0, 2) . '\x'.substr($end, 2, 2) . 
                        '";');
            }
            // $pattern = '(?:' . implode('|', $gaiji) . ')';
            $pattern = '[' . implode('', $gaiji) . ']';
        }

        mb_regex_encoding('SJIS');

        return 
            mb_ereg_replace($pattern, "\x81\xAC", $attribute);
    }
}
