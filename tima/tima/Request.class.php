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
 * ���饤����ȤΥꥯ�����Ⱦ�������
 * 
 * @package  tima
 * @version  SVN: $Id: Request.class.php 6 2007-08-17 08:46:57Z do_ikare $
 * @todo     URI|URL|path��ۣ��˻Ȥ�ʬ�����Ƥ���Τ�����������
 */
class Request
{

    /**
     * �ꥯ��������
     * 
     * @var    array
     * @access private
     */
    var $_parameters = array();

    /**
     * ���󥹥ȥ饯��
     * 
     * �ꥯ�������ѿ��ϰ����ǻ��ꤵ�줿�᥽�åɤ�����ͭ���ˤʤ�
     * ��������ά���줿���ˤϥꥯ�����ȡ��᥽�åɤΤߤ��оݤˤ���
     * 
     * - g: $_GET
     * - p: $_POST
     * - c: $_COOKIE
     * - q: $_SERVER['QUERY_STRING']
     *  - URL���󥳡��ɤ��줿�ޤޤΥ�����ʸ����
     * 
     * @param  array $method �᥽�å�̾
     * @access public
     */
    function Request($method = null)
    {
        if ($method === null) {
            switch (true) {
            case $this->isPost() : 
                $method = array('p');
                break;
            case $this->isGet() : 
                $method = array('g');
                break;
           default : 
                $method = array();
                break;
            }
        }
       if (!is_array($method)) {
           $method = (array)$method;
       }

        foreach ($method as $section) {
            switch ($section) {
            case 'g' : 
                foreach ($_GET as $varkey => $varvalue) {
                    if (strrpos($varkey, '_x') === (strlen($varkey) - 2)) {
                        $varvalue = array('x' => $varvalue);
                    } elseif (strrpos($varkey, '_y') === (strlen($varkey) - 2)) {
                        $varvalue = array('y' => $varvalue);
                    }
                    $this->set($varkey, $varvalue);
                }
                break;
            case 'p' : 
                foreach ($_POST as $varkey => $varvalue) {
                    if (strrpos($varkey, '_x') === (strlen($varkey) - 2)) {
                        $varvalue = array('x' => $varvalue);
                    } elseif (strrpos($varkey, '_y') === (strlen($varkey) - 2)) {
                        $varvalue = array('y' => $varvalue);
                    }
                    $this->set($varkey, $varvalue);
                }
                break;
            case 'c' : 
                foreach ($_COOKIE as $varkey => $varvalue) {
                    $this->set($varkey, $varvalue);
                }
                break;
            case 'q' : 
                if (!isset($_SERVER['QUERY_STRING'])) {
                    break;
                }
                foreach (explode('&', $_SERVER['QUERY_STRING']) as $query) {
                    $query    = split('=', $query);
                    $varkey   = $query[0];
                    $varvalue = (isset($query[1]) ? $query[1] : null);
                    if ($varkey === '') {
                        continue;
                    }
                    $this->set($varkey, $varvalue);
                }
                break;
            }
        }
    }

    /**
     * �����Υꥯ�������ͤ���Ͽ
     * 
     * ������Ͽ����Ƥ���С��ͤΥޡ������ߤ�
     * 1. GET���ѿ���$b�פ������array(1,2,3)��
     * 2. POST���ѿ���$b�פ�ʸ�����hoge��
     * 3. ���åȤ������ϡ�GET��POST��
     * 4. ��Ͽ����ͤϡ�array(1,2,3,hoge)��
     * 
     * @param  string $varkey   �ꥯ�����ȥ���
     * @param  mixed  $varvalue �ꥯ��������
     * @return void
     * @access public
     */
    function set($varkey, $varvalue)
    {
        // if (isset($this->_parameters[$varkey]) && 
        //     is_array($this->_parameters[$varkey])) {
        //     $varvalue = Utility::merge(
        //         $this->_parameters[$varkey], (array)$varvalue);
        // }
        $this->_parameters[$varkey] = $varvalue;
    }

