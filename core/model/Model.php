<?php

abstract class Model {
    private static $conection;
    const PREFIJO_SETTERS="set_";
    const PREFIJO_GETTERS="get_";
    final public function __construct($variables=false){
//        if($variables) $this->init($variables);
        self::$conection=self::singleton();
        if($variables) 
            $this->init($variables);
        return $this;
    }
   final public function getId(){
        $id_tabla = strtolower(static::$id_tabla);
        $metodo=self::PREFIJO_GETTERS.$id_tabla;
        if(method_exists($this,$metodo))
            return $this->$metodo();
        return false;
    }
    final public function generar_id(){
        return $this->generar_id_secuencial();
    }
    final private function generar_id_secuencial(){
        
        $sql="SELECT COALESCE(max(".static::$id_tabla."),0) + 1 as max from ". get_class($this);
        $result=$this->execute_select($sql);
        # Falta verificar el Overflow
        if($result AND $result->rowCount()) {
            $row=$result->FetchRow();
            return $row['max'];
        }
        return false;
    }
    final public function setId($id){
        $id_tabla = strtolower(static::$id_tabla);
        $metodo=self::PREFIJO_SETTERS.$id_tabla;
        if(method_exists($this,$metodo));
            return $this->$metodo($id);
        return false;
    }

    final public function set_id($id){ return $this->setId($id);}

    final public function get_id(){return $this->getId();}
    
    final public static function singleton() {
        if (!isset(self::$conection)) {
            $DB = NewADOConnection(DB_ENGINE);
            try {
                $resultado=$DB->Connect(DB_CONNECT.":".DB_PORT, DB_USER, DB_PASS,DB_NAME);
            } catch (Exception $e) {
                Logger::developper($e->getMessage());
                $resultado=false;
            }
////            if($resultado AND false) Logger::all('Conexion establecida con la base de datos.');
//            if(!$resultado AND ACTIVAR_LOG_APACHE_LOGIN) Logger::all('Fallo al establecerse la conexion con la base de datos.');
            if(!$resultado) exit();
            $DB->SetCharSet('utf8');
            
            self::$conection=$DB;

        }
        return self::$conection;
    }
    public static function execute_select($sql,$variables=false,$limit=-1){
        if($limit==-1) 
            $limit=MAXIMO_REGISTROS_POR_CONSULTA;
        if(substr($sql, 0,6)=="DELETE") 
            $limit=-1;
        return self::$conection->SelectLimit($sql,$limit,-1,$variables);
    }
     protected static function execute_update($parametros,$where=null){
        self::StartTrans();
        try {
            $tabla=strtolower(static::$prefijo_tabla.get_called_class());
            if(isset($parametros[static::$id_tabla])){
                $id=$parametros[static::$id_tabla];
                unset($parametros[static::$id_tabla]);
                if($where==null) $where=static::$id_tabla."={$id}";
            }
            if($where==null) return false; # Sin where y sin Id, no parece una buena idea
            if(ACTIVAR_LOG_APACHE_DE_CONSULTAS_SQL) 
                Logger::developper('UPDATE '.$tabla.' WHERE '.$where, 0);
            $tiempo_inicio=microtime(true);
            
            $result=self::$conection->AutoExecute($tabla, $parametros, 'UPDATE',$where);
            if($result) $resultado=true;
            else $resultado=false;
            $duracion=microtime(true)-$tiempo_inicio;

            if(isset($id)) $parametros[static::$id_tabla]=$id;
//            if($resultado) $resultado=Gestor_de_log::set_auto('UPDATE',$tabla,static::$id_tabla,$parametros, $resultado,$duracion);
            
        } catch (Exception $e) {
            $resultado=false;
            Logger::developper($e->getMessage());
        }
        self::CompleteTrans();

        if($resultado){
            return $result;
        }
        return false;
    }
    final private function init($variables){
        # DEJAR EN PRIVATE!
        foreach($variables as $propiedad=>$valor):
          $method=self::PREFIJO_SETTERS.ucfirst($propiedad);
          if(method_exists($this, $method) && $valor!=='')
            $this->$method($valor);
        endforeach;

        return true;
    }
   
