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
    public static function select_prestamos($variables){
        $sql = "select A.id_prestamo,B.nombre,B.apellido,B.documento,(select count(*) as coutas from pagare where id_prestamo= A.id_prestamo) as cuotas , A.monto_total , (select round(sum(monto),2) as monto from pagare where id_prestamo= A.id_prestamo) as precio_final from prestamo A 
                left join cliente B on A.id_cliente=B.id_cliente ";
        return self::execute_select($sql);
    }
    public static function select_ingresos(){
        $sql="SELECT fecha_move, round(sum(monto),2) as total FROM movimientos A LEFT join pagare B on A.id_pagare = B.id_pagare group by fecha_move";
        return self::execute_select($sql);
    }
    public static function select_egresos(){
        $sql="SELECT sum(A.monto_total) as total ,B.fecha_contrato FROM prestamo A LEFT JOIN contrato B ON A.id_contrato=B.id_contrato group by fecha_contrato";
        return self::execute_select($sql);
    }
}
