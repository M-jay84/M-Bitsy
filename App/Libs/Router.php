<?php

namespace App\Libs;

/*
* Run App From URL Params
* Examaple domain/controller/method/param
*/
class Router
{
    protected $currentController = 'Home';
    protected $currentMethod;
    protected $params = [];

    public function __construct()
    {
        // Get Url
        $url = $this->getUrl();

        // Get Controller
        $this->currentController = empty($url[0]) ? 'Home' : ucwords($url[0]);
        unset($url[0]);

        // Get Controller
        if (file_exists('../App/Controllers/User/' . $this->currentController . '.php')) {
            $namespace = "App\\Controllers\\User\\" . $this->currentController;
        } elseif (file_exists('../App/Controllers/Admin/' . $this->currentController . '.php')) {
            $namespace = "App\\Controllers\\Admin\\" . $this->currentController;
        } else {
            die('Page not found');
        }
   
        // Instantiate the current controller
        $this->currentController = new $namespace;

        // Get Method
        if (isset($url[1]) && method_exists($this->currentController, $url[1])) {
            $this->currentMethod = $url[1];
            unset($url[1]);
        } else {
            $this->currentMethod = 'index';
        }

        // Get params - Any values left over in url are params
        $this->params = $url ? array_values($url) : [];

        // Call a callback with an array of parameters
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    // Construct URL From $_GET['url']
    public function getUrl()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
    }
}