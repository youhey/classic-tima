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
 * �ե��ȡ�����ȥ���
 * 
 * @package  tima
 * @version  SVN: $Id: Front.class.php 37 2007-10-12 06:51:54Z do_ikare $
 */
class Front
{

    /**
     * �ƥ�ץ졼�ȡ��ե�����γ�ĥ��
     * 
     * @var    string
     * @access protected
     */
    var $templateExt = '.html';

    // /**
    //  * ��������󡦥���ȥ�������־���
    //  * 
    //  * @var    mixed
    //  * @access protected
    //  */
    // var $actionLocation = null;

    /**
     * �¹ԥץ���
     * 
     * @var    Process
     * @access private
     */
    var $_process = null;

    /**
     * �ӥ塼
     * 
     * @var    View
     * @access private
     */
    var $_view = null;

    /**
     * �ꥯ������
     * 
     * @var    Request
     * @access private
     */
    var $_request = null;

    /**
     * ���å����
     * 
     * @var    session
     * @access private
     */
    var $_session = null;

    /**
     * �쥹�ݥ�
     * 
     * @var    Response
     * @access private
     */
    var $_response = null;

    /**
     * �桼���������������
     * 
     * @var    UserAgent
     * @access private
     */
    var $_userAgent = null;

    /**
     * �ե��륿
     * 
     * @var    array
     * @access private
     */
    var $_filters = array();

    /**
     * ����
     * 
     * @var    Config
     * @access private
     */
    var $_config = null;

    /**
     * ����
     * 
     * @var    Logger
     * @access private
     */
    var $_log = null;

    /**
     * ��ư���ϻ���
     * 
     * @var    DateMicrotimeAccessor
     * @access private
     */
    var $_beginning = null;

    /**
     * ���󥹥ȥ饯��
     * - �㳰��ȯ��������۾ｪλ
     *  - ���������饹��¸�ߤ��ʤ�
     *   - ���顼����Logger '������̾��' not found
     *   - �쥹�ݥ󥹡�500 Internal Server Error
     * 
     * @param  void
     * @access public
     */
    function Front()
    {
        $this->_beginning = &new DateMicrotimeAccessor;
        $this->_process   = &new Process;
        $this->_request   = &new Request;
        $this->_response  = &new Response;
        $this->_config    = &new Config;
        $this->_userAgent = &new UserAgent;

        // ����ե�������ɤ߹���
        $root_etc = ROOT_PATH . DS . 'etc';
        $app_etc  = $this->getAppDir() . DS . 'etc';
        $this->_config->setEtcDir($root_etc);
        if ($root_etc !== $app_etc) {
            $this->_config->setEtcDir($app_etc);
        }
        $reading_result = $this->_config->readConfig('all');
        if (!$reading_result) {
            trigger_error(
                "Unable to read the configuration 'all-ini.php'", 
                E_USER_WARNING);
        }

        // ����ȥ����ͭ������ե�������ɤ߹���
        $this->_config->readConfig(get_class($this));

        // ���å��������
        $this->_session = 
            &new Session(
                '__Front', 
                $this->_config->get('session_name', 'env'), 
                $this->_config->get('session_lifetime', 'env'));

        // �ӥ塼�����
        $basedir = $this->getAppDir() . DS . 'templates';
        $device  = $this->_userAgent->isMobile() ? 'mobile' : 'pc';
        $this->_view = 
            &new Smarty4View(
                $this->getInternalEncoding(), $this->getContentsEncoding(), 
                Utility::merge(
                    $this->_config->getByNamespace('template'), 
                    array(
                        'template_dir' => $basedir . DS . 'template' . DS . $device, 
                        'compile_dir'  => $basedir . DS . 'templates_c')));
        $this->_view->registerObject('Request', $this->_request);
        $this->_view->registerObject('UserAgent', $this->_userAgent);
        $this->_view->registerObject('Date', $this->_beginning);

        // �������
        $logger_class = $this->_config->get('logger_class', 'env');
        if (($logger_class === null) || !class_exists($logger_class)) {
            header('HTTP/1.0 500 Internal Server Error');
            trigger_error("Logger '${logger_class}' not found", E_USER_ERROR);
            exit;
        }
        $this->_log = 
            &new $logger_class(
                $this->_config->get('log_level', 'env'), 
                $this->_config->get('log_option', 'env'));
    }

