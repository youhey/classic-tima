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
 * ���վ�������Ū�����
 * 
 * �������θ���Ĥġ����դ��ñ���
 * - 2007ǯ1��31����������׻�
 * - 2007ǯ1��31����������׻�
 * - 2007ǯ1��31�����⽵��׻�
 * - 2007ǯ1��31�������ν���׻�
 * - 2007ǯ1��31��������2007ǯ2��28��
 * - 2007ǯ3��31���������2007ǯ2��28��
 * - 2004ǯ2��29������ǯ��2005ǯ2��28��
 * - 2007ǯ1��31���η����콵��2007ǯ1��3��
 * - 2007ǯ1��31���η���������2007ǯ1��1��
 * - 2007ǯ1��1���η�κǽ�����2007ǯ1��31��
 * 
 * �������绨�Ĥ˷׻����Ƥ���Τǰ����ν��������Ψ
 * - Ĺ���֤����϶��Ǥ�
 *  - 10ǯ�塿20ǯ���������κƸ��ʤ�
 *  - ���δ��֤��󤷤Ʒ׻�����Τ�Ĺ��������
 * 
 * @package  tima
 * @version  SVN: $Id: DateController.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class DateController extends DateAccessor
{

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  DateAccessor
     * @access public
     */
    function DateController(&$date_accessor)
    {
        $this->setTime($date_accessor->getTime());
    }

    /**
     * ���ꤵ�줿ǯ�����ư
     * 
     * @param  int  $year ��ư����ǯ��
     * @return void
     * @access public
     */
    function moveYear($year)
    {
        $this->moveMonth((int)$year * 12);
    }

    /**
     * ���ꤵ�줿������ư
     * 
     * @param  int  $month ��ư������
     * @return void
     * @access public
     */
    function moveMonth($month)
    {
        $repeat_num = (int)$month;

        if ($repeat_num < 0) {
            // ������������ʢ���
            $this->_reverseMonth(abs($repeat_num));
        } elseif ($repeat_num > 0) {
            // ���ʹߤ˿ʤ�ʢ���
            $this->_advanceMonth($repeat_num);
        }
    }

    /**
     * ���ꤵ�줿�������ư
     * 
     * @param  int  $week ��ư���뽵��
     * @return void
     * @access public
     */
    function moveWeek($week)
    {
        $this->_addTime((int)$week * WEEK);
    }

    /**
     * ���ꤵ�줿�������ư
     * 
     * @param  int  $day ��ư��������
     * @return void
     * @access public
     */
    function moveDay($day)
    {
        $this->_addTime((int)$day * DAY);
    }

    /**
     * ��ν����˰�ư
     * 
     * @param  void
     * @param  void
     * @access public
     */
    function moveFirstDay()
    {
        $this->setTime(strtotime(date('Y-m-1 H:i:s', $this->getTime())));
    }

    /**
     * ��κǽ����˰�ư
     * 
     * @param  void
     * @param  void
     * @access public
     */
    function moveLastDay()
    {
        $this->setTime(strtotime(date('Y-m-t H:i:s', $this->getTime())));
    }

    /**
     * �����콵�ܤ˰�ư
     * 
     * @param  void
     * @param  void
     * @access public
     */
    function moveFirstWeek()
    {
        for ($i = $this->getDay(); $i > 0; $i -= 7);
        $this->setTime(strtotime(date('Y-m-'.($i+7).' H:i:s', $this->getTime())));
    }

    /**
     * �����ॹ����פ�û�
     * 
     * @param  integer $timestamp �û����륿���ॹ�����
     * @return void 
     * @access private
     */
    function _addTime($timestamp)
    {
        $this->setTime($this->getTime() + (int)$timestamp);
    }


    /**
     * �����ʬ�������ʹߤ˷��ʤ��
     * 
     * @param  int  $repeat �����֤����ʿʤ�����
     * @return void
     * @access private
     */
    function _advanceMonth($repeat)
    {
        $original_time = $this->getTime();

        for ($i = 1, $n = (int)$repeat; $i <= $n; ++$i) {
            // ñ������Ʊ���λ
            $added_time = strtotime('+'.(string)$i.'month', $original_time);

            // ���ν����˰�ư
            $this->moveLastDay();
            $this->moveDay(1);

            // ���Ʊ���λ��̤����������ˤʤäƤ���С�
            // �����������ɾ���Ǥ���ΤǷ�̤��񤭤���
            // --------------------------------------------------
            // ��ȷ�̤����ˤʤ�ʤ����ˤϡ�
            // ����ȯ�����Ƥ���Τ����������ذ�ư���ƽ�λ
            // --------------------------------------------------
            if ((int)date('n', $added_time) === $this->getMonth()) {
                $this->setTime($added_time);
            } else {
                $this->moveLastDay();
            }
        }
    }

    /**
     * �����ʬ������������˷���᤹
     * 
     * @param  int  $repeat �����֤������������
     * @return void
     * @access private
     */
    function _reverseMonth($repeat)
    {
        $original_time = $this->getTime();

        for ($i = 1, $n = (int)$repeat; $i <= $n; ++$i) {
            // ñ�������Ʊ���λ
            $deducted_time = strtotime('-'.(string)$i.'month', $original_time);

            // ����������˰�ư
            $this->moveFirstDay();
            $this->moveDay(-1);

            // ����Ʊ���λ��̤�����������ˤʤäƤ���С�
            // �����������ɾ���Ǥ���ΤǷ�̤��񤭤���
            // --------------------------------------------------
            // ��ȷ�̤�����ˤʤ�ʤ����ˤϡ�
            // ����ȯ�����Ƥ���Τ����������ذ�ư�����ޤ޽�λ
            // --------------------------------------------------
            if ((int)date('n', $deducted_time) === $this->getMonth()) {
                $this->setTime($deducted_time);
            }
        }
    }
}
