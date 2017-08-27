<?php
// namespace Models;
class Useris extends Model
{

  	public static $prefijo_tabla='';
  	public static $id_tabla='id_usuario';
  	private $id_usuario;
	private $fecha;
	private $sesion;
	private $machine;
	private $id_usuariocode;
	private $cstr;
	private $punch;

	public function get_id_usuario(){return $this->id_usuario;}
	public function get_fecha(){return $this->fecha;}
	public function get_sesion(){return $this->sesion;}
	public function get_machine(){return $this->machine;}
	public function get_id_usuariocode(){return $this->id_usuariocode;}
	public function get_cstr(){return $this->cstr;}
	public function get_punch(){return $this->punch;}

	public function set_id_usuario($variable){$this->id_usuario=$variable; return $this->id_usuario;}
	public function set_fecha($variable){ $this->fecha=$variable; return $this->fecha;}
	public function set_sesion($variable){ $this->sesion=$variable; return $this->sesion;}
	public function set_machine($variable){ $this->machine=$variable; return $this->machine;}
	public function set_id_usuariocode($variable){ $this->id_usuariocode=$variable; return $this->id_usuariocode;}
	public function set_cstr($variable){ $this->cstr=$variable; return $this->cstr;}
	public function set_punch($variable){ $this->punch=$variable; return $this->punch;}

	public function update_insert()
	{
		# Debo escribir esta funcion pq useris tiene clave doble
		$parametros=$this->parametros();
		$registros=self::select(array('id_usuario'=>$parametros['id_usuario'],'id_usuariocode'=>$parametros['id_usuariocode']));
                
		if(!$registros) return false;
		if($registros->RowCount()==1){
			return self::execute_update($parametros," id_usuario=".$parametros['id_usuario']." AND id_usuariocode=".$parametros['id_usuariocode']);
		}
		else return self::execute_insert($parametros);
    }

    public static function select_usuarios_online()
    {
    	$now=new Datetime('now');
    	$now->sub(new DateInterval('PT10M'));
    	$variables=array();
    	$variables[]=$now->format(FORMATO_FECHA_POSTGRES);
    	$sql=" SELECT A.fecha, B.id_marchand, B.username, B.completo, C.minirs, A.machine
					FROM ho_useris A JOIN cd_usumarchand B 
					ON A.id_usuario=B.id_usumarchand AND A.id_usuariocode=1 
					JOIN cd_marchand C ON B.id_marchand=C.id_marchand 
					JOIN ho_ulog D ON D.id_usumarchand=B.id_usumarchand 
					AND D.id_usuariocode=1 WHERE D.fecha> ?
					GROUP BY 1,2,3,4,5,6
    			";
    	return self::execute_select($sql,$variables);
    }

}