    /**
     * ����ȥ���������ʥ����ѡ����饹�Ǥ϶���
     * 
     * �Ѿ����饹�ǽ�������Ф�������򥪡��С��饤��
     * ���ν���������ϥ������¹Ԥΰ��ֺǽ�˸ƤӽФ����
     * 
     * @param  void
     * @return void
     * @access public
     */
    function initialize() {}

    /**
     * �ե��ȡ�����ȥ����¹�
     * - �¹Էײ褫�鿷�����������������ƥץ�����¹�
     * - �㳰��ȯ��������۾ｪλ
     *  - �롼�ƥ��󥰤˼��ԡü¹Էײ褬����
     *   - ���顼����Unable to dispatch process in mapping
     *   - �쥹�ݥ󥹡�500 Internal Server Error
     * 
     * @param  void
     * @return void
     * @access public
     * @see    Front::route()
     * @see    Front::dispatch()
     * @see    Front::process()
     * @see    Front::_applyFiltersBeforeMethod()
     * @see    Front::_applyFiltersAfterMethod()
     */
    function start($mapping = null)
    {
        // �ե��ȡ�����ȥ��������
        $this->initialize();

        // �����ե��륿
        $this->_applyFiltersBeforeMethod();

        // �ꥯ�����Ȥ�롼�ƥ��󥰢��¹Էײ�����
        if ($mapping === null) {
            $mapping = $this->route();
        }
        if (!is_array($mapping) || !isset($mapping['path'])) {
            header('HTTP/1.0 500 Internal Server Error');
            trigger_error('Unable to dispatch process in mapping', E_USER_ERROR);
            exit;
        }
        if (!array_key_exists('method', $mapping)) {
            $mapping['method'] = null;
        }

        // �¹Էײ袪��������������ץ�������Ͽ
        $this->dispatch($mapping['path'], $mapping['method']);

        // ��������󡦥���ȥ��������ե�������ɤ߹���
        $this->_config->readConfig($mapping['path']);

        // ����������¹�
        $this->process();

        // ����ե��륿
        $this->_applyFiltersAfterMethod();

        // ��̤����
        $this->_response->flush();
    }

    /**
     * ������쥯��
     * - �쥹�ݥ󥹤˥�����쥯�ȤΤ���Υإå��������Ͽ
     * - �쥹�ݥ󥹤˥�����쥯�ȤΤ���Υ���ƥ�Ĥ���Ͽ
     *  - ���Υ᥽�åɤ����ľ�ܥ�����쥯�Ƚ�����ȯ�����ʤ�
     *  - ��¸�Υ쥹�ݥ󥹾���������쥯�Ȥξ�����ѹ��������
     *   - �᥽�åɤθƤӽФ����ǥ쥹�ݥ����Ƥ��񤭴�������Х�����쥯�Ȥ�̵��
     *   - ��¸�Υ쥹�ݥ󥹾���ϥ�����쥯�Ⱦ���ǽ񤭴������Ƽ���
     * - �ǥե���ȤΥ��ơ����������ɤϡ�302 Moved Temporarily��
     *  - RFC2616�λ��ͤǤ�302���ơ����������ɤǤϥꥯ�����Ȥ�ȯ�Ծ����Ѥ����ʤ�
     *   - POST�Υꥯ�����Ȥ�GET�ǥ�����쥯�Ȥ��뤳�ȤϤǤ��ʤ�
     *   - ����Ū�ʥ桼������������ȤǤ�302���ơ����������ɤǤ�����̤��ư���
     *    - POST�Υꥯ�����Ȥ�GET�ǥ�����쥯�Ȥ��Ƥ�ٹ�ʤ�ȯ�����ʤ�
     *  - �����CGI�ʤɤǤΥ�����쥯�Ȥˤϡ�303 See Other�פ���Ѥ���٤�
     *   - �桼������������Ȥˤ�äƤ��б����Ƥ��ʤ���Τ�����
     *   - �б����Ƥ���桼������������ȤǤ�Х�����򸫤�����
     *  - ����Ū�ʥ桼������������ȤȤθߴ������θ����302���ơ����������ɤ�
     *  - ����Ū�˷ٹ�ʤɽФ��桼������������Ȥ��ФƤ�����ƹ�
     * 
     * @param  string     $link
     * @param  array|null $params
     * @param  string     $status_code
     * @return void
     * @access public
     */
    function redirect($link, $params = array(), $status_code = '302')
    {
        $url = htmlentities($link, ENT_QUOTES, $this->getHttpCharSet());

        if (is_array($params) && (count($params) > 0)) {
            $query = array();
            foreach ($params as $varkey => $varvalue) {
                $query[] = urlencode($varkey) . '=' . urlencode($varvalue);
            }
            $url .= '?' . implode('&', $query);
        }

        $this->_response->clearHeader();
        $this->_response->setStatus($status_code);
        $this->_response->setHeader('Location', $url);

        $this->_response->setContents(
            "<html><head>\n" . 
            "<meta http-equiv=\"refresh\" content=\"0;url=${url}\" />\n" . 
            "</head></html>\n");
    }

