<?php
    namespace App\controllers;

    use Delight\Auth\Auth;
    use App\QueryBuilder;

    class UserController
    {
        private $auth;

        

        public function __construct(){
            $db = new \PDO('mysql:host=127.0.0.1;dbname=exam2;charset=utf8', 'root', '');
            $this->auth = new Auth($db);
        }

        public function register_user(){
            try {
                $userId = $this->auth->register($_POST['email'], $_POST['password'], $_POST['username'], function ($selector, $token) {
                    try {
                        $this->auth->confirmEmail($selector, $token);
                    
                        echo 'Email address has been verified';
                    }
                    catch (\Delight\Auth\UserAlreadyExistsException $e) {
                        die('Email address already exists');
                    }
                    catch (\Delight\Auth\TooManyRequestsException $e) {
                        die('Too many requests');
                    }
                });

                try {
                    $this->auth->admin()->addRoleForUserById($userId, \Delight\Auth\Role::ADMIN);
                }
                catch (\Delight\Auth\UnknownIdException $e) {
                    die('Unknown user ID');
                }
            
                echo 'We have signed up a new user with the ID ' . $userId;
                
            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                flash()->error('Некорректный email');
                header('Location: /register');
                die('Invalid email address');
            }
            catch (\Delight\Auth\InvalidPasswordException $e) {
                flash()->error('Некорректный пароль');
                header('Location: /register');
                die('Invalid password');
            }
            catch (\Delight\Auth\UserAlreadyExistsException $e) {
                flash()->error('Такой пользователь уже существует');
                header('Location: /register');
                die('User already exists');
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                flash()->error('Слишком много попыток');
                header('Location: /register');
                die('Too many requests');
            }

            
            flash()->success('Вы успешно зарегистрировались');
            header('Location: /');
            
        }

        public function login(){
            try {
                $this->auth->login($_POST['email'], $_POST['password']);
                
                header('Location: /users');
                echo 'User is logged in';
            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                flash()->error('Неверный email или пароль');
                header('Location: /');
                die('Wrong email address');
            }
            catch (\Delight\Auth\InvalidPasswordException $e) {
                flash()->error('Неверный email или пароль');
                header('Location: /');
                die('Wrong password');
            }
            catch (\Delight\Auth\EmailNotVerifiedException $e) {
                flash()->error('email не подтвержден');
                header('Location: /');
                die('Email not verified');
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                flash()->error('Слишком много попыток');
                header('Location: /register');
                die('Too many requests');
            }

            if ($this->auth->isLoggedIn()) {
                echo 'User is signed in';
                header('Location: /users');
            }
        }

        public function logout(){
            $this->auth->logOut();
            header('Location: /');
        }

        public function create_user(){
            try {
                $userId = $this->auth->admin()->createUser($_POST['email'], $_POST['password'], $_POST['username']);
                try {
                    $this->auth->admin()->addRoleForUserById($userId, \Delight\Auth\Role::CONSUMER);
                }
                catch (\Delight\Auth\UnknownIdException $e) {
                    die('Unknown user ID');
                }
                $image_name = uniqid() . '.' . end(explode('.', $_FILES['image']['name']));
                $file_directory = '../public/img/demo/avatars/' . $image_name;
                move_uploaded_file($_FILES['image']['tmp_name'], $file_directory);
                $db = new QueryBuilder();

                $db->insert('user_info', [
                    'email' => $_POST['email'],
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT), 
                    'name' => $_POST['username'],
                    'job' => $_POST['job'],
                    'phone' => $_POST['phone'],
                    'address' => $_POST['adress'],
                    'status' => $_POST['user_status'],
                    'image' => $file_directory,
                    'vk' => $_POST['vk'],
                    'telegram' => $_POST['telegram'],
                    'instagram' => $_POST['instagram'],
                    'user_id' => $userId,
                ]);
                flash()->success('Пользователь успешно создан');
                header('Location: /users');
                echo 'We have signed up a new user with the ID ' . $userId;
            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                flash()->error('Неверный email или пароль');
                header('Location: /users');
                die('Invalid email address');
            }
            catch (\Delight\Auth\InvalidPasswordException $e) {
                flash()->error('Неверный email или пароль');
                header('Location: /users');
                die('Invalid password');
            }
            catch (\Delight\Auth\UserAlreadyExistsException $e) {
                flash()->error('Такой пользователь уже существует');
                header('Location: /users');
                die('User already exists');
            }

            
        }

        public function edit_user(){
            
                
                $db = new QueryBuilder();

                $db->update('user_info', [
                     
                    'name' => $_POST['username'],
                    'job' => $_POST['job'],
                    'phone' => $_POST['phone'],
                    'address' => $_POST['adress']
                ], $_POST['id']);

                flash()->success('Пользователь успешно изменен');
                header('Location: /users');
        }

        public function edit_credentials(){
            
              
            $db = new QueryBuilder();

            $db->update('user_info', [
                 
                'email' => $_POST['email']
            ], $_POST['user_card_id']);

            $db->update('users', [
                 
                'email' => $_POST['email']
            ], $_POST['id']);

            try {
                $this->auth->changePassword($_POST['oldPassword'], $_POST['newPassword']);
            
                echo 'Password has been changed';
            }
            catch (\Delight\Auth\NotLoggedInException $e) {
                die('Not logged in');
            }
            catch (\Delight\Auth\InvalidPasswordException $e) {
                die('Invalid password(s)');
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                die('Too many requests');
            }

            flash()->success('Пользователь успешно изменен');
            header('Location: /users');
        }

        public function set_status(){
            
              
            $db = new QueryBuilder();

            $db->update('user_info', [
                'status' => $_POST['status']
            ], $_POST['id']);

            flash()->success('Пользователь успешно изменен');
            header('Location: /users');
        }

        public function edit_image(){
            
              
            $db = new QueryBuilder();

            $image_name = uniqid() . '.' . end(explode('.', $_FILES['image']['name']));
            $file_directory = '../public/img/demo/avatars/' . $image_name;
            move_uploaded_file($_FILES['image']['tmp_name'], $file_directory);

            $db->update('user_info', [
                'image' => $file_directory
            ], $_POST['id']);

            flash()->success('Пользователь успешно изменен');
            header('Location: /users');
        }

        public function delete_user($vars){
            
            $id = $vars['id'];

            $db = new QueryBuilder();

            $user = $db->getByID('user_info', $id);
            $user_id = $user['user_id'];

            $db->delete('user_info', $id);
            $db->delete('users', $user_id);
            if( $this->auth->getUserId() != $user_id ){
                flash()->success('Пользователь успешно удален');
                header('Location: /users');
            }else{
                $this->auth->logOut();
                header('Location: /');
            }
            
        }
    }
?>