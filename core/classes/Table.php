<?php

// namespace Classes;
class Table extends View {

    const INTERRUPTOR = 'Interruptor';
    const PAGAR = 'Pagar';
    const CLASE_ACTIVADO = 'fa fa-toggle-on fa-2x green';
    const CLASE_DESACTIVADO = 'fa fa-toggle-off fa-2x red';
    const CLASE_PAGADO = 'fa fa-money fa-2x green';
    const CLASE_NO_PAGADO= 'fa fa-money fa-2x red';
    const REEMPLAZO_BINARIOS = 'Bin';
    const REEMPLAZO_TRUE = 'True';
    const REEMPLAZO_FALSE = 'False';
    const REEMPLAZO_NO_DETERMINADO = 'No determinado';
    const CLASE_PROHIBIDO="fa fa-ban fa-2x red";

    # Retorna la tabla. Recibe un recordset y un conjunto de acciones

    public function __construct($registros, $desde_registro, $hasta_registro, $acciones = null) {
        parent::__construct();
        if ($desde_registro == null AND $hasta_registro == null) {
            if (is_array($registros))
                return $this->construir_desde_array($registros, $acciones);
        }
        if (is_numeric($desde_registro) AND is_numeric($hasta_registro)) {
            if (is_object($registros) AND get_class($registros) == 'ADORecordSet_mysqli')
                return $this->construir_desde_recordset($registros, $desde_registro, $hasta_registro, $acciones);
        }
        return $this->retornar_tabla_vacia();
    }

    private function construir_desde_recordset(ADORecordSet_mysqli $registros, $desde_registro, $hasta_registro, $acciones = null) {
        $table = $this->createElement('table');
        $table->setAttribute("class", "table table-bordered table-responsive");
//        $table->setAttribute("style", "style='overflow-x: auto'");
        $this->appendChild($table);
        if ($registros === false)
            return $table;

        #Encabezado de la tabla
        if ($registros->rowCount() === 0)
            return $table;
        $tr = $this->createElement('tr');
        $thead= $this->createElement("thead");
        foreach ($registros->fields as $columna => $valor):
            if (is_numeric($columna)) {
                $meta = $registros->FetchField($columna);
                switch ($registros->MetaType($meta->type)) {
                    case 'X':
                        #Si son textos (o xml) recorto 50 caracteres
                        $th = $this->createElement('th');
                        $th->setAttribute('title', 'Se muestran los primeros ' . MAXIMO_CARACTERES_CELDA . ' caracteres.');
                        $th->appendChild($this->createTextNode(ucfirst($meta->name)));
                        $tr->appendChild($th);
                        break;
                    case 'I':
                    case 'N':
                        $th = $this->createElement('th');
                        $th->appendChild($this->createTextNode(ucfirst($meta->name)));
                        $th->setAttribute('style', "text-align:right");
                        $tr->appendChild($th);
                        break;
                    default:
                        $th = $this->createElement('th');
                        $th->setAttribute("scope", "row");
                        $th->appendChild($this->createTextNode(ucfirst($meta->name)));
                        
                        $tr->appendChild($th);
                        break;
                }
            }
        endforeach;
        if ($acciones != null) {
            foreach ($acciones as $accion):
                if(isset($accion['title']))
                    $th = $this->createElement('th',$accion['title']);
                else
                    $th = $this->createElement('th','Accion');
                $tr->appendChild($th);
                $tr->appendChild($th);
            endforeach;
        }
        $thead->appendChild($tr);
        $table->appendChild($thead);
        #Cuerpo de la tabla
        $registros->Move($desde_registro - 1);
        while ($desde_registro <= $hasta_registro):

            $registro = $registros->FetchRow();
            $tr = $this->createElement('tr');
            foreach ($registro as $columna => $valor):
                if (is_numeric($columna)) {
                    $meta = $registros->FetchField($columna);
                    switch ($registros->MetaType($meta->type)) {
                        case 'L':
                            #Si son campos Booleanos
                            if (!isset($valor))
                                $reemplazo = self::REEMPLAZO_NO_DETERMINADO;
                            elseif ($valor == 't')
                                $reemplazo = self::REEMPLAZO_TRUE;
                            else
                                $reemplazo = self::REEMPLAZO_FALSE;
                            $td = $this->createElement('td', $reemplazo);
                            $td->setAttribute('title', 'Es una campo booleano.');
                            $tr->appendChild($td);
                            break;
                        case 'B':
                            #Si son cadenas Binarias
                            $td = $this->createElement('td');
                            $td->setAttribute('title', 'Es una cadena Binaria que no se puede mostrar.');
                            $td->appendChild($this->createTextNode(self::REEMPLAZO_BINARIOS));
                            $tr->appendChild($td);
                            break;
                        case 'X':
                            # Si son textos o XML recorto X caracteres
                            $td = $this->createElement('td');
                            $td->appendChild($this->createTextNode(substr(ucfirst($valor), 0, MAXIMO_CARACTERES_CELDA)));
                            $td->setAttribute('title', $valor);
                            $tr->appendChild($td);
                            break;
                        case 'T':
                            $td = $this->createElement('td');
                            if (!$valor) {
                                $td->appendChild($this->createTextNode(' '));
                                $td->setAttribute('title', 'El campo se encuentra vacío en la base de datos.');
                            } else {
                                $td->appendChild($this->createTextNode(formato_fecha($valor)));
                                $td->setAttribute('title', $valor);
                            }
                            $tr->appendChild($td);
                            break;
                        case 'I':
                        case 'N':
                            $td = $this->createElement('td');
                            $td->appendChild($this->createTextNode($valor));
                            $td->setAttribute('style', "text-align:right");
                            $tr->appendChild($td);
                            break;
                        default:
                            $td = $this->createElement('td');
                            $td->appendChild($this->createTextNode($valor));
                            $tr->appendChild($td);
                            break;
                    }
                }
            endforeach;

            $this->procesar_acciones($tr, $registro, $acciones);
            $table->appendChild($tr);

            $desde_registro++;
        endwhile;

        return $table;
    }