    /**
     * ư���楳��ȥ����Ǥ�դΥ��������˥�����쥯��
     * - �ѥ�᡼����Ǥ�ե��������λ���������Ͽ
     * - ���å���󤬳��Ϥ���Ƥ���С��ѥ�᡼���˥��å���������ɲ�
     * 
     * @param  string     $action
     * @param  array|null $params
     * @return void
     * @access public
     * @see    Front::redirect()
     */
    function redirectAction($action, $params = array())
    {
        // ������������
        $query = array(
                $this->_config->get('action_key', 'env') => $action, 
            );

        // ���å�������
        if ($this->_session->isStarted()) {
            $use_cookie = $this->_config->get('use_cookie', 'session');
            if ($this->_userAgent->isMobile() || ($use_cookie !== true)) {
                $query[$this->_session->getSessionName()] = $this->_session->getId();
            }
        }

        $this->redirect($this->_request->getUrl(), Utility::merge($query, $params));
    }

    /**
     * �������
     * - �㳰��ȯ��������۾ｪλ
     *  - �ƥ�ץ졼�ȡ��ե����뤬¸�ߤ��ʤ�
     *   - ���顼����Template '�ƥ�ץ졼�ȡ��ե�����' not found
     *   - �쥹�ݥ󥹡�500 Internal Server Error'
     * 
     * @param  string $attribute
     * @return void
     * @access public
     * @see    View::isTemplateExists()
     * @see    View::render()
     * @see    Response::getDataModel()
     * @see    Response::setContents()
     */
    function render($attribute)
    {
        $template = $attribute . $this->templateExt;

        if (!$this->_view->isTemplateExists($template)) {
            header('HTTP/1.0 500 Internal Server Error');
            trigger_error("Template '${attribute}' not found", E_USER_ERROR);
            exit;
        }

        $this->_response->setContents(
            $this->_view->render($template, $this->_response->getDataModel()));
    }

    /**
     * �ե��륿����Ͽ
     * - �㳰��ȯ��������۾ｪλ
     *  - �ե��륿���ɤ߹���ʤ�
     *   - ���顼����Filter '�ե��륿��̾��' not found
     *   - �쥹�ݥ󥹡�500 Internal Server Error
     * 
     * @param  string $filter
     * @return void
     * @access public
     * @see    ClassLoader::load()
     */
    function setFilter($filter)
    {
        static $class_loader;
        if (!isset($class_loader)) {
            $class_loader = &new ClassLoader;
            $class_loader->setParents('Filter');
            $class_loader->setIncludePath(ROOT_PATH);
        }

        $class_name = $class_loader->load($filter);
        if ($class_name === '') {
            header('HTTP/1.0 500 Internal Server Error');
            trigger_error("Filter '${filter}' not found", E_USER_ERROR);
            exit;
        }

        $this->_filters[] = &new $class_name;
    }

