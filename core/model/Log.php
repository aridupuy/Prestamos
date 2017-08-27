<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of log
 *
 * @author ariel
 */
class Log extends Model{
    const LOG_USUARIO=1;
    const LOG_SISTEMAS=2;
    // public static $mensaje="";
    public static $prefijo_tabla="";
    public static $id_tabla="id_log";
    public static $secuencia="sq_bo_log";
    private $id_log;
    private $tabla;
    private $accion;
    private $data;
    private $fecha_reg;
    private $log_level;
    private $posted;
    private $id_usuario;
    public function set_id_usuario($id_usuario){
        $this->id_usuario=$id_usuario;
    }
    public function get_id_usuario(){
        return $this->id_usuario;
    }
    public function set_posted($posted){
        $this->posted=$posted;
    }
    public function get_posted(){
        return $this->posted;
    }
    public function set_log_level($log_level){
        $this->log_level=$log_level;
    }
    public function get_log_level(){
        return $this->log_level;
    }
    public function get_id_log() {
        return $this->id_log;
    }

    public function get_tabla() {
        return $this->tabla;
    }

    public function get_accion() {
        return $this->accion;
    }

    public function get_data() {
        return $this->data;
    }

    public function get_fecha_reg() {
        return $this->fecha_reg;
    }

    public function set_id_log($id_log) {
        $this->id_log = $id_log;
    }

    public function set_tabla($tabla) {
        $this->tabla = $tabla;
    }

    public function set_accion($accion) {
        $this->accion = $accion;
    }

    public function set_data($data) {
        $this->data = $data;
    }

    public function set_fecha_reg($fecha_reg) {
        $this->fecha_reg = $fecha_reg;
    }
    public static function obtener_ultimos_logs($num){
        $sql="SELECT * FROM log where log_level=? and posted is null order by id_log desc";
        $recordset=self::execute_select($sql,array(self::LOG_USUARIO),$num);
//        print_r("hola");
//        var_dump($recordset);
        foreach( $recordset as $row ){
            $log=new Log();
            $log->set_id_log($row['id_log']);
            if($row['posted']!=true){
                $log->set_posted(true);
                if($log->set()){
                    return $row["accion"];
                }
            }
        }
        return false;
    }


}
