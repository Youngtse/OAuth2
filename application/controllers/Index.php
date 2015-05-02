<?php

/**
 * Created by PhpStorm.
 * User: Yanggen
 * Date: 15/4/8
 * Time: 上午11:09
 */
class IndexController extends BaseController
{
    /**
     * @var OAuth2\Server
     */
    protected $server;
    /**
     * @var OAuth2\Storage\Pdo
     */
    protected $storage;

    public function init()
    {
        parent::init();
        $config = Yaf_Registry::get('config');
        $dsn = 'mysql:host=' . $config->db->oauth->host . ';port=' . $config->db->oauth->port . ';dbname=' . $config->db->oauth->dbname;
        $username = $config->db->oauth->user;
        $password = $config->db->oauth->pass;
        Yaf_Loader::import(LIB_PATH . '/OAuth2/Autoloader.php');
        OAuth2\Autoloader::register();

        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        $this->storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

        // Pass a storage object or array of storage objects to the OAuth2 server class
        $this->server = new OAuth2\Server($this->storage, array('allow_implicit' => true, 'allow_credentials_in_request_body' => true));

        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
//        $this->server->addGrantType(new OAuth2\GrantType\ClientCredentials($this->storage));

        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $this->server->addGrantType(new OAuth2\GrantType\AuthorizationCode($this->storage));
    }

    public function registerAction()
    {
        $this->disableView();
        if (!$this->getRequest()->isCli()) {
            exit('Sorry, you have no permission~');
        }

        echo 'Enter redirect_uri: ';
        $redirect_uri = trim(fgets(STDIN));
        fwrite(STDOUT, "redirect_uri: " . $redirect_uri);

//        echo 'Enter client_name: ';
//        $client_name = trim(fgets(STDIN));
//        fwrite(STDOUT, "client_name: " . $client_name);
//
//        echo 'Enter client_logo_url: ';
//        $client_logo_url = trim(fgets(STDIN));
//        fwrite(STDOUT, "client_logo_url: " . $client_logo_url);
//
//        echo 'Enter client_homepage: ';
//        $client_homepage = trim(fgets(STDIN));
//        fwrite(STDOUT, "client_homepage: " . $client_homepage);

        $client_secret = $this->generateKey();
        if ($this->storage->setClientDetails($client_secret, $redirect_uri, $client_name=null, $client_logo_url=null, $client_homepage=null, $grant_types = null, $scope = null, $user_id = null)) {
            $client_id = $this->storage->getInsertId();
            exit('SUCCESS! ClientId: ' . $client_id . '=========ClientSecret: ' . $client_secret . '.');
        } else {
            exit('Error! ');
        }
//        if (!empty($_POST)) {
//            $this->disableView();
//            $redirect_uri = $this->getLegalParam('redirect_uri', 'str', [], '');
//            $client_name = $this->getLegalParam('client_name', 'str', [], '');
//            $client_logo_url = $this->getLegalParam('client_logo_url', 'str', [], '');
//            $client_homepage = $this->getLegalParam('client_homepage', 'str', [], '');
//
//            $client_secret = $this->generateKey();
//            if ($this->storage->setClientDetails($client_secret, $redirect_uri, $client_name, $client_logo_url, $client_homepage, $grant_types = null, $scope = null, $user_id = null)) {
//                $client_id = $this->storage->getInsertId();
//                exit('SUCCESS! ClientId: ' . $client_id . '=========ClientSecret: ' . $client_secret . '.');
//            } else {
//                exit('Error! ');
//            }
//        }
    }

    //login ＋ access
    public function indexAction()
    {
        if (!(isset($_GET['client_id']) && isset($_GET['redirect_uri']) && isset($_GET['state']))) {
            exit("Please Check Url Params.");
        }

        if($_POST) {
            $this->disableView();
            $client_id = $this->getLegalParam('client_id', 'int', [], 0);
            $login_uid = $this->getLegalParam('userId', 'str', [], '');
            $pwd = $this->getLegalParam('password', 'str', [], '');

            if(!empty($login_uid)) {
                $rs = $this->storage->getUser($login_uid);
                $password = empty($rs) ? '' : $rs['password'];

                if(md5($pwd) == $password) {

                    if (!isset($_GET['response_type'])) {
                        $_GET['response_type'] = 'code';
                    }

                    $request = OAuth2\Request::createFromGlobals();
                    $response = new OAuth2\Response();

                    // validate the authorize request
                    if (!$this->server->validateAuthorizeRequest($request, $response)) {
                        $response->send();
                        die;
                    }

                    $this->server->handleAuthorizeRequest($request, $response, true, $login_uid);

                    $parsed_url = parse_url($response->getHttpHeader('Location'));
                    $parsed_url['query'] = (isset($parsed_url['query']) ? $parsed_url['query'] . '&' : '') . 'login_uid=' . $login_uid;
                    $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
                    $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
                    $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
                    $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
                    $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
                    $pass = ($user || $pass) ? "$pass@" : '';
                    $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
                    $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
                    $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
                    $new_nrl = "$scheme$user$pass$host$port$path$query$fragment";
                    header('Location: ' . $new_nrl);
                    die;
                } else {
                    echo 'Wrong username or password~<a href="" >重新登录</a>';
                }
            }
        }
    }

    public function authorizeAction()
    {

    }

    public function tokenAction()
    {
        $this->disableView();
        $this->server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
    }

    /**
     * Generate a unique key
     *
     * @param boolean unique    force the key to be unique
     * @return string
     */
    protected function generateKey($unique = false)
    {
        $key = md5(uniqid(rand(), true));
        if ($unique) {
            list($usec, $sec) = explode(' ', microtime());
            $key .= dechex($usec) . dechex($sec);
        }
        return $key;
    }
}