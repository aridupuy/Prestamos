var mensaje_error="<div class='teatro'><i class='fa fa-5x fa-flash'></i>Ha ocurrido un error. recargue la página.</div>";
var cargando="<div class='teatro'><i class='fa fa-5x fa-circle-o-notch fa-spin'></i></div>";

var url_servidor='index.php';
var destino_respuesta_servidor='#main';


$(function(){
    
    load(0);
});
$(document).ready(function() {
    $('.tabla').stacktable();
});

function load(nav){
    var listar=$("#listado").attr("name");
//    if(listar==null || listar=="nada"){
//        $("#sistema_admin").hide();
//        $("#sistema_medicos").hide();
//    }
//    if (listar =="medico"){
//        $("#sistema_admin").hide();
//        $("#sistema_medicos").show();
//
//    }
//    else if(listar=="admin"){
//        $("#sistema_admin").show();
//        $("#sistema_medicos").hide();
//    }
    $("#generador").click(function(){
        var pass=Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 30);
        alert(pass);
        $("#password").val(pass);
    });
    $(".mensaje_log").click(function (){
        $(this).slideToggle( "slow" )
    });
    $("#cerrar").click(function (e) {
        $(this).parent().hide();
    })
    $(".debug-container").append($(".debug"));
    $("[type='submit']").not("button").off('click');
    $("#miFormulario [type='submit']").not("button").on('click',function(event){
        alert("miFormulario");
        if($('#miFormulario')[0].checkValidity() ){
            $(this).attr('type','button');
            if($("[type='file']").length=='1') file_link($(this).attr('data-nav'),$("#miFormulario [type='file']").eq(0).attr('data-nav'),$("#miFormulario [type='file']").eq(0).attr('data-method'));
            else link($(this).attr('data-nav'),$(this).attr('data-method'),'');    
        }
    });
    $("#elFormModal [type='submit']").not("button").on('click',function(event){
        alert("elFormModal");
        if($('#elFormModal')[0].checkValidity() ){
            $(this).attr('type','button');
            if($("[type='file']").length=='1') file_link($(this).attr('data-nav'),$("#elFormModal [type='file']").eq(0).attr('data-nav'),$("#elFormModal [type='file']").eq(0).attr('data-method'));
            else link_modal($(this).attr('data-nav'),$(this).attr('data-method'));    
            
        }
    });
    
    $("[type='button']").not("button").off('click');
    $("[type='button']").not("button").on('click',function(event){
        link($(this).attr('data-nav'),$(this).attr('data-method'),'',this);
    });
    $("input[type='button']").off('click');
    $("input[type='button']").on('click',function(event){
        link($(this).attr('data-nav'),$(this).attr('data-method'),'',this);
    });
    $(".selectpicker").off('change');
    $(".selectpicker").on('change',function(event){
        link($(this).attr('data-nav'),$(this).attr('data-method'),'',this);
    });
    $("#paciente").off("change");
    $("#paciente").on("change",function (){
      if($(this).val()==0){
          $("#carga").html("");
      }
      else{
        link_reemplazo("turnos","mostrar_datos_turno","carga");
        $('#paciente').val($(this).val());
        $('#paciente').selectpicker('refresh');
//        load(0);
      }
      $('#paciente').selectpicker('refresh');
    });
    $("a[data-nav]").on('click',function(evt){
        link($(this).attr('data-nav'),$(this).attr('data-method'),'');
    });
    escuchar_checkboxes();
    escuchar_mensaje_log();
    escuchar_paginador();  
     $(".mensaje_log").on('click', function (event) {
        $(".mensaje_log").animate({'margin-left': '+=400'}, 250, 'linear', function (event) {
            $(this).hide();
        });

    });
    $(".selectpicker").selectpicker();
    
}
 