    /**
     * �����Υꥯ�������ͤ��ֵ�
     * 
     * @param  string $varkey �ꥯ�����ȥ���
     * @return mixed
     * @access public
     */
    function get($varkey)
    {
        if (!is_string($varkey) || ($varkey === '')) {
            return null;
        }
        if (!isset($this->_parameters[$varkey])) {
            return null;
        }

        return $this->_parameters[$varkey];
    }

    /**
     * ���ƤΥꥯ�������ͤ�������ֵ�
     * 
     * @param  void
     * @return array
     * @access public
     */
    function getAll()
    {
        return $this->_parameters;
    }

    /**
     * ���ꤷ���᥽�åɤλ����ѿ�̾�Υꥯ�������ͤ��ֵ�
     * 
     * - �ꥯ�����ȡ��᥽�åɰʳ��Υꥯ�������ͤ�ľ�ܥ�������
     * - �оݤΥꥯ�������ͤϥ᥽�å�̾��ɾ�������ꤹ�뤳�Ȥ��Ǥ���
     *  - �ǥե���ȤǤϡ�GET��POST�פν��ɾ��
     * 
     * @param  string $varkey �ꥯ�����ȥ���
     * @param  array  $order  �᥽�åɤ�̾��
     * @return mixed
     * @access public
     */
    function getAcceptRequest($varkey, $order = array('g', 'p'))
    {
        $varvalue = null;

        if (!is_array($order)) {
            $order = array();
        }
        foreach ($order as $method) {
            // �ͤ�null�ʤ�¸�ߤ��ʤ���Τ�Ƚ�Ǥ���
            switch ($method) {
            case 'g' : 
                if (isset($_GET[$varkey])) {
                    $varvalue = $_GET[$varkey];
                }
                break;
            case 'p' : 
                if (isset($_POST[$varkey])) {
                    $varvalue = $_POST[$varkey];
                }
                break;
            case 'c' : 
                if (isset($_COOKIE[$varkey])) {
                    $varvalue = $_COOKIE[$varkey];
                }
                break;
            case 'f' : 
                if (isset($_FILES[$varkey])) {
                    $varvalue = $_FILES[$varkey];
                }
                break;
            case 'q' : 
                if (!isset($_SERVER['QUERY_STRING'])) {
                    break;
                }
                foreach (explode('&', $_SERVER['QUERY_STRING']) as $query) {
                    $query       = split('=', $query);
                    $query_name  = $query[0];
                    $query_value = (isset($query[1]) ? $query[1] : null);
                    if ($query_name === $varkey) {
                        if (isset($query_value)) {
                            $varvalue = $query_value;
                        }
                        break;
                    }
                }
                break;
            }
        }

        return $varvalue;
    }

    /**
     * �Ķ��ѿ���PATH_INFO��Ϣ��������ֵ�
     * 
     * - /example.php/foo/1/bar/2/hoge/3 => array('foo'=>1, 'bar'=>2, 'hoge'=>3)
     * 
     * @param  void
     * @return array
     * @access public
     */
    function getPathInfo()
    {
        $request = $this->f('PATH_INFO');
        if ($request === null) {
            $request = $this->getEnv('ORIG_PATH_INFO');
        }
        if ($request === null) {
            $request = '';
        }
        $query     = explode('/', trim($request, '/'));
        $path_info = array();
        for ($i = 0; $i < count($query); $i += 2) {
            $path_info[$query[$i]] = 
                (isset($query[$i + 1]) ? $query[$i + 1] : null);
        }

        return $path_info;
    }

    /**
     * ���ꤵ�줿�ѿ�̾�Υꥯ�����Ȥ�¸�ߤ��뤫�򸡺�
     * 
     * @param  string $varkey �ꥯ�����ȥ���
     * @return boolean
     * @access public
     */
    function exists($varkey)
    {
        // �ͤ�null�Υꥯ�����Ȥϡ�¸�ߤ��ʤ��פ�ɾ������
        // �ͤ�null����Ͽ����Ȥ����԰٤�¸�ߤ�õ���Τȹͤ���
        return 
            isset($this->_parameters[$varkey]);
    }