    /**
     * ���ץꥱ�����������֤����ǥ��쥯�ȥ�����Хѥ����ֵ�
     * 
     * @param  void
     * @return string
     * @access public
     */
    function getAppDir()
    {
        $app_dir = '';

        $varvalue = defined('APP_DIR') ? APP_DIR : ROOT_PATH;
        if (OS_WINDOWS) {
            if (preg_match('/^[a-z]:/i', $varvalue) && ($varvalue{2} === DS)) {
                $app_dir = $varvalue;
            }
        } elseif ($varvalue{0} === DS) {
            $app_dir = $varvalue;
        }
        if ($app_dir === '') {
            $app_dir = realpath(ROOT_PATH . DS . $varvalue);
        }

        return $app_dir;
    }

    /**
     * ����ʸ�����󥳡��ǥ��󥰤��ֵ�
     * 
     * ����ʸ�����󥳡��ǥ��󥰤������̵ͭ�ˤ�ä�ư���Ѳ�
     * - ���ꤢ�ꡧ���ꤵ�줿�ͤ��ֵ�
     * - ����ʤ���PHP��ư��Ķ����������Ͽ => ��Ͽ�����ͤ��ֵ�
     *  - ư��Ķ�����ο�¬�ˤ�mb_internal_encoding()�ؿ������
     *  - ����������ѹ��ˤ�äƤ����������ʤ��ʤ���⤢��
     * 
     * @param  void
     * @return string|null
     * @access public
     */
    function getInternalEncoding()
    {
        $internal_encoding = $this->_config->get('internal_encoding', 'env');
        if ($internal_encoding === null) {
            $internal_encoding = mb_internal_encoding();
        }

        return $internal_encoding;
    }

    /**
     * ���Ϥ�ʸ�����󥳡��ǥ��󥰤��ֵ�
     * 
     * �����̵ͭ�ˤ�ä�ư���Ѳ�
     * - ���ꤢ�ꡧ���ꤵ�줿�ͤ��ֵ�
     * - ����ʤ���PHP��ư��Ķ����������Ͽ => ��Ͽ�����ͤ��ֵ�
     *  - ư��Ķ�����ο�¬�ˤ�mb_http_output()�ؿ������
     *  - ��������꤬��ư��������������ʤ��ʤ���⤢��
     * 
     * @param  void
     * @return string|null
     * @access public
     */
    function getContentsEncoding()
    {
        $http_encoding = $this->_config->get('contents_encoding', 'env');
        if ($http_encoding === null) {
            $http_encoding = mb_http_output();
        }

        return $http_encoding;
    }

    /**
     * ���Ϥ���HTML�Υ���饯�������åȤ��ֵ�
     * 
     * @param  void
     * @return string|null
     * @access public
     * @see    Front::getContentsEncoding()
     */
    function getHttpCharSet()
    {
        return 
            mb_preferred_mime_name($this->getContentsEncoding());
    }

    /**
     * �ꥯ�����Ȥ��ֵ�
     * 
     * @param  void
     * @return Request
     * @access public
     */
    function &getRequest()
    {
        return $this->_request;
    }

    /**
     * ���å������ֵ�
     * 
     * @param  void
     * @return Session
     * @access public
     */
    function &getSession()
    {
        return $this->_session;
    }

    /**
     * �쥹�ݥ󥹤��ֵ�
     * 
     * @param  void
     * @return Response
     * @access public
     */
    function &getResponse()
    {
        return $this->_response;
    }

    /**
     * �桼��������������Ȥ��ֵ�
     * 
     * @param  void
     * @return Session
     * @access public
     */
    function &getUserAgent()
    {
        return $this->_userAgent;
    }

    /**
     * �ץ������ֵ�
     * 
     * @param  void
     * @return Process
     * @access public
     */
    function &getProcess()
    {
        return $this->_process;
    }

    /**
     * �ƥ�ץ졼�ȡ����󥸥���ֵ�
     * 
     * @param  void
     * @return object|null
     * @access public
     */
    function &getTemplateEngine()
    {
        return $this->_view->getEngine();
    }

    /**
     * ������ֵ�
     * 
     * @param  void
     * @return Config
     * @access public
     */
    function &getConfig()
    {
        return $this->_config;
    }

    /**
     * �������ֵ�
     * 
     * @param  void
     * @return Logger
     * @access public
     */
    function &getLogger()
    {
        return $this->_log;
    }

