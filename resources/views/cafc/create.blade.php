@extends('layouts.index')

@section('content')   

<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos"> AGREGAR CAFC - PARA FACTURAS MANUALES</h3>
    </div>
   
    <form method="POST" action="{{ url('cafc') }}" autocomplete="off">
    @csrf
        <div  class="divformulario"> 
            <div class="row">
                <div class="form-group col-md-3" > 
                    <label for="cargo">{{ __('Sucursal') }}</label>
                    <select name ="id_sucursal" class="form-control " type="number" > 
                        @foreach($sucursales as $sucu)
                            <option value="{{$sucu->id}}">{{$sucu->descripcion}}</option>
                        @endforeach 
                    </select>
                </div>
                <div class="form-group col-md-3" > 
                    <label for="cargo">{{ __('Punto Venta') }}</label>
                    <select name ="punto_venta" class="form-control " type="number" > 
                      <option value="0">0</option>
                      <option value="1">1</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="name">{{ __('Codigo CAFC') }}</label>
                    <input name="codigo_cafc" type="text" class="form-control"  maxlength="100"  required>
                </div> 
                <div class="form-group col-md-3">
                    <label for="name">{{ __('Nro Inicial') }}</label>
                    <input name="nro_inical" type="number" class="form-control"  maxlength="100"  required>
                </div>
                <div class="form-group col-md-3">
                    <label for="name">{{ __('Nro Final') }}</label>
                    <input name="nro_final" type="number" class="form-control"  maxlength="100"  required>
                </div>
                <div class="form-group col-md-3">
                    <label for="name">{{ __('Fecha Vigencia') }}</label>
                    <input name="fecha_vigencia" type="date" class="form-control"  maxlength="100" required>
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