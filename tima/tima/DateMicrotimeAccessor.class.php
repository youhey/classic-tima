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
 * DateTimeAccessorクラスにマイクロ秒の情報を拡張
 * 
 * @package  tima
 * @version  SVN: $Id: DateMicrotimeAccessor.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class DateMicrotimeAccessor extends DateTimeAccessor
{

    /**
     * マイクロ秒
     * 
     * @var    float 
     * @access private
     */
    var $_microsec;

    /**
     * コンストラクタ
     * 
     * @param  integer|null $year  年
     * @param  integer|null $month 月
     * @param  integer|null $day   日
     * @param  integer|null $hour  時
     * @param  integer|null $min   分
     * @param  integer|null $sec   秒
     * @param  float|null   $msec  マイクロ秒
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
     * マイクロ秒を返却
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

        $date_array['microsec'] = $this->getMicrosec();

        return $date_array;
    }

    /**
     * 日時情報を書式化した文字列で返却
     * 
     * フォーマットに「s」があれば、秒をマイクロ秒に補正
     * 
     * @param  string $format date()関数の書式
     * @return string 
     * @access public
     * @see    DateAccessor::format()
     * @todo   置換処理、要検討
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
     * マイクロ秒を設定
     * 
     * @param  float 
     * @return void
     * @access private
     * @todo   マイナスのマイクロ秒の処理、要検討
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
            // この計算なにも考えてません
            // | -5.253 => -6秒 && 0.747マイクロ秒 | これは正常？
            $ceilsec   = floor($microsec);
            $microsec += abs($ceilsec);
            $this->setTime($this->getTime() + (int)$ceilsec);
            break;
        }
        $this->_microsec = $microsec;
    }
}
