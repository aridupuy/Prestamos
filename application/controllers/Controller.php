<?php
abstract class Controller extends Application{
    protected $view;
    public final function __construct() {
        $this->view= new View('1.0','utf-8');
        return $this;
    }
    public final function Despachar($metodo,$variables){
        if(Application::$usuario)
            Logger::developper("Usuario ".Application::$usuario."Navega a ".get_called_class()." al metodo ".$metodo);
        else 
            Logger::developper("Usuario no logueado Navega a ".get_called_class()." al metodo ".$metodo);
        if(Gestor_de_permisos::puede(Application::$usuario, get_class()))
            return $this->$metodo($variables);
        else{
            Logger::all("No tiene permiso para realizar la accion", get_class());
            return (new Controller_main())->home($varibles);
        }
    }
}
