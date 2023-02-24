@extends('layouts.index')

@section('content')
<div class="divpagina">
    <div class = "titulobotonagregar">
        <div>    
        <h3 class='titulos'><i class="fas fa-angle-double-right"></i> CAFC PARA EMISION DE FACTURAS MANUALES</h3>
        </div>
            <div>
            @if (session('status'))
                <div class="alert alert-success"  id="midiv">
                    {{ session('status') }}
                </div>
            @endif
            </div>
 
        <div class="divbotonagregar">
            <a href="{{ action('CafcController@create') }}" class="btn btn-primary">Agregar CAFC +</a>
        </div>    
    </div>

    <div class="table-responsive">
        <table class ="table table-sm table-striped table-bordered" id="grupo">
            <thead class="table-success">
                <tr class="tr1">
                    <td>Codigo</td>
                    <td>Descripcion</td>
                    <td>Nro Inicial</td>
                    <td>Nro Final</td>
                    <td>Fecha Vigencia</td>                    
                    <td>Actions</td>                   
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    setTimeout(function() {
		// Declaramos la capa mediante una clase para ocultarlo
        $("#midiv").fadeOut(1500);
    },5000);
});

$(function () {
 
 var table = $('#grupo').DataTable({
     
     "language": {
         "lengthMenu": "Filas por pagina  _MENU_",
         "zeroRecords": "Ningun elempento coincide con la busqueda",
         "info":           "_END_ de _TOTAL_ ",
         "infoEmpty": "No hay registros disponibles",     
         "search":         "Buscar:",
         
     "searchPlaceholder": "Busqueda items",
    
         "paginate": {
             "next":       "<i class='fas fa-arrow-alt-circle-right'></i>",
             "previous":   "<i class='fas fa-arrow-alt-circle-left'></i>",
         },
     },
     "pagingType": "simple",
     
     
     responsive: true,
     dom: '<"top"f><t><"ajaxdatables" <"tamañomenuajax" l> i p>',
     //dom: '<"top" l f>t<"bottom" i p>',
     
     processing: true,
     serverSide: true,
     ordering: false,
     
     ajax: "{{ url('ajaxcafc') }}",
     columns: [
         
         {data: 'id', name: 'id'},
         {data: 'codigo_cafc', name: 'codigo_cafc'},
         {data: 'nro_inicial', name: 'nro_inicial'},
         {data: 'nro_final', name: 'nro_final'},
         {data: 'fecha_vigencia', name: 'fecha_vigencia'},       
         {
             data: 'btn', 
             name: 'btn', 
             orderable: true, 
             searchable: true
         },
     ]
 });
 $('#myInputTextField').keyup(function(){
   table.search($(this).val()).draw() ;
});
 
});
</script>
@endsection