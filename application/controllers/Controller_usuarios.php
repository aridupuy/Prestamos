<?php
class Controller_usuarios extends Controller{
    public function home($variables){
        $pagina_a_mostrar=1;
        if(isset($variables['pagina_a_mostrar']))
            $pagina_a_mostrar=$variables['pagina_a_mostrar'];
        $usuario=new Usuario();
        $this->view->cargar_vista("views/Usuarios.home.html");
        $recordSet=$usuario->select();
        $filters=new Filters($recordSet, $variables, "usuarios", "home");
        $pager=new Pager($recordSet, $pagina_a_mostrar, "usuario", "home");
        $acciones[]=array("token"=>"usuarios.cambiar_estado","etiqueta"=> Table::INTERRUPTOR,"id"=>"id_usuario" ,"campo"=>"id_estado");
        $acciones[]=array("token"=>"usuarios.editar","etiqueta"=> "Editar","id"=>"id_usuario" ,"campo"=>"id_usuario");
        $table=new Table($recordSet, $pager->desde_registro, $pager->hasta_registro,$acciones);
        $table->eliminar_columna(3);
        $table->eliminar_columna(3);
//        $table->eliminar_columna(3);
        $form= $this->view->getElementById("miFormulario");
        $form->appendChild($this->view->importNode($filters->documentElement,true));
        $form->appendChild($this->view->importNode($pager->documentElement,true));
        $form->appendChild($this->view->importNode($table->documentElement,true));
        $this->view->appendChild($form);
        
        return $this->view;
    }
    
    public function editar($variables){
        $this->view->cargar_vista("views/Usuarios.editar.html");
        $usuario=new Usuario();
        $usuario->get($variables['id']);
        $usu=$this->view->getElementById("usuario");
        $usu->appendChild($this->view->createTextNode($usuario->get_usuario()));
        
        return $this->view;
    }
    public function cambiar_estado($variables){
      $usuario=new Usuario();
        $usuario->get($variables['id']);
        if($usuario->get_id_estado()== Estado::ACTIVO)
            $usuario->set_id_estado (Estado::INACTIVO);
        else
            $usuario->set_id_estado (Estado::ACTIVO);

        if($usuario->set())
            Logger::all ("Estado actualizado correctamente.", get_class ());
        else
            Logger::all ("Error al actualizar.", get_class ());
        print_r($usuario);
        return $this->home($variables);
    }
    public function nuevo(){
        $this->view("views/usuario.editar.html");
        return $this->view;
    }
    public function crear_post($variables){
        
    }
}
