@extends('layouts.index')

@section('content')

<div class="divpagina">
    <div class = "titulobotonagregar">
        <div>    
        <h3 class='titulos'><i class="fas fa-angle-double-right"></i> FACTURAS</h3>
        </div>

          <center>
            @if (session('status'))
                    <!-- Button trigger modal -->
                    <button style= "display:none;" type="button" class="btn btn-primary" id ="modal_respuesta_servidor" data-bs-toggle="modal" data-bs-target="#exampleModal2">
                      Launch demo modal
                    </button>
                
            @endif
           
          </center>
          <div class="divbotonagregar">
            <a class="btn btn-primary " href="{{ action('FacturacionController@create') }}"> NUEVA FACTURA</a>
          </div>
         
          
    </div>

    <div class="table-responsive">
        <table class ="table table-sm table-striped table-bordered" id="factura">
            <thead class="color_table">
                <tr class="tr1">
                    <td>PDF - QR</td>
                    <td>Nro Fac.  Linea</td>
                 
                    <td>Fecha</td>
                    <td>Razon Social</td>
                    <td>Nit</td>
                    <td>Importe</td> 
                    <td>Estado</td>
                    <td>Acción</td>                   
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal ANULACION -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">ANULACION</h5>
        
      </div>
      <div class="modal-body">
          <form  method="POST" action="{{ url('anular_fac') }}" autocomplete="off">
          @csrf
            <div class="form-group col-md-10">
                <label for="name">{{ __('Selecione Motivo Anulacion:') }}</label>
                <select name="codigo_motivo_anulacion" type = "number" class="form-control">
                    @foreach($motivo_anulacion as $ma)
                        <option value="{{$ma->codigo_clasificador}}">{{$ma->descripcion}}</option>
                    @endforeach
                </select>
                <input type="hidden" name = "id_factura" id = "id_factura">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-danger">Anular</button>
      </div>
    </div>
    </form>
  </div>
</div>


<!-- Modal QR -->
<div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">CODIGO QR</h5>
        
      </div>
      <div class="modal-body">
        <center>
            <div id ="#qr">

            </div>
        </center>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CERRAR</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal MENSAJE SISTEMA-->
<div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Mensaje del Sistema</h5>
       
      </div>
      <div class="modal-body">
        {{ session('status') }}
      </div>
      <div class="modal-footer">
        <button type="button " class="btn btn-secondary btn-sm" data-bs-dismiss="modal">cerrar</button>
      </div>
    </div>
  </div>
</div>



<!-- Modal ENVENTO SIGNIFICATIVO FUERA DE LINEA-->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Evento Significativo - Factura Fuera de Linea</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <form method="POST" action="{{ url('emisionFueraLinea') }}" >
        @csrf
          <div class="modal-body">
            <select name="evento_significado"  class="form-control">
          
              @foreach($evento_significativo as $evs)
                <option value="{{$evs->codigo_clasificador}}">{{$evs->descripcion}}</option>
              @endforeach
            </select>
          </div>
        
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Enviar paquete</button>
          </div>
        </form>
    </div>
  </div>
</div>



<!-- Modal -->
<div class="modal fade" id="staticBackdrop2" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Evento Significativo - Facturas Manueales</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <form method="POST" action="{{ url('emisionManuales') }}" >
        @csrf
          <div class="modal-body">
            <select name="evento_significado"  class="form-control">
          
              @foreach($evento_significativo2 as $evs)
                <option value="{{$evs->codigo_clasificador}}">{{$evs->descripcion}}</option>
              @endforeach
            </select>
          </div>
        
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Enviar paquete</button>
          </div>
        </form>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
  


function obtenerId(id){
    console.log(id);
    $("#id_factura").val(id);
}

function qr(id){
    var parametros={
       "dato": id,
    };
    $("#qr").empty();
    $.ajaxSetup({
         headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          }
      });


    $.ajax({
         method:'GET',
         url:"{{ url('codigoQR_modal')}}",
         data:parametros
       
      }).done(function(res){
        var arreglo = JSON.parse(res);
        console.log(res);
          console.log(arreglo.prueba);
          var a = document.createElement("img");
            a.src = 'data:image/png;base64,'+arreglo.prueba;
            console.log(a);
            document.getElementById("#qr").innerHTML='<img src="data:image/png;base64,'+arreglo.prueba+'" />';
        //document.body.appendChild(a);
          //document.querySelector("#qr").appendChild(a)
        //$("#qr").append('<img src="data:image/png;base64,'+arreglo.prueba+'">');
    });
}


$(document).ready(function() {
  
    $('#modal_respuesta_servidor').trigger('click');
    
  

});


$(function () {
 
 var table = $('#factura').DataTable({
     
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
     
     ajax: "{{ url('ajaxfactura') }}",
     columns: [
        {
             data: 'pdf', 
             name: 'pdf', 
             orderable: true, 
             searchable: true
         },
         {data: 'id_factura', name: 'id_factura'},
       
         {data: 'fecha', name: 'fecha'},
         {data: 'razon_social', name: 'razon_social'},
         {data: 'nro_documento', name: 'nro_documento'},
         {data: 'monto_total_moneda', name: 'monto_total_moneda'},
         {data: 'estado', //, name: 'anulado'},
                       render: function (data,type,row) {
                           if (data == 1) {
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