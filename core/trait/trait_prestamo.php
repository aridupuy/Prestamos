<?php

trait trait_prestamo {

    private static $MAXIMA_CANTIDAD_CUOTAS = 36;
    private static $MAXIMO_INTERES = 100;
    private static $MINIMO_INTERES = 0.1;
    private static $MAXIMO_MONTO = 1000000;
    private static $MINIMO_MONTO = 10;
    private static $MODALIDAD_MENSUAL = 1;
    private static $MODALIDAD_QUINCENAL = 2;
    private static $MODALIDAD_DIARIO = 3;
    private static $MODALIDAD_ANUAL = 4;
    protected $prestamo;
    protected $cliente;
    protected $pagare = array();
    protected $contrato;

    public function crear_prestamo($id_cliente, $monto, $interes, $cuotas = 1, $modalidad = 1) {
        $prestamo = new prestamo();
        if (!is_numeric($monto))
            throw new Exception("El monto no es numerico.");
        if (!is_numeric($interes))
            throw new Exception("El interes no es numerico.");
        if (!is_numeric($cuotas))
            throw new Exception("Las cuitas no son numericas.");
        if (!in_array($modalidad, array(1, 2, 3, 4)))
            throw new Exception("La modalidad no es válida.");
        if ($cuotas < 0 or $cuotas > self::$MAXIMA_CANTIDAD_CUOTAS) {
            throw new Exception("La cantidad de cuotas no es válida.");
        }
        if (!isset($id_cliente)) {
            throw new Exception("No se puede asociar el prestamo a un cliente.");
        }
        if ($monto < self::$MINIMO_MONTO or $monto > self::$MAXIMO_MONTO) {
            throw new Exception("El monto no es válido.");
        }
        if ($interes < self::$MINIMO_INTERES or $interes > self::$MAXIMO_INTERES) {
            throw new Exception("El interes no es válido.");
        }
        $this->cliente = new Cliente();
        $this->cliente->get($id_cliente);
        $prestamo = $this->calcular_prestamo($monto, $interes, $cuotas, $modalidad);
        if(!$prestamo){
            return false;
        }
        return $this;
    }

    private function calcular_prestamo($monto, $interes, $cuotas, $modalidad) {

        $this->contrato = new Contrato();
        Model::StartTrans();
        $this->contrato->set_id_estado(Estado::ACTIVO);
        $this->contrato->set_id_cliente($this->cliente->get_id_cliente());
        $this->contrato->set_fecha_contrato((new DateTime("now"))->format("Y-m-d"));
        $this->contrato->set_contrato(Contrato::CONTRATO);
        if ($this->contrato->set()) {

            $this->prestamo = new prestamo();
            $this->prestamo->set_id_cliente($this->cliente->get_id_cliente());
            $this->prestamo->set_id_contrato($this->contrato->get_id_contrato());
            $this->prestamo->set_monto_total($monto);
            if ($this->prestamo->set()) {
                $monto_cuotas = $this->calcular_monto_cuota($monto, $interes, $cuotas);
                for ($cuota=1;$cuota<=$cuotas ; $cuota++) {
                    $pagare = new Pagare();
                    $pagare->set_id_estado(Estado::ACTIVO);
                    $pagare->set_id_prestamo($this->prestamo->get_id_prestamo());
                    $pagare->set_nro_cuota($cuota);
                    $pagare->set_monto($monto_cuotas);
                    $pagare->set_fecha_vto($this->obtener_fecha_vto($cuotas, $cuota, $modalidad)->format("Y-m-d"));
                    if ($pagare->set()) {
                        $this->pagare[] = $pagare;
                    } else{
                        Model::FailTrans();
                        throw new Exception("Error al generar la cuota $cuota.");
                    }
                }
            }
            else{
                Model::FailTrans();
                throw new Exception("Error al generar el prestamo.");
            }
        }
        else {
            Model::FailTrans();
            throw new Exception("Error al generar el contrato.");
        }
        if(!Model::CompleteTrans()){
            return $this->prestamo;
        }
        return false;
    }

    private function calcular_monto_cuota($monto, $interes, $cuotas) {
        return ($monto+($monto * $interes) )/ $cuotas;
    }

    private function obtener_fecha_vto($cuotas, $cuota, $modalidad) {
        $fecha = new DateTime("now");
        switch ($modalidad) {
            case self::$MODALIDAD_MENSUAL :
                $fecha->add(new DateInterval("P" . (1 * $cuota) . "M"));
                break;
            case self::$MODALIDAD_QUINCENAL :
                $fecha->add(new DateInterval("P" . (15 * $cuota) . "D"));
                break;
            case self::$MODALIDAD_DIARIO :
                $fecha->add(new DateInterval("P" . (1 * $cuota) . ".D"));
                break;
            case self::$MODALIDAD_ANUAL:
                $fecha->add(new DateInterval("P" . (1 * $cuota) . "Y"));
                break;
        }
        return $fecha;
    }

}