    /**
     * ��ư���ϻ�����ֵ�
     * 
     * @param  void
     * @return DateMicrotimeAccessor
     * @access public
     */
    function &getBeginning()
    {
        return $this->_beginning;
    }

    /**
     * �ꥯ�����Ȥ�URI�ȥѥ�᡼������¹Էײ�����
     * - �ꥯ������URI���饢������󡦥���ȥ���Υѥ������
     *  - /foo/bar/hoge.php => test_html_foo_bar_hoge
     * - �ꥯ�������ͤ���¹ԥ��������̾�����
     *  - GET::action::bar => bar
     * - web_root_path�����ꤵ�줿�ѥ����ϩ����Ƭ�������
     *  - '/'��/test/html/foo/bar/hoge.php => test_html_foo_bar_hoge
     *  - '/test/html/'��/test/html/foo/bar/hoge.php => foo_bar_hoge
     * - �ɥåȡ�.�˰ʹߤ�ե������ĥ�Ҥ�Ƚ�Ǥ��ƺ��
     *  - /foo/bar/hoge => foo_bar_hoge
     *  - /foo/bar/hoge.php => foo_bar_hoge
     *  - /foo/bar/hoge.html => foo_bar_hoge
     * - �ե�����̾����ά���줿���������ϡ�index�פȤ��ƽ���
     * 
     * @param  void
     * @return array
     * @access protected
     */
    function route()
    {
        $rootpath = 
            $this->_config->get(
                $this->_request->isSecure() ? 'https_root_path' : 'http_root_path', 
                'env');
        if (($rootpath === null) || !is_string($rootpath) || ($rootpath === '')) {
            $rootpath = '/';
        }
        if ($rootpath{0} !== '/') {
            $rootpath = '/' . $rootpath;
        }

        $pathuri  = $this->_request->getPathUri();

        if (strpos($pathuri, $rootpath) === 0) {
            $start   = strlen($rootpath);
            $pathuri = substr($pathuri, $start, (strlen($pathuri) - $start));
        }
        if (strpos($pathuri, '.') !== false) {
            $pathuri = substr($pathuri, 0, strpos($pathuri, '.'));
        }

        if (($pathuri === '') || ($pathuri === false)) {
            $pathuri = 'index';
        } else {
            $i = strlen($pathuri) - 1;
            if ($pathuri{$i} === '/') {
                $pathuri .= 'index';
            }
        }

        return 
            array(
                    'path' => 
                        preg_replace(
                            '/[^a-z0-9_]/i', '', 
                            str_replace(
                                array('/', '-'), '_', 
                                trim(strtolower($pathuri), '/'))), 
                    'method' => 
                        $this->_request->getAcceptRequest(
                            $this->_config->get('action_key', 'env'), 
                            array('p', 'g')), 
                );
    }

    /**
     * �¹Էײ褫�饿�������������ƥץ�������Ͽ
     * - �㳰��ȯ��������۾ｪλ
     *  - �ꥯ�����Ȥ��б�������������󡦥���ȥ��餬¸�ߤ��ʤ�
     *   - ���顼����Controller '����ȥ���̾' not found
     *   - �쥹�ݥ󥹡�404 Not Found
     *  - ��������󡦥���ȥ���Υ��ɤ˼���
     *   - ���顼����"Unable to use the Controller '����ȥ���̾'
     *   - �쥹�ݥ󥹡�404 Not Found
     * 
     * @param  string $ctrlpath ��������󡦥���ȥ���Υѥ�
     * @param  string $action   �¹Ԥ��륢�������
     * @return void
     * @access protected
     * @see    Front::_findCtrl()
     * @see    ClassLoader::load()
     */
    function dispatch($ctrlpath, $action)
    {
        $class_loader = &new ClassLoader;
        $class_loader->setParents('Action');
        $class_loader->setIncludePath($this->getAppDir());

        $discovery = $this->_findCtrl($ctrlpath, $class_loader);
        // $this->actionLocation = $this->_findCtrl($ctrlpath, $class_loader);
        if ($discovery === null) {
            header('HTTP/1.0 404 Not Found');
            trigger_error("Action-controller '${ctrlpath}' not found", E_USER_ERROR);
            exit;
        }

        $action_class = $class_loader->load($discovery['ctrl']);
        // $action_class = $class_loader->load($this->actionLocation['ctrl']);
        if ($action_class === '') {
            header('HTTP/1.0 404 Not Found');
            trigger_error(
                "Unable to load the action-controller '${ctrlpath}'", 
                E_USER_ERROR);
            exit;
        }

        $task = &new $action_class($this);
        $this->_process->set($task, $action, $discovery);
        // $this->_process->set($task, $action, $this->actionLocation);
    }

