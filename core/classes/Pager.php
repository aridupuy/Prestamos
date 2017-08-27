<?php
// namespace Classes;
class Pager extends View{
	# La tabla se muestra desde $this->desde_registro hasta $this->hasta_registro
	public $desde_registro;
	public $hasta_registro;
	# Retorna el paginador. Recibe un recordset y arma los controles necesarios

	public function __construct($registros,$pagina_a_mostrar,$controller,$metodo){
		
		parent::__construct();
		$div=$this->createElement('div');
		$div->setAttribute('class','row paginador');
		$pager=$this->createElement('div');
                $div->appendChild($pager);
		$this->appendChild($div);
		$pager->setAttribute('class','col-lg-12');
		$pager->setAttribute('data-nav',$controller);
		$pager->setAttribute('data-method',$controller);
		$pager->setAttribute('data-pagina-actual',$pagina_a_mostrar);
		if($registros===false) return $pager;

		$cantidad_de_registros=$registros->rowCount();

		$cantidad_de_paginas=ceil($cantidad_de_registros/REGISTROS_POR_PAGINA);
		$pager->setAttribute('data-cantidad-paginas',$cantidad_de_paginas);
		if($cantidad_de_paginas==0) $pagina_a_mostrar=0;

		if($cantidad_de_registros<($pagina_a_mostrar-1)*REGISTROS_POR_PAGINA+1){
			$pagina_a_mostrar=1;
		}
		
		$this->desde_registro=($pagina_a_mostrar-1)*REGISTROS_POR_PAGINA+1;
		$this->hasta_registro=$this->desde_registro+REGISTROS_POR_PAGINA-1;
		
		
		if($this->hasta_registro>$cantidad_de_registros) $this->hasta_registro=$cantidad_de_registros;
                $linea= $this->createElement("div");
                $linea->setAttribute("class", "col-xs-offset-3 col-xs-6");
		$primera=$this->createElement('i');
		$primera->setAttribute('class','fa fa-fast-backward icono_pager');
		$linea->appendChild($primera);		

		$anterior=$this->createElement('i');
		$anterior->setAttribute('class','fa fa-backward icono_pager');
		$linea->appendChild($anterior);

		$descripcion=$this->createElement('i');
		$descripcion->appendChild($this->createTextNode('Página '.$pagina_a_mostrar.' de '.$cantidad_de_paginas));
		$descripcion->setAttribute('title','Se muestran '.REGISTROS_POR_PAGINA.' registros por página');
		$linea->appendChild($descripcion);

		$siguiente=$this->createElement('i');
		$siguiente->setAttribute('class','fa fa-forward icono_pager');
		$linea->appendChild($siguiente);

		$ultima=$this->createElement('i');
		$ultima->setAttribute('class','fa fa-fast-forward icono_pager');
		$linea->appendChild($ultima);
                $pager->appendChild($linea);
		if($cantidad_de_registros<MAXIMO_REGISTROS_POR_CONSULTA){
			$mensaje=$cantidad_de_registros.' Registros.';
		}
		else{
			$mensaje='Más de '.$cantidad_de_registros.' Registros. ';
		}
		$cantidad_registros=$this->createElement('div',$mensaje);
		$cantidad_registros->setAttribute('class','cantidad_registros');
		$pager->appendChild($cantidad_registros);

		return $this;
	}


}







