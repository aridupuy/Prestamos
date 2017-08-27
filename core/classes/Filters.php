<?php
// namespace Classes;
class Filters extends View{

	# Retorna los filtros del util. Recibe un recordset y arma los filtros para cada columna
	public function __construct($registros,$variables,$controller,$metodo){

		parent::__construct();
		$div=$this->createElement('div');
		$div->setAttribute('class','row filters');
		$this->appendChild($div);
		if($registros)
		{
			$i=0;
			while($campo=$registros->FetchField($i) AND $campo->name)
			{	

				$recorrer[$campo->name]=$i;	
				$i++;
			}
		    foreach($recorrer as $columna=>$indice):
		      	$meta=$registros->FetchField($indice); # Todo quedo dado vuelta 
                        $div_content=$this->createElement("div");
                        $div_content->setAttribute("class", "col-lg-3");
		      	$input=$this->createElement('input');
                        $input->setAttribute('class','form-control filter_input');
			    $tipo=$registros->MetaType($meta->type);
			    switch ($tipo) {
			    	case 'C':
			    		$input->setAttribute('type','text');		
			    		break;
			    	case 'I':
			    		$input->setAttribute('type','number');
			    		break;
			    	case 'N':
			    		$input->setAttribute('type','number');
			    		$input->setAttribute('step','0.01');
			    		break;
			    	case 'D':
			    	case 'T':
			    		$input->setAttribute('type','date');
			    		break;
			    	default:
			    		$input->setAttribute('type','text');
			    		break;
			    }
			    
			    $input->setAttribute('name',"filter.".$meta->name);
			    $input->setAttribute('placeholder',ucfirst($meta->name));
			    if(isset($variables["filter.".$meta->name])) $input->setAttribute('value',$variables["filter.".$meta->name]);
		        $div_content->appendChild($input);
		        $div->appendChild($div_content);
		    endforeach;
	  	}

		$button=$this->createElement('input');
		$button->setAttribute('type','submit');
		$button->setAttribute('data-nav',$controller);
		$button->setAttribute('data-method',$metodo);
		$button->setAttribute('Value','Filtrar');
		$button->setAttribute('class','btn btn-primary filter_button');

		$div->appendChild($button);

		return $div;
	}

}
