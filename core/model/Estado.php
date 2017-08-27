<?php
class Estado extends Model{
    const ACTIVO=1;
    const INACTIVO=2;
    const SUSPENDIDO=3;
    const BAJA=4;
    const AUSENTE=5;
    const PRESENTE=6;
    public static $id_tabla="id_estado";
    public static $prefijo_tabla="";
    public static $secuencia="";

    private $id_estado;
    private $estado;
    public function set_id_estado($id_estado){ $this->id_estado=$id_estado;}
    public function set_estado($estado){ $this->estado=$estado;}
    public function get_id_estado(){return $this->id_estado;}
    public function get_estado(){return $this->estado;}

}