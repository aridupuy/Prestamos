<?php
abstract class Controller extends Application{
    protected $view;
    public final function __construct() {
        $this->view= new View('1.0','utf-8');
        return $this;
    }
    public final function Despachar($metodo,$variables){
        return $this->$metodo($variables);
    }
}
