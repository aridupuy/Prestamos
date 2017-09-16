<?php

class controller_prestamos_otorgados extends Controller{
    public function home($variables){
        $pagina_a_mostrar=1;
        if(isset($variables["pagina_a_mostrar"])){
            $pagina_a_mostrar=$variables["pagina_a_mostrar"];
        }
        $form= $this->view->createElement("form");
        $form->setAttribute("id","miFormulario");
        $div= $this->view->createElement("div");
        $div->setAttribute("class","container-fluid");
        
        $div2= $this->view->createElement("div");
        $div2->setAttribute("class","col-lg-12");
        $div->appendChild($div2);
        $form->appendChild($div);
        $recorset= prestamo::select_prestamos($variables);
        $filters=new Filters($recorset, $variables, get_class(), "home");
        $filters->eliminar_campo(1);
        $pager=new Pager($recorset, $pagina_a_mostrar, get_class(), "home");
        $acciones[]=array("token"=>"prestamos_otorgados.ver_pagares","etiqueta"=>"ver cuotas","id"=>"id_prestamo");
        $tabla=new Table($recorset, $pager->desde_registro, $pager->hasta_registro,$acciones);
        $tabla->eliminar_columna(1);
        $div2->appendChild($this->view->importNode($filters->documentElement,true));
        $div2->appendChild($this->view->importNode($pager->documentElement,true));
        $div2->appendChild($this->view->importNode($tabla->documentElement,true));
        $this->view->appendChild($form);
        return $this->view;
    }
    public function ver_pagares($variables){
        $pagina_a_mostrar=1;
        if(isset($variables["pagina_a_mostrar"])){
            $pagina_a_mostrar=$variables["pagina_a_mostrar"];
        }
        $form= $this->view->createElement("form");
        $form->setAttribute("id","miFormulario");
        $div= $this->view->createElement("div");
        $div->setAttribute("class","container-fluid");
        
        $div2= $this->view->createElement("div");
        $div2->setAttribute("class","col-lg-12");
        $div->appendChild($div2);
        $form->appendChild($div);
        $recorset= pagare::select_pagares_prestamo($variables);
        $filters=new Filters($recorset, $variables, "prestamos_otorgados", "ver_pagares");
        $filters->eliminar_campo(1);
        $filters->eliminar_campo(1);
        $pager=new Pager($recorset, $pagina_a_mostrar, get_class(), "ver_pagares");
        $acciones[]=array("token"=>"prestamos_otorgados.pagar","etiqueta"=>"Pagar","campo"=>"id_estado",'id'=>'id_pagare','title'=>'Pagar');
        $acciones[]=array("token"=>"prestamos_otorgados.desactivar","etiqueta"=>"Interruptor","campo"=>"id_estado",'id'=>'id_pagare','title'=>'Cambiar Estado');
        $tabla=new Table($recorset, $pager->desde_registro, $pager->hasta_registro,$acciones);
        $tabla->eliminar_columna(1);
        $tabla->eliminar_columna(1);
        $tabla->eliminar_columna(4);
//        $tabla->eliminar_columna(1);
        $div2->appendChild($this->view->importNode($filters->documentElement,true));
        $div2->appendChild($this->view->importNode($pager->documentElement,true));
        $div2->appendChild($this->view->importNode($tabla->documentElement,true));
        $this->view->appendChild($form);
        return $this->view;
    }
    public function desactivar($variables){
        if(isset($variables['id'])){
            $pagare=new Pagare();
            $pagare->get($variables['id']);
            if($pagare->get_id_estado()== Estado::ACTIVO){
                $pagare->set_id_estado(Estado::INACTIVO);
            }
            else {
                $pagare->set_id_estado(Estado::ACTIVO);
            }
            if(!$pagare->set()){
                Logger::all("Error al cambiar el estado", get_class());
                return $this->home($variables);
                
            }
            else{
                Logger::all("Estado modificado correctamente", get_class());
            }
            $variables['id']=$pagare->get_id_prestamo();
        }
        return $this->ver_pagares($variables);
    }
    public function pagar($variables){
       if(isset($variables['id'])){
           $pagare=new Pagare();
           $pagare->get($variables['id']);
           $prestamos=new prestamo();
           $prestamos->get($pagare->get_id_prestamo());
           $movimiento=new Movimientos();
           $movimiento->set_id_cliente($prestamos->get_id_cliente());
           $movimiento->set_id_prestamo($prestamos->get_id_prestamo());
           $movimiento->set_id_pagare($pagare->get_id_pagare());
           $fecha=new DateTime("now");
           $movimiento->set_fecha_move($fecha->format("Y-m-d"));
           $movimiento->set_fecha_pago($fecha->format("Y-m-d"));
           Model::StartTrans();
           if($movimiento->set()){
               $pagare->set_id_estado(Estado::PAGADO);
                if($pagare->set()){
                    Logger::all("Pagado Correctamente.", get_class());
               }
               else{
                Model::FailTrans();
                Logger::all ("Error al realizar el pago", get_class ());
               }
           }
           else{
                Logger::all ("Error al realizar el pago", get_class ());
                Model::FailTrans();
           }
           if(Model::CompleteTrans()){
              $variables['id']=$pagare->get_id_pagare();
              return $this->ver_pagares($variables);
           }
       }
        return $this->home(false);
    }
}
