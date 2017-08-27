<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Controller_prestamo
 *
 * @author Ariel_dupuy
 */
class Controller_prestamo extends Controller {
    use trait_prestamo;
    public function home($variables){
//        var_dump($variables);
        $this->view->cargar_vista("views/prestamo.home.html");
        $this->cargar_clientes();
        $cliente=new Cliente();
        if(isset($variables['id_cliente'])){
            $cliente->get($variables['id_cliente']);
        }
        $this->cargar_info_cliente($cliente);
        $this->view->cargar_variables($variables);
        return $this->view;
    }
    public function cargar_clientes(){
        $recordset= Cliente::select();
        $select= $this->view->getElementById("id_cliente");
        foreach ($recordset as $row){
            $option= $this->view->createElement("option",$row['nombre']." ".$row["apellido"]);
            $option->setAttribute("value",$row['id_cliente']);
            $select->appendChild($option);
        }
    }
    private function cargar_info_cliente(Cliente $cliente=null){
        
        if($cliente!=null and $cliente->get_id()!=null){
            $elemento= $this->view->getElementById("nombre");
            $elemento->appendChild($this->view->createTextNode($cliente->get_nombre()));
            $elemento= $this->view->getElementById("apellido");
            $elemento->appendChild($this->view->createTextNode($cliente->get_apellido()));
            $elemento= $this->view->getElementById("documento");
            $elemento->appendChild($this->view->createTextNode($cliente->get_documento()));
//            $elemento= $this->view->getElementById("email");
//            $elemento->appendChild($this->view->createTextNode($cliente->get_()));
            $elemento= $this->view->getElementById("direccion");
            $elemento->appendChild($this->view->createTextNode($cliente->get_direccion()));
            $elemento= $this->view->getElementById("numero");
            $elemento->appendChild($this->view->createTextNode($cliente->get_numero()));
            $elemento= $this->view->getElementById("telefono");
            $elemento->appendChild($this->view->createTextNode($cliente->get_telefono()));
            $elemento= $this->view->getElementById("celular");
            $elemento->appendChild($this->view->createTextNode($cliente->get_celular()));
            $elemento= $this->view->getElementById("localidad");
            $elemento->appendChild($this->view->createTextNode($cliente->get_localidad()));
            $elemento= $this->view->getElementById("nacionalidad");
            $elemento->appendChild($this->view->createTextNode($cliente->get_nacionalidad()));
            $elemento= $this->view->getElementById("fec_nac");
//            var_dump($cliente->get_fec_nac());
            $fecha= DateTime::createFromFormat("Y-m-d H:i:s",$cliente->get_fec_nac());
            $elemento->appendChild($this->view->createTextNode($fecha->format("d/m/Y")));
            return;
            
        }
        else{
            $elemento= $this->view->getElementById("datos_cliente");
            $elemento->setAttribute("class","esconder");
            return;
        }
    }
            

    public function prestamo_create_post($variables){
        if(isset($variables['id_cliente'])){
            $cliente=new Cliente();
            $cliente->get($variables['id_cliente']);
        }
        try{
            $this->crear_prestamo($variables["id_cliente"], $variables["monto_total"], $variables["tasa"], $variables["cuotas"], $variables["modalidad"]);
        } catch (Exception $ex){
            Logger::all($ex->getMessage(), get_class());
            
        }
        Logger::all("Prestamo Generado Correctamente", get_class());
        return $this->home($variables);
    }
}

