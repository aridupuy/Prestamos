<?php
class Gestor_de_permisos {
    
    public static function puede(Usuario $usuario, String $class){
        $permiso=new Usupermiso();
        $recordset=$permiso->puede($usuario,$class);
        if($recordset->rowCount()>0){
            return true;
        }
        return false;
    }
}
