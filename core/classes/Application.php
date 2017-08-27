<?php

class Application {

    protected static $wrapper;
    protected static $cookie;
    protected static $instancia;
    protected static $loggin;
    public static $usuario;

    public function __construct($id_usuario_cookie) {
        $objeto = self::get_instance($id_usuario_cookie);

        return $objeto;
    }

    public static function get_instance($id_usuario_cookie) {

        if (!isset(self::$wrapper) or self::$wrapper === null) {
            self::$wrapper = new View("1.0", "utf-8");
            self::$wrapper->cargar_vista('views/Wrapper.html');
            if ($id_usuario_cookie == null OR ! $id_usuario = Gestor_de_cookies::get($id_usuario_cookie, 'login_usuario'))
                return false;
            self::$cookie = $id_usuario_cookie;
            $resultado = self::login_de_usuario($id_usuario);
        }
        return $resultado;
    }

    final public function navegar($page, $method, $variables, $wrapper, $lista = false) {

        $vista = new View();
        if (!empty($variables)) {
            $variables = $this->tratamiento_de_variables($variables);
        }
        if (isset($variables["instancia"])) {
            self::$instancia = $variables["instancia"][0];
            unset($variables["instancia"]);
        }
        $variables = $this->cargar_variables_instancia($variables, $page);
        $this->guardar_variables($variables, $page);
        if (!self::$usuario and $method != "login_post") {
            $controller = new Controller_main();
            return $this->view_login($controller->login($variables));
        } elseif ((!self::$usuario and $method == "login_post")) {
            $controller = new Controller_main();
            if (!$controller->login_post($variables)) {
                Logger::usuario("Autenticacion fallida", "Application");
                print_r("autenticacion fallida");
                return $this->view_login($controller->login($variables), $page, $lista);
            } else
                $method = "home";
        }
        elseif (self::$usuario and $method == "user_logout") {
            $controller = new Controller_main();
            if (!$controller->user_logout($variables)) {
                Logger::usuario("Autenticacion fallida", "Application");
                print_r("Imposible salir");
                $method = "home";
            } else {
                return $this->view_login($controller->login($variables), $page, $lista);
            }
        }
        if ($method == "index")
            $method = "home";
        $this->eliminar_instancia_anteriores("main");
        $clase = ucfirst(strtolower("Controller_" . $page));
        $clase_existe = class_exists($clase);
        $metodo_existe = method_exists($clase, 'Despachar');
        $herencia_correcta = false;
        if ($clase_existe) {
            $reflector = new ReflectionClass($clase);
            $herencia_correcta = $reflector->isSubclassOf('Controller');
        }

        if ($clase_existe and $metodo_existe and $herencia_correcta) {
            $util = new $clase();
            $vista = $util->Despachar($method, $variables);
        }
        if ($wrapper) {
            return $this->render_template($vista, $page, $lista);
        }
        return $this->render($vista, $page, $lista);
    }

    final protected function render_template(View $view) {
        $forms = $view->getElementsByTagName('form');
        $form = $forms->item(0);
        if (Logger::ultimos_logs(1) !== false) {
            $mensaje = $view->createElement('a', Logger::ultimos_logs(1));
            $mensaje->setAttribute('title', Logger::ultimos_logs(10));
            $mensaje->setAttribute('class', 'mensaje_log');
            $form->appendChild($mensaje);
        }
        $span_usuario = self::$wrapper->getElementById("usuario");
        $span_usuario->appendChild(self::$wrapper->createTextNode(Application::$usuario->get_usuario()));
        $main = self::$wrapper->getElementById('main');
        $hidden = $view->createElement("input");
        $hidden->setAttribute("type", "hidden");
        $hidden->setAttribute("name", "instancia");
        $hidden->setAttribute("id", "instancia");
        // $lista->setAttribute("style", "display:none");
        if (Logger::ultimos_logs(1) !== false) {
            $mensaje = $view->createElement('a', ucfirst(Logger::ultimos_logs(1)));
            $mensaje->setAttribute('title', ucfirst(Logger::ultimos_logs(10)));
            $mensaje->setAttribute('class', 'mensaje_log');
            $form->appendChild($mensaje);
        }
        $hidden->setAttribute("value", self::$instancia);
        $form->appendChild($hidden);
        if ($main) {
            $main->appendChild(self::$wrapper->importNode($form, true));
        }
        return self::$wrapper->saveHTML();
    }

    final protected function render($view, $page, $lista = true) {

//            print_r("hola");
        if (is_object($view) and get_class($view) == 'View') {
            $vista = $view->createElement("input");
            $vista->setAttribute("id", "listado");
            if (!$lista) {
                $vista->setAttribute("name", "nada");
            } elseif ($page == "turnos_medico") {
                $vista->setAttribute("name", "medico");
            } else {
                $vista->setAttribute("name", "admin");
            }
            $vista->setAttribute("type", "hidden");
            $view->appendChild($vista);
            $forms = $view->getElementsByTagName('form');
            if ($forms->length == 1) {
                $form = $forms->item(0);
                $msj = Logger::ultimos_logs(1);
                if ($msj !== false) {
                    $mensaje = $view->createElement('a', $msj);
                    $mensaje->setAttribute('title', Logger::ultimos_logs(10));
                    $mensaje->setAttribute('class', 'mensaje_log');
                    $form->appendChild($mensaje);
                }
                $hidden = $view->createElement("input");
                $hidden->setAttribute("type", "hidden");
                $hidden->setAttribute("name", "instancia");
                $hidden->setAttribute("id", "instancia");
                $hidden->setAttribute("value", self::$instancia);
                $form->appendChild($hidden);
            }

            return $view->saveHTML();
        }
    }

