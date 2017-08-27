<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of prestamo
 *
 * @author Ariel_dupuy
 */
class prestamo extends Model {

    public static $id_tabla = "id_prestamo";
    public static $prefijo_tabla = "";
    public static $secuencia = "";
    private $id_prestamo;
    private $id_contrato;
    private $id_movimiento;
    private $id_cliente;
    private $monto_total;

    function get_id_prestamo() {
        return $this->id_prestamo;
    }

    function get_monto_total() {
        return $this->monto_total;
    }

    function set_monto_total($monto_total) {
        $this->monto_total = $monto_total;
    }

    function get_id_contrato() {
        return $this->id_contrato;
    }

    function get_id_movimiento() {
        return $this->id_movimiento;
    }

    function get_id_cliente() {
        return $this->id_cliente;
    }

    function set_id_prestamo($id_prestamo) {
        $this->id_prestamo = $id_prestamo;
    }

    function set_id_contrato($id_contrato) {
        $this->id_contrato = $id_contrato;
    }

    function set_id_movimiento($id_movimiento) {
        $this->id_movimiento = $id_movimiento;
    }

    function set_id_cliente($id_cliente) {
        $this->id_cliente = $id_cliente;
    }

}
