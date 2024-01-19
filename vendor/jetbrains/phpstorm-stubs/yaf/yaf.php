<?php

namespace DEPTRAC_202401;

/**
 * The constants below are defined by this extension, and will only be available when the extension has either been compiled into PHP or dynamically loaded at runtime.
 * @link https://php.net/manual/en/yaf.constants.php
 */
\define('YAF_VERSION', '3.3.3', \true);
\define('YAF_ENVIRON', 'product', \true);
\define('YAF_ERR_STARTUP_FAILED', 512, \true);
\define('YAF_ERR_ROUTE_FAILED', 513, \true);
\define('YAF_ERR_DISPATCH_FAILED', 514, \true);
\define('YAF_ERR_NOTFOUND_MODULE', 515, \true);
\define('YAF_ERR_NOTFOUND_CONTROLLER', 516, \true);
\define('YAF_ERR_NOTFOUND_ACTION', 517, \true);
\define('YAF_ERR_NOTFOUND_VIEW', 518, \true);
\define('YAF_ERR_CALL_FAILED', 519, \true);
\define('YAF_ERR_AUTOLOAD_FAILED', 520, \true);
\define('YAF_ERR_TYPE_ERROR', 521, \true);
\define('YAF_ERR_ACCESS_ERROR', 522);
//================================================
/**
 * Yaf_Application provides a bootstrapping facility for applications which provides reusable resources, common- and module-based bootstrap classes and dependency checking.
 * <br/>
 * <b>Note:</b>
 * <p>
 * Yaf_Application implements the singleton pattern, and Yaf_Application can not be serialized or un-serialized which will cause problem when you try to use PHPUnit to write some test case for Yaf.<br/>
 * You may use &#64;backupGlobals annotation of PHPUnit to control the backup and restore operations for global variables. thus can solve this problem.
 * </p>
 * @link https://secure.php.net/manual/en/class.yaf-application.php
 */
