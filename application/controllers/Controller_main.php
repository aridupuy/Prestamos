<?php
class Controller_main extends Controller{


    public function home($varibles) {
        $this->view->cargar_vista("views/dashboard.html");
        $recordset= prestamo::select_ingresos();
        $labels="";
        $data="";
        foreach ($recordset as $row){
            $fecha= DateTime::createFromFormat('Y-m-d H:i:s', $row['fecha_move']);
            $labels.=$fecha->format("d/m/Y").",";
            $data.=$row["total"].",";
        }
//        $data="0, 10, 5, 2, 20, 30, 45";
//        $labels="Enero,Febrero, Marzo, Abrli, Mayo, Junio, Julio";
        $datos=$this->view->getElementById("data-ingresos");
        $datos->setAttribute("value",$data);
        $label=$this->view->getElementById("labels-ingresos");
        $label->setAttribute("value",$labels);
        $label=$this->view->getElementById("type_chart-ingresos");
        $label->setAttribute("value","line");
        
        
        
        
        $recordset= prestamo::select_egresos();
        $labels="";
        $data="";
        foreach ($recordset as $row){
            $fecha= DateTime::createFromFormat('Y-m-d H:i:s', $row['fecha_contrato']);
            $labels.=$fecha->format("d/m/Y").",";
            $data.=$row["total"].",";
        }
        $datos=$this->view->getElementById("data-egresos");
        $datos->setAttribute("value",$data);
        $label=$this->view->getElementById("labels-egresos");
        $label->setAttribute("value",$labels);
        $label=$this->view->getElementById("type_chart-egresos");
        $label->setAttribute("value","line");
        
        $datos=$this->view->getElementById("data-nuevo");
        $datos->setAttribute("value","80,100");
        $label=$this->view->getElementById("labels-nuevo");
        $label->setAttribute("value","80,100");
        $label=$this->view->getElementById("type_chart_nuevo");
        $label->setAttribute("value","doughnut");
        // $this->view->cargar_vista("views/index.html");
        // $this->view->cargar_vista("views/index.html");
        
        return $this->view;
    }
    public function login($variables){
        $this->view->cargar_vista("views/login.html");
        // $this->view->cargar_vista("views/index.html");
        // $this->view->cargar_vista("views/index.html");
        
        return $this->view;
    }
    public function login_post($variables){
        $usuario=new Usuario();
//        var_dump($variables);
        if(isset($variables['usuario']) and isset($variables['pass'])){
            self::$usuario=$usuario->login($variables['usuario'],$variables['pass']);
            Gestor_de_cookies::set(self::$usuario->get_id_usuario(), 1);
        }
        return self::$usuario;
    }
    
    public function user_logout($variables){
        Logger::developper("Usuario ".Application::$usuario." Intenta cerrar sesion.");
        if(Gestor_de_cookies::destruir()){
            return true;
        }
        return false;
    }
}

