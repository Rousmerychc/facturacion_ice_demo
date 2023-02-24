@extends('layouts.index')

@section('content')   

<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos"> EDITAR CAFC - PARA FACTURAS MANUALES</h3>
    </div>
   
    <form method="POST" action="{{ url('/cafc/'.$cafc->id) }}" autocomplete="off">
    @csrf
    @method('PATCH')
        <div  class="divformulario"> 
            <div class="row">
                <div class="form-group col-md-3" > 
                    <label for="cargo">{{ __('Sucursal') }}</label>
                    <select name ="id_sucursal" class="form-control " type="number" > 
                        @foreach($sucursales as $sucu)
                            @if($sucu->id_sucursal == $cafc->id_sucursal)
                                <option value="{{$sucu->id}}" selected>{{$sucu->descripcion}}</option>
                            @else
                                <option value="{{$sucu->id}}">{{$sucu->descripcion}}</option>
                            @endif
                        @endforeach 
                    </select>
                </div>
                <div class="form-group col-md-3" > 
                    <label for="cargo">{{ __(' Punto Venta') }}</label>
                    <select name ="punto_venta" class="form-control " type="number" > 
                        @if($cafc->id_punto_venta == 0)
                        <option value="0" selected>0</option>
                        <option value="1">1</option>
                        @else
                        <option value="0" >0</option>
                        <option value="1" selected>1</option>
                        @endif
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="name">{{ __('Codigo CAFC') }}</label>
                    <input name="codigo_cafc" type="text" class="form-control"  maxlength="100"  value = "{{$cafc->codigo_cafc}}" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="name">{{ __('Nro Inicial') }}</label>
                    <input name="nro_inicial" type="number" class="form-control"  maxlength="100"  value = "{{$cafc->nro_inicial}}" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="name">{{ __('Nro Final') }}</label>
                    <input name="nro_final" type="number" class="form-control"  maxlength="100"  value = "{{$cafc->nro_final}}" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="name">{{ __('Fecha Vigencia') }}</label>
                    <input name="fecha_vigencia" type="date" class="form-control"  maxlength="100"  value = "{{$cafc->fecha_vigencia}}" required>
                </div>                                    
            </div>
        </div>
            
        <div class="botones_atras_guardar">
            <div class="botonatras">
                <a class="btn btn-outline-danger" href="{{  action('CafcController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
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