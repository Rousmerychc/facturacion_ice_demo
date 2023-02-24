@extends('layouts.index')

@section('content')
<div class="divpagina">
    <div class = "titulobotonagregar">
        <div>    
        <h3 class='titulos'><i class="fas fa-user"></i>  Usuarios</h3>
        </div>
        <div>
        @if (session('status'))
            <div class="alert alert-success"  id="midiv">
                 {{ session('status') }}
            </div>
        @endif
       </div>
 
        <div class="divbotonagregar">
            <a href="{{ action('UsuarioController@create') }}" class="btn btn-primary">Agregar Usuario +</a>
        </div>    
    </div>

    <div class="table-responsive">
        <table class ="table table-sm table-striped table-bordered" id="user">
        <thead class="color_table">
            <tr class="tr1">
                <td>Nombre</td>
                <td>Apellido</td>
                <td>Correo Electrónico</td>
                <td>Cargo</td>
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
 
 var table = $('#user').DataTable({
     
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
     
     ajax: "{{ url('usuariosajax') }}",
     columns: [
        {data: 'name', name: 'name'},
         {data: 'apellido', name: 'apellido'},
         {data: 'email', name: 'email'},
         {data: 'rol',
                      render: function (data,type,row) {
                          if (data == 1) {
                            return 'Administrador';
                          } else {
                              if(data == 2){
                                return 'Administrador Jr.';
                              }else{
                                  if(data == 3){
                                    return 'Operador';        
                                  }else{
                                    return 'Reportes';
                                  }
                              }
                            
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

 
});
</script>
@endsection