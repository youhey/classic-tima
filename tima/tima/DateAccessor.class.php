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
 * 日付情報オブジェクト
 * 
 * 特定の日時情報に簡単アクセス
 * - 2007年1月1日の年「2007」を取得
 * - 2007年1月1日の月「1」を取得
 * - 2007年1月1日の日「1」を取得
 * - 日付の文字列を取得
 * - 日付を配列で取得
 * 
 * アクセサメソッドとは違うものの、Dateクラスだと何か被りそうなので
 * 操作に特化しているからアクセサでも間違いではない？
 * 
 * @package  tima
 * @version  SVN: $Id: DateAccessor.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class DateAccessor
{

    /**
     * タイムスタンプ
     * 
     * @var    integer 
     * @access private
     */
    var $_timestamp;

    /**
     * コンストラクタ
     * 
     * @param  integer|null $year  年
     * @param  integer|null $month 月
     * @param  integer|null $day   日
     * @access public
     */
    function DateAccessor($year = null, $month = null, $day = null)
    {
        $stamp = time();
        foreach(array('Y'=>'year', 'n'=>'month', 'j'=>'day') as $key => $var) {
            $$var = (int)(isset($$var) ? $$var : date($key, $stamp));
        }
        $this->setTime(mktime(0, 0, 0, $month, $day, $year));
    }

    /**
     * 年を返却
     * 
     * @param  void
     * @return integer 
     * @access public
     */
    function getYear()
    {
        return (int)date('Y', $this->getTime());
    }

    /**
     * 月を返却
     * 
     * @param  void
     * @return integer 
     * @access public
     */
    function getMonth()
    {
        return (int)date('n', $this->getTime());
    }

    /**
     * 日を返却
     * 
     * @param  void
     * @return integer 
     * @access public
     */
    function getDay()
    {
        return (int)date('j', $this->getTime());
    }

    /**
     * 日時情報を配列で返却
     * 
     * @param  void 
     * @return array 
     * @access public
     */
    function toArray()
    {
        return array(
                'year'   => $this->getYear(), 
                'month'  => $this->getMonth(), 
                'day'    => $this->getDay(), 
            );
    }

    /**
     * 日時情報を書式化した文字列で返却
     * 
     * @param  string $format date()関数の書式
     * @return string 
     * @access public
     */
    function format($format = DATE_FORMAT_SIMPLEDATE)
    {
        return date($format, $this->getTime());
    }

    /**
     * タイムスタンプを設定
     * 
     * @param  integer $timestamp タイムスタンプ
     * @return void 
     * @access protected
     */
    function setTime($timestamp)
    {
        $this->_timestamp = (int)$timestamp;
    }

    /**
     * タイムスタンプを返却
     * 
     * @param  void 
     * @return integer 
     * @access protected
     */
    function getTime()
    {
        return $this->_timestamp;
    }
}
