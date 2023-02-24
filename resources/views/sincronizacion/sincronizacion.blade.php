@extends('layouts.index')

@section('content')   
<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos"> <i class="fas fa-angle-double-right"></i> SINCRONIZACION DE PARAMETRICAS...</h3>
   
    
    @if (session('status'))
        <div class="alert alert-success"  id="midiv">
            {{ session('status') }}
        </div>
    @endif
    </div>
    
    <div  class="divformulario"> 
        <div class="row">
            <div class="form-group col-md-3">
                <label for="name">{{ __('Seleccionar Parametrica:') }}</label>
                <select name="tablap" id="tablap" type = "number" class="form-control " required>   
                   <option value="1">Actividades</option>
                   <option value="2">Documento Sector</option>
                   <option value="3">Leyendas Factura</option>
                   <option value="4">Mensajes Servicios</option>
                   <option value="5">Productos Servicios</option>
                   <option value="6">Eventos Significativos</option>
                   <option value="7">Motivo Anulacion</option>
                   <option value="8">Pais Origen</option>
                   <option value="9">Documento Tipo Identidad</option>
                   <option value="10">Tipo Documento Sector</option>
                   <option value="11">Tipo Emision</option>
                   <option value="12">Tipo Habitacion</option>
                   <option value="13">Metodo Pago</option>
                   <option value="14">Tipo Moneda</option>
                   <option value="15">Punto Venta</option>
                   <option value="16">Tipos Factura</option>
                   <option value="17">Unidad Medida</option>
                </select>
            </div>

            <div class="col-md-5">
                <a href="{{ action('SincronizacionController@actualizar') }}" class="btn btn-primary">Actualizar Parametricas</a>
            </div>  
        </div>
    </div>

    <div  class="divformulario"> 
        <table class = "table1 table table-hover"  id = "detalle">
            <thead>
                <tr>
                    <th class = "tabla_sincronizacion1">Codigo Clasificar / codigo actividad</th>
                    <th class = "tabla_sincronizacion2">Descripcion/ codigo Producto</th>
                    <th class = "tabla_sincronizacion3">Descripcion</th>
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
        $("#midiv").fadeOut(3000);
    },5000);
});
    $(document).ready(function() {

        $("#tablap").change(function(){
            $('#detalle > tbody').empty();
        var id_tablap = document.getElementById("tablap").value;

        var parametros={
        "dato": id_tablap,
            };
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                method:'GET',
                url:"{{ url('mostrartabla') }}",
                data:parametros

            }).done(function(res){

                var arreglo = JSON.parse(res);
                console.log(arreglo.consulta[0].id)

                    for (let index = 0; index < arreglo.consulta.length; index++) {
                        console.log('hol');
                       
                        var linea0 = '<tr>'
                        var linea1 ='<td>'+arreglo.consulta[index].codigo+'</td>';
                        var linea2 ='<td>'+arreglo.consulta[index].descripcion+'</td>';
                        var linea3 ='<td>'+arreglo.consulta[index].tipotres+'</td></tr>';
                        
                        var linea = linea0+linea1+linea2+linea3
                        $('#detalle > tbody:first').append(linea);       
                    }
                });
        });

    });
</script>
@endsection