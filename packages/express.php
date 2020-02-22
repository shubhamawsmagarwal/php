<?php
session_start();
function express(){
	$_SESSION['res']=new response();
	$_SESSION['req']=new request();
    $app=new routes();
	return $app;
}


/*********** Response ********************/
class response{
	public function render(...$param){
		global $app;
		$action=$param[0];
		$parameters="";
		if(count($param)==2)
			$parameters=$param[1];
		if($_SESSION['req']->curr==='running'){
			if($app->set('view engine','php')===true)
		      $action= 'views/'.$action.'.php';
		    else
		 	  $action= 'views/'.$action;
		 	$app->render_listen($action,false,$parameters);

		}
		else{
			$app->render_listen($action,true,$parameters);
		}
	}
	public function redirect($action){
		global $app;
		@$action=trim($action,'/');
		if($_SESSION['req']->curr==='running'){
		    $app->redirect_listen($action,false);
	    }
	    else{
	    	$app->redirect_listen($action,true);
	    }
	}
	public function send($action){
		global $app;
		if($_SESSION['req']->curr==='running'){
		    $app->send_listen($action,false);
	    }
	    else{
	    	$app->send_listen($action,true);
	    }
	}
}




/************ Request *****************************/
class request{
	function __construct(){
		$this->curr='ready';
		$this->arr=array();
		$this->body=array();
		$parameters=NULL;
		$databaseRefernce=NULL;
	}
	public function isAuthenticated(){
		return (isset($_SESSION['username']));
	}
	public function logout(){
		unset($_SESSION['username']);
		return true;
	}
	public function signinn($username){
		$_SESSION['username']=$username;
		return true;
	}
	public $curr;
	public $arr;
	public $parameters;
	public $body;
	public $databaseRefernce;
}



/************ Routes *********************/
class routes{
	private $empty;
	private $default;
	private $folder;
	private $get_routes;
	private $post_routes;
	public function __construct(){
		$get_routes=array();
        $post_routes=array();
        $folder=array();
	}
	public function get(...$param){
		$action=$param[0];
		@$action=trim($action,'/');
		@$_SESSION['req']->curr=$action;
		if(count($param)===2){
			$callback=$param[1];
			if($action==='*')
			    $this->default=$callback;
		    else if(@$action==='')
			   $this->empty=$callback;
		    else 
		    	$this->get_routes[$action]=$callback;
		}
		else{
			$middle_ware=$param[1];
			$callback=$param[2];
			if(call_user_func($middle_ware,$_SESSION['req'],$_SESSION['res'])){
			    if($action==='*')
			       $this->default=$callback;
		        else if(@$action==='')
			       $this->empty=$callback;
		        else 
		    	   $this->get_routes[$action]=$callback;
			}
		}
	}
	public function post($action,$callback){
		@$action=trim($action,'/');
		@$_SESSION['req']->curr=$action;
		$this->post_routes[$action]=$callback;
	}
	public function redirect_listen($action,$boolean){
		if($boolean){
			$parent=$_SESSION['req']->curr;
			if(@$action===''){
				if($parent==='*'){
					$this->default=$this->empty;
				}
				else{
				    $this->get_routes[$parent]=$this->empty;
			    }
			}
			else if($action==='*'){
				if(@$parent===''){
					$this->empty=$this->default;
				}
				else{
				    $this->get_routes[$parent]=$this->default;
			    }
			}
			else{
				if(@$parent===''){
					$this->empty=$this->get_routes[$action];
				}
				else if($parent==='*'){
					$this->default=$this->get_routes[$action];
				}
				else{
				    $this->get_routes[$parent]=$this->get_routes[$action];
			    }
			}
			return;
		}
		if(@$action===''){
			call_user_func($this->empty,$_SESSION['req'],$_SESSION['res']);	
		}
		else if(@$this->get_routes[$action]===NULL){
			call_user_func($this->default,$_SESSION['req'],$_SESSION['res']);
		}else{
			call_user_func($this->get_routes[$action],$_SESSION['req'],$_SESSION['res']);
		}
	}
	public function render_listen($action,$boolean,$parameters){
		if($boolean){
			$parent=$_SESSION['req']->curr;
			if(@$parent===''){
			    $_SESSION['req']->arr['emp']=$action;
			    $this->empty=function($req,$res){
			    	$action=$_SESSION['req']->arr['emp'];
			    	$parameters=$_SESSION['req']->parameters;
			    	$res->render($action,$parameters);
			    };
			}
			else if($parent==='*'){
				$_SESSION['req']->arr['def']=$action;
			    $this->default=function($req,$res){
			    	$action=$_SESSION['req']->arr['def'];
			    	$parameters=$_SESSION['req']->parameters;
			    	$res->render($action,$parameters);
			    };	
			}
			else{
				$_SESSION['req']->arr[$parent]=$action;
			    $this->get_routes[$parent]=function($req,$res){
			    	$action=$_SESSION['req']->arr[$_GET['url']];
			    	$parameters=$_SESSION['req']->parameters; 
			    	$res->render($action,$parameters);
			    };
			}
			return;
        }
	    require_once $action;
	}
	public function send_listen($action,$boolean){
		if($boolean){
			$parent=$_SESSION['req']->curr;
			if(@$parent===''){
			    $_SESSION['req']->arr['emp']=$action;
			    $this->empty=function($req,$res){$action=$_SESSION['req']->arr['emp']; $res->send($action);};
			}
			else if($parent==='*'){
				$_SESSION['req']->arr['def']=$action;
			    $this->default=function($req,$res){$action=$_SESSION['req']->arr['def']; $res->send($action);};	
			}
			else{
				$_SESSION['req']->arr[$parent]=$action;
			    $this->get_routes[$parent]=function($req,$res){$action=$_SESSION['req']->arr[$_GET['url']]; $res->send($action);};
			}
			return;
		}
		echo $action;
	}
	public function listen(){
		$_SESSION['req']->curr='running';
		@$action=$_GET['url'];
		@$action=trim($action,'/');
		if($action=="index.php")
			@$action="";
		if($_SERVER['REQUEST_METHOD']==='GET'){
			if(@$action===''){
				call_user_func($this->empty,$_SESSION['req'],$_SESSION['res']);
			}
			else if(@$this->get_routes[$action]===NULL){
				call_user_func($this->default,$_SESSION['req'],$_SESSION['res']);
			}else{
				call_user_func($this->get_routes[$action],$_SESSION['req'],$_SESSION['res']);
			}
		}
		else{
			if(@$action!=="" && @$this->post_routes[$action]!==NULL)
			   call_user_func($this->post_routes[$action],$_SESSION['req'],$_SESSION['res']);
			else
				echo 'INVALID POST REQUEST';
		}
	}
	public function databaseSession($refernce){
		$_SESSION['req']->databaseRefernce=$refernce;
	}
	public function set($fold, $ext){
		@$arr=explode(' ',$fold);
		if(@$this->folder[$arr[0]]!=NULL)
			return true;
		$this->folder[$arr[0]]='.'.$ext;
		return false;
	}
}
?>