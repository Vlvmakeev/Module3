<?php
    if( !session_id() ) @session_start();
    require '../vendor/autoload.php';
    use DI\ContainerBuilder;

    $builder = new ContainerBuilder();
    $container = $builder->build();
    
    
    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/', ['App\controllers\PageController', 'index']);
        $r->addRoute('GET', '/register', ['App\controllers\PageController', 'register']);
        $r->addRoute('GET', '/users', ['App\controllers\PageController', 'users']);
        $r->addRoute('GET', '/create', ['App\controllers\PageController', 'create_user']);
        $r->addRoute('GET', '/edit/{id:\d+}', ['App\controllers\PageController', 'edit_user']);
        $r->addRoute('GET', '/user_media/{id:\d+}', ['App\controllers\PageController', 'user_media']);
        $r->addRoute('GET', '/user/{id:\d+}', ['App\controllers\PageController', 'user_profile']);
        $r->addRoute('GET', '/security/{id:\d+}', ['App\controllers\PageController', 'user_security']);
        $r->addRoute('GET', '/status/{id:\d+}', ['App\controllers\PageController', 'user_status']);
        $r->addRoute('GET', '/media/{id:\d+}', ['App\controllers\PageController', 'user_media']);
        $r->addRoute('GET', '/logout', ['App\controllers\UserController', 'logout']);
        $r->addRoute('GET', '/user_profile/{id:\d+}', ['App\controllers\PageController', 'user_profile']);
        
        $r->addRoute('POST', '/registration', ['App\controllers\UserController', 'register_user']);
        $r->addRoute('POST', '/login', ['App\controllers\UserController', 'login']);
        $r->addRoute('POST', '/create', ['App\controllers\UserController', 'create_user']);
        $r->addRoute('POST', '/edit', ['App\controllers\UserController', 'edit_user']);
        $r->addRoute('POST', '/credentials', ['App\controllers\UserController', 'edit_credentials']);
        $r->addRoute('POST', '/set_status', ['App\controllers\UserController', 'set_status']);
        $r->addRoute('POST', '/edit_image', ['App\controllers\UserController', 'edit_image']);
        $r->addRoute('GET', '/delete/{id:\d+}', ['App\controllers\UserController', 'delete_user']);
        


        
        
        $r->addRoute('GET', '/123', 'get_all_users_handler');
        // {id} must be a number (\d+)
        //$r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
        // The /{title} suffix is optional
        $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
    });
    
    // Fetch method and URI from somewhere
    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    
    // Strip query string (?foo=bar) and decode URI
    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }
    $uri = rawurldecode($uri);
    
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            // ... 404 Not Found
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            // ... 405 Method Not Allowed
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];
            //$container->call($handler, $vars);
            $controller = new $handler[0];
            
            call_user_func([$controller, $handler[1]], $vars);
            break;
    }
?>