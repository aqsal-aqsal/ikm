<?php
class App {
    protected $controller = 'DashboardController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseURL();

        // Check Controller
        if (isset($url[0]) && file_exists('../app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        } else {
            // Optional: Handle 404 or default
            // For now, if controller not found, it stays DashboardController
            // But if URL is empty, it also stays DashboardController
            // If URL is 'something' and 'SomethingController' doesn't exist, it stays DashboardController? 
            // Better to default to Dashboard only if empty, else 404?
            // For simplicity, let's keep Dashboard as default, but if url[0] is set and not found, maybe show error?
            // Let's stick to standard behavior: if not found, use default (or maybe Auth if not logged in).
        }

        require_once '../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Check Method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Params
        if (!empty($url)) {
            $this->params = array_values($url);
        }

        // Run
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return ['Auth']; // Default to Login
    }
}
