<?php
class Controller_main extends Controller{


    public function home($varibles) {
        $this->view->cargar_vista("views/index.html");
        // $this->view->cargar_vista("views/index.html");
        // $this->view->cargar_vista("views/index.html");
        
        return $this->view;
    }
    public function login($variables){
        $this->view->cargar_vista("views/login.html");
        // $this->view->cargar_vista("views/index.html");
        // $this->view->cargar_vista("views/index.html");
        
        return $this->view;
    }
    public function login_post($variables){
        $usuario=new Usuario();
        if(isset($variables['usuario']) and isset($variables['pass'])){
            self::$usuario=$usuario->login($variables['usuario'],$variables['pass']);
            Gestor_de_cookies::set(self::$usuario->get_id_usuario(), 1);
        }
        return self::$usuario;
    }
    
    public function user_logout($variables){
        var_dump($variables);
        if(Gestor_de_cookies::destruir())
            return true;
        else            
            return false;
    }
}

