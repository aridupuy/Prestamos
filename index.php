<?php
set_time_limit(25);
require __DIR__.'/core/config.ini';
$wrapper=false;
$variables=array();
if(isset($_COOKIE["prestamos_cookie"]))
	$id_usuario_cookie=$_COOKIE["prestamos_cookie"];
else{
//    setcookie("turnos_cookie",$_SERVER['REMOTE_ADDR']);
    $id_usuario_cookie=false;
}
if(empty($_POST))
    $wrapper=true;
else
    $variables=$_POST;
if(isset($_POST['nav'])){
    $page=$_POST['nav'];
    if ($_POST['method']){
        $method=$_POST['method'];
        $lista=true;
    }
}
 else {
     $page="main";
     $method="index";
     $lista=false;
}
$application=new Application($id_usuario_cookie);
$html=$application->navegar($page,$method,$variables,$wrapper,$lista);
//if(isset(Application::$usuario) and is_object(Application::$usuario));
//    setcookie("prestamos_cookie", Application::$usuario->get_id_usuario());
if(!$html){
    Logger::developper("error_fatal");
    print_r("Error Fatal");
}
echo $html;
exit(0);
?>

