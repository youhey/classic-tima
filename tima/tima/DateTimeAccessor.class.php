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
 * DateAccessorクラスに時間情報を拡張
 * 
 * 特定の日時情報に簡単アクセス
 * - 時／分／秒／秒を取得
 * - 日時の文字列を取得
 * - 日時を配列で取得
 * 
 * @package  tima
 * @version  SVN: $Id: DateTimeAccessor.class.php 4 2007-06-20 07:16:44Z do_ikare $
$
 */
class DateTimeAccessor extends DateAccessor
{

    /**
     * コンストラクタ
     * 
     * @param  integer|null $year  年
     * @param  integer|null $month 月
     * @param  integer|null $day   日
     * @param  integer|null $hour  時
     * @param  integer|null $min   分
     * @param  integer|null $sec   秒
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
     * 時を返却
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
     * 分を返却
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
     * 秒を返却
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
     * 日時情報を配列で返却
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
     * 日時情報を書式化した文字列で返却
     * 
     * @param  string $format date()関数の書式
     * @return string 
     * @access public
     * @see    DateAccessor::format()
     */
    function format($format = DATE_FORMAT_ISO)
    {
        return parent::format($format);
    }
}