    /**
     * �ꥯ�����Ȥ��ѿ�̾�������ֵ�
     * 
     * @param  void
     * @return array
     * @access public
     */
    function getNames()
    {
        return 
            array_keys($this->_parameters);
    }

    /**
     * �Ķ��ѿ����ͤ��ֵ�
     * - ����ʸ�������ƽ���뤿�ᡢ�ͤ��ƶ���������ǽ������
     * 
     * @param  string $varkey
     * @return string|null
     * @static string $ctrl_cahr
     * @access public
     */
    function getEnv($varkey)
    {
        $varvalue = 
            isset($_SERVER[$varkey]) ? 
                Utility::to('EraseCtrlChar', $_SERVER[$varkey]) : null;

        return $varvalue;
    }

    /**
     * �ꥯ�����Ȥ��줿�ۥ��ȥ����Ф�̾�����ֵ�
     * - �ꥯ�����ȥإå��Ρ�host�פ���
     *  - HTTP/1.0�Ǥ�¸�ߤ��ʤ�
     *  - HTTP/1.1�Ǥ�ꥯ�����Ȥˤ�äƤ�¸�ߤ��ʤ�
     *  - �ꥯ�����Ȥ˥ݡ����ֹ椬����Хݡ����ֹ��ޤ�
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getHost()
    {
        $host = $this->getEnv('HTTP_X_FORWARDED_HOST');
        if ($host === null) {
            $host = $this->getEnv('HTTP_HOST');
        }
        if ($host === null) {
            $host = '';
        }

        return $host;
    }

    /**
     * �꥽�����Υۥ��ȡ������Ф�̾�����ֵ�
     * - �����Ф����ꤵ��Ƥ���̾��
     *  - Apache�Ǥ���С�ServerName�ץǥ��쥯�ƥ��֤�������
     *  - �С������ۥ��ȤǤϥ����Ф�����ˤ�äƤ�
     *    Request::getHost()���ͤȰۤʤ��ǽ������
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getServerName()
    {
        $server_name = $this->getEnv('HTTP_X_FORWARDED_SERVER');
        if ($server_name === null) {
            $server_name = $this->getEnv('SERVER_NAME');
        }
        if ($server_name === null) {
            $server_name = '';
        }

        return $server_name;
    }

    /**
     * ��ե��顼���ֵ�
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getReferer()
    {
        $referer = $this->getEnv('HTTP_REFERER');
        if ($referer === null) {
            $referer = '';
        }

        return $referer;
    }

    /**
     * �¹Ԥ��Ƥ��륹����ץȤΥɥ�����ȥ롼�Ȥ���Υѥ����ֵ�
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getScriptName()
    {
        $script_name = $this->getEnv('SCRIPT_NAME');
        if ($script_name === null) {
            $script_name = $this->getEnv('ORIG_SCRIPT_NAME');
        }
        if ($script_name === null) {
            $script_name = '';
        }

        return $script_name;
    }

    /**
     * �¹Ԥ��Ƥ����main�ȤʤäƤ���˥�����ץȤΥѥ����ֵ�
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getMainPath()
    {
        $script_filename = $this->getEnv('SCRIPT_FILENAME');
        if ($script_filename === null) {
            $script_filename = $this->getEnv('ORIG_SCRIPT_FILENAME');
        }
        if ($script_filename === null) {
            $script_filename = '';
        }

        return $script_filename;
    }

    /**
     * �꥽�����Υ���������ֵ�
     * - http:
     * - https:
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getScheme()
    {
        $scheme = ($this->isSecure() ? 'https:' : 'http:');

        return $scheme;
    }

    /**
     * �꥽�����Υ�������ȥۥ��Ȥ�̾�����ֵ�
     * - http://example.com
     * - https://example.com
     * - http://example.com:8080
     * 
     * @param  void
     * @return string
     * @access public
     * @todo ͥ��Ū�ˡ�HTTP_HOST�פ��ͤ���Ѥ���Τǡ�
     *       �ݡ����ֹ椬�Ȥ��Ƥ���ȡ�http://example.com:8080:8080��
     *       �Ȥ����ͤ��ֵѤ����ǽ��������Τ��׽���
     */
    function getHostUri()
    {
        $host_name = $this->getHost();
        if ($host_name === '') {
            $host_name = $this->getServerName();
        }
        $host_uri = $this->getScheme() . '//' . $host_name;
        $std_port = ($this->isSecure() ? '443' : '80');
        $port_no  = $this->getEnv('SERVER_PORT');
        if (isset($port_no) && ($port_no !== '') && ($port_no !== $std_port)) {
            $host_uri .= ':' . $port_no;
        }

        return $host_uri;
    }

