<?php

class Controller_permisos extends Controller {

    public function home($variables) {
        $this->view->cargar_vista("views/Permisos.home.html");
        $this->cargar_usuarios($variables);
        $this->cargar_cuadros_permisos();
        if (isset($variables['id_usuario'])) {
            $usuario = new Usuario();
            $usuario->get($variables['id_usuario']);

            $recordset = Usupermiso::select(array("id_usuario" => $usuario->get_id_usuario()));
            foreach ($recordset as $row) {
                $usupermiso = new Usupermiso($row);
                $permiso = new Permiso();
                $permiso->get($usupermiso->get_id_permiso());
                $div = $this->view->getElementById($usupermiso->get_id_permiso());
                if($usupermiso->get_id_estado()== Estado::ACTIVO)
                    $div->setAttribute("class", "cuadrado permiso alert-success");
            }
        }
//        "alert-danger"
//        "alert-success"
        $this->view->cargar_variables($variables);
        return $this->view;
    }

    private function cargar_cuadros_permisos() {
        $recordset = Permiso::select();
        $contenedor = $this->view->getElementById("contenedor_permisos");
        foreach ($recordset as $row) {
            $permiso = new Permiso($row);
//             <div class="cuadrado permiso" type="button" data-nav="permisos" data-method="asignar" name="">
//                <span>Prestamos</span>
//            </div>

            $div = $this->view->createElement("div");
            $span = $this->view->createElement("span", $permiso->get_descripcion());
            $div->setAttribute("class", "cuadrado permiso alert-danger");
            $div->setAttribute("type", "button");
            $div->setAttribute("data-nav", "permisos");
            $div->setAttribute("data-method", "asignar");
            $div->setAttribute("data", "id_permiso");
            $div->setAttribute("value", $permiso->get_id_permiso());
            $div->setAttribute("id", $permiso->get_id_permiso());
            $div->appendChild($span);

            $contenedor->appendChild($div);
        }
    }

    private function cargar_usuarios($variables) {
        $select = $this->view->getElementById("usuario");
        $select_copiar = $this->view->getElementById("id_usuario_copiar");
        $recordset = Usuario::select();
        foreach ($recordset as $row) {
            $usuario = new Usuario($row);
            $option2 = $this->view->createElement("option", $usuario->get_usuario());
            $option2->setAttribute("value", $usuario->get_id_usuario());
            if ($usuario->get_id() !== Application::$usuario->get_id()) {
                $option = $this->view->createElement("option", $usuario->get_usuario());
                $option->setAttribute("value", $usuario->get_id_usuario());
                if (isset($variables["id_usuario"]) AND $variables["id_usuario"] === $usuario->get_id()) {
                    $option->setAttribute("selected", "selected");
                }
                if (isset($variables["id_usuario_copiar"]) AND $variables["id_usuario_copiar"] === $usuario->get_id()) {
                    $option2->setAttribute("selected", "selected");
                }
                $select->appendChild($option);
            }
            if (isset($variables["id_usuario_copiar"]) AND $variables["id_usuario_copiar"] === $usuario->get_id()) {
                $option2->setAttribute("selected", "selected");
            }
            $select_copiar->appendChild($option2);
        }
    }

    public function asignar($variables) {
        if (isset($variables["id_permiso"])) {
            if (isset($variables["id_usuario"])) {
                $usuario = new Usuario();
                $usuario->get($variables["id_usuario"]);
                $usupermiso = new usupermiso();
                $permiso = new Permiso();
                $permiso->get($variables['id_permiso']);
                $recordset = Usupermiso::select(array("id_permiso" => $permiso->get_id(), "id_usuario" => $usuario->get_id()));
                if ($recordset->rowCount()==0) {
                    $usupermiso->set_id_permiso($permiso->get_id());
                    $usupermiso->set_id_usuario($usuario->get_id());
                    $usupermiso->set_id_estado(Estado::ACTIVO);
                    if ($usupermiso->set()) {
                        Logger::all("Permiso error al asignar permiso.", get_class());
                    } else {
                        Logger::all("Error al asignar permiso.", get_class());
                    }
                } else {
                    $row=$recordset->fetchRow();
                    $usupermiso->get($row['id_usupermiso']);
                    if ($usupermiso->get_id_estado() == Estado::ACTIVO) {
                        $usupermiso->set_id_estado(Estado::INACTIVO);
                    } else {
                        $usupermiso->set_id_estado(Estado::ACTIVO);
                    }
                    if (!$usupermiso->set()) {
                        Logger::all("Error al cambiar permiso.", get_class());
                    } else {
                        Logger::all("Estado correctamente asignado.", get_class());
                    }
                }
            }
        }
        return $this->home($variables);
    }
    
    public function copiar($variables){
        $error=false;
        if(isset($variables['id_usuario'])){
            if(isset($variables['id_usuario_copiar'])){
                $usuario_copiar=new Usuario();
                $usuario=new Usuario();
                $usuario_copiar->get($variables['id_usuario_copiar']);
                $usuario->get($variables['id_usuario']);
                $recorset= Usupermiso::select(array("id_usuario"=>$usuario_copiar->get_id()));
                foreach ($recorset as $row){
                    $usupermiso=new Usupermiso();
                    $usupermiso->set_id_usuario($usuario->get_id());
                    $usupermiso->set_id_permiso($row["id_permiso"]);
                    $usupermiso->set_id_estado(Estado::ACTIVO);
                    if(!$usupermiso->set()){
                        $error=true;
                    }
                }
            }
        }
        else{
            Logger::all("Debe seleccionar un usuario del cual copiar", get_class());
        }
        if(!$error){
            Logger::all("Permisos asignados correctamente.", get_class());
        }
        else{
            Logger::all("Error al asignar permisos", get_class());
        }
        return $this->home($variables);
    }
}
