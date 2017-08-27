<?php

class Cliente extends Model {

    public static $id_tabla = "id_cliente";
    public static $prefijo_tabla = "";
    public static $secuencia = "";
    private $id_cliente;
    private $nombre;
    private $apellido;
    private $fec_nac;
    private $documento;
    private $direccion;
    private $numero;
    private $telefono;
    private $celular;
    private $localidad;
    private $nacionalidad;
    private $id_estado;

    function get_id_cliente() {
        return $this->id_cliente;
    }

    function get_nombre() {
        return $this->nombre;
    }

    function get_apellido() {
        return $this->apellido;
    }

    function get_fec_nac() {
        return $this->fec_nac;
    }

    function get_documento() {
        return $this->documento;
    }

    function get_direccion() {
        return $this->direccion;
    }

    function get_numero() {
        return $this->numero;
    }

    function get_telefono() {
        return $this->telefono;
    }

    function get_celular() {
        return $this->celular;
    }

    function get_localidad() {
        return $this->localidad;
    }

    function get_nacionalidad() {
        return $this->nacionalidad;
    }

    function get_id_estado() {
        return $this->id_estado;
    }

    function set_id_cliente($id_cliente) {
        $this->id_cliente = $id_cliente;
    }

    function set_nombre($nombre) {
        $this->nombre = $nombre;
    }

    function set_apellido($apellido) {
        $this->apellido = $apellido;
    }

    function set_fec_nac($fec_nac) {
        $this->fec_nac = $fec_nac;
    }

    function set_documento($documento) {
        $this->documento = $documento;
    }

    function set_direccion($direccion) {
        $this->direccion = $direccion;
    }

    function set_numero($numero) {
        $this->numero = $numero;
    }

    function set_telefono($telefono) {
        $this->telefono = $telefono;
    }

    function set_celular($celular) {
        $this->celular = $celular;
    }

    function set_localidad($localidad) {
        $this->localidad = $localidad;
    }

    function set_nacionalidad($nacionalidad) {
        $this->nacionalidad = $nacionalidad;
    }

    function set_id_estado($id_estado) {
        $this->id_estado = $id_estado;
    }

}
