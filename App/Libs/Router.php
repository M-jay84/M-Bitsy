<?php

namespace App\Libs;

/*
* Run App From URL Params
* Examaple domain/controller/method/param
*/
class Router
{
    protected $currentController;
    protected $currentMethod;
    protected $params = [];

    public function __construct()
    {
        // Get Url
        $url = $this->getUrl();

        // Get Controller
        $this->currentController = empty($url[0]) ? 'Home' : ucwords($url[0]);
        unset($url[0]);

        // Set Namespace
        if (str_contains($this->currentController, 'Admin')) {
            $namepace = '\\App\\Controllers\\Admin\\' . $this->currentController;
        } else {
            $namepace = '\\App\\Controllers\\User\\' . $this->currentController;
        }
   
        // Instantiate the current controller
        $this->currentController = new $namepace;

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