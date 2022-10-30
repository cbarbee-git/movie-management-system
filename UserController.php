<?php

include_once('Crud.php');
include_once('Session.php');

class UserController{

    private $crud;

    public function __construct(){

        $this->crud = new Crud();

    }

    public function login($username,$password){
        $params = [
            'username' => $username,
            'password' => md5($password)
            ];
        $sql = 'Select username,name,role,photo_img from users where username = :username and password = :password';

        $user = $this->crud->read($sql,$params,'row');
        if(count($user)){
            appSession::start();
            appSession::set('active_user', $user);
            header('Location: admin/list-movies.php');
            exit();
        }else{
            return false;
        }

    }

    public function logout(){
        appSession::destroy('active_user');
        header('Location: login.php');
        exit();
    }
}
