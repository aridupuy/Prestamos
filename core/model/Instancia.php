<?php

class Instancia extends Model{
    
    public static $prefijo_tabla="";
    public  static $id_tabla="id_instancia";
    public  static $secuencia="";
    private $id_instancia;
    private $cookie;
    private $variables;
    private $controller;
    public static function get_secuencia() {
        return self::$secuencia;
    }

    public function get_id_instancia() {
        return $this->id_instancia;
    }

    public function get_cookie() {
        return $this->cookie;
    }

    public function get_variables() {
        return $this->variables;
    }

    public function get_controller() {
        return $this->controller;
    }

    public static function set_secuencia($secuencia) {
        self::$secuencia = $secuencia;
    }

    public function set_id_instancia($id_instancia) {
        $this->id_instancia = $id_instancia;
    }

    public function set_cookie($cookie) {
        $this->cookie = $cookie;
    }

    public function set_variables($variables) {
        $this->variables = $variables;
    }

    public function set_controller($controller) {
        $this->controller = $controller;
    }

    public static function generar_nueva_instancia($cookie,$controller,$variables){
        $recordsSet = Instancia::select_ultima_instancia($cookie);
        $instancia=new Instancia();

        if($recordsSet->rowCount()>0){
            $row=$recordsSet->fetchRow();
            $instancia->set_id_instancia($row['instancia']+1);
        }
        else
            $instancia->set_id_instancia(0);
        $instancia->set_controller($controller);
        $instancia->set_cookie($cookie);
        $instancia->set_variables(json_encode($variables));
        if($instancia->set())
            return $instancia;
        return false;
    }
    public static function select_ultima_instancia($cookie){
        $sql="SELECT max(id_instancia) as instancia FROM instancia where cookie=?";
        $variables=array();
        $variables[]=$cookie;
        return self::execute_select($sql,$variables);
    }
    public static function eliminar_instancia($cookie,$instancia,$page){
        $sql="DELETE FROM instancia where cookie = ? and id_instancia < ? and controller = ? ";
        $variables=array();
        $variables[]=$cookie;
        $variables[]=$instancia;
        $variables[]=$page;
        $recordsSet=self::execute_select($sql,$variables);
        if(!$recordsSet AND $recordsSet->rowCount()==0){
            return false;
        }
        return false;
    }
    

}