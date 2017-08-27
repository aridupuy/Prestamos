<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of controller_cliente
 *
 * @author Ariel_dupuy
 */
class controller_cliente  extends Controller{
    public function home($variables){
        $pagina_a_mostrar=1;
        if(isset($variables['pagina'])){
            $pagina_a_mostrar=$variables['pagina'];
            unset($variables['pagina']);
        }
        $form=$this->view->createElement("form");
        $raiz=$this->view->createElement("div");
        $raiz->setAttribute("class","container-fluid");
        $form->setAttribute('id','miFormulario');
        $form->appendChild($raiz);
        $recordset= Cliente::select();
//        print_r($filters->saveHTML());
//        print_r($pager->saveHTML());
//        print_r($table->saveHTML());
        $filters=new Filters($recordset, $variables, "cliente", "home");
        $pager=new Pager($recordset, $pagina_a_mostrar, "cliente", 'home');
        $acciones[]=array("token"=>"cliente.editar_cliente","etiqueta"=>"editar","id"=>"id_cliente");
        $table= new Table($recordset, $pager->desde_registro, $pager->hasta_registro,$acciones);
        $raiz->appendChild($this->view->importNode($filters->documentElement,true));
        $raiz->appendChild($this->view->importNode($pager->documentElement,true));
        $raiz->appendChild($this->view->importNode($table->documentElement,true));
        $header=$this->view->createElement("header");
        $nuevo=$this->view->createElement("input");
        $nuevo->setAttribute("type","button");
        $nuevo->setAttribute("data-nav","Cliente");
        $nuevo->setAttribute("data-method","crear_cliente");
        $nuevo->setAttribute("value","Nuevo Cliente");
        $nuevo->setAttribute("class","btn col-lg-1 col-lg-offset-11 btn-header");
        $header->appendChild($nuevo);
        $this->view->appendChild($header);
                
        $this->view->appendChild($form);
        return $this->view;
    }
    public function crear_cliente(){
        $this->view->cargar_vista("views/Clientes.new.html");
        return $this->view;
    }
    public function crear_cliente_modal($variables){
        $vista=new View();
        $vista=$this->crear_cliente();
        $form=$vista->getElementById("miFormulario");
        $form->setAttribute("id", "elFormModal");
        return $vista;
    }

    public function editar_cliente($variables){
        print_r($variables);
        $this->view->cargar_vista("views/Clientes.new.html");
        $cliente=new Cliente();
        $cliente->get($variables['id']);
        $array=array();
        $array["Nombre"]=$cliente->get_nombre();
        $array["Apellido"]=$cliente->get_apellido();
        $array["Documento"]=$cliente->get_documento();
//        print_r($cliente->get_fec_nac());
        $fecha= DateTime::createFromFormat("Y-m-d H:i:s", $cliente->get_fec_nac());
        $array["Fecha_nacimiento"]=$fecha->format("Y-m-d");
        $array["Localidad"]=$cliente->get_localidad();
        $array["Nacionalidad"]=$cliente->get_nacionalidad();
        $array["Numero"]=$cliente->get_numero();
        $array["Telefono"]=$cliente->get_telefono();
        $array["Celular"]=$cliente->get_celular();
        $array["Direccion"]=$cliente->get_direccion();
        $this->view->cargar_variables($array);
        return $this->view;
    }
    public function crear_post($variables){
        $cliente=new Cliente();
//        print_r($variables);
        if(!isset($variables['Apellido'])){
            Logger::all("error falta el parametro Apellido  ", get_class($this));
            return $this->home($variables);
        }
        if(!isset($variables['Nombre'])){
            Logger::all("error falta el parametro Nombre  ", get_class($this));
            return $this->home($variables);
        }
        if(!isset($variables['Fecha_nacimiento'])){
            Logger::all("error falta el parametro Fecha_nacimiento  ", get_class($this));
            return $this->home($variables);
        }
        if(!isset($variables['Documento'])){
            Logger::all("error falta el parametro Documento  ", get_class($this));
            return $this->home($variables);
        }
        if(!isset($variables['Direccion'])){
            Logger::all("error falta el parametro Direccion  ", get_class($this));
            return $this->home($variables);
        }
        if(!isset($variables['Numero'])){
            Logger::all("error falta el parametro Numero  ", get_class($this));
            return $this->home($variables);
        }
        if(!isset($variables['Telefono'])){
            Logger::all("error falta el parametro Telefono  ", get_class($this));
            return $this->home($variables);
        }
        if(!isset($variables['Celular'])){
            Logger::all("error falta el parametro Celular  ", get_class($this));
            return $this->home($variables);
        }
        if(!isset($variables['Localidad'])){
            Logger::all("error falta el parametro Localidad  ", get_class($this));
            return $this->home($variables);
        }
        if(!isset($variables['Nacionalidad'])){
            Logger::all("error falta el parametro Nacionalidad  ", get_class($this));
            return $this->home($variables);
        }
        if(isset($variables['id'])){
            $cliente->get($variables['id']);
        }
        $cliente->set_apellido($variables['Apellido']);
        $cliente->set_nombre($variables['Nombre']);
        $cliente->set_documento($variables['Documento']);
        $cliente->set_celular($variables['Celular']);
        $cliente->set_telefono($variables['Telefono']);
        $cliente->set_direccion($variables['Direccion']);
        $cliente->set_numero($variables['Numero']);
        $cliente->set_localidad($variables['Localidad']);
        $cliente->set_id_estado(Estado::ACTIVO);
        $fecha= DateTime::createFromFormat("Y-m-d", $variables['Fecha_nacimiento']);
        $cliente->set_fec_nac($fecha->format("Y-m-d"));
        $cliente->set_nacionalidad($variables['Nacionalidad']);
//        var_dump($cliente);
        if(!$cliente->set()){
            Logger::all("Error en la operacion, Reintente.", get_class($this));
            return $this->home($variables);
        }
        Logger::all("Operacion realizada correctamente.", get_class($this));
        return $this->home($variables);
    }
}
