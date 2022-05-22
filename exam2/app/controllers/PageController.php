<?php
    namespace App\controllers;

    use League\Plates\Engine;

    use Delight\Auth\Auth;

    use App\QueryBuilder;
    
    class PageController
    {
        private $templates;

        private $auth;

        public function __construct(){
            $db = new \PDO('mysql:host=127.0.0.1;dbname=exam2;charset=utf8', 'root', '');
            $this->auth = new Auth($db);
            $this->templates = new Engine('../app/views');
        }

        public function index(){
            echo $this->templates->render('page_login', ['name' => 'Jonathan']);
        }

        public function register(){
            echo $this->templates->render('page_register', ['name' => 'Jonathan']);
        }

        public function users(){
            if ($this->auth->isLoggedIn()) {
                $is_admin = $this->auth->hasRole(\Delight\Auth\Role::ADMIN);
                
                $db = new QueryBuilder();
                $users = $db->getAll('user_info');
                  
                echo $this->templates->render('users', ['is_admin' => $is_admin, 'users' => $users]);
            }
            else {
                flash()->error('Вам необходимо авторизоваться');
                header('Location: /');
            }
            
        }

        public function edit_user($vars){
            $db = new QueryBuilder();
            $user = $db->getByID('user_info', $vars['id']);
            
            if ($this->auth->id() == $user['user_id']) {
                $is_admin = $this->auth->hasRole(\Delight\Auth\Role::ADMIN);
                $id = $vars['id'];
                $db = new QueryBuilder();
                
                $user = $db->getByID('user_info', $id);
                
                echo $this->templates->render('edit', ['is_admin' => $is_admin, 'user' => $user]);
            }
            else {
                flash()->error('Можно редактировать только свой профиль');
                header('Location: /users');
            }
        }

        public function create_user(){
            if ($this->auth->isLoggedIn()) {
                echo $this->templates->render('create_user', ['name' => 'Jonathan']);
            }
            else {
                flash()->error('Вам необходимо авторизоваться');
                header('Location: /');
            }
        }

        public function user_profile($vars){
            
            if ($this->auth->isLoggedIn()) {
                $id = $vars['id'];
                $db = new QueryBuilder();
                $user = $db->getByID('user_info', $id);
                echo $this->templates->render('page_profile', ['user' => $user]);
            }
            else {
                flash()->error('Вам необходимо авторизоваться');
                header('Location: /');
            }
        }

        public function user_security($vars){
            $db = new QueryBuilder();
            $user = $db->getByID('user_info', $vars['id']);
            
            if ($this->auth->id() == $user['user_id']) {
                
                $id = $vars['id'];
                $db = new QueryBuilder();
                
                $user = $db->getByID('user_info', $id);
                $user_id = $user['user_id'];
                $user_card_id = $user['id'];
                $user_credentials = $db->getByID('users', $user_id);
                echo $this->templates->render('security', ['user' => $user_credentials, 'user_card_id' => $user_card_id]);
            }
            else {
                flash()->error('Можно редактировать только свой профиль');
                header('Location: /users');
            }
        }

        public function user_status($vars){
            $db = new QueryBuilder();
            $user = $db->getByID('user_info', $vars['id']);
            
            if ($this->auth->id() == $user['user_id']) {
                
                $id = $vars['id'];
                $db = new QueryBuilder();
                
                $user = $db->getByID('user_info', $id);
                
                
                echo $this->templates->render('status', ['user' => $user]);
            }
            else {
                flash()->error('Можно редактировать только свой профиль');
                header('Location: /users');
            }
        }

        public function user_media($vars){
            $db = new QueryBuilder();
            $user = $db->getByID('user_info', $vars['id']);
            
            if ($this->auth->id() == $user['user_id']) {
                
                $id = $vars['id'];
                
                
                $user = $db->getByID('user_info', $id);
                
                
                echo $this->templates->render('media', ['user' => $user]);
            }
            else {
                flash()->error('Можно редактировать только свой профиль');
                header('Location: /users');
            }
        }

       
    }
?>