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
 * @version  SVN: $Id: Request.class.php 37 2007-10-12 06:51:54Z do_ikare $
 * @todo     URI|URL|path��ۣ��ʤΤ����Τ�������
 * @todo     �ե�����Υ��åץ��ɤ��б�����
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
     * - ����᥽�åɤΥꥯ�������ѿ��Τߤ�ͭ���ˤ���
     *  - g: $_GET
     *  - p: $_POST
     * - ͭ���ˤ���᥽�åɤ������ʣ�������ǽ
     * - �᥽�åɤ���ά���줿���ˤϥꥯ�����ȤΥ᥽�åɤ��оݤˤ���
     * 
     * @param  array $method �᥽�å�̾
     * @access public
     */
    function Request($method = null)
    {
        if ($method === null) {
            $method = array();
                if ($this->isPost()) $method = array('p');
            elseif ($this->isGet())  $method = array('g');
        }
        if (!is_array($method)) {
            $method = (array)$method;
        }

        foreach ($method as $section) {
            switch ($section) {
            case 'g' : $this->_fetchGetParameters();
                break;
            case 'p' : $this->_fetchPostParameters();
                break;
            }
        }
    }

    /**
     * �ꥯ�������ѿ����ͤ��ֵ�
     * 
     * @param  string $varkey
     * @return mixed
     * @access public
     */
    function get($varkey)
    {
        $varvalue = 
            isset($this->_parameters[$varkey]) ? $this->_parameters[$varkey] : null;

        return $varvalue;
    }

    /**
     * �ꥯ�������ѿ����ͤ���
     * 
     * @param  string $varkey
     * @param  mixed  $varvalue
     * @return void
     * @access public
     */
    function set($varkey, $varvalue)
    {
        if ($this->exists($varkey)) {
            $this->_parameters[$varkey] = $varvalue;
        } else {
            trigger_error("Nonexistent request::${varkey}", E_USER_NOTICE);
        }
    }

    /**
     * ���ꤵ�줿�ѿ�̾�Υꥯ�����Ȥ�¸�ߤ��뤫�򸡺�
     * - �ͤ�null�ʤ��¸�ߤ��ʤ��פ�ɾ������
     *  - �ͤ�null����Ͽ�����¸�ߤ�õ����Ƚ��
     * 
     * @param  string $varkey
     * @return boolean
     * @access public
     */
    function exists($varkey)
    {
        return 
            isset($this->_parameters[$varkey]);
    }

    /**
     * ���ƤΥꥯ�����Ȥ�������ֵ�
     * 
     * @return array
     * @access public
     */
    function getAll()
    {
        return $this->_parameters;
    }

    /**
     * �ꥯ�����Ȥ��ѿ�̾�������ֵ�
     * 
     * @return array
     * @access public
     */
    function getNames()
    {
        return 
            array_keys($this->_parameters);
    }

    /**
     * ����᥽�åɤΥꥯ�����Ȥ��ֵ�
     * - PHP����Υꥯ�������ѿ���ľ�ܥ�������
     *  - �ե졼�����Υե��륿��󥰤ʤɤ˱ƶ�����ʤ����ǡ���
     * - �������ɤ߹���᥽�åɤ�����ǽ
     *  - g: $_GET
     *  - p: $_POST
     *  - c: $_COOKIE
     *  - q: $_SERVER['QUERY_STRING']
     * - �᥽�å��̤�Ʊ̾���ѿ���¸�ߤ���������Ǿ��
     *  - �ǥե���ȤǤϡ�GET��POST�פν����ɾ��
     * 
     * @param  string $varkey
     * @param  array  $order
     * @return mixed
     * @access public
     */
    function getAcceptRequest($varkey, $order = array('g', 'p'))
    {
        $varvalue = null;

        if (!is_array($order)) $order = (array)$order;

        foreach ($order as $method) {
            // �ͤ�null�ʤ�¸�ߤ��ʤ���Τ�Ƚ�Ǥ���
            switch ($method) {
            case 'g' : if (isset($_GET[$varkey])) $varvalue = $_GET[$varkey];
                break;
            case 'p' : if (isset($_POST[$varkey])) $varvalue = $_POST[$varkey];
                break;
            case 'c' : if (isset($_COOKIE[$varkey])) $varvalue = $_COOKIE[$varkey];
                break;
            case 'q' : 
                $query_string = $this->getEnv('QUERY_STRING');
                if ($query_string !== null) {
                    parse_str($query_string, $params);
                    if (isset($params[$varkey])) $varvalue = $params[$varkey];
                }
                break;
            }
        }

        return $varvalue;
    }

    /**
     * �Ķ��ѿ���PATH_INFO�פ�Ϣ��������ֵ�
     * - /example.php/foo/1/bar/2/hoge/3 => array('foo'=>1, 'bar'=>2, 'hoge'=>3)
     * 
     * @param  void
     * @return array
     * @access public
     */
    function getPathInfo()
    {
        $request = $this->getEnv('PATH_INFO');
        if ($request === null) $request = $this->getEnv('ORIG_PATH_INFO');
        if ($request === null) $request = '';

        $query     = explode('/', trim($request, '/'));
        $path_info = array();
        for ($i = 0; $i < count($query); $i += 2) {
            $path_info[$query[$i]] = isset($query[$i + 1]) ? $query[$i + 1] : null;
        }

        return $path_info;
    }

    /**
     * �Ķ��ѿ����ֵ�
     * - ����ʸ�������ƽ���뤿�ᡢ�ͤ��ƶ���������ǽ������
     * 
     * @param  string $varkey
     * @return string|null
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
     * @return string
     * @access public
     */
    function getHost()
    {
        $host = $this->getEnv('HTTP_X_FORWARDED_HOST');
        if ($host === null) $host = $this->getEnv('HTTP_HOST');
        if ($host === null) $host = '';

        return $host;
    }

    /**
     * �꥽�����Υۥ��ȡ������Ф�̾�����ֵ�
     * - �����Ф����ꤵ��Ƥ���̾��
     *  - Apache�Ǥ���С�ServerName�ץǥ��쥯�ƥ��֤�������
     *  - �С������ۥ��ȤǤϥ����Ф�����ˤ�äƤ�
     *    Request::getHost()���ͤȰۤʤ��ǽ������
     * 
     * @return string
     * @access public
     */
    function getServerName()
    {
        $server_name = $this->getEnv('HTTP_X_FORWARDED_SERVER');
        if ($server_name === null) $server_name = $this->getEnv('SERVER_NAME');
        if ($server_name === null) $server_name = '';

        return $server_name;
    }

    /**
     * ��ե��顼���ֵ�
     * 
     * @return string
     * @access public
     */
    function getReferer()
    {
        $referer = $this->getEnv('HTTP_REFERER');
        if ($referer === null) $referer = '';

        return $referer;
    }

    /**
     * �¹Ԥ��Ƥ��륹����ץȤΥɥ�����ȥ롼�Ȥ���Υѥ����ֵ�
     * 
     * @return string
     * @access public
     */
    function getScriptName()
    {
        $script_name = $this->getEnv('SCRIPT_NAME');
        if ($script_name === null) $script_name = $this->getEnv('ORIG_SCRIPT_NAME');
        if ($script_name === null) $script_name = '';

        return $script_name;
    }

    /**
     * �¹Ԥ��Ƥ����main�ȤʤäƤ���˥�����ץȤΥѥ����ֵ�
     * 
     * @return string
     * @access public
     */
    function getMainPath()
    {
        $script_filename = $this->getEnv('SCRIPT_FILENAME');
        if ($script_filename === null) 
            $script_filename = $this->getEnv('ORIG_SCRIPT_FILENAME');
        if ($script_filename === null) $script_filename = '';

        return $script_filename;
    }

    /**
     * �꥽�����Υ���������ֵ�
     * - http:
     * - https:
     * 
     * @return string
     * @access public
     */
    function getScheme()
    {
        $scheme = $this->isSecure() ? 'https:' : 'http:';

        return $scheme;
    }

    /**
     * �꥽�����Υ�������ȥۥ��Ȥ�̾�����ֵ�
     * - http://example.com
     * - https://example.com
     * - http://example.com:8080
     * 
     * @return string
     * @access public
     * @todo ͥ��Ū�ˡ�HTTP_HOST�פ��ͤ���Ѥ���Τǡ�
     *       �ݡ����ֹ椬�Ȥ��Ƥ���ȡ�http://example.com:8080:8080��
     *       �Ȥ����ͤ��ֵѤ����ǽ��������Τ��׽���
     */
    function getHostUri()
    {
        $host_name = $this->getHost();
        if ($host_name === '') $host_name = $this->getServerName();

        $host_uri = $this->getScheme() . '//' . $host_name;
        $std_port = $this->isSecure() ? '443' : '80';
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
     * @return string
     * @access public
     */
    function getPathUri()
    {
        $path = $this->getEnv('REQUEST_URI');
        if ($path === null) $path = '';

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
     * @return string
     * @access public
     */
    function getUrl()
    {
        return 
            $this->getHostUri() . $this->getPathUri();
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
     * @return boolean
     * @access public
     */
    function isSecure()
    {
        return 
            ($this->getEnv('HTTPS') === 'on') || 
            ($this->getEnv('HTTP_X_FORWARDED_PROTO') === 'https');
    }

    /**
     * GET�᥽�åɤΥꥯ�������ѿ��������
     * 
     * @access private
     * @todo   ���᡼�����ܥ���Υ���å��ͤ򥹥ޡ��Ȥ˼�����
     *         if (strrpos($varkey, '_x') === (strlen($varkey) - 2))
     *         if (strrpos($varkey, '_y') === (strlen($varkey) - 2))
     */
    function _fetchGetParameters()
    {
        foreach ($_GET as $varkey => $varvalue) {
            $this->_parameters[$varkey] = $varvalue;
        }
    }

    /**
     * POST�᥽�åɤΥꥯ�������ѿ��������
     * 
     * @return void
     * @access private
     * @todo   ���᡼�����ܥ���Υ���å��ͤ򥹥ޡ��Ȥ˼�����
     *         if (strrpos($varkey, '_x') === (strlen($varkey) - 2))
     *         if (strrpos($varkey, '_y') === (strlen($varkey) - 2))
     */
    function _fetchPostParameters()
    {
        foreach ($_POST as $varkey => $varvalue) {
            $this->_parameters[$varkey] = $varvalue;
        }
    }
}