    /**
     * �꥽�����Υѥ����ֵ�
     * - /foo/bar/hoge.html
     * - /foo/bar/
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getPathUri()
    {
        $path = $this->getEnv('REQUEST_URI');
        if ($path === null) {
            $path = '';
        }
        if (preg_match('/^(?:http)(?:s)?:\/\//', $path)) {
            $url  = parse_url($path);
            $path = $url['path'];
        }
        if (($i = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $i);
        }

        return $path;
    }

    /**
     * �꥽������URL���ֵ�
     * - http://example.com/
     * - https://example.com/foo/bar/
     * - http://example.com:8080/foo/bar/hoge.html
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getUrl()
    {
        $url = $this->getHostUri() . $this->getPathUri();

        return $url;
    }

    /**
     * �ꥯ�����ȡ��᥽�åɤ��ֵ�
     * - GET
     * - POST
     * - PUT
     * - DELETE
     * - HEAD
     * - ����ʳ��Υ᥽�åɤϡ�GET�פˤ�����
     *
     * @param  void
     * @return string
     * @access public
     */
    function getMethod()
    {
        $method = $this->getEnv('REQUEST_METHOD');
        switch ($method) {
        case 'GET' : 
        case 'POST' : 
        case 'PUT' : 
        case 'DELETE' : 
        case 'HEAD' : 
            break;
        default : 
            $method = 'GET';
        }

        return $method;
    }

    /**
     * �ꥯ�����ȤΥ᥽�åɤ���GET�פ��򸡺�
     *
     * @param  void
     * @return boolean
     * @access public
     */
    function isGet()
    {
        return 
            ($this->getMethod() === 'GET');
    }

    /**
     * �ꥯ�����ȤΥ᥽�åɤ���POST�פ��򸡺�
     *
     * @param  void
     * @return boolean
     * @access public
     */
    function isPost()
    {
        return 
            ($this->getMethod() === 'POST');
    }

    /**
     * �ꥯ�����ȤΥ᥽�åɤ���PUT�פ��򸡺�
     *
     * @param  void
     * @return boolean
     * @access public
     */
    function isPut()
    {
        return 
            ($this->getMethod() === 'PUT');
    }

    /**
     * �ꥯ�����ȤΥ᥽�åɤ���DELETE�פ��򸡺�
     *
     * @param  void
     * @return boolean
     * @access public
     */
    function isDelete()
    {
        return 
            ($this->getMethod() === 'DELETE');
    }

    /**
     * �ꥯ�����ȤΥ᥽�åɤ���HEAD�פ��򸡺�
     *
     * @param  void
     * @return boolean
     * @access public
     */
    function isHead()
    {
        return 
            ($this->getMethod() === 'HEAD');
    }

    /**
     * �ꥯ�����Ȥ�HTTPS�ץ�ȥ���ˤ��Ź��̿����򸡺�
     *
     * @param  void
     * @return boolean
     * @access public
     */
    function isSecure()
    {
        return (
            ($this->getEnv('HTTPS') === 'on') || 
            ($this->getEnv('HTTP_X_FORWARDED_PROTO') === 'https'));
    }
}
