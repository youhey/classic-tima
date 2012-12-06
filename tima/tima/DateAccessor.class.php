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
 * ���վ��󥪥֥�������
 * 
 * �������������˴�ñ��������
 * - 2007ǯ1��1����ǯ��2007�פ����
 * - 2007ǯ1��1���η��1�פ����
 * - 2007ǯ1��1��������1�פ����
 * - ���դ�ʸ��������
 * - ���դ�����Ǽ���
 * 
 * ���������᥽�åɤȤϰ㤦��ΤΡ�Date���饹���Ȳ�����ꤽ���ʤΤ�
 * �����ò����Ƥ��뤫�饢�������Ǥ�ְ㤤�ǤϤʤ���
 * 
 * @package  tima
 * @version  SVN: $Id: DateAccessor.class.php 4 2007-06-20 07:16:44Z do_ikare $
 */
class DateAccessor
{

    /**
     * �����ॹ�����
     * 
     * @var    integer 
     * @access private
     */
    var $_timestamp;

    /**
     * ���󥹥ȥ饯��
     * 
     * @param  integer|null $year  ǯ
     * @param  integer|null $month ��
     * @param  integer|null $day   ��
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
     * ǯ���ֵ�
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
     * ����ֵ�
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
     * �����ֵ�
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
     * ���������������ֵ�
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
     * ���������񼰲�����ʸ������ֵ�
     * 
     * @param  string $format date()�ؿ��ν�
     * @return string 
     * @access public
     */
    function format($format = DATE_FORMAT_SIMPLEDATE)
    {
        return date($format, $this->getTime());
    }

    /**
     * �����ॹ����פ�����
     * 
     * @param  integer $timestamp �����ॹ�����
     * @return void 
     * @access protected
     */
    function setTime($timestamp)
    {
        $this->_timestamp = (int)$timestamp;
    }

    /**
     * �����ॹ����פ��ֵ�
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
