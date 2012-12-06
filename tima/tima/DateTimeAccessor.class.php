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
 * DateAccessor���饹�˻��־�����ĥ
 * 
 * �������������˴�ñ��������
 * - ����ʬ���á��ä����
 * - ������ʸ��������
 * - ����������Ǽ���
 * 
 * @package  tima
 * @version  SVN: $Id: DateTimeAccessor.class.php 4 2007-06-20 07:16:44Z do_ikare $
$
 */
class DateTimeAccessor extends DateAccessor
{

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  integer|null $year  ǯ
     * @param  integer|null $month ��
     * @param  integer|null $day   ��
     * @param  integer|null $hour  ��
     * @param  integer|null $min   ʬ
     * @param  integer|null $sec   ��
     * @access public
     */
    function DateTimeAccessor($year = null, $month = null, $day = null, 
                              $hour = null, $min = null, $sec = null)
    {
        $stamp = time();
        foreach(array(
                'Y'=>'year', 'n'=>'month', 'j'=>'day', 
                'H'=>'hour', 'i'=>'min', 's'=>'sec'
            ) as $key => $var) {
            $$var = (int)(isset($$var) ? $$var : date($key, $stamp));
        }
        $this->setTime(mktime($hour, $min, $sec, $month, $day, $year));
    }

    /**
     * �����ֵ�
     * 
     * @param  void
     * @return integer 
     * @access public
     */
    function getHour()
    {
        return (int)date('G', $this->getTime());
    }

    /**
     * ʬ���ֵ�
     * 
     * @param  void
     * @return integer 
     * @access public
     */
    function getMinute()
    {
        return (int)date('i', $this->getTime());
    }

    /**
     * �ä��ֵ�
     * 
     * @param  void
     * @return integer 
     * @access public
     */
    function getSecond()
    {
        return (int)date('s', $this->getTime());
    }

    /**
     * ���������������ֵ�
     * 
     * @param  void 
     * @return array 
     * @access public
     * @see    DateAccessor::toArray()
     */
    function toArray()
    {
        $date_array = parent::toArray();

        $date_array['hour']   = $this->getHour();
        $date_array['minute'] = $this->getMinute();
        $date_array['second'] = $this->getSecond();

        return $date_array;
    }

    /**
     * ���������񼰲�����ʸ������ֵ�
     * 
     * @param  string $format date()�ؿ��ν�
     * @return string 
     * @access public
     * @see    DateAccessor::format()
     */
    function format($format = DATE_FORMAT_ISO)
    {
        return parent::format($format);
    }
}
