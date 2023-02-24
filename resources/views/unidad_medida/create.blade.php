@extends('layouts.index')

@section('content')   
<div class ="divpagina">

<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos">UNIDAD MEDIDA</h3>
    </div>
   
<form method="POST" action="{{ url('unidad/medida/selecion') }}" autocomplete="off">
@csrf
    <div  class="divformulario"> 
        <table class = "table-bordered table-hover">
            <thead>
                <tr class="color_table">
                    <th class = "padding_tabla" style="width : 60px;">ID</th>
                    <th class = "padding_tabla" style="width : 150px;">Unidad Medidad</th>
                    <th class = "padding_tabla" style="width : 70px;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unidad_medida as $u)
                    <tr>
                        <td class = "padding_tabla" ><input type="text" class ="sinborde" name = id[] value = "{{$u->id}}" style="width : 50px;"  readonly></td>
                        
                        <td class = "padding_tabla"style="width : 300px;">{{$u->descripcion}} </td>
                        
                        <td class = "padding_tabla" >
                         @if($u->estado == 1 )
                                <input  type="checkbox" name="estado[]" value = "{{$u->id}}" checked>  
                            @else    
                                <input type="checkbox" name="estado[]" value = "{{$u->id}}">
                            @endif   
                        </td>
                    </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
        
     <div class="botones_atras_guardar">          
        <div class="botonatras">
            <a class="btn btn-outline-danger" href="{{  action('UnidadMedidaController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
        </div>

        <div>
            <button type="submit" class="btn btn-primary"> Guardar </button>
        </div>
    </div>
         
</form>
</div>                              
        
@endsection
@section('js')
<script>



</script>
@endsection 