function link(nav,id,pagina_a_mostrar,$this=false){
    var checkboxes_string=obtener_checkboxes_sin_tildar();
    if($this!=false)
        var div_string=div_serialize($this);
    var hiddens=hidden_serialize();
    var parametros = {
            "nav" : nav,
            "pagina":pagina_a_mostrar,
            "method":id,
            "wrapper":false,
            "data": $("#miFormulario").serialize()+hiddens+checkboxes_string+div_string
    };

    $.ajax({
            data:  parametros,
            url: url_servidor,
            type:  'post',
            beforeSend: function () {
                antes_de_navegar();
            },
            success:  function (response) {
                despues_de_navegar(response);
                load(nav);
            },

            error: function(){
                $("#miFormulario").html(mensaje_error);
                load(nav);
            }
    });
}
function link_modal(nav,id,pagina_a_mostrar,$this=false){
    var accion_modal;
    var checkboxes_string=obtener_checkboxes_sin_tildar_modal();
    if($this!=false)
        var div_string=div_serialize($this);
//    var hiddens=hidden_serialize();
    var hiddens=hidden_serialize_modal();
    var parametros = {
            "nav" : nav,
            "pagina":pagina_a_mostrar,
            "method":id,
            "wrapper":false,
            "data": $("#elFormModal").serialize()+hiddens+checkboxes_string+div_string
    };
    $.ajax({
            data:  parametros,
            url: url_servidor,
            type:  'post',
            beforeSend: function () {
                $("#elFormModal").html(cargando);
            },
            success:  function (response) {
               $(".close").click();
                link_modal.accion_modal();
            },

            error: function(){
                $("#elFormModal").html(mensaje_error);
                load(nav);
            }
    });
}
link_modal.accion_modal=function (){
    
}
function link_reemplazo(nav,id,id_elemento_a_reemplazar,fx){
    var checkboxes_string=obtener_checkboxes_sin_tildar();
    var div_string=div_serialize(this);
    var hiddens=hidden_serialize();
    var parametros = {
            "nav" : nav,
            "method":id,
            "data": $("#miFormulario").serialize()+hiddens+checkboxes_string+div_string
    };

    $.ajax({
            data:  parametros,
            url:   url_servidor,
            type:  'post',
            beforeSend: function () {
                // Hacer esto si el pedido supera determinado tiempo!
                $("#"+id_elemento_a_reemplazar).html(cargando);
            },
            success:  function (response) {
                $("#"+id_elemento_a_reemplazar).html(response);
                load(nav);
                if(fx){
                    fx();
                }
            },

            error: function(){
                $("#miFormulario").html(mensaje_error);
                load(nav);
            }
    });
}
function file_link(nav,nombre_archivo,id_archivo){
    var inputFileImage = document.getElementById(id_archivo);
    var file = inputFileImage.files[0];
    var objeto = new FormData();
    var checkboxes_string=obtener_checkboxes_sin_tildar();
    var div_string=div_serialize(this);
    console.log(div_string);
    objeto.append('archivo',file);
    objeto.append('nav',nav);
    objeto.append('data',$("#miFormulario").serialize()+checkboxes_string)+div_string

    $.ajax({
        url:url_servidor,
        type:'POST',
        contentType:false,
        data:objeto,
        processData:false,
        cache:false,
        beforeSend: function () {
            antes_de_navegar();
        },
        success:  function (response) {
            despues_de_navegar(response);
            load(nav);

        },

        error: function(){
            $("#miFormulario").html(mensaje_error);
            load(nav);
        }});
}
function div_serialize($this){
    var string='';
    $($this).each(function(){
        if($(this).attr('value')!='undefined'){
            //Esto puede traer problemas de memoria?
            string=string+'&'+$(this).attr('data')+'='+$(this).attr('value');
        }
    });
    return string;
}
function hidden_serialize(){
    var string='';
    $("[type=hidden]").each(function(){
        if($(this).attr('value')!='undefined'){
            //Esto puede traer problemas de memoria?
            string=string+'&'+$(this).attr('name')+'='+$(this).attr('value');
        }
    });
    return string;
}
function hidden_serialize_modal(){
    var string='';
    $("elFormModal [type=hidden]").each(function(){
        if($(this).attr('value')!='undefined'){
            //Esto puede traer problemas de memoria?
            string=string+'&'+$(this).attr('name')+'='+$(this).attr('value');
        }
    });
    return string;
}
function escuchar_paginador(){
    var pagina_actual= parseInt($('.paginador').attr('data-pagina-actual'));
    var cantidad_paginas= parseInt($('.paginador').attr('data-cantidad-paginas'));
    var controller=$('.paginador').attr('data-nav');
    var metodo=$('.paginador').attr('data-method');
    $('.paginador .fa-fast-forward').on('click',function(event){
        if(pagina_actual<cantidad_paginas) link(controller,metodo,'',cantidad_paginas);
    });
    $('.paginador .fa-forward').on('click',function(event){
        var pagina_a_mostrar= parseInt($('.paginador').attr('data-pagina-actual'))+ 1;
        if(cantidad_paginas>=pagina_a_mostrar) link(controller,metodo,'',pagina_a_mostrar);
    });
    $('.paginador .fa-backward').on('click',function(event){
        var pagina_a_mostrar= parseInt($('.paginador').attr('data-pagina-actual'))- 1;
        if(pagina_a_mostrar>=1) link(controller,metodo,'',pagina_a_mostrar);
    });
    $('.paginador .fa-fast-backward').on('click',function(event){
        if(pagina_actual!=1) link(controller,metodo,'',1);
    });
}
function escuchar_checkboxes(){
    $("[type='checkbox']").on('click',function(event){
        if($(this).attr('checked')=='checked' || $(this).attr('checked')==true)
        {
            $(this).attr('value',0);
            $(this).attr('checked',false);
        }
        else
        {
            $(this).attr('value',1);
            $(this).attr('checked',true);
        }
    });
}
function escuchar_mensaje_log(){
    $(".mensaje_log").on('click',function(event){
        $(".mensaje_log").animate({'margin-left':'+=400'},250,'linear',function(event){
            $(this).hide();
        } );
        
    });
}
function antes_de_navegar(){
    $('.paginador .fa-fast-forward').off('click');
    $('.paginador .fa-forward').off('click');
    $('.paginador .fa-backward').off('click');
    $('.paginador .fa-fast-backward').off('click');
    $('a[data-nav]').off('click');
    $("[type='button']").not("button").off('click');
    $("[type='submit']").not("button").off('click');
    $("#miFormulario").html(cargando);
    $(".debug").remove();
    // Hacer esto si el pedido supera determinado tiempo!
}
function despues_de_navegar(response){
    $(destino_respuesta_servidor).html(response);
}
function obtener_checkboxes_sin_tildar(){
    // Usamos esta funcion para enviar los checkboxes sin tildar
    // solo aquellos que tengan value=0
    var string='';
    $("#miFormulario [type='checkbox']").each(function(){
        if($(this).val()!=1){
            //Esto puede traer problemas de memoria?
            string=string+'&'+$(this).attr('name')+'=0';
        }
    });
    return string;
}
function obtener_checkboxes_sin_tildar_modal(){
    // Usamos esta funcion para enviar los checkboxes sin tildar
    // solo aquellos que tengan value=0
    var string='';
    $("#elFormModal [type='checkbox']").each(function(){
        if($(this).val()!=1){
            //Esto puede traer problemas de memoria?
            string=string+'&'+$(this).attr('name')+'=0';
        }
    });
    return string;
}
function generar_chart(ctx,tipo,label,color_borde,labels,data){
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: tipo,

    // The data for our dataset
    data: {
        labels: labels,
        datasets: [{
            label: label,
//            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: color_borde,
            data: data,
            borderWidth: 1
        }]
    },

    // Configuration options go here
    options: {
    rotation: 1 * Math.PI,
    circumference: 1 * Math.PI
    }
});
}
