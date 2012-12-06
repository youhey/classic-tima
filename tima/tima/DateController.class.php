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
 * 日付情報を抽象的に操作
 * 
 * 慣習を考慮しつつ、日付を簡単操作
 * - 2007年1月31日の翌日を計算
 * - 2007年1月31日の前日を計算
 * - 2007年1月31日の翌週を計算
 * - 2007年1月31日の前の週を計算
 * - 2007年1月31日の翌月は2007年2月28日
 * - 2007年3月31日の前月も2007年2月28日
 * - 2004年2月29日の翌年は2005年2月28日
 * - 2007年1月31日の月の第一週は2007年1月3日
 * - 2007年1月31日の月の第一日は2007年1月1日
 * - 2007年1月1日の月の最終日は2007年1月31日
 * 
 * 慣習を大雑把に計算しているので一部の処理は非効率
 * - 長期間の操作は苦手です
 *  - 10年後／20年前の日時の再現など
 *  - その期間を巡回して計算するので長いと不利
 * 
 * @package  tima
 * @version  SVN: $Id: DateController.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class DateController extends DateAccessor
{

    /**
     * コンストラクタ
     * 
     * @param  DateAccessor
     * @access public
     */
    function DateController(&$date_accessor)
    {
        $this->setTime($date_accessor->getTime());
    }

    /**
     * 指定された年数を移動
     * 
     * @param  int  $year 移動する年数
     * @return void
     * @access public
     */
    function moveYear($year)
    {
        $this->moveMonth((int)$year * 12);
    }

    /**
     * 指定された月数を移動
     * 
     * @param  int  $month 移動する月数
     * @return void
     * @access public
     */
    function moveMonth($month)
    {
        $repeat_num = (int)$month;

        if ($repeat_num < 0) {
            // 前月以前に戻る（←）
            $this->_reverseMonth(abs($repeat_num));
        } elseif ($repeat_num > 0) {
            // 翌月以降に進む（→）
            $this->_advanceMonth($repeat_num);
        }
    }

    /**
     * 指定された週数を移動
     * 
     * @param  int  $week 移動する週数
     * @return void
     * @access public
     */
    function moveWeek($week)
    {
        $this->_addTime((int)$week * WEEK);
    }

    /**
     * 指定された日数を移動
     * 
     * @param  int  $day 移動する日数
     * @return void
     * @access public
     */
    function moveDay($day)
    {
        $this->_addTime((int)$day * DAY);
    }

    /**
     * 月の初日に移動
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
     * 月の最終日に移動
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
     * 月の第一週目に移動
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
     * タイムスタンプを加算
     * 
     * @param  integer $timestamp 加算するタイムスタンプ
     * @return void 
     * @access private
     */
    function _addTime($timestamp)
    {
        $this->setTime($this->getTime() + (int)$timestamp);
    }


    /**
     * 指定数分だけ翌月以降に月を進める
     * 
     * @param  int  $repeat 繰り返し数（進む月数）
     * @return void
     * @access private
     */
    function _advanceMonth($repeat)
    {
        $original_time = $this->getTime();

        for ($i = 1, $n = (int)$repeat; $i <= $n; ++$i) {
            // 単純な翌月同日の試算
            $added_time = strtotime('+'.(string)$i.'month', $original_time);

            // 翌月の初日に移動
            $this->moveLastDay();
            $this->moveDay(1);

            // 翌月同日の試算結果が正しく翌月になっていれば、
            // 試算が正しいと評価できるので結果を上書きする
            // --------------------------------------------------
            // 試算家結果が翌月にならない場合には、
            // 誤差が発生しているので翌月の末日へ移動して終了
            // --------------------------------------------------
            if ((int)date('n', $added_time) === $this->getMonth()) {
                $this->setTime($added_time);
            } else {
                $this->moveLastDay();
            }
        }
    }

    /**
     * 指定数分だけ前月以前に月を戻す
     * 
     * @param  int  $repeat 繰り返し数（戻る月数）
     * @return void
     * @access private
     */
    function _reverseMonth($repeat)
    {
        $original_time = $this->getTime();

        for ($i = 1, $n = (int)$repeat; $i <= $n; ++$i) {
            // 単純な前月同日の試算
            $deducted_time = strtotime('-'.(string)$i.'month', $original_time);

            // 前月の末日に移動
            $this->moveFirstDay();
            $this->moveDay(-1);

            // 前月同日の試算結果が正しく前月になっていれば、
            // 試算が正しいと評価できるので結果を上書きする
            // --------------------------------------------------
            // 試算家結果が前月にならない場合には、
            // 誤差が発生しているので前月末日へ移動したまま終了
            // --------------------------------------------------
            if ((int)date('n', $deducted_time) === $this->getMonth()) {
                $this->setTime($deducted_time);
            }
        }
    }
}
