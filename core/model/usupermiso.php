<?php

class usupermiso extends Model{
    public static $id_tabla="id_usupermiso";
    public static $secuencia="";
    public static $prefijo_tabla ="";
    private $id_usupermiso;
    private $id_usuario;
    private $id_permiso;
    private $id_estado;
    
    function get_id_estado() {
        return $this->id_estado;
    }

    function set_id_estado($id_estado) {
        $this->id_estado = $id_estado;
    }

        function get_id_usupermiso() {
        return $this->id_usupermiso;
    }

    function get_id_usuario() {
        return $this->id_usuario;
    }

    function get_id_permiso() {
        return $this->id_permiso;
    }

    function set_id_usupermiso($id_usupermiso) {
        $this->id_usupermiso = $id_usupermiso;
    }

    function set_id_usuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    function set_id_permiso($id_permiso) {
        $this->id_permiso = $id_permiso;
    }

    public function puede(Usuario $usuario, String $class){
        $sql="SELECT * FROM usupermiso A left join permiso B on A.id_permiso=B.id_permiso where (B.descripcion = ? and A.id_usuario=?) or (A.id_usuario=? and B.id_permiso=? )";
        $variables[]=$class;
        $variables[]=$usuario->get_id_usuario();
        $variables[]=$usuario->get_id_usuario();
        $variables[]=5000;
        return self::execute_select($sql, $variables);
    }
    
}
