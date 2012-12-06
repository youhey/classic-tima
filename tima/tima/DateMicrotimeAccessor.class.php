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
 * DateTimeAccessor���饹�˥ޥ������äξ�����ĥ
 * 
 * @package  tima
 * @version  SVN: $Id: DateMicrotimeAccessor.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class DateMicrotimeAccessor extends DateTimeAccessor
{

    /**
     * �ޥ�������
     * 
     * @var    float 
     * @access private
     */
    var $_microsec;

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  integer|null $year  ǯ
     * @param  integer|null $month ��
     * @param  integer|null $day   ��
     * @param  integer|null $hour  ��
     * @param  integer|null $min   ʬ
     * @param  integer|null $sec   ��
     * @param  float|null   $msec  �ޥ�������
     * @access public
     */
    function DateMicrotimeAccessor($year = null, $month = null, $day = null, 
                                   $hour = null, $min = null, $sec = null, 
                                   $msec = null)
    {
        list($u,$s) = explode(" ", microtime());
        $stamp = ((float)$u + (float)$s);

        foreach(array(
                'Y'=>'year', 'n'=>'month', 'j'=>'day', 
                'H'=>'hour', 'i'=>'min', 's'=>'sec'
            ) as $key => $var) {
            $$var = (int)(isset($$var) ? $$var : date($key, (int)floor($stamp)));
        }
        $this->setTime(mktime($hour, $min, $sec, $month, $day, $year));

        $msec  = (isset($msec) ? (float)$msec : ($stamp - floor($stamp)));
        $this->_setMicrosec($msec);
    }

    /**
     * �ޥ������ä��ֵ�
     * 
     * @param  void
     * @return float 
     * @access public
     */
    function getMicrosec()
    {
        return $this->_microsec;
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

        $date_array['microsec'] = $this->getMicrosec();

        return $date_array;
    }

    /**
     * ���������񼰲�����ʸ������ֵ�
     * 
     * �ե����ޥåȤˡ�s�פ�����С��ä�ޥ������ä�����
     * 
     * @param  string $format date()�ؿ��ν�
     * @return string 
     * @access public
     * @see    DateAccessor::format()
     * @todo   �ִ��������׸�Ƥ
     */
    function format($format = 'Y-m-d H:i:s')
    {
        return 
            str_replace(
                '%%%%%%%%%%', 
                date('s', $this->getTime()) . substr(sprintf('%.05f', 0.123), 1), 
                parent::format(str_replace('s', '%%%%%%%%%%', $format)));
    }

    /**
     * �ޥ������ä�����
     * 
     * @param  float 
     * @return void
     * @access private
     * @todo   �ޥ��ʥ��Υޥ������äν������׸�Ƥ
     */
    function _setMicrosec($microsec)
    {
        switch($microsec = (float)$microsec) {
        case ($microsec > 1) : 
            $ceilsec   = floor($microsec);
            $microsec -= $ceilsec;
            $this->setTime($this->getTime() + (int)$ceilsec);
            break;
        case ($microsec < -1) : 
            // ���η׻��ʤˤ�ͤ��Ƥޤ���
            // | -5.253 => -6�� && 0.747�ޥ������� | ��������
            $ceilsec   = floor($microsec);
            $microsec += abs($ceilsec);
            $this->setTime($this->getTime() + (int)$ceilsec);
            break;
        }
        $this->_microsec = $microsec;
    }
}
