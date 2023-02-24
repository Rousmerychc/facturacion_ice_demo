@extends('layouts.index')

@section('content')
<div class="divpagina">
    <div class = "titulobotonagregar">
        <div>    
        <h3 class='titulos'><i class="fas fa-angle-double-right"></i> GRUPOS</h3>
        </div>
            <div>
            @if (session('status'))
                <div class="alert alert-success"  id="midiv">
                    {{ session('status') }}
                </div>
            @endif
            </div>
 
        <div class="divbotonagregar">
            <a href="{{ action('Grupo2Controller@create') }}" class="btn btn-primary">Agregar Grupo +</a>
        </div>    
    </div>

    <div class="table-responsive">
        <table class ="table table-sm table-striped table-bordered" id="grupo">
            <thead class="table-success">
                <tr class="tr1">
                    <td>Codigo</td>
                    <td>Descripcion</td>                 
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
     
     ajax: "{{ url('grupo2ajax') }}",
     columns: [
         
         {data: 'id', name: 'id'},
         {data: 'descripcion_grupo', name: 'descripcion_grupo'},       
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