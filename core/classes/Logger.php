<?php
     class Logger {
         const PREFIJO_DESARROLLO="Mensaje desarrollador: "; 
         private static  function log($mensaje,$tabla=false,$variables=false,$log_level){
            $log=new Log();
            if($tabla!==false)
                $log->set_tabla($tabla);
            if($variables!=false){
                // print_r($variables);
                $json=(json_encode($variables));
                if($json!=false)
                    $log->set_data($json);
            }
            $log->set_accion($mensaje);
            $log->set_log_level($log_level);
            $log->set_posted(false);
            // print_r($mensaje);
            if($mensaje!==false AND $mensaje!==null AND $mensaje!=="")
                if($log->set()){
                    return true;
                }
            return false;
         }
         public static function developper($mensaje){
             error_log(self::PREFIJO_DESARROLLO.$mensaje);
             return true;
         }
         public static function usuario($mensaje,$clase,$variables=false){
             $log_level=Log::LOG_USUARIO;
            if(self::log($mensaje,$clase,$variables,$log_level))
                return true;
            return false;
         }
         public static function all($mensaje,$clase){
             if(self::usuario($mensaje,$clase)){
                self::developper($mensaje);
                return true;
             }
            return false;
         }
         public static function log_tabla($mensaje,$tabla=false,$variables=false){
             $log_level=Log::LOG_SISTEMAS;
             if(self::log($mensaje,$tabla,$variables,$log_level))
                return true;
                return false;

         }
         public static function ultimos_logs($num){
            $mensaje= Log::Obtener_ultimos_logs($num);
            return $mensaje;
         }
     } 
?>