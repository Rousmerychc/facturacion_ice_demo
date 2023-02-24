@extends('layouts.index')

@section('content')
<div class="divpagina">
    <div class = "titulobotonagregar">
        <div>    
        <h3 class='titulos'><i class="fas fa-warehouse"></i>  SUCURSALES</h3>
        </div>
            <div>
            @if (session('status'))
                <div class="alert alert-success"  id="midiv">
                    {{ session('status') }}
                </div>
            @endif
            </div>
 
        <div class="divbotonagregar">
            <a href="{{ action('SucursalController@create') }}" class="btn btn-primary">Agregar Sucursal +</a>
        </div>    
    </div>

    <div class="table-responsive">
        <table class ="table table-sm table-striped table-bordered" id="sucursal">
            <thead class="color_table">
                <tr class="tr1">
                    <td>Codigo</td>
                    <td>Descripcion</td>
                    <td>Direccion</td>
                    <td>Telefono</td>
                    <td>Municipio</td>
                    <td>Estado</td>
                    <td>Accion</td>
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
 
 var table = $('#sucursal').DataTable({
     
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
     
     ajax: "{{ url('sucursalajax') }}",
     columns: [
         
         {data: 'nro_sucursal', name: 'id'},
         {data: 'descripcion', name: 'descripcion'},
         {data: 'direccion', name: 'direccion'},
         {data: 'telefono', name: 'telefono'},
         {data: 'municipio', name: 'municipio'},
          
         {data: "estado",//, name: 'anulado'},
                       render: function (data,type,row) {
                           if (data == 0) {
                             return '<span class="badge badge-pill badge-danger"><i class="fas fa-times"></i></span>';
                           } else {
                             return '<span class="badge badge-pill badge-success"><i class="fas fa-check"></i></span>';
                           }
                         return data;
                       } 
                     },        
         
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