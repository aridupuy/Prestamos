<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cotrato
 *
 * @author Ariel_dupuy
 */
class contrato extends Model{
    const CONTRATO="";
    public static $id_tabla="id_contrato";
    public static $prefijo_tabla ="";
    private $id_contrato;
    private $id_cliente;
    private $contrato;	
    private $fecha_contrato;
    private $id_estado;
    
    
    function get_id_contrato() {
        return $this->id_contrato;
    }

    function get_id_cliente() {
        return $this->id_cliente;
    }

    function get_contrato() {
        return $this->contrato;
    }

    function get_fecha_contrato() {
        return $this->fecha_contrato;
    }

    function get_id_estado() {
        return $this->id_estado;
    }

    function set_id_contrato($id_contrato) {
        $this->id_contrato = $id_contrato;
    }

    function set_id_cliente($id_cliente) {
        $this->id_cliente = $id_cliente;
    }

    function set_contrato($contrato) {
        $this->contrato = $contrato;
    }

    function set_fecha_contrato($fecha_contrato) {
        $this->fecha_contrato = $fecha_contrato;
    }

    function set_id_estado($id_estado) {
        $this->id_estado = $id_estado;
    }


}