    final protected function tratamiento_de_variables($variables) {

        $temp = array();
        if (isset($variables['data'])) {
            $explotado = explode('&', $variables['data']);


            foreach ($explotado as $unaVariable) :
                $array = explode('=', str_replace("undefined", "", $unaVariable));
                unset($clave);
                unset($valor);
                if (isset($array[0])) {
                    $clave = $array[0];
                }
                if (isset($array[1]) and $array[1] !== '') {
                    $aux = str_replace('+', ' ', $array[1]);
                    $valor = trim(rawurldecode($aux));
                    unset($aux);
                } else {
                    # YA NO SE DESCARTAN LOS CAMPOS VACIOS
                    # (Se descartan al levantar variables en el gestor de instancias)
                    // $valor='';
                    unset($unaVariable);
                }
                if (isset($temp[$clave]) and isset($valor)) {
                    # Para soportar multiples $_POST con el mismo Name
                    if (!is_array($temp[$clave])) {
                        $aux = $temp[$clave];
                        unset($temp[$clave]);
                        $temp[$clave] = array();
                        $temp[$clave][] = trim($aux);
                    }

                    $temp[$clave][] = trim($valor);
                } elseif (isset($clave) and isset($valor)) {
                    $temp[$clave] = trim($valor);
                }
                unset($array);
            endforeach;
        }

        if (isset($variables['id']) and $variables['id'] !== '') {
            $temp['id'] = $variables['id'];
        }
        if (isset($variables['pagina']) and $variables['pagina'] !== '') {
            if (is_numeric($variables['pagina']) and $variables['pagina'] > 0) {
                $temp['pagina'] = (int) $variables['pagina'];
            }
        }
        return $temp;
    }

    private function cargar_variables_instancia($variables, $page) {
        $recordSet = Instancia::select(array("cookie" => self::$cookie, "id_instancia" => self::$instancia, "controller" => $page));
        unset($variables['instancia']);
        if ($recordSet and $recordSet->rowCount() > 0) {
            $row = $recordSet->fetchRow();
            $json_variables = $row["variables"];
            $temp = json_decode($json_variables, true);
            if ($temp) {
                foreach ($temp as $key => $var) {
                    if (!isset($variables[$key])) {
                        $variables[$key] = $var;
                    }
                }
            }
        }
        return $variables;
    }

    private function guardar_variables($variables, $page) {
        if (!$page or $page == null) {
            return false;
        }
        $instancia = new Instancia();
        $json_variables = json_encode($variables);
        $instancia->get(self::$instancia);
        if (self::$instancia != false and self::$instancia != null) {
            $instancia->set_variables($json_variables);
            $instancia->set_controller($page);
            if ($instancia->set()) {
                Logger::developper("instancia actualizada");
                return true;
            }
        } else {
            if ($this->generar_nueva_instancia($page, $json_variables)) {
                Logger::developper("Instancia generada");
                return true;
            }
        }
        Logger::developper("variables NO guardadas");
        return false;
    }

    public function eliminar_instancia_anteriores($page) {
        if (self::$instancia) {
            if (Instancia::eliminar_instancia(self::$cookie, self::$instancia, $page)) {
                return true;
            }
        } else {
            return true;
        }
        return false;
    }

    private function generar_nueva_instancia($page, $jsoninstancia) {
        $instancia = new Instancia();
        $instancia->set_controller($page);
        $instancia->set_cookie(self::$cookie);
        $instancia->set_variables($jsoninstancia);
        if ($instancia->set()) {
            self::$instancia = $instancia->get_id_instancia();
            return true;
        }
        return false;
    }

    protected final static function login_de_usuario($id_usuario) {
        #Login desde el sitio
        self::$usuario = new Usuario();
        if (self::$usuario->get($id_usuario)) {
            self::$loggin = "login_usuario";
            return true;
        } else {
            self::logout();
            return false;
        }
    }

    public final static function logout() {
        self::$loggin = false;
//		Gestor_de_cookies::destruir();
        self::$usuario = false;
        self::$instancia = false;
        return true;
    }

    public final function view_login(View $view) {
        if (is_object($view) and get_class($view) == 'View') {
            $forms = $view->getElementsByTagName('form');
            if ($forms->length == 1) {
                $form = $forms->item(0);
                $msj = Logger::ultimos_logs(1);
                if ($msj !== false) {
                    $mensaje = $view->createElement('a', $msj);
                    $mensaje->setAttribute('title', Logger::ultimos_logs(10));
                    $mensaje->setAttribute('class', 'mensaje_log');
                    $form->appendChild($mensaje);
                }
            }
            return $view->saveHTML();
        }
    }

}