    private function construir_desde_array($registros, $acciones = null) {
        $table = $this->createElement('table');
        $this->appendChild($table);
        if (count($registros) === 0)
            return $table;
        $tr = $this->createElement('tr');
        foreach ($registros[0] as $clave => $valor) {
            $th = $this->createElement('th');
            $tr->appendChild($th);
        }
        foreach ($acciones as $accion):
            $th = $this->createElement('th');
            $tr->appendChild($th);
            $tr->appendChild($th);
        endforeach;
        $table->appendChild($tr);
        foreach ($registros as $registro) {
            $tr = $this->createElement('tr');
            foreach ($registro as $clave => $valor) {
                $td = $this->createElement('td', $registro[$clave]);
                $tr->appendChild($td);
            }
            $this->procesar_acciones($tr, $registro, $acciones);
            $table->appendChild($tr);
        }
        return $table;
    }

    private function procesar_acciones($tr, $registro, $acciones) {
        if (is_array($acciones) AND count($acciones)) {
            foreach ($acciones as $accion):
                if (isset($accion['etiqueta']) AND $accion['etiqueta'] == 'checkbox') {
                    $this->procesar_selectores($tr, $registro, $accion);
                } else {
                    $td = $this->createElement('td');
                    $td->setAttribute('class', 'link');
                    $td->setAttribute('type', 'button');
                    if (isset($accion['token'])){
                        $tokens= explode(".", $accion['token']);
                        $td->setAttribute('data-nav', $tokens[0]);
                        $td->setAttribute('data-method', $tokens[1]);
                    }
                    if (isset($accion['id']))
                        $td->setAttribute('value', $registro[$accion['id']]);
                        $td->setAttribute('data','id');

                    if (isset($accion['etiqueta']) AND $accion['etiqueta'] == self::INTERRUPTOR)  {
                        $td = $this->armar_interruptor($td, $accion, $registro);
                    } 
                    elseif(isset($accion['etiqueta']) AND $accion['etiqueta'] == self::PAGAR){
                        $td = $this->armar_interruptor_pagar($td, $accion, $registro);
                    }
                    else {
                        if (isset($accion['etiqueta'])) {
                            $td->appendChild($this->createTextNode($accion['etiqueta']));
                        }
                    }
                    $tr->appendChild($td);
                }
            endforeach;
        }
    }

