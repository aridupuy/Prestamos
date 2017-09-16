<?php
class Movimientos extends Model{
    public static $id_tabla="id_movimiento";
    public static $secuencia="";
    public static $prefijo_tabla ="";
    private $id_movimiento;	
    private $id_prestamo;	
    private $id_cliente	;
    private $id_pagare;	
    private $fecha_move;	
    private $fecha_pago;
    
    function get_id_movimiento() {
        return $this->id_movimiento;
    }

    function get_id_prestamo() {
        return $this->id_prestamo;
    }

    function get_id_cliente() {
        return $this->id_cliente;
    }

    function get_id_pagare() {
        return $this->id_pagare;
    }

    function get_fecha_move() {
        return $this->fecha_move;
    }

    function get_fecha_pago() {
        return $this->fecha_pago;
    }

    function set_id_movimiento($id_movimiento) {
        $this->id_movimiento = $id_movimiento;
    }

    function set_id_prestamo($id_prestamo) {
        $this->id_prestamo = $id_prestamo;
    }

    function set_id_cliente($id_cliente) {
        $this->id_cliente = $id_cliente;
    }

    function set_id_pagare($id_pagare) {
        $this->id_pagare = $id_pagare;
    }

    function set_fecha_move($fecha_move) {
        $this->fecha_move = $fecha_move;
    }

    function set_fecha_pago($fecha_pago) {
        $this->fecha_pago = $fecha_pago;
    }


}
