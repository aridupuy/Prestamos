<?php
class Usuario extends Model {

    public static $prefijo_tabla="";
    public static $id_tabla="id_usuario";
    public static $secuencia="";
    private $id_usuario;
    private $usuario;
    private $id_estado;
    private $password;
    
    function get_id_usuario() {
        return $this->id_usuario;
    }

    function get_usuario() {
        return $this->usuario;
    }

    function get_id_estado() {
        return $this->id_estado;
    }

    function get_password() {
        return $this->password;
    }

    function set_id_usuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    function set_usuario($usuario) {
        $this->usuario = $usuario;
    }

    function set_id_estado($id_estado) {
        $this->id_estado = $id_estado;
    }

    function set_password($password) {
        $this->password = $password;
    }

    public function login($usuario,$password){
        $sql="SELECT * FROM usuario where usuario=? and password=?";
        $variables['usuario']=$usuario;
        $variables['password']=$password;
        $recordset=self::execute_select($sql,$variables);
        if($recordset and $recordset->rowCount()==1){
            $row=$recordset->fetchRow();
//            var_dump(new Usuario($row));
            return new Usuario($row);
        }
        return null;
    }
}
