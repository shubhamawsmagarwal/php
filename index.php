<?php

/********* Requiring pacakages  ********/
$express=req_uire('express');
$app=express();
$bodyParaser=req_uire('body-parser');
$sql=req_uire('sql');

/********* Using express **********/
$app->set('view engine','php');

/********** Using database *******/

$sql=sql();
$sql->connect("localhost://root://1st");
$app->databaseSession($sql);


/*********** Get routes ************/

$app->get('/',function($req,$res){
    $res->render('home');
});
$app->get('/home',function($req,$res){
    $res->redirect('/');
});
$app->get('/register',function($req,$res){
	if($req->isAuthenticated()){
        return true;
    }
    $res->render('register');
},function($req,$res){
	$res->render('user');
});
$app->get('/signin',function($req,$res){
	if($req->isAuthenticated()){
        return true;
    }
    $res->render('signin');
},function($req,$res){
	$res->render('user');
});
$app->get('/user',function($req,$res){
	if($req->isAuthenticated()){
        return true;
    }
    $res->send('You must sign in first!!!!!');
},function($req,$res){
	$res->render('user');
});
$app->get('/logout',function($req,$res){
    if($req->isAuthenticated()){
        return true;
    }
    $res->send('You cant logout without signing in!!!!!');
},function($req,$res){
    $req->logout();
    $res->send('You logged out successfully!!!!!!');
});
$app->get('*',function($req,$res){
	$res->render('error',array("message"=>"Try a different url"));
});
$app->get('/error',function($req,$res){
    $res->render('error',array("message"=>"Try a different url difinetly not error"));
});


/********** Post routes ******************/
$app->post('/signin',function($req,$res){
    if($req->isAuthenticated()){
        return true;
    }
    $success=false;
    $username=$req->body['username'];
    $password=$req->body['password'];
    $query="SELECT `id` FROM `users` WHERE `username`='$username' AND `password`='$password'";
    $queryNumRow=$req->databaseRefernce->queryNumRow($query);
    if($queryNumRow===1)
        $success=true;
    if($success){
        $req->signinn($username);
        $res->render('user');
    }
    else
        $res->send('Email or password incorrect!!!!!!!');
});
$app->post('/register',function($req,$res){
    if($req->isAuthenticated()){
        return true;
    }
    $success=false;
    $username=$req->body['username'];
    $password=$req->body['password'];
    $query="SELECT `id` FROM `users` WHERE `username`='$username'";
    $queryNumRow=$req->databaseRefernce->queryNumRow($query);
    if($queryNumRow===0){
        $query="INSERT INTO `users`(`username`, `password`) VALUES ('$username','$password')";
        $queryInsert=$req->databaseRefernce->queryInsert($query);
        $success=true;
    }
    if($success){
        $req->signinn($username);
        $res->render('user');
    }
    else
        $res->send('Email already exists!!!!!!!!!!');
});


$app->listen();
?>