<?php
class Estado extends Model{
    const ACTIVO=1;
    const INACTIVO=2;
    const PENDIENTE=3;
    const PAGADO=4;
    const VENCIDO=5;
    const PARCIAL=6;
    const NO_ACEPTADO=7;
    const ACEPTADO=8;
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