    /**
     * �ץ����Υ�������¹Ԥ��Ƽ¹Է�̤��б�
     * 
     * @param  void
     * @return void
     * @access protected
     * @see    Process::invoke()
     * @see    Front::redirectAction()
     * @see    Front::redirect()
     * @see    Front::render()
     */
    function process()
    {
        // �ץ�����¹�
        $reply = $this->_process->invoke();

        // �¹Է�̤�ɾ��
        switch (true) {
        case preg_match('/^action::(.+)$/i', $reply, $match) : 
            $this->redirectAction($match[1]);
            break;
        case preg_match('/^redirect::(.+)$/i', $reply, $match) : 
            $this->redirect($match[1]);
            break;
        case ($reply !== '') : 
            $this->render($reply);
            break;
        }
    }

    /**
     * �¹����ե��륿
     * 
     * @param  void
     * @return void
     * @access private
     */
    function _applyFiltersBeforeMethod()
    {
        for ($i = 0, $n = count($this->_filters); $i < $n; ++$i) {
            if (!method_exists($this->_filters[$i], 'before')) {
                continue;
            }
            $this->_filters[$i]->before($this);
        }
    }

    /**
     * �¹Ը�ե��륿
     * 
     * @param  void
     * @return void
     * @access private
     */
    function _applyFiltersAfterMethod()
    {
        for ($i = (count($this->_filters) - 1); $i >= 0; --$i) {
            if (!method_exists($this->_filters[$i], 'after')) {
                continue;
            }
            $this->_filters[$i]->after($this);
        }
    }

    /**
     * ��������󡦥���ȥ���Υѥ����饯�饹̾�򸡺�
     * - �ѥ���foo_bar_hoge_moge_abc�פ���õ���ե�����
     *  1. __action__/FooBarHogeMogeAbc.class.php
     *  2. __action__/foo/BarHogeMogeAbc.class.php
     *  3. __action__/foo_bar/HogeMogeAbc.class.php
     *  4. __action__/foo_bar_hoge/MogeAbc.class.php
     *  5. __action__/foo_bar_hoge_moge/Abc.class.php
     *  6. ���ԡ�null���ֵѡ�
     * 
     * @param  string      $ctrlpath
     * @param  ClassLoader $class_loader
     * @return array|null
     * @access private
     * @see    Utility::camelize()
     * @see    ClassLoader::isReadable()
     */
    function _findCtrl($ctrlpath, &$class_loader)
    {
        $ctrl_class = null;
        $classname  = Utility::camelize($ctrlpath);

        if ($class_loader->isReadable($classname)) {
            // �ѥ��᥯�饹̾
            $ctrl_class = array(
                    'dir'  => '', 
                    'ctrl' => $classname, 
                );
        } else {
            // ���֥ǥ��쥯�ȥꤢ��
            // --------------------------------------------------
            // �ѥ��ζ��ڤ��_�ˤ���Ƭ�����Ϥ��⤯
            $subname  = $ctrlpath;
            $routelen = strlen($ctrlpath);
            while ($subname = strstr($subname, '_')) {
                $sublen  = strlen($subname);
                $subname = substr($subname, 1, $sublen - 1);
                $dirname = substr($ctrlpath, 0, $routelen - $sublen);

                $class_loader->setParents('Action', $dirname);
                $classname = Utility::camelize($subname);

                if ($class_loader->isReadable($classname)) {
                    $ctrl_class = array(
                            'dir'  => $dirname, 
                            'ctrl' => $classname, 
                        );
                    break;
                }
            }
        }

        return $ctrl_class;
    }
}