final class Yaf_Application
{
    /**
     * @var Yaf_Application
     */
    protected static $_app;
    /**
     * @var Yaf_Config_Abstract
     */
    protected $config;
    /**
     * @var Yaf_Dispatcher
     */
    protected $dispatcher;
    /**
     * @var array
     */
    protected $_modules;
    /**
     * @var string
     */
    protected $_running = "";
    /**
     * @var string
     */
    protected $_environ = \YAF_ENVIRON;
    /**
     * @since 2.1.2
     * @var int
     */
    protected $_err_no = 0;
    /**
     * @since 2.1.2
     * @var string
     */
    protected $_err_msg = "";
    /**
     * @link https://secure.php.net/manual/en/yaf-application.construct.php
     *
     * @param string|array $config A ini config file path, or a config array
     * <p>
     * If is a ini config file, there should be a section named as the one defined by yaf.environ, which is "product" by default.
     * </p>
     * <br/>
     * <b>Note:</b>
     * <p>If you use a ini configuration file as your application's config container. you would open the yaf.cache_config to improve performance.</p>
     * <p>And the config entry(and there default value) list blow:</p>
     *
     * <p>
     *    <b>Example #1 A ini config file example</b><br/>
     *    [product]<br/>
     *    ;this one should always be defined, and have no default value<br/>
     *    application.directory=APPLICATION_PATH<br/><br/>
     * </p>
     * <p>
     *    ;following configs have default value, you may no need to define them
     * <br/>
     *    application.library = APPLICATION_PATH . "/library" <br/>
     *    application.dispatcher.throwException=1 <br/>
     *    application.dispatcher.catchException=1 <br/><br/>
     * </p>
     * <p>application.baseUri=""<br/><br/></p>
     * <p>
     *    ;the php script ext name<br/>
     *    ap.ext=php<br/><br/>
     * </p>
     * <p>
     *    ;the view template ext name<br/>
     *    ap.view.ext=phtml<br/><br/>
     * </p>
     * <p>
     *    ap.dispatcher.defaultModule=Index<br/>
     *    ap.dispatcher.defaultController=Index<br/>
     *    ap.dispatcher.defaultAction=index<br/><br/>
     * </p>
     * <p>
     *    ;defined modules<br/>
     *    ap.modules=Index
     * </p>
     * @param string $environ Which section will be loaded as the final config
     *
     * @throws Yaf_Exception_TypeError|Yaf_Exception_StartupError
     */
    public function __construct($config, $environ = null)
    {
    }
    public function getInstance()
    {
    }
    /**
     * Run a Yaf_Application, let the Yaf_Application accept a request, and route the request, dispatch to controller/action, and render response.
     * return response to client finally.
     *
     * @link https://secure.php.net/manual/en/yaf-application.run.php
     * @throws Yaf_Exception_StartupError
     */
    public function run()
    {
    }
    /**
     * This method is typically used to run Yaf_Application in a crontab work.
     * Make the crontab work can also use the autoloader and Bootstrap mechanism.
     *
     * @link https://secure.php.net/manual/en/yaf-application.execute.php
     *
     * @param callable $entry a valid callback
     * @param string ...$_ parameters will pass to the callback
     */
    public function execute(callable $entry, ...$_)
    {
    }
    /**
     * Retrieve the Yaf_Application instance, alternatively, we also could use Yaf_Dispatcher::getApplication().
     *
     * @link https://secure.php.net/manual/en/yaf-application.app.php
     *
     * @return Yaf_Application|null an Yaf_Application instance, if no Yaf_Application initialized before, NULL will be returned.
     */
    public static function app()
    {
    }
    /**
     * Retrieve environ which was defined in yaf.environ which has a default value "product".
     *
     * @link https://secure.php.net/manual/en/yaf-application.environ.php
     *
     * @return string
     */
    public function environ()
    {
    }
    /**
     * Run a Bootstrap, all the methods defined in the Bootstrap and named with prefix "_init" will be called according to their declaration order, if the parameter bootstrap is not supplied, Yaf will look for a Bootstrap under application.directory.
     *
     * @link https://secure.php.net/manual/en/yaf-application.bootstrap.php
     *
     * @param Yaf_Bootstrap_Abstract $bootstrap A Yaf_Bootstrap_Abstract instance
     * @return Yaf_Application
     */
    public function bootstrap($bootstrap = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-application.getconfig.php
     *
     * @return Yaf_Config_Abstract
     */
    public function getConfig()
    {
    }
    /**
     * Get the modules list defined in config, if no one defined, there will always be a module named "Index".
     *
     * @link https://secure.php.net/manual/en/yaf-application.getmodules.php
     *
     * @return array
     */
    public function getModules()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-application.getdispatcher.php
     *
     * @return Yaf_Dispatcher
     */
    public function getDispatcher()
    {
    }
    /**
     * Change the application directory
     *
     * @param string $directory
     * @return Yaf_Application
     * @since 2.1.4
     * @link https://secure.php.net/manual/en/yaf-application.setappdirectory.php
     */
    public function setAppDirectory($directory)
    {
    }
    /**
     * @return string
     * @link https://secure.php.net/manual/en/yaf-application.getappdirectory.php
     *
     * @since 2.1.4
     */
    public function getAppDirectory()
    {
    }
    /**
     * @return int
     * @link https://secure.php.net/manual/en/yaf-application.getlasterrorno.php
     *
     * @since 2.1.2
     */
    public function getLastErrorNo()
    {
    }
    /**
     * @return string
     * @link https://secure.php.net/manual/en/yaf-application.getlasterrormsg.php
     *
     * @since 2.1.2
     */
    public function getLastErrorMsg()
    {
    }
    /**
     * @since 2.1.2
     * @link https://secure.php.net/manual/en/yaf-application.clearlasterror.php
     */
    public function clearLastError()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-application.destruct.php
     */
    public function __destruct()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-application.clone.php
     */
    private function __clone()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-application.sleep.php
     */
    private function __sleep()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-application.wakeup.php
     */
    private function __wakeup()
    {
    }
}
//================================================
/**
 * Yaf_Application provides a bootstrapping facility for applications which provides reusable resources, common- and module-based bootstrap classes and dependency checking.
 * <br/>
 * <b>Note:</b>
 * <p>
 * Yaf_Application implements the singleton pattern, and Yaf_Application can not be serialized or un-serialized which will cause problem when you try to use PHPUnit to write some test case for Yaf.<br/>
 * You may use &#64;backupGlobals annotation of PHPUnit to control the backup and restore operations for global variables. thus can solve this problem.
 * </p>
 * @link https://secure.php.net/manual/en/class.yaf-application.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Application', 'Yaf_Application', \false);
/**
 * <p><b>Yaf_Dispatcher</b> purpose is to initialize the request environment, route the incoming request, and then dispatch any discovered actions; it aggregates any responses and returns them when the process is complete.</p><br/>
 * <p><b>Yaf_Dispatcher</b> also implements the Singleton pattern, meaning only a single instance of it may be available at any given time. This allows it to also act as a registry on which the other objects in the dispatch process may draw.</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-dispatcher.php
 */
final class Yaf_Dispatcher
{
    /**
     * @var Yaf_Dispatcher
     */
    protected static $_instance;
    /**
     * @var Yaf_Router
     */
    protected $_router;
    /**
     * @var Yaf_View_Interface
     */
    protected $_view;
    /**
     * @var Yaf_Request_Abstract
     */
    protected $_request;
    /**
     * @var Yaf_Plugin_Abstract
     */
    protected $_plugins;
    /**
     * @var bool
     */
    protected $_auto_render = \true;
    /**
     * @var string
     */
    protected $_return_response = "";
    /**
     * @var string
     */
    protected $_instantly_flush = "";
    /**
     * @var string
     */
    protected $_default_module;
    /**
     * @var string
     */
    protected $_default_controller;
    /**
     * @var string
     */
    protected $_default_action;
    /**
     * @link https://secure.php.net/manual/en/yaf-dispatcher.construct.php
     */
    private function __construct()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-dispatcher.clone.php
     */
    private function __clone()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-dispatcher.sleep.php
     */
    private function __sleep()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-dispatcher.wakeup.php
     */
    private function __wakeup()
    {
    }
    /**
     * enable view rendering
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.enableview.php
     *
     * @return Yaf_Dispatcher
     */
    public function enableView()
    {
    }
    public function getResponse()
    {
    }
    public function getDefaultModule()
    {
    }
    public function getDefaultController()
    {
    }
    public function getDefaultAction()
    {
    }
    /**
     * <p>disable view engine, used in some app that user will output by himself</p><br/>
     * <b>Note:</b>
     * <p>you can simply return FALSE in a action to prevent the auto-rendering of that action</p>
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.disableview.php
     *
     * @return bool
     */
    public function disableView()
    {
    }
    /**
     * Initialize view and return it
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.initview.php
     *
     * @param string $templates_dir
     * @param array|null $options
     * @return Yaf_View_Interface
     */
    public function initView($templates_dir, ?array $options = null)
    {
    }
    /**
     * This method provides a solution for that if you want use a custom view engine instead of Yaf_View_Simple
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.setview.php
     *
     * @param Yaf_View_Interface $view A Yaf_View_Interface instance
     * @return Yaf_Dispatcher
     */
    public function setView($view)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-dispatcher.setrequest.php
     *
     * @param Yaf_Request_Abstract $request
     * @return Yaf_Dispatcher
     */
    public function setRequest($request)
    {
    }
    /**
     * Retrieve the Yaf_Application instance. same as Yaf_Application::app().
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.getapplication.php
     * @return Yaf_Application
     */
    public function getApplication()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-dispatcher.getrouter.php
     *
     * @return Yaf_Router
     */
    public function getRouter()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-dispatcher.getrequest.php
     *
     * @return Yaf_Request_Abstract
     */
    public function getRequest()
    {
    }
    /**
     * <p>Set error handler for Yaf. when application.dispatcher.throwException is off, Yaf will trigger catch-able error while unexpected errors occurred.</p><br/>
     * <p>Thus, this error handler will be called while the error raise.</p>
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.seterrorhandler.php
     *
     * @param callable $callback a callable callback
     * @param int $error_types YAF_ERR_* constants mask
     *
     * @return Yaf_Dispatcher
     */
    public function setErrorHandler($callback, $error_types = \YAF_ERR_TYPE_ERROR)
    {
    }
    /**
     * Change default module name
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.setdefaultmodule.php
     *
     * @param string $module
     * @return Yaf_Dispatcher
     */
    public function setDefaultModule($module)
    {
    }
    /**
     * Change default controller name
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.setdefaultcontroller.php
     *
     * @param string $controller
     * @return Yaf_Dispatcher
     */
    public function setDefaultController($controller)
    {
    }
    /**
     * Change default action name
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.setdefaultaction.php
     *
     * @param string $action
     * @return Yaf_Dispatcher
     */
    public function setDefaultAction($action)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-dispatcher.returnresponse.php
     *
     * @param bool $flag
     * @return Yaf_Dispatcher
     */
    public function returnResponse($flag)
    {
    }
    /**
     * <p>Yaf_Dispatcher will render automatically after dispatches an incoming request, you can prevent the rendering by calling this method with $flag TRUE</p><br/>
     * <b>Note:</b>
     * <p>you can simply return FALSE in a action to prevent the auto-rendering of that action</p>
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.autorender.php
     *
     * @param bool $flag since 2.2.0, if this parameter is not given, then the current state will be set
     * @return Yaf_Dispatcher
     */
    public function autoRender($flag = null)
    {
    }
    /**
     * Switch on/off the instant flushing
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.flushinstantly.php
     *
     * @param bool $flag since 2.2.0, if this parameter is not given, then the current state will be set
     * @return Yaf_Dispatcher
     */
    public function flushInstantly($flag = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-dispatcher.getinstance.php
     *
     * @return Yaf_Dispatcher
     */
    public static function getInstance()
    {
    }
    /**
     * <p>This method does the heavy work of the Yaf_Dispatcher. It take a request object.</p><br/>
     * <p>The dispatch process has three distinct events:</p>
     * <ul>
     * <li>Routing</li>
     * <li>Dispatching</li>
     * <li>Response</li>
     * </ul>
     * <p>Routing takes place exactly once, using the values in the request object when dispatch() is called. Dispatching takes place in a loop; a request may either indicate multiple actions to dispatch, or the controller or a plugin may reset the request object to force additional actions to dispatch(see Yaf_Plugin_Abstract. When all is done, the Yaf_Dispatcher returns a response.</p>
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.dispatch.php
     *
     * @param Yaf_Request_Abstract $request
     *
     * @throws Yaf_Exception_TypeError
     * @throws Yaf_Exception_RouterFailed
     * @throws Yaf_Exception_DispatchFailed
     * @throws Yaf_Exception_LoadFailed
     * @throws Yaf_Exception_LoadFailed_Action
     * @throws Yaf_Exception_LoadFailed_Controller
     *
     * @return Yaf_Response_Abstract
     */
    public function dispatch($request)
    {
    }
    /**
     * <p>Switch on/off exception throwing while unexpected error occurring. When this is on, Yaf will throwing exceptions instead of triggering catchable errors.</p><br/>
     * <p>You can also use application.dispatcher.throwException to achieve the same purpose.</p>
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.throwexception.php
     *
     * @param bool $flag
     * @return Yaf_Dispatcher
     */
    public function throwException($flag = null)
    {
    }
    /**
     * <p>While the application.dispatcher.throwException is On(you can also calling to <b>Yaf_Dispatcher::throwException(TRUE)</b> to enable it), Yaf will throw Exception whe error occurs instead of trigger error.</p><br/>
     * <p>then if you enable <b>Yaf_Dispatcher::catchException()</b>(also can enabled by set application.dispatcher.catchException), all uncaught Exceptions will be caught by ErrorController::error if you have defined one.</p>
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.catchexception.php
     *
     * @param bool $flag
     * @return Yaf_Dispatcher
     */
    public function catchException($flag = null)
    {
    }
    /**
     * Register a plugin(see Yaf_Plugin_Abstract). Generally, we register plugins in Bootstrap(see Yaf_Bootstrap_Abstract).
     *
     * @link https://secure.php.net/manual/en/yaf-dispatcher.registerplugin.php
     *
     * @param Yaf_Plugin_Abstract $plugin
     * @return Yaf_Dispatcher
     */
    public function registerPlugin($plugin)
    {
    }
    public function setResponse($response)
    {
    }
}
/**
 * <p><b>Yaf_Dispatcher</b> purpose is to initialize the request environment, route the incoming request, and then dispatch any discovered actions; it aggregates any responses and returns them when the process is complete.</p><br/>
 * <p><b>Yaf_Dispatcher</b> also implements the Singleton pattern, meaning only a single instance of it may be available at any given time. This allows it to also act as a registry on which the other objects in the dispatch process may draw.</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-dispatcher.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Dispatcher', 'Yaf_Dispatcher', \false);
/**
 * <p><b>Yaf_Loader</b> introduces a comprehensive autoloading solution for Yaf.</p>
 * <br/>
 * <p>The first time an instance of Yaf_Application is retrieved, <b>Yaf_Loader</b> will instance a singleton, and registers itself with spl_autoload. You retrieve an instance using the Yaf_Loader::getInstance()</p>
 * <br/>
 * <p><b>Yaf_Loader</b> attempt to load a class only one shot, if failed, depend on yaf.use_spl_autoload, if this config is On Yaf_Loader::autoload() will return FALSE, thus give the chance to other autoload function. if it is Off (by default), Yaf_Loader::autoload() will return TRUE, and more important is that a very useful warning will be triggered (very useful to find out why a class could not be loaded).</p>
 * <br/>
 * <b>Note:</b>
 * <p>Please keep yaf.use_spl_autoload Off unless there is some library have their own autoload mechanism and impossible to rewrite it.</p>
 * <br/>
 * <p>If you want <b>Yaf_Loader</b> search some classes(libraries) in the local class directory(which is defined in application.ini, and by default, it is application.directory . "/library"), you should register the class prefix using the Yaf_Loader::registerLocalNameSpace()</p>
 * @link https://secure.php.net/manual/en/class.yaf-loader.php
 */
class Yaf_Loader
{
    /**
     * @var string
     */
    protected $_local_ns;
    /**
     * By default, this value is application.directory . "/library", you can change this either in the application.ini(application.library) or call to Yaf_Loader::setLibraryPath()
     * @var string
     */
    protected $_library;
    /**
     * @var string
     */
    protected $_global_library;
    /**
     * @var Yaf_Loader
     */
    protected static $_instance;
    /**
     * @link https://secure.php.net/manual/en/yaf-loader.construct.php
     */
    private function __construct()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-loader.clone.php
     */
    private function __clone()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-loader.sleep.php
     */
    private function __sleep()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-loader.wakeup.php
     */
    private function __wakeup()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-loader.autoload.php
     *
     * @param string $class_name
     *
     * @return bool
     */
    public function autoload($class_name)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-loader.getinstance.php
     *
     * @param string $local_library_path
     * @param string $global_library_path
     *
     * @return Yaf_Loader
     */
    public static function getInstance($local_library_path = null, $global_library_path = null)
    {
    }
    /**
     * <p>Register local class prefix name, Yaf_Loader search classes in two library directories, the one is configured via application.library.directory(in application.ini) which is called local library directory; the other is configured via yaf.library (in php.ini) which is called global library directory, since it can be shared by many applications in the same server.</p>
     * <br/>
     * <p>When an autoloading is triggered, Yaf_Loader will determine which library directory should be searched in by examining the prefix name of the missed classname. If the prefix name is registered as a local namespace then look for it in local library directory, otherwise look for it in global library directory.</p>
     * <br/>
     * <b>Note:</b>
     * <p>If yaf.library is not configured, then the global library directory is assumed to be the local library directory. in that case, all autoloading will look for local library directory. But if you want your Yaf application be strong, then always register your own classes as local classes.</p>
     * @link https://secure.php.net/manual/en/yaf-loader.registerlocalnamespace.php
     *
     * @param string|string[] $namespace a string or a array of class name prefix. all class prefix with these prefix will be loaded in local library path.
     * @param string $path
     *
     * @return bool
     */
    public function registerLocalNamespace($namespace, $path = '')
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-loader.getlocalnamespace.php
     *
     * @return string
     */
    public function getLocalNamespace()
    {
    }
    public function getNamespaces()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-loader.clearlocalnamespace.php
     */
    public function clearLocalNamespace()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-loader.islocalname.php
     *
     * @param string $class_name
     *
     * @return bool
     */
    public function isLocalName($class_name)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-loader.import.php
     *
     * @param string $file
     *
     * @return bool
     */
    public static function import($file)
    {
    }
    /**
     * @param string $library_path
     * @param bool $is_global
     *
     * @return Yaf_Loader
     * @link https://secure.php.net/manual/en/yaf-loader.setlibrarypath.php
     *
     * @since 2.1.4
     */
    public function setLibraryPath($library_path, $is_global = \false)
    {
    }
    /**
     * @param bool $is_global
     *
     * @return string
     * @since 2.1.4
     * @link https://secure.php.net/manual/en/yaf-loader.getlibrarypath.php
     */
    public function getLibraryPath($is_global = \false)
    {
    }
    public function registerNamespace($namespace, $path = '')
    {
    }
    public function getNamespacePath($class_name)
    {
    }
}
/**
 * <p><b>Yaf_Loader</b> introduces a comprehensive autoloading solution for Yaf.</p>
 * <br/>
 * <p>The first time an instance of Yaf_Application is retrieved, <b>Yaf_Loader</b> will instance a singleton, and registers itself with spl_autoload. You retrieve an instance using the Yaf_Loader::getInstance()</p>
 * <br/>
 * <p><b>Yaf_Loader</b> attempt to load a class only one shot, if failed, depend on yaf.use_spl_autoload, if this config is On Yaf_Loader::autoload() will return FALSE, thus give the chance to other autoload function. if it is Off (by default), Yaf_Loader::autoload() will return TRUE, and more important is that a very useful warning will be triggered (very useful to find out why a class could not be loaded).</p>
 * <br/>
 * <b>Note:</b>
 * <p>Please keep yaf.use_spl_autoload Off unless there is some library have their own autoload mechanism and impossible to rewrite it.</p>
 * <br/>
 * <p>If you want <b>Yaf_Loader</b> search some classes(libraries) in the local class directory(which is defined in application.ini, and by default, it is application.directory . "/library"), you should register the class prefix using the Yaf_Loader::registerLocalNameSpace()</p>
 * @link https://secure.php.net/manual/en/class.yaf-loader.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Loader', 'Yaf_Loader', \false);
/**
 * <p>All methods of <b>Yaf_Registry</b> declared as static, making it universally accessible. This provides the ability to get or set any custom data from anyway in your code as necessary.</p>
 * @link https://secure.php.net/manual/en/class.yaf-registry.php
 */
final class Yaf_Registry
{
    /**
     * @var Yaf_Registry
     */
    protected static $_instance;
    /**
     * @var array
     */
    protected $_entries;
    /**
     * @link https://secure.php.net/manual/en/yaf-registry.construct.php
     */
    private function __construct()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-registry.clone.php
     */
    private function __clone()
    {
    }
    /**
     * Retrieve an item from registry
     *
     * @link https://secure.php.net/manual/en/yaf-registry.get.php
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function get($name)
    {
    }
    /**
     * Check whether an item exists
     *
     * @link https://secure.php.net/manual/en/yaf-registry.has.php
     *
     * @param string $name
     *
     * @return bool
     */
    public static function has($name)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-registry.set.php
     *
     * @param string $name
     * @param string $value
     *
     * @return bool
     */
    public static function set($name, $value)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-registry.del.php
     *
     * @param string $name
     *
     * @return void|bool
     */
    public static function del($name)
    {
    }
}
/**
 * <p>All methods of <b>Yaf_Registry</b> declared as static, making it universally accessible. This provides the ability to get or set any custom data from anyway in your code as necessary.</p>
 * @link https://secure.php.net/manual/en/class.yaf-registry.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Registry', 'Yaf_Registry', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-session.php
 * @version 2.2.9
 */
final class Yaf_Session implements \Iterator, \ArrayAccess, \Countable
{
    /**
     * @var Yaf_Session
     */
    protected static $_instance;
    /**
     * @var array
     */
    protected $_session;
    /**
     * @var bool
     */
    protected $_started = \true;
    /**
     * @link https://secure.php.net/manual/en/yaf-session.construct.php
     */
    private function __construct()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-session.clone.php
     */
    private function __clone()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-session.sleep.php
     */
    private function __sleep()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-session.wakeup.php
     */
    private function __wakeup()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-session.getinstance.php
     *
     * @return Yaf_Session
     */
    public static function getInstance()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-session.start.php
     *
     * @return Yaf_Session
     */
    public function start()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-session.get.php
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-session.has.php
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-session.set.php
     *
     * @param string $name
     * @param mixed $value
     *
     * @return Yaf_Session|false return FALSE on failure
     */
    public function set($name, $value)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-session.del.php
     *
     * @param string $name
     *
     * @return Yaf_Session|false return FALSE on failure
     */
    public function del($name)
    {
    }
    /**
     * @see Countable::count
     */
    public function count()
    {
    }
    /**
     * @see Iterator::rewind
     */
    public function rewind()
    {
    }
    /**
     * @see Iterator::current
     */
    public function current()
    {
    }
    /**
     * @see Iterator::next
     */
    public function next()
    {
    }
    /**
     * @see Iterator::valid
     */
    public function valid()
    {
    }
    /**
     * @see Iterator::key
     */
    public function key()
    {
    }
    /**
     * @param string $name
     * @see ArrayAccess::offsetUnset
     */
    public function offsetUnset($name)
    {
    }
    /**
     * @param string $name
     * @return mixed
     * @see ArrayAccess::offsetGet
     */
    public function offsetGet($name)
    {
    }
    /**
     * @see ArrayAccess::offsetExists
     */
    public function offsetExists($name)
    {
    }
    /**
     * @param string $name
     * @param string $value
     * @return void
     * @see ArrayAccess::offsetSet
     */
    public function offsetSet($name, $value)
    {
    }
    /**
     * @see Yaf_Session::get()
     */
    public function __get($name)
    {
    }
    /**
     * @see Yaf_Session::has()
     */
    public function __isset($name)
    {
    }
    /**
     * @see Yaf_Session::set()
     */
    public function __set($name, $value)
    {
    }
    /**
     * @see Yaf_Session::del()
     */
    public function __unset($name)
    {
    }
    public function clear()
    {
    }
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-session.php
 * @version 2.2.9
 */
\class_alias('DEPTRAC_202401\\Yaf_Session', 'Yaf_Session', \false);
/**
 * <p><b>Yaf_Router</b> is the standard framework router. Routing is the process of taking a URI endpoint (that part of the URI which comes after the base URI: see Yaf_Request_Abstract::setBaseUri()) and decomposing it into parameters to determine which module, controller, and action of that controller should receive the request. This values of the module, controller, action and other parameters are packaged into a Yaf_Request_Abstract object which is then processed by Yaf_Dispatcher. Routing occurs only once: when the request is initially received and before the first controller is dispatched. Yaf_Router is designed to allow for mod_rewrite-like functionality using pure PHP structures. It is very loosely based on Ruby on Rails routing and does not require any prior knowledge of webserver URL rewriting</p>
 * <br/>
 * <b>Default Route</b>
 * <br/>
 * <p><b>Yaf_Router</b> comes pre-configured with a default route Yaf_Route_Static, which will match URIs in the shape of controller/action. Additionally, a module name may be specified as the first path element, allowing URIs of the form module/controller/action. Finally, it will also match any additional parameters appended to the URI by default - controller/action/var1/value1/var2/value2.</p>
 * <br/>
 * <b>Note:</b>
 * <p>Module name must be defined in config, considering application.module="Index,Foo,Bar", in this case, only index, foo and bar can be considered as a module name. if doesn't config, there is only one module named "Index".</p>
 * <br/>
 * <p>** See examples by opening the external documentation</p>
 * @link https://secure.php.net/manual/en/class.yaf-router.php
 */
class Yaf_Router
{
    /**
     * @var Yaf_Route_Interface[] registered routes stack
     */
    protected $_routes;
    /**
     * @var string after routing phase, this indicated the name of which route is used to route current request. you can get this name by Yaf_Router::getCurrentRoute()
     */
    protected $_current;
    /**
     * @link https://secure.php.net/manual/en/yaf-router.construct.php
     */
    public function __construct()
    {
    }
    /**
     * <p>by default, Yaf_Router using a Yaf_Route_Static as its default route. you can add new routes into router's route stack by calling this method.</p>
     * <br/>
     * <p>the newer route will be called before the older(route stack), and if the newer router return TRUE, the router process will be end. otherwise, the older one will be called.</p>
     *
     * @link https://secure.php.net/manual/en/yaf-router.addroute.php
     *
     * @param string $name
     * @param Yaf_Route_Interface $route
     *
     * @return Yaf_Router|false return FALSE on failure
     */
    public function addRoute($name, $route)
    {
    }
    /**
     * <p>Add routes defined by configs into Yaf_Router's route stack</p>
     *
     * @link https://secure.php.net/manual/en/yaf-router.addconfig.php
     *
     * @param Yaf_Config_Abstract $config
     *
     * @return Yaf_Router|false return FALSE on failure
     */
    public function addConfig($config)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-router.route.php
     *
     * @param Yaf_Request_Abstract $request
     *
     * @return Yaf_Router|false return FALSE on failure
     */
    public function route($request)
    {
    }
    /**
     * <p>Retrieve a route by name, see also Yaf_Router::getCurrentRoute()</p>
     *
     * @link https://secure.php.net/manual/en/yaf-router.getroute.php
     *
     * @param string $name
     *
     * @return Yaf_Route_Interface
     */
    public function getRoute($name)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-router.getroutes.php
     *
     * @return Yaf_Route_Interface[]
     */
    public function getRoutes()
    {
    }
    /**
     * <p>Get the name of the route which is effective in the route process.</p>
     * <br/>
     * <b>Note:</b>
     * <p>You should call this method after the route process finished, since before that, this method will always return NULL.</p>
     *
     * @link https://secure.php.net/manual/en/yaf-router.getcurrentroute.php
     *
     * @return string the name of the effective route.
     */
    public function getCurrentRoute()
    {
    }
}
/**
 * <p><b>Yaf_Router</b> is the standard framework router. Routing is the process of taking a URI endpoint (that part of the URI which comes after the base URI: see Yaf_Request_Abstract::setBaseUri()) and decomposing it into parameters to determine which module, controller, and action of that controller should receive the request. This values of the module, controller, action and other parameters are packaged into a Yaf_Request_Abstract object which is then processed by Yaf_Dispatcher. Routing occurs only once: when the request is initially received and before the first controller is dispatched. Yaf_Router is designed to allow for mod_rewrite-like functionality using pure PHP structures. It is very loosely based on Ruby on Rails routing and does not require any prior knowledge of webserver URL rewriting</p>
 * <br/>
 * <b>Default Route</b>
 * <br/>
 * <p><b>Yaf_Router</b> comes pre-configured with a default route Yaf_Route_Static, which will match URIs in the shape of controller/action. Additionally, a module name may be specified as the first path element, allowing URIs of the form module/controller/action. Finally, it will also match any additional parameters appended to the URI by default - controller/action/var1/value1/var2/value2.</p>
 * <br/>
 * <b>Note:</b>
 * <p>Module name must be defined in config, considering application.module="Index,Foo,Bar", in this case, only index, foo and bar can be considered as a module name. if doesn't config, there is only one module named "Index".</p>
 * <br/>
 * <p>** See examples by opening the external documentation</p>
 * @link https://secure.php.net/manual/en/class.yaf-router.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Router', 'Yaf_Router', \false);
/**
 * <p>Bootstrap is a mechanism used to do some initial config before a Application run.<br/><br/></p>
 * <p>User may define their own Bootstrap class by inheriting <b>Yaf_Bootstrap_Abstract</b><br/><br/></p>
 * <p>Any method declared in Bootstrap class with leading "_init", will be called by Yaf_Application::bootstrap() one by one according to their defined order<br/><br/></p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-bootstrap-abstract.php
 */
abstract class Yaf_Bootstrap_Abstract
{
}
/**
 * <p>Bootstrap is a mechanism used to do some initial config before a Application run.<br/><br/></p>
 * <p>User may define their own Bootstrap class by inheriting <b>Yaf_Bootstrap_Abstract</b><br/><br/></p>
 * <p>Any method declared in Bootstrap class with leading "_init", will be called by Yaf_Application::bootstrap() one by one according to their defined order<br/><br/></p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-bootstrap-abstract.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Bootstrap_Abstract', 'Yaf_Bootstrap_Abstract', \false);
/**
 * <p><b>Yaf_Controller_Abstract</b> is the heart of Yaf's system. MVC stands for Model-View-Controller and is a design pattern targeted at separating application logic from display logic.</p>
 * <br/>
 * <p>Every custom controller shall inherit <b>Yaf_Controller_Abstract</b>.</p>
 * <br/>
 * <p>You will find that you can not define __construct function for your custom controller, thus, <b>Yaf_Controller_Abstract</b> provides a magic method: Yaf_Controller_Abstract::init().</p>
 * <br/>
 * <p>If you have defined a init() method in your custom controller, it will be called as long as the controller was instantiated.</p>
 * <br/>
 * <p>Action may have arguments, when a request coming, if there are the same name variable in the request parameters(see Yaf_Request_Abstract::getParam()) after routed, Yaf will pass them to the action method (see Yaf_Action_Abstract::execute()).</p>
 * <br/>
 * <b>Note:</b>
 * <p>These arguments are directly fetched without filtering, it should be carefully processed before use them.</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-controller-abstract.php
 */
abstract class Yaf_Controller_Abstract
{
    /**
     * @see Yaf_Action_Abstract
     * @var array You can also define a action method in a separate PHP script by using this property and Yaf_Action_Abstract.
     */
    public $actions;
    /**
     * @var string module name
     */
    protected $_module;
    /**
     * @var string controller name
     */
    protected $_name;
    /**
     * @var Yaf_Request_Abstract current request object
     */
    protected $_request;
    /**
     * @var Yaf_Response_Abstract current response object
     */
    protected $_response;
    /**
     * @var array
     */
    protected $_invoke_args;
    /**
     * @var Yaf_View_Interface view engine object
     */
    protected $_view;
    /**
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.render.php
     *
     * @param string $tpl
     * @param array|null $parameters
     *
     * @return string
     */
    protected function render($tpl, ?array $parameters = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.display.php
     *
     * @param string $tpl
     * @param array|null $parameters
     *
     * @return bool
     */
    protected function display($tpl, ?array $parameters = null)
    {
    }
    /**
     * retrieve current request object
     *
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.getrequest.php
     *
     * @return Yaf_Request_Abstract
     */
    public function getRequest()
    {
    }
    /**
     * retrieve current response object
     *
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.getresponse.php
     *
     * @return Yaf_Response_Abstract
     */
    public function getResponse()
    {
    }
    /**
     * get the controller's module name
     *
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.getmodulename.php
     *
     * @return string
     */
    public function getModuleName()
    {
    }
    /**
     * retrieve view engine
     *
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.getview.php
     *
     * @return Yaf_View_Interface
     */
    public function getView()
    {
    }
    public function getName()
    {
    }
    /**
     * @param array|null $options
     *
     * @return Yaf_Response_Abstract
     * @deprecated not_implemented
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.initview.php
     */
    public function initView(?array $options = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.setviewpath.php
     *
     * @param string $view_directory
     *
     * @return bool
     */
    public function setViewpath($view_directory)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.getviewpath.php
     *
     * @return string
     */
    public function getViewpath()
    {
    }
    /**
     * <p>forward current execution process to other action.</p>
     * <br/>
     * <b>Note:</b>
     * <p>this method doesn't switch to the destination action immediately, it will take place after current flow finish.</p>
     * <br/>
     * <b>Notice, there are 3 available method signatures:</b>
     * <p>Yaf_Controller_Abstract::forward ( string $module , string $controller , string $action [, array $parameters ] )</p>
     * <p>Yaf_Controller_Abstract::forward ( string $controller , string $action [, array $parameters ] )</p>
     * <p>Yaf_Controller_Abstract::forward ( string $action [, array $parameters ] )</p>
     *
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.forward.php
     *
     * @param string $module destination module name, if NULL was given, then default module name is assumed
     * @param string $controller destination controller name
     * @param string $action destination action name
     * @param array|null $parameters calling arguments
     *
     * @return bool return FALSE on failure
     */
    public function forward($module, $controller = null, $action = null, ?array $parameters = null)
    {
    }
    /**
     * redirect to a URL by sending a 302 header
     *
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.redirect.php
     *
     * @param string $url a location URL
     *
     * @return bool
     */
    public function redirect($url)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.getinvokeargs.php
     *
     * @return array
     */
    public function getInvokeArgs()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.getinvokearg.php
     * @param string $name
     *
     * @return mixed|null
     */
    public function getInvokeArg($name)
    {
    }
    /**
     * <p>Yaf_Controller_Abstract::__construct() is final, which means users can not override it. but users can define <b>Yaf_Controller_Abstract::init()</b>, which will be called after controller object is instantiated.</p>
     *
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.init.php
     */
    public function init()
    {
    }
    /**
     * <b>Yaf_Controller_Abstract</b>::__construct() is final, which means it can not be overridden. You may want to see Yaf_Controller_Abstract::init() instead.
     *
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstract $response
     * @param Yaf_View_Interface $view
     * @param array|null $args
     * @see Yaf_Controller_Abstract::init()
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.construct.php
     */
    public function __construct($request, $response, $view, ?array $args = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-controller-abstract.clone.php
     */
    private function __clone()
    {
    }
}
/**
 * <p><b>Yaf_Controller_Abstract</b> is the heart of Yaf's system. MVC stands for Model-View-Controller and is a design pattern targeted at separating application logic from display logic.</p>
 * <br/>
 * <p>Every custom controller shall inherit <b>Yaf_Controller_Abstract</b>.</p>
 * <br/>
 * <p>You will find that you can not define __construct function for your custom controller, thus, <b>Yaf_Controller_Abstract</b> provides a magic method: Yaf_Controller_Abstract::init().</p>
 * <br/>
 * <p>If you have defined a init() method in your custom controller, it will be called as long as the controller was instantiated.</p>
 * <br/>
 * <p>Action may have arguments, when a request coming, if there are the same name variable in the request parameters(see Yaf_Request_Abstract::getParam()) after routed, Yaf will pass them to the action method (see Yaf_Action_Abstract::execute()).</p>
 * <br/>
 * <b>Note:</b>
 * <p>These arguments are directly fetched without filtering, it should be carefully processed before use them.</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-controller-abstract.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Controller_Abstract', 'Yaf_Controller_Abstract', \false);
/**
 * <p>A action can be defined in a separate file in Yaf(see Yaf_Controller_Abstract). that is a action method can also be a <b>Yaf_Action_Abstract</b> class.</P>
 * <br/>
 * <p>Since there should be a entry point which can be called by Yaf (as of PHP 5.3, there is a new magic method __invoke, but Yaf is not only works with PHP 5.3+, Yaf choose another magic method execute), you must implement the abstract method Yaf_Action_Abstract::execute() in your custom action class.</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-action-abstract.php
 */
abstract class Yaf_Action_Abstract extends \Yaf_Controller_Abstract
{
    /**
     * @var Yaf_Controller_Abstract
     */
    protected $_controller;
    /**
     * <p>user should always define this method for a action, this is the entry point of an action. <b>Yaf_Action_Abstract::execute()</b> may have arguments.</p>
     * <br/>
     * <b>Note:</b>
     * <p>The value retrieved from the request is not safe. you should do some filtering work before you use it.</p>
     * @link https://secure.php.net/manual/en/yaf-action-abstract.execute.php
     *
     * @param mixed ... unlimited number of arguments
     * @return mixed
     */
    public abstract function execute();
    /**
     * retrieve current controller object.
     *
     * @link https://secure.php.net/manual/en/yaf-action-abstract.getcontroller.php
     *
     * @return Yaf_Controller_Abstract
     */
    public function getController()
    {
    }
    public function getControllerName()
    {
    }
}
/**
 * <p>A action can be defined in a separate file in Yaf(see Yaf_Controller_Abstract). that is a action method can also be a <b>Yaf_Action_Abstract</b> class.</P>
 * <br/>
 * <p>Since there should be a entry point which can be called by Yaf (as of PHP 5.3, there is a new magic method __invoke, but Yaf is not only works with PHP 5.3+, Yaf choose another magic method execute), you must implement the abstract method Yaf_Action_Abstract::execute() in your custom action class.</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-action-abstract.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Action_Abstract', 'Yaf_Action_Abstract', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-config-abstract.php
 */
abstract class Yaf_Config_Abstract implements \Iterator, \ArrayAccess, \Countable
{
    /**
     * @var array
     */
    protected $_config = null;
    /**
     * @var bool
     */
    protected $_readonly = \true;
    /**
     * @link https://secure.php.net/manual/en/yaf-config-abstract.get.php
     *
     * @param string $name
     * @return mixed
     */
    public abstract function get($name = null);
    /**
     * @link https://secure.php.net/manual/en/yaf-config-abstract.set.php
     *
     * @param string $name
     * @param mixed $value
     * @return Yaf_Config_Abstract
     */
    public abstract function set($name, $value);
    public function count()
    {
    }
    public function rewind()
    {
    }
    public function current()
    {
    }
    public function key()
    {
    }
    public function next()
    {
    }
    public function valid()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-config-abstract.readonly.php
     *
     * @return bool
     */
    public abstract function readonly();
    /**
     * @link https://secure.php.net/manual/en/yaf-config-abstract.toarray.php
     *
     * @return array
     */
    public abstract function toArray();
    public function offsetSet($name, $value)
    {
    }
    public function offsetUnset($name)
    {
    }
    public function offsetExists($name)
    {
    }
    public function offsetGet($name = '')
    {
    }
    public function __get($name = '')
    {
    }
    public function __isset($name)
    {
    }
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-config-abstract.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Config_Abstract', 'Yaf_Config_Abstract', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-request-abstract.php
 */
abstract class Yaf_Request_Abstract
{
    public const SCHEME_HTTP = 'http';
    public const SCHEME_HTTPS = 'https';
    /**
     * @var string
     */
    public $module;
    /**
     * @var string
     */
    public $controller;
    /**
     * @var string
     */
    public $action;
    /**
     * @var string
     */
    public $method;
    /**
     * @var array
     */
    protected $params;
    /**
     * @var string
     */
    protected $language;
    /**
     * @var Yaf_Exception
     */
    protected $_exception;
    /**
     * @var string
     */
    protected $_base_uri = "";
    /**
     * @var string
     */
    protected $uri = "";
    /**
     * @var string
     */
    protected $dispatched = "";
    /**
     * @var string
     */
    protected $routed = "";
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.isget.php
     *
     * @return bool
     */
    public function isGet()
    {
    }
    public function isDelete()
    {
    }
    public function isPatch()
    {
    }
    public function getRaw()
    {
    }
    public function clearParams()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.ispost.php
     *
     * @return bool
     */
    public function isPost()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.isput.php
     *
     * @return bool
     */
    public function isPut()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.ishead.php
     *
     * @return bool
     */
    public function isHead()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.isoptions.php
     *
     * @return bool
     */
    public function isOptions()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.iscli.php
     *
     * @return bool
     */
    public function isCli()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.isdispached.php
     *
     * @return bool
     */
    public final function isDispatched()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.isrouted.php
     *
     * @return bool
     */
    public final function isRouted()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.isxmlhttprequest.php
     *
     * @return bool
     */
    public function isXmlHttpRequest()
    {
    }
    /**
     * Retrieve $_SERVER variable
     *
     * @link https://secure.php.net/manual/en/yaf-request-abstract.getserver.php
     *
     * @param string $name the variable name, if not provided returns all
     * @param string $default if this parameter is provide, this will be returned if the variable can not be found
     *
     * @return mixed
     */
    public function getServer($name = null, $default = null)
    {
    }
    /**
     * Retrieve $_ENV variable
     *
     * @link https://secure.php.net/manual/en/yaf-request-abstract.getenv.php
     *
     * @param string $name the variable name, if not provided returns all
     * @param string $default if this parameter is provide, this will be returned if the variable can not be found
     *
     * @return mixed
     */
    public function getEnv($name = null, $default = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.getparam.php
     *
     * @param string $name
     * @param string $default
     *
     * @return mixed
     */
    public function getParam($name = '', $default = '')
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.getparams.php
     *
     * @return array
     */
    public function getParams()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.getexception.php
     *
     * @return Yaf_Exception
     */
    public function getException()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.getmoudlename.php
     *
     * @return string
     */
    public function getModuleName()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.getcontrollername.php
     *
     * @return string
     */
    public function getControllerName()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.getactionname.php
     *
     * @return string
     */
    public function getActionName()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.setparam.php
     *
     * @param string|array $name the variable name, or an array of key=>value pairs
     * @param string $value
     *
     * @return Yaf_Request_Abstract|bool
     */
    public function setParam($name, $value = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.setmodulename.php
     *
     * @param string $module
     * @param bool $format_name
     *
     * @return Yaf_Request_Abstract|bool
     */
    public function setModuleName($module, $format_name = \true)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.setcontrollername.php
     *
     * @param string $controller
     * @param bool $format_name
     *
     * @return Yaf_Request_Abstract|bool
     */
    public function setControllerName($controller, $format_name = \true)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.setactionname.php
     *
     * @param string $action
     * @param bool $format_name
     *
     * @return Yaf_Request_Abstract|bool
     */
    public function setActionName($action, $format_name = \true)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.getmethod.php
     *
     * @return string
     */
    public function getMethod()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.getlanguage.php
     *
     * @return string
     */
    public function getLanguage()
    {
    }
    /**
     * <p>Set base URI, base URI is used when doing routing, in routing phase request URI is used to route a request, while base URI is used to skip the leading part(base URI) of request URI. That is, if comes a request with request URI a/b/c, then if you set base URI to "a/b", only "/c" will be used in routing phase.</p>
     * <br/>
     * <b>Note:</b>
     * <p>generally, you don't need to set this, Yaf will determine it automatically.</p>
     *
     * @link https://secure.php.net/manual/en/yaf-request-abstract.setbaseuri.php
     *
     * @param string $uri base URI
     *
     * @return bool
     */
    public function setBaseUri($uri)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.getbaseuri.php
     *
     * @return string
     */
    public function getBaseUri()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-abstract.getrequesturi.php
     *
     * @return string
     */
    public function getRequestUri()
    {
    }
    /**
     * @param string $uri request URI
     * @link https://secure.php.net/manual/en/yaf-request-abstract.setrequesturi.php
     *
     * @since 2.1.0
     */
    public function setRequestUri($uri)
    {
    }
    /**
     * Set request as dispatched
     *
     * @link https://secure.php.net/manual/en/yaf-request-abstract.setdispatched.php
     *
     * @return bool
     */
    public final function setDispatched($dispatched = null)
    {
    }
    /**
     * Set request as routed
     *
     * @link https://secure.php.net/manual/en/yaf-request-abstract.setrouted.php
     *
     * @return Yaf_Request_Abstract|bool
     */
    public final function setRouted($flag = null)
    {
    }
    public function get($name = null, $default = null)
    {
    }
    public function getFiles($name = null, $default = null)
    {
    }
    public function getCookie($name = null, $default = null)
    {
    }
    public function getPost($name = null, $default = null)
    {
    }
    public function getRequest($name = null, $default = null)
    {
    }
    public function getQuery($name = null, $default = null)
    {
    }
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-request-abstract.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Request_Abstract', 'Yaf_Request_Abstract', \false);
/**
 * <p>Plugins allow for easy extensibility and customization of the framework.</p>
 * <br/>
 * <p>Plugins are classes. The actual class definition will vary based on the component -- you may need to implement this interface, but the fact remains that the plugin is itself a class.</p>
 * <br/>
 * <p>A plugin could be loaded into Yaf by using Yaf_Dispatcher::registerPlugin(), after registered, All the methods which the plugin implemented according to this interface, will be called at the proper time.</p>
 * @link https://secure.php.net/manual/en/class.yaf-plugin-abstract.php
 */
abstract class Yaf_Plugin_Abstract
{
    /**
     * This is the earliest hook in Yaf plugin hook system, if a custom plugin implement this method, then it will be called before routing a request.
     *
     * @link https://secure.php.net/manual/en/yaf-plugin-abstract.routerstartup.php
     *
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstract $response
     *
     * @return bool true
     */
    public function routerStartup(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
    {
    }
    /**
     * This hook will be trigged after the route process finished, this hook is usually used for login check.
     *
     * @link https://secure.php.net/manual/en/yaf-plugin-abstract.routershutdown.php
     *
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstract $response
     *
     * @return bool true
     */
    public function routerShutdown(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-plugin-abstract.dispatchloopstartup.php
     *
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstract $response
     *
     * @return bool true
     */
    public function dispatchLoopStartup(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
    {
    }
    /**
     * This is the latest hook in Yaf plugin hook system, if a custom plugin implement this method, then it will be called after the dispatch loop finished.
     *
     * @link https://secure.php.net/manual/en/yaf-plugin-abstract.dispatchloopshutdown.php
     *
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstract $response
     *
     * @return bool true
     */
    public function dispatchLoopShutdown(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-plugin-abstract.predispatch.php
     *
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstract $response
     *
     * @return bool true
     */
    public function preDispatch(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-plugin-abstract.postdispatch.php
     *
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstract $response
     *
     * @return bool true
     */
    public function postDispatch(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-plugin-abstract.preresponse.php
     *
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstract $response
     *
     * @return bool true
     */
    public function preResponse(\Yaf_Request_Abstract $request, \Yaf_Response_Abstract $response)
    {
    }
}
/**
 * <p>Plugins allow for easy extensibility and customization of the framework.</p>
 * <br/>
 * <p>Plugins are classes. The actual class definition will vary based on the component -- you may need to implement this interface, but the fact remains that the plugin is itself a class.</p>
 * <br/>
 * <p>A plugin could be loaded into Yaf by using Yaf_Dispatcher::registerPlugin(), after registered, All the methods which the plugin implemented according to this interface, will be called at the proper time.</p>
 * @link https://secure.php.net/manual/en/class.yaf-plugin-abstract.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Plugin_Abstract', 'Yaf_Plugin_Abstract', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-response-abstract.php
 */
abstract class Yaf_Response_Abstract
{
    public const DEFAULT_BODY = "content";
    /**
     * @var string
     */
    protected $_header;
    /**
     * @var string
     */
    protected $_body;
    /**
     * @var bool
     */
    protected $_sendheader;
    /**
     * @link https://secure.php.net/manual/en/yaf-response-abstract.construct.php
     */
    public function __construct()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-response-abstract.destruct.php
     */
    public function __destruct()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-response-abstract.clone.php
     */
    private function __clone()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-response-abstract.tostring.php
     */
    public function __toString()
    {
    }
    /**
     * Send response
     * @link https://secure.php.net/manual/en/yaf-response-abstract.response.php
     *
     * @return void
     */
    public function response()
    {
    }
    /**
     * Set response header
     * @link https://secure.php.net/manual/en/yaf-response-abstract.setheader.php
     *
     * @param string $name header name
     * @param string $value header value
     * @param bool $rep
     *
     * @return bool
     */
    public function setHeader($name, $value, $rep = \false)
    {
    }
    /**
     * Set content to response
     *
     * @link https://secure.php.net/manual/en/yaf-response-abstract.setbody.php
     *
     * @param string $body content string
     * @param string $name <p>the content key, you can set a content with a key, if you don't specific, then Yaf_Response_Abstract::DEFAULT_BODY will be used</p>
     * <br/>
     * <b>Note:</b>
     * <p>this parameter is introduced as of 2.2.0</p>
     *
     * @return bool
     */
    public function setBody($body, $name = self::DEFAULT_BODY)
    {
    }
    /**
     * append a content to a exists content block
     *
     * @link https://secure.php.net/manual/en/yaf-response-abstract.appendbody.php
     *
     * @param string $body content string
     * @param string $name <p>the content key, you can set a content with a key, if you don't specific, then Yaf_Response_Abstract::DEFAULT_BODY will be used</p>
     * <br/>
     * <b>Note:</b>
     * <p>this parameter is introduced as of 2.2.0</p>
     *
     * @return bool
     */
    public function appendBody($body, $name = self::DEFAULT_BODY)
    {
    }
    /**
     * prepend a content to a exists content block
     *
     * @link https://secure.php.net/manual/en/yaf-response-abstract.prependbody.php
     *
     * @param string $body content string
     * @param string $name <p>the content key, you can set a content with a key, if you don't specific, then Yaf_Response_Abstract::DEFAULT_BODY will be used</p>
     * <br/>
     * <b>Note:</b>
     * <p>this parameter is introduced as of 2.2.0</p>
     *
     * @return bool
     */
    public function prependBody($body, $name = self::DEFAULT_BODY)
    {
    }
    /**
     * Clear existing content
     *
     * @link https://secure.php.net/manual/en/yaf-response-abstract.clearbody.php
     *
     * @param string $name <p>the content key, you can set a content with a key, if you don't specific, then Yaf_Response_Abstract::DEFAULT_BODY will be used</p>
     * <br/>
     * <b>Note:</b>
     * <p>this parameter is introduced as of 2.2.0</p>
     *
     * @return bool
     */
    public function clearBody($name = self::DEFAULT_BODY)
    {
    }
    /**
     * Retrieve an existing content
     *
     * @link https://secure.php.net/manual/en/yaf-response-abstract.getbody.php
     *
     * @param string|null $name <p>the content key, if you don't specific, then Yaf_Response_Abstract::DEFAULT_BODY will be used. if you pass in a NULL, then all contents will be returned as a array</p>
     * <br/>
     * <b>Note:</b>
     * <p>this parameter is introduced as of 2.2.0</p>
     *
     * @return mixed
     */
    public function getBody($name = self::DEFAULT_BODY)
    {
    }
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-response-abstract.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Response_Abstract', 'Yaf_Response_Abstract', \false);
/**
 * Yaf provides a ability for developers to use custom view engine instead of build-in engine which is Yaf_View_Simple. There is a example to explain how to do this, please see Yaf_Dispatcher::setView()
 *
 * @link https://secure.php.net/manual/en/class.yaf-view-interface.php
 */
interface Yaf_View_Interface
{
    /**
     * Assign values to View engine, then the value can access directly by name in template.
     *
     * @link https://secure.php.net/manual/en/yaf-view-interface.assign.php
     *
     * @param string|array $name
     * @param string $value
     * @return bool
     */
    public function assign($name, $value = '');
    /**
     * Render a template and output the result immediately.
     *
     * @link https://secure.php.net/manual/en/yaf-view-interface.display.php
     *
     * @param string $tpl
     * @param array $tpl_vars
     * @return bool
     */
    public function display($tpl, $tpl_vars = null);
    /**
     * @link https://secure.php.net/manual/en/yaf-view-interface.getscriptpath.php
     *
     * @return string
     */
    public function getScriptPath($request = null);
    /**
     * Render a template and return the result.
     *
     * @link https://secure.php.net/manual/en/yaf-view-interface.render.php
     *
     * @param string $tpl
     * @param array $tpl_vars
     * @return string
     */
    public function render($tpl, $tpl_vars = null);
    /**
     * Set the templates base directory, this is usually called by Yaf_Dispatcher
     *
     * @link https://secure.php.net/manual/en/yaf-view-interface.setscriptpath.php
     *
     * @param string $template_dir An absolute path to the template directory, by default, Yaf_Dispatcher use application.directory . "/views" as this parameter.
     */
    public function setScriptPath($template_dir);
}
/**
 * Yaf provides a ability for developers to use custom view engine instead of build-in engine which is Yaf_View_Simple. There is a example to explain how to do this, please see Yaf_Dispatcher::setView()
 *
 * @link https://secure.php.net/manual/en/class.yaf-view-interface.php
 */
\class_alias('DEPTRAC_202401\\Yaf_View_Interface', 'Yaf_View_Interface', \false);
/**
 * <b>Yaf_Route_Interface</b> used for developer defined their custom route.
 *
 * @link https://secure.php.net/manual/en/class.yaf-route-interface.php
 */
interface Yaf_Route_Interface
{
    /**
     * <p><b>Yaf_Route_Interface::route()</b> is the only method that a custom route should implement.</p><br/>
     * <p>if this method return TRUE, then the route process will be end. otherwise, Yaf_Router will call next route in the route stack to route request.</p><br/>
     * <p>This method would set the route result to the parameter request, by calling Yaf_Request_Abstract::setControllerName(), Yaf_Request_Abstract::setActionName() and Yaf_Request_Abstract::setModuleName().</p><br/>
     * <p>This method should also call Yaf_Request_Abstract::setRouted() to make the request routed at last.</p>
     *
     * @link https://secure.php.net/manual/en/yaf-route-interface.route.php
     *
     * @param Yaf_Request_Abstract $request
     * @return bool
     */
    public function route($request);
    /**
     * <p><b>Yaf_Route_Interface::assemble()</b> - assemble a request</p><br/>
     * <p>this method returns a url according to the argument info, and append query strings to the url according to the argument query.</p>
     * <p>a route should implement this method according to its own route rules, and do a reverse progress.</p>
     *
     * @link https://secure.php.net/manual/en/yaf-route-interface.assemble.php
     *
     * @param array $info
     * @param array|null $query
     * @return bool
     */
    public function assemble(array $info, ?array $query = null);
}
/**
 * <b>Yaf_Route_Interface</b> used for developer defined their custom route.
 *
 * @link https://secure.php.net/manual/en/class.yaf-route-interface.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Route_Interface', 'Yaf_Route_Interface', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception.php
 */
class Yaf_Exception extends \Exception
{
    protected $message;
    protected $code;
    protected $previous;
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Exception', 'Yaf_Exception', \false);
class Yaf_Response_Http extends \Yaf_Response_Abstract
{
    /**
     * @var int
     */
    protected $_response_code = 0;
    private function __clone()
    {
    }
    /**
     * @return string
     */
    public function __toString()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-response-abstract.setheader.php
     *
     * @param string $name
     * @param string $value
     * @param bool $rep
     * @param int $response_code
     *
     * @return bool
     */
    public function setHeader($name, $value, $rep = \false, $response_code = 0)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-response-abstract.setallheaders.php
     *
     * @param array $headers
     *
     * @return bool
     */
    public function setAllHeaders($headers)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-response-abstract.getheader.php
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getHeader($name = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-response-abstract.clearheaders.php
     *
     *
     * @return Yaf_Response_Abstract|false
     */
    public function clearHeaders()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-response-abstract.setredirect.php
     *
     * @param string $url
     *
     * @return bool
     */
    public function setRedirect($url)
    {
    }
    /**
     * send response
     *
     * @link https://secure.php.net/manual/en/yaf-response-abstract.response.php
     *
     * @return bool
     */
    public function response()
    {
    }
}
\class_alias('DEPTRAC_202401\\Yaf_Response_Http', 'Yaf_Response_Http', \false);
class Yaf_Response_Cli extends \Yaf_Response_Abstract
{
    private function __clone()
    {
    }
    /**
     * @return string
     */
    public function __toString()
    {
    }
}
\class_alias('DEPTRAC_202401\\Yaf_Response_Cli', 'Yaf_Response_Cli', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-request-http.php
 */
class Yaf_Request_Http extends \Yaf_Request_Abstract
{
    /**
     * Retrieve $_GET variable
     *
     * @link https://secure.php.net/manual/en/yaf-request-http.getquery.php
     *
     * @param string $name the variable name, if not provided returns all
     * @param mixed $default if this parameter is provide, this will be returned if the variable can not be found
     *
     * @return mixed
     */
    public function getQuery($name = null, $default = null)
    {
    }
    /**
     * Retrieve $_REQUEST variable
     *
     * @link https://secure.php.net/manual/en/yaf-request-http.getrequest.php
     *
     * @param string $name the variable name, if not provided returns all
     * @param mixed $default if this parameter is provide, this will be returned if the variable can not be found
     *
     * @return mixed
     */
    public function getRequest($name = null, $default = null)
    {
    }
    /**
     * Retrieve $_POST variable
     *
     * @link https://secure.php.net/manual/en/yaf-request-http.getpost.php
     *
     * @param string $name the variable name, if not provided returns all
     * @param mixed $default if this parameter is provide, this will be returned if the variable can not be found
     *
     * @return mixed
     */
    public function getPost($name = null, $default = null)
    {
    }
    /**
     * Retrieve $_COOKIE variable
     *
     * @link https://secure.php.net/manual/en/yaf-request-http.getcookie.php
     *
     * @param string $name the variable name, if not provided returns all
     * @param mixed $default if this parameter is provide, this will be returned if the variable can not be found
     *
     * @return mixed
     */
    public function getCookie($name = null, $default = null)
    {
    }
    /**
     * Retrieve $_FILES variable
     *
     * @link https://secure.php.net/manual/en/yaf-request-http.getfiles.php
     *
     * @param string $name the variable name, if not provided returns all
     * @param mixed $default if this parameter is provide, this will be returned if the variable can not be found
     *
     * @return mixed
     */
    public function getFiles($name = null, $default = null)
    {
    }
    /**
     * Retrieve variable from client, this method will search the name in $_REQUEST params, if the name is not found, then will search in $_POST, $_GET, $_COOKIE, $_SERVER
     *
     * @link https://secure.php.net/manual/en/yaf-request-http.get.php
     *
     * @param string $name the variable name
     * @param string $default if this parameter is provide, this will be returned if the variable can not be found
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
    }
    /**
     * Check the request whether it is a Ajax Request
     *
     * <br/>
     * <b>Note:</b>
     * <p>
     * This method depends on the request header: HTTP_X_REQUESTED_WITH, some Javascript library doesn't set this header while doing Ajax request
     * </p>
     * @link https://secure.php.net/manual/en/yaf-request-http.isxmlhttprequest.php
     *
     * @return bool
     */
    public function isXmlHttpRequest()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-http.construct.php
     *
     * @param string $request_uri
     * @param string $base_uri
     */
    public function __construct($request_uri = '', $base_uri = '')
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-http.clone.php
     */
    private function __clone()
    {
    }
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-request-http.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Request_Http', 'Yaf_Request_Http', \false);
/**
 * <b>Yaf_Request_Simple</b> is particularly used for test purpose. ie. simulate a spacial request under CLI mode.
 * @link https://secure.php.net/manual/en/class.yaf-request-simple.php
 */
class Yaf_Request_Simple extends \Yaf_Request_Abstract
{
    /**
     * Retrieve $_GET variable
     *
     * @link https://secure.php.net/manual/en/yaf-request-simple.getquery.php
     *
     * @param string $name the variable name, if not provided returns all
     * @param string $default if this parameter is provide, this will be returned if the variable can not be found
     *
     * @return mixed
     */
    public function getQuery($name = null, $default = null)
    {
    }
    /**
     * Retrieve $_REQUEST variable
     *
     * @link https://secure.php.net/manual/en/yaf-request-simple.getrequest.php
     *
     * @param string $name the variable name, if not provided returns all
     * @param string $default if this parameter is provide, this will be returned if the variable can not be found
     *
     * @return mixed
     */
    public function getRequest($name = null, $default = null)
    {
    }
    /**
     * Retrieve $_POST variable
     *
     * @link https://secure.php.net/manual/en/yaf-request-simple.getpost.php
     *
     * @param string $name the variable name, if not provided returns all
     * @param string $default if this parameter is provide, this will be returned if the variable can not be found
     *
     * @return mixed
     */
    public function getPost($name = null, $default = null)
    {
    }
    /**
     * Retrieve $_Cookie variable
     *
     * @link https://secure.php.net/manual/en/yaf-request-simple.getcookie.php
     *
     * @param string $name the variable name, if not provided returns all
     * @param string $default if this parameter is provide, this will be returned if the variable can not be found
     *
     * @return mixed
     */
    public function getCookie($name = null, $default = null)
    {
    }
    /**
     * @param mixed $name
     * @param null $default
     *
     * @return array
     */
    public function getFiles($name = null, $default = null)
    {
    }
    /**
     * Retrieve variable from client, this method will search the name in $_REQUEST params, if the name is not found, then will search in $_POST, $_GET, $_COOKIE, $_SERVER
     *
     * @link https://secure.php.net/manual/en/yaf-request-simple.get.php
     *
     * @param string $name the variable name
     * @param string $default if this parameter is provide, this will be returned if the variable can not be found
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
    }
    /**
     * Check the request whether it is a Ajax Request
     *
     * <br/>
     * <b>Note:</b>
     * <p>
     * This method depends on the request header: HTTP_X_REQUESTED_WITH, some Javascript library doesn't set this header while doing Ajax request
     * </p>
     * @link https://secure.php.net/manual/en/yaf-request-simple.isxmlhttprequest.php
     *
     * @return bool
     */
    public function isXmlHttpRequest()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-simple.construct.php
     *
     * @param string $method
     * @param string $module
     * @param string $controller
     * @param string $action
     * @param array $params
     *
     * @throws Yaf_Exception_TypeError
     */
    public function __construct($method = '', $module = '', $controller = '', $action = '', $params = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-request-simple.clone.php
     */
    private function __clone()
    {
    }
}
/**
 * <b>Yaf_Request_Simple</b> is particularly used for test purpose. ie. simulate a spacial request under CLI mode.
 * @link https://secure.php.net/manual/en/class.yaf-request-simple.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Request_Simple', 'Yaf_Request_Simple', \false);
/**
 * <p>Yaf_Config_Ini enables developers to store configuration data in a familiar INI format and read them in the application by using nested object property syntax. The INI format is specialized to provide both the ability to have a hierarchy of configuration data keys and inheritance between configuration data sections. Configuration data hierarchies are supported by separating the keys with the dot or period character ("."). A section may extend or inherit from another section by following the section name with a colon character (":") and the name of the section from which data are to be inherited.</p><br/>
 * <b>Note:</b>
 * <p>Yaf_Config_Ini utilizes the » parse_ini_file() PHP function. Please review this documentation to be aware of its specific behaviors, which propagate to Yaf_Config_Ini, such as how the special values of "TRUE", "FALSE", "yes", "no", and "NULL" are handled.</p>
 * @link https://secure.php.net/manual/en/class.yaf-config-ini.php
 */
class Yaf_Config_Ini extends \Yaf_Config_Abstract implements \Iterator, \ArrayAccess, \Countable
{
    /**
     * @see Yaf_Config_Abstract::get
     */
    public function __get($name = null)
    {
    }
    /**
     * @see Yaf_Config_Abstract::set
     */
    public function __set($name, $value)
    {
    }
    /**
     * @see Yaf_Config_Abstract::get
     */
    public function get($name = null)
    {
    }
    /**
     * @see Yaf_Config_Abstract::set
     * @deprecated not_implemented
     */
    public function set($name, $value)
    {
    }
    /**
     * @see Yaf_Config_Abstract::toArray
     */
    public function toArray()
    {
    }
    /**
     * @see Yaf_Config_Abstract::readonly
     */
    public function readonly()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-config-ini.construct.php
     *
     * @param string $config_file path to an INI configure file
     * @param string $section which section in that INI file you want to be parsed
     *
     * @throws Yaf_Exception_TypeError
     */
    public function __construct($config_file, $section = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-config-ini.isset.php
     * @param string $name
     */
    public function __isset($name)
    {
    }
    /**
     * @see Countable::count
     */
    public function count()
    {
    }
    /**
     * @see Iterator::rewind
     */
    public function rewind()
    {
    }
    /**
     * @see Iterator::current
     */
    public function current()
    {
    }
    /**
     * @see Iterator::next
     */
    public function next()
    {
    }
    /**
     * @see Iterator::valid
     */
    public function valid()
    {
    }
    /**
     * @see Iterator::key
     */
    public function key()
    {
    }
    /**
     * @see ArrayAccess::offsetUnset
     * @deprecated not_implemented
     */
    public function offsetUnset($name)
    {
    }
    /**
     * @see ArrayAccess::offsetGet
     */
    public function offsetGet($name = '')
    {
    }
    /**
     * @see ArrayAccess::offsetExists
     */
    public function offsetExists($name)
    {
    }
    /**
     * @see ArrayAccess::offsetSet
     */
    public function offsetSet($name, $value)
    {
    }
}
/**
 * <p>Yaf_Config_Ini enables developers to store configuration data in a familiar INI format and read them in the application by using nested object property syntax. The INI format is specialized to provide both the ability to have a hierarchy of configuration data keys and inheritance between configuration data sections. Configuration data hierarchies are supported by separating the keys with the dot or period character ("."). A section may extend or inherit from another section by following the section name with a colon character (":") and the name of the section from which data are to be inherited.</p><br/>
 * <b>Note:</b>
 * <p>Yaf_Config_Ini utilizes the » parse_ini_file() PHP function. Please review this documentation to be aware of its specific behaviors, which propagate to Yaf_Config_Ini, such as how the special values of "TRUE", "FALSE", "yes", "no", and "NULL" are handled.</p>
 * @link https://secure.php.net/manual/en/class.yaf-config-ini.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Config_Ini', 'Yaf_Config_Ini', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-config-simple.php
 */
class Yaf_Config_Simple extends \Yaf_Config_Abstract implements \Iterator, \ArrayAccess, \Countable
{
    /**
     * @see Yaf_Config_Abstract::get
     */
    public function __get($name = null)
    {
    }
    /**
     * @see Yaf_Config_Abstract::set
     */
    public function __set($name, $value)
    {
    }
    /**
     * @see Yaf_Config_Abstract::get
     */
    public function get($name = null)
    {
    }
    /**
     * @see Yaf_Config_Abstract::set
     */
    public function set($name, $value)
    {
    }
    /**
     * @see Yaf_Config_Abstract::toArray
     */
    public function toArray()
    {
    }
    /**
     * @see Yaf_Config_Abstract::readonly
     */
    public function readonly()
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-config-simple.construct.php
     *
     * @param array $config
     * @param bool $readonly
     */
    public function __construct($config, $readonly = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-config-simple.isset.php
     * @param string $name
     */
    public function __isset($name)
    {
    }
    /**
     * @see Countable::count
     */
    public function count()
    {
    }
    /**
     * @see Iterator::rewind
     */
    public function rewind()
    {
    }
    /**
     * @see Iterator::current
     */
    public function current()
    {
    }
    /**
     * @see Iterator::next
     */
    public function next()
    {
    }
    /**
     * @see Iterator::valid
     */
    public function valid()
    {
    }
    /**
     * @see Iterator::key
     */
    public function key()
    {
    }
    /**
     * @see ArrayAccess::offsetUnset
     */
    public function offsetUnset($name)
    {
    }
    /**
     * @see ArrayAccess::offsetGet
     */
    public function offsetGet($name)
    {
    }
    /**
     * @see ArrayAccess::offsetExists
     */
    public function offsetExists($name)
    {
    }
    /**
     * @see ArrayAccess::offsetSet
     */
    public function offsetSet($name, $value)
    {
    }
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-config-simple.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Config_Simple', 'Yaf_Config_Simple', \false);
/**
 * <b>Yaf_View_Simple</b> is the built-in template engine in Yaf, it is a simple but fast template engine, and only support PHP script template.
 * @link https://secure.php.net/manual/en/class.yaf-view-simple.php
 */
class Yaf_View_Simple implements \Yaf_View_Interface
{
    /**
     * @var string
     */
    protected $_tpl_dir;
    /**
     * @var array
     */
    protected $_tpl_vars;
    /**
     * @var array
     */
    protected $_options;
    /**
     * @link https://secure.php.net/manual/en/yaf-view-simple.construct.php
     *
     * @param string $template_dir The base directory of the templates, by default, it is APPLICATION . "/views" for Yaf.
     * @param array|null $options <p>Options for the engine, as of Yaf 2.1.13, you can use short tag
     * "<?=$var?>" in your template(regardless of "short_open_tag"),
     * so comes a option named "short_tag",  you can switch this off
     * to prevent use short_tag in template.
     * </p>
     * @throws Yaf_Exception_TypeError
     */
    public final function __construct($template_dir, ?array $options = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-view-simple.isset.php
     *
     * @param string $name
     */
    public function __isset($name)
    {
    }
    /**
     * assign variable to view engine
     *
     * @link https://secure.php.net/manual/en/yaf-view-simple.assign.php
     *
     * @param string|array $name A string or an array.<br/>if is string, then the next argument $value is required.
     * @param mixed $value mixed value
     * @return Yaf_View_Simple
     */
    public function assign($name, $value = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-view-simple.render.php
     *
     * @param string $tpl
     * @param array $tpl_vars
     *
     * @throws Yaf_Exception_LoadFailed_View
     *
     * @return string|void
     */
    public function render($tpl, $tpl_vars = null)
    {
    }
    /**
     * <p>Render a template and display the result instantly.</p>
     *
     * @link https://secure.php.net/manual/en/yaf-view-simple.display.php
     *
     * @param string $tpl
     * @param array $tpl_vars
     *
     * @throws Yaf_Exception_LoadFailed_View
     *
     * @return bool
     */
    public function display($tpl, $tpl_vars = null)
    {
    }
    /**
     * <p>unlike Yaf_View_Simple::assign(), this method assign a ref value to engine.</p>
     * @link https://secure.php.net/manual/en/yaf-view-simple.assignref.php
     *
     * @param string $name A string name which will be used to access the value in the template.
     * @param mixed &$value mixed value
     *
     * @return Yaf_View_Simple
     */
    public function assignRef($name, &$value)
    {
    }
    /**
     * clear assigned variable
     * @link https://secure.php.net/manual/en/yaf-view-simple.clear.php
     *
     * @param string $name assigned variable name. <br/>if empty, will clear all assigned variables.
     *
     * @return Yaf_View_Simple
     */
    public function clear($name = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-view-simple.setscriptpath.php
     *
     * @param string $template_dir
     *
     * @return Yaf_View_Simple
     */
    public function setScriptPath($template_dir)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-view-simple.getscriptpath.php
     *
     * @return string
     */
    public function getScriptPath($request = null)
    {
    }
    /**
     * <p>Retrieve assigned variable</p>
     * <br/>
     * <b>Note:</b>
     * <p>$name parameter can be empty since 2.1.11</p>
     * @link https://secure.php.net/manual/en/yaf-view-simple.get.php
     *
     * @param string $name <p>the assigned variable name</p>
     * <br/>
     * <p>if this is empty, all assigned variables will be returned</p>
     *
     * @return mixed
     */
    public function __get($name = null)
    {
    }
    /**
     * <p>This is a alternative and easier way to Yaf_View_Simple::assign().</p>
     *
     * @link https://secure.php.net/manual/en/yaf-view-simple.set.php
     *
     * @param string $name A string value name.
     * @param mixed $value mixed value
     */
    public function __set($name, $value = null)
    {
    }
    /**
     * <p>Render a string template and return the result.</p>
     * @link https://secure.php.net/manual/en/yaf-view-simple.eval.php
     * @param string $tpl_str string template
     * @param array $vars
     * @return void|false return FALSE on failure
     */
    public function eval($tpl_str, $vars = null)
    {
    }
    public function get($name = '')
    {
    }
}
/**
 * <b>Yaf_View_Simple</b> is the built-in template engine in Yaf, it is a simple but fast template engine, and only support PHP script template.
 * @link https://secure.php.net/manual/en/class.yaf-view-simple.php
 */
\class_alias('DEPTRAC_202401\\Yaf_View_Simple', 'Yaf_View_Simple', \false);
/**
 * <p>by default, Yaf_Router only have a <b>Yaf_Route_Static</b> as its default route.</p>
 * <br/>
 * <p><b>Yaf_Route_Static</b> is designed to handle 80% of normal requirements.</p>
 * <br/>
 * <b>Note:</b>
 * <p> it is unnecessary to instance a <b>Yaf_Route_Static</b>, also unnecessary to add it into Yaf_Router's routes stack, since there is always be one in Yaf_Router's routes stack, and always be called at the last time.</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-route-static.php
 */
class Yaf_Route_Static implements \Yaf_Route_Interface
{
    /**
     * @param string $uri
     *
     * @return bool
     * @deprecated not_implemented
     * @link https://secure.php.net/manual/en/yaf-route-static.match.php
     */
    public function match($uri)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-route-static.route.php
     *
     * @param Yaf_Request_Abstract $request
     *
     * @return bool always TRUE
     */
    public function route($request)
    {
    }
    /**
     * <p><b>Yaf_Route_Static::assemble()</b> - Assemble a url</p>
     *
     * @link https://secure.php.net/manual/en/yaf-route-static.assemble.php
     *
     * @param array $info
     * @param array|null $query
     * @return bool
     */
    public function assemble(array $info, ?array $query = null)
    {
    }
}
/**
 * <p>by default, Yaf_Router only have a <b>Yaf_Route_Static</b> as its default route.</p>
 * <br/>
 * <p><b>Yaf_Route_Static</b> is designed to handle 80% of normal requirements.</p>
 * <br/>
 * <b>Note:</b>
 * <p> it is unnecessary to instance a <b>Yaf_Route_Static</b>, also unnecessary to add it into Yaf_Router's routes stack, since there is always be one in Yaf_Router's routes stack, and always be called at the last time.</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-route-static.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Route_Static', 'Yaf_Route_Static', \false);
/**
 * <p><b>Yaf_Route_Simple</b> will match the query string, and find the route info.</p>
 * <br/>
 * <p>all you need to do is tell <b>Yaf_Route_Simple</b> what key in the $_GET is module, what key is controller, and what key is action.</p>
 * <br/>
 * <p>Yaf_Route_Simple::route() will always return TRUE, so it is important put <b>Yaf_Route_Simple</b> in the front of the Route stack, otherwise all the other routes will not be called</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-route-simple.php
 */
final class Yaf_Route_Simple implements \Yaf_Route_Interface
{
    /**
     * @var string
     */
    protected $controller;
    /**
     * @var string
     */
    protected $module;
    /**
     * @var string
     */
    protected $action;
    /**
     * <p>Yaf_Route_Simple will get route info from query string. and the parameters of this constructor will used as keys while searching for the route info in $_GET.</p>
     *
     * @link https://secure.php.net/manual/en/yaf-route-simple.construct.php
     *
     * @param string $module_name
     * @param string $controller_name
     * @param string $action_name
     *
     * @throws Yaf_Exception_TypeError
     */
    public function __construct($module_name, $controller_name, $action_name)
    {
    }
    /**
     * <p>see Yaf_Route_Simple::__construct()</p>
     *
     * @link https://secure.php.net/manual/en/yaf-route-simple.route.php
     *
     * @param Yaf_Request_Abstract $request
     *
     * @return bool always TRUE
     */
    public function route($request)
    {
    }
    /**
     * <p><b>Yaf_Route_Simple::assemble()</b> - Assemble a url</p>
     *
     * @link https://secure.php.net/manual/en/yaf-route-simple.assemble.php
     *
     * @param array $info
     * @param array|null $query
     * @return bool
     */
    public function assemble(array $info, ?array $query = null)
    {
    }
}
/**
 * <p><b>Yaf_Route_Simple</b> will match the query string, and find the route info.</p>
 * <br/>
 * <p>all you need to do is tell <b>Yaf_Route_Simple</b> what key in the $_GET is module, what key is controller, and what key is action.</p>
 * <br/>
 * <p>Yaf_Route_Simple::route() will always return TRUE, so it is important put <b>Yaf_Route_Simple</b> in the front of the Route stack, otherwise all the other routes will not be called</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-route-simple.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Route_Simple', 'Yaf_Route_Simple', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-route-supervar.php
 */
final class Yaf_Route_Supervar implements \Yaf_Route_Interface
{
    /**
     * @var string
     */
    protected $_var_name;
    /**
     * <p>Yaf_Route_Supervar is similar to Yaf_Route_Static, the difference is that Yaf_Route_Supervar will look for path info in query string, and the parameter supervar_name is the key.</p>
     *
     * @link https://secure.php.net/manual/en/yaf-route-supervar.construct.php
     *
     * @param string $supervar_name The name of key.
     *
     * @throws Yaf_Exception_TypeError
     */
    public function __construct($supervar_name)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-route-supervar.route.php
     *
     * @param Yaf_Request_Abstract $request
     *
     * @return bool If there is a key(which was defined in Yaf_Route_Supervar::__construct()) in $_GET, return TRUE. otherwise return FALSE.
     */
    public function route($request)
    {
    }
    /**
     * <p><b>Yaf_Route_Supervar::assemble()</b> - Assemble a url</p>
     *
     * @link https://secure.php.net/manual/en/yaf-route-supervar.assemble.php
     *
     * @param array $info
     * @param array|null $query
     * @return bool
     */
    public function assemble(array $info, ?array $query = null)
    {
    }
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-route-supervar.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Route_Supervar', 'Yaf_Route_Supervar', \false);
/**
 * <p>For usage, please see the example section of Yaf_Route_Rewrite::__construct()</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-route-rewrite.php
 */
final class Yaf_Route_Rewrite extends \Yaf_Router implements \Yaf_Route_Interface
{
    /**
     * @var string
     */
    protected $_route;
    /**
     * @var array
     */
    protected $_default;
    /**
     * @var array
     */
    protected $_verify;
    /**
     * @link https://secure.php.net/manual/en/yaf-route-rewrite.construct.php
     *
     * @param string $match A pattern, will be used to match a request uri, if doesn't matched, Yaf_Route_Rewrite will return FALSE.
     * @param array $route <p>When the match pattern matches the request uri, Yaf_Route_Rewrite will use this to decide which m/c/a to routed.</p>
     * <br/>
     * <p>either of m/c/a in this array is optional, if you don't assign a specific value, it will be routed to default.</p>
     * @param array $verify
     * @param string $reverse
     *
     * @throws Yaf_Exception_TypeError
     */
    public function __construct($match, array $route, array $verify = null, $reverse = null)
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-route-rewrite.route.php
     *
     * @param Yaf_Request_Abstract $request
     *
     * @return bool
     */
    public function route($request)
    {
    }
    /**
     * <p><b>Yaf_Route_Rewrite::assemble()</b> - Assemble a url</p>
     *
     * @link https://secure.php.net/manual/en/yaf-route-rewrite.assemble.php
     *
     * @param array $info
     * @param array|null $query
     * @return bool
     */
    public function assemble(array $info, ?array $query = null)
    {
    }
    public function match($uri)
    {
    }
}
/**
 * <p>For usage, please see the example section of Yaf_Route_Rewrite::__construct()</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-route-rewrite.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Route_Rewrite', 'Yaf_Route_Rewrite', \false);
/**
 * <p><b>Yaf_Route_Regex</b> is the most flexible route among the Yaf built-in routes.</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-route-regex.php
 */
final class Yaf_Route_Regex extends \Yaf_Router implements \Yaf_Route_Interface
{
    /**
     * @var string
     */
    protected $_route;
    /**
     * @var array
     */
    protected $_default;
    /**
     * @var array
     */
    protected $_maps;
    /**
     * @var array
     */
    protected $_verify;
    /**
     * @var string
     */
    protected $_reverse;
    /**
     * @link https://secure.php.net/manual/en/yaf-route-regex.construct.php
     *
     * @param string $match A complete Regex pattern, will be used to match a request uri, if doesn't matched, Yaf_Route_Regex will return FALSE.
     * @param array $route <p>When the match pattern matches the request uri, Yaf_Route_Regex will use this to decide which m/c/a to routed.</p>
     * <br/>
     * <p>either of m/c/a in this array is optional, if you don't assign a specific value, it will be routed to default.</p>
     * @param array|null $map A array to assign name to the captures in the match result.
     * @param array|null $verify
     * @param string $reverse
     *
     * @throws Yaf_Exception_TypeError
     */
    public function __construct($match, array $route, ?array $map = null, ?array $verify = null, $reverse = null)
    {
    }
    /**
     * Route a incoming request.
     *
     * @link https://secure.php.net/manual/en/yaf-route-regex.route.php
     *
     * @param Yaf_Request_Abstract $request
     *
     * @return bool If the pattern given by the first parameter of Yaf_Route_Regex::_construct() matches the request uri, return TRUE, otherwise return FALSE.
     */
    public function route($request)
    {
    }
    /**
     * <p><b>Yaf_Route_Regex::assemble()</b> - Assemble a url</p>
     *
     * @link https://secure.php.net/manual/en/yaf-route-regex.assemble.php
     *
     * @param array $info
     * @param array $query
     * @return bool
     */
    public function assemble(array $info, ?array $query = null)
    {
    }
    public function match($uri)
    {
    }
}
/**
 * <p><b>Yaf_Route_Regex</b> is the most flexible route among the Yaf built-in routes.</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-route-regex.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Route_Regex', 'Yaf_Route_Regex', \false);
/**
 * <p><b>Yaf_Route_Map</b> is a built-in route, it simply convert a URI endpoint (that part of the URI which comes after the base URI: see Yaf_Request_Abstract::setBaseUri()) to a controller name or action name(depends on the parameter passed to Yaf_Route_Map::__construct()) in following rule: A => controller A. A/B/C => controller A_B_C. A/B/C/D/E => controller A_B_C_D_E.</p>
 * <br/>
 * <p>If the second parameter of Yaf_Route_Map::__construct() is specified, then only the part before delimiter of URI will used to routing, the part after it is used to routing request parameters (see the example section of Yaf_Route_Map::__construct()).</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-route-map.php
 */
final class Yaf_Route_Map implements \Yaf_Route_Interface
{
    /**
     * @var string
     */
    protected $_ctl_router = '';
    /**
     * @var string
     */
    protected $_delimiter;
    /**
     * @link https://secure.php.net/manual/en/yaf-route-map.construct.php
     *
     * @param bool $controller_prefer Whether the result should considering as controller or action
     * @param string $delimiter
     */
    public function __construct($controller_prefer = \false, $delimiter = '')
    {
    }
    /**
     * @link https://secure.php.net/manual/en/yaf-route-map.route.php
     *
     * @param Yaf_Request_Abstract $request
     *
     * @return bool
     */
    public function route($request)
    {
    }
    /**
     * <p><b>Yaf_Route_Map::assemble()</b> - Assemble a url</p>
     *
     * @link https://secure.php.net/manual/en/yaf-route-map.assemble.php
     *
     * @param array $info
     * @param array|null $query
     * @return bool
     */
    public function assemble(array $info, ?array $query = null)
    {
    }
}
/**
 * <p><b>Yaf_Route_Map</b> is a built-in route, it simply convert a URI endpoint (that part of the URI which comes after the base URI: see Yaf_Request_Abstract::setBaseUri()) to a controller name or action name(depends on the parameter passed to Yaf_Route_Map::__construct()) in following rule: A => controller A. A/B/C => controller A_B_C. A/B/C/D/E => controller A_B_C_D_E.</p>
 * <br/>
 * <p>If the second parameter of Yaf_Route_Map::__construct() is specified, then only the part before delimiter of URI will used to routing, the part after it is used to routing request parameters (see the example section of Yaf_Route_Map::__construct()).</p>
 *
 * @link https://secure.php.net/manual/en/class.yaf-route-map.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Route_Map', 'Yaf_Route_Map', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-typeerror.php
 */
class Yaf_Exception_TypeError extends \Yaf_Exception
{
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-typeerror.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Exception_TypeError', 'Yaf_Exception_TypeError', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-startuperror.php
 */
class Yaf_Exception_StartupError extends \Yaf_Exception
{
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-startuperror.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Exception_StartupError', 'Yaf_Exception_StartupError', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-routefaild.php
 */
class Yaf_Exception_RouterFailed extends \Yaf_Exception
{
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-routefaild.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Exception_RouterFailed', 'Yaf_Exception_RouterFailed', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-dispatchfaild.php
 */
class Yaf_Exception_DispatchFailed extends \Yaf_Exception
{
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-dispatchfaild.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Exception_DispatchFailed', 'Yaf_Exception_DispatchFailed', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-loadfaild.php
 */
class Yaf_Exception_LoadFailed extends \Yaf_Exception
{
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-loadfaild.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Exception_LoadFailed', 'Yaf_Exception_LoadFailed', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-loadfaild-module.php
 */
class Yaf_Exception_LoadFailed_Module extends \Yaf_Exception_LoadFailed
{
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-loadfaild-module.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Exception_LoadFailed_Module', 'Yaf_Exception_LoadFailed_Module', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-loadfaild-controller.php
 */
class Yaf_Exception_LoadFailed_Controller extends \Yaf_Exception_LoadFailed
{
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-loadfaild-controller.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Exception_LoadFailed_Controller', 'Yaf_Exception_LoadFailed_Controller', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-loadfaild-action.php
 */
class Yaf_Exception_LoadFailed_Action extends \Yaf_Exception_LoadFailed
{
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-loadfaild-action.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Exception_LoadFailed_Action', 'Yaf_Exception_LoadFailed_Action', \false);
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-loadfaild-view.php
 */
class Yaf_Exception_LoadFailed_View extends \Yaf_Exception_LoadFailed
{
}
/**
 * @link https://secure.php.net/manual/en/class.yaf-exception-loadfaild-view.php
 */
\class_alias('DEPTRAC_202401\\Yaf_Exception_LoadFailed_View', 'Yaf_Exception_LoadFailed_View', \false);