    public function procesar_selectores($tr, $registro, $accion) {
        $prefijo_para_names = $accion['prefijo'];
        $valor = $registro[$accion['id']];
        $td = $this->createElement('td');
        $checkbox = $this->createElement('input');
        $checkbox->setAttribute('type', 'checkbox');
        $checkbox->setAttribute('name', $prefijo_para_names . $valor);
        $td->appendChild($checkbox);
        $tr->insertBefore($td, $tr->childNodes->item(0));
    }

    public function cambiar_encabezados($encabezados) {

        $ths = $this->getElementsByTagName('th');
        $i = 0;

        foreach ($ths as $th) {
            if (isset($encabezados[$i])) {
                if ($th->childNodes->length !== 0)
                    $th->removeChild($th->childNodes->item(0));
                $th->appendChild($this->createTextNode($encabezados[$i]));
            }
            $i++;
        }
        return $this;
    }

    public function eliminar_columna($numero) {

        $filas = $this->getElementsByTagName('tr');
        foreach ($filas as $fila) {
            $fila->removeChild($fila->childNodes->item($numero - 1));
        }
    }

    private function armar_interruptor($td, $accion, $registro) {

        switch ($registro[$accion['campo']]) {
            case Estado::ACTIVO:
                $span= $this->createElement("span");
                $span->setAttribute('class', self::CLASE_ACTIVADO);
                $td->appendChild($span);
                $td->setAttribute('title', 'Activado');
                break;
            case Estado::INACTIVO:
                $td->setAttribute('title', 'Desactivado');
                $span= $this->createElement("span");
                $span->setAttribute('class', self::CLASE_DESACTIVADO);
                $td->appendChild($span);
                break;
            default:
                $td->removeAttribute('type');
                $td->removeAttribute('name');
                $td->removeAttribute('class');
                break;
        }
        return $td;
    }
    private function armar_interruptor_pagar(DOMElement $td, $accion, $registro) {

        switch ($registro[$accion['campo']]) {
            case Estado::PAGADO:
                $span= $this->createElement("span");
                $span->setAttribute('class', self::CLASE_PAGADO);
                $td->appendChild($span);
                $td->setAttribute('title', 'Pagado');
                $td->removeAttribute("data-nav");
                $td->removeAttribute("type");
                $td->removeAttribute("data-method");
                break;
            case Estado::ACTIVO:
            case Estado::PARCIAL:
            case Estado::PENDIENTE:
                $td->setAttribute('title', 'No pagado');
                $span= $this->createElement("span");
                $span->setAttribute('class', self::CLASE_NO_PAGADO);
                $td->appendChild($span);
                break;
            case Estado::INACTIVO:
            case Estado::VENCIDO:
                 $td->setAttribute('title', 'Desactivado');
                $span= $this->createElement("span");
                $span->setAttribute('class', self::CLASE_PROHIBIDO);
//                $td->removeAttribute("data-nav");
//                $td->removeAttribute("data-method");
//                $td->setAttribute("data-nav","prestamos_otorgados");
//                $td->setAttribute("data-method","home");
                $td->appendChild($span);
                break;
            default:
                $td->removeAttribute('type');
                $td->removeAttribute('name');
                $td->removeAttribute('class');
                break;
        }
        return $td;
    }

    private function retornar_tabla_vacia() {
        $table = $this->createElement('table');
        $this->appendChild($table);
        return $table;
    }

}
