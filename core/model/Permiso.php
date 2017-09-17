<?php
class Permiso extends Model{
    public static $id_tabla="id_permiso";
    public static $secuencia="";
    public static $prefijo_tabla="";
    private $id_permiso;
    private $descripcion;
    private $id_estado;
    function get_id_permiso() {
        return $this->id_permiso;
    }

    function get_descripcion() {
        return $this->descripcion;
    }

    function get_id_estado() {
        return $this->id_estado;
    }

    function set_id_permiso($id_permiso) {
        $this->id_permiso = $id_permiso;
    }

    function set_descripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function set_id_estado($id_estado) {
        $this->id_estado = $id_estado;
    }


}
