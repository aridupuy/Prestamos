<?php

class View extends DOMDocument {

    protected $version;
    protected $encode;

    public function __construct($version = null, $encoding = null) {
        parent::__construct($version, $encoding);
        $this->version = $version;
        $this->encode = $encoding;
        return $this;
    }

    public function cargar_vista($direccion) {
        libxml_use_internal_errors(true);
        $this->loadHTMLFile(PATH_APPLICATION . $direccion);
        return $this;
    }

    public function cargar_variables($variables) {
        foreach ($variables as $key => $value) {
            $objeto = $this->getElementById($key);
            if ($objeto) {
                if ($objeto->nodeName == "select") {
                    $nodos = $objeto->childNodes;
                    foreach ($nodos as $nodo) {
//                        $d=new DOMNode();
                        foreach ($nodo->attributes as $attr) {
                            if($attr->nodeName=="value"){
                                if($attr->nodeValue==$value)
                                    $nodo->setAttribute("selected", "selected");
                            }
                        }
                    }
                } else
                    $objeto->setAttribute("value", $value);
            }
        }
    }

}
