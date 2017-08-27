<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pagare
 *
 * @author Ariel_dupuy
 */
class Pagare extends Model{
    public static $id_tabla="id_pagare";
    public static $prefijo_tabla ="";
    private $id_pagare;
        private $id_prestamo;
        private $fecha_vto;
        private $monto;	
        private $nro_cuota;
        private $id_estado;
        function get_id_pagare() {
            return $this->id_pagare;
        }

        function get_id_prestamo() {
            return $this->id_prestamo;
        }

        function get_fecha_vto() {
            return $this->fecha_vto;
        }

        function get_monto() {
            return $this->monto;
        }

        function get_nro_cuota() {
            return $this->nro_cuota;
        }

        function get_id_estado() {
            return $this->id_estado;
        }

        function set_id_pagare($id_pagare) {
            $this->id_pagare = $id_pagare;
        }

        function set_id_prestamo($id_prestamo) {
            $this->id_prestamo = $id_prestamo;
        }

        function set_fecha_vto($fecha_vto) {
            $this->fecha_vto = $fecha_vto;
        }

        function set_monto($monto) {
            $this->monto = $monto;
        }

        function set_nro_cuota($nro_cuota) {
            $this->nro_cuota = $nro_cuota;
        }

        function set_id_estado($id_estado) {
            $this->id_estado = $id_estado;
        }


        
}