    public function get($id){
        if(!is_numeric($id)) return false;
        $sql="  SELECT *
                FROM ".static::$prefijo_tabla.strtolower(get_class($this))."
                WHERE ".static::$id_tabla."= ?
                ";
        $result=$this->execute_select($sql,$id);        
        if($result AND $result->rowCount()==1) { return $this->init($result->GetRowAssoc(false)); }

        return false;
    }

    final public function parametros(){
        $parametros=array();
        $metodos=get_class_methods(get_class($this));
        foreach($metodos as $metodo):
            $atributo=explode(self::PREFIJO_GETTERS, $metodo);
            if(isset($atributo[1]) && $atributo[1]!==''){
                $atributo=strtolower($atributo[1]);
                if($this->$metodo()!=null) $parametros[$atributo]=$this->$metodo();
            }
        endforeach;
        unset($parametros['conexion']);
        return $parametros;
    }

    public function set(){
        $parametros=$this->parametros();
        $id_tabla=static::$id_tabla;
        $resultado=false;
        if(!$this->getId())
        {
            $parametros[$id_tabla]=$this->generar_id();
            
            if($parametros[$id_tabla]!==false)
            {   
                    $this->setId($parametros[$id_tabla]);
                    $parametros['id']=$this->get_id();
                    $resultado= $this->execute_insert($parametros);
                    if(!$resultado) $this->setId('');
                    if(get_class($this)!="Log")
                        Logger::log_tabla("Insert",get_class($this),$parametros);
            }
        }
        else
        {    
            foreach($parametros as $clave=>$valor){
                    $parametros[$clave]=trim($valor);
            }   
            $resultado= $this->execute_update($parametros);
            if(get_class($this)!="Log")
                Logger::log_tabla("Update",get_class($this),$parametros);
                
        }
        if($resultado) { return $resultado;}
        return false;
    }
    public static function StartTrans(){
        return self::$conection->StartTrans();
    }

    public static function CompleteTrans(){
        return self::$conection->CompleteTrans();
        }

    public static function getTrans(){
        return self::$conection->_transOK;
    }

    public static function FailTrans(){
        return self::$conection->FailTrans();
    }

    public static function RollbackTrans(){
        return self::$conection->RollbackTrans();
    }

    
    
    final public static function select($variables=false){
        $filtros=self::preparar_filtros($variables);
        $tabla = static::$prefijo_tabla.strtolower((get_called_class())); 
        $id_tabla = strtolower(static::$id_tabla); 
        $sql="  SELECT *
                FROM $tabla
                $filtros
                ORDER BY $id_tabla DESC 
                ";
        $result=self::execute_select($sql,$variables);    
        if($result) { return $result; }
        return false;
    }
     protected static function preparar_filtros($variables=false){
        # Deprecar esta funcion, se puede hacer tranquilamente con ADOdb selectlimit
        $filtros='';
        if($variables){
            $filtros='WHERE true ';
            foreach ($variables as $clave => $valor):
                $clave=strtolower($clave);
                $filtros .=" AND ".$clave."= ? ";    
            endforeach;
        }
        return $filtros;
    }
    protected static function execute_insert($parametros){
        # Retorna true en exito, false en fracaso.
        self::StartTrans();
        try {
            $tabla=strtolower(static::$prefijo_tabla.get_called_class());
            if(ACTIVAR_LOG_APACHE_DE_CONSULTAS_SQL) 
                Logger::developper('INSERT '.$tabla.' VALUES '.static::$id_tabla.'='.$parametros['id'], 0);
            $tiempo_inicio=microtime(true);
            $result=self::$conection->AutoExecute($tabla, $parametros, 'INSERT');
            if($result) $resultado=true;
            else $resultado=false;
            $duracion=microtime(true)-$tiempo_inicio;
//            if($resultado) $resultado=Gestor_de_log::set_auto('INSERT',$tabla,static::$id_tabla,$parametros, $resultado,$duracion);

        } catch (Exception $e) {
            $resultado=false;
            error_log($e->getMessage());
        }
        self::CompleteTrans();
        if($resultado){
            return $result;
        }
        return false;     
    }
}
