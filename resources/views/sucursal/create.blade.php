@extends('layouts.index')

@section('content')   
<div class ="divpagina">

<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos"> <i class="fas fa-warehouse"></i> Crear nueva sucursal</h3>
    </div>
   
<form method="POST" action="{{ url('sucursal') }}" autocomplete="off">
@csrf
    <div  class="divformulario"> 
        <div class="row">

            <div class="form-group col-md-4">
                <label for="name">{{ __('Nro sucursal') }}</label>
                @php
                    $sucunext = '';
                @endphp
                <select name="nro_sucursal" id="" class="form-control" disabled>
                    @foreach($sucursal as $sucu)
                        @if($sucu->nro_sucursal == 0)
                        <option value="{{$sucu->nro_sucursal}}"> {{$sucu->nro_sucursal}} - Casa Matriz</option>
                        @else
                        <option value="{{$sucu->nro_sucursal}}"> {{$sucu->nro_sucursal}}</option>
                        @endif
                        @php
                            $sucunext = $sucu->nro_sucursal;
                        @endphp
                    @endforeach
                    <option value="{{$sucunext + 1}}" selected> {{$sucunext +1}} </option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="name">{{ __('Descripcion') }}</label>
                <input name="descripcion" type="text" class="form-control"  maxlength="100"  required>
            </div>
            <div class="form-group col-md-4">
                <label for="name">{{ __('Direccion') }}</label>
                <input name="direccion" type="text" class="form-control"  maxlength="100"  required>
            </div> 
            <div class="form-group col-md-4">
                <label for="name">{{ __('Telefono') }}</label>
                <input name="telefono" type="text" class="form-control"  maxlength="100"  required>
            </div> 
            <div class="form-group col-md-4">
                <label for="name">{{ __('Municipio') }}</label>
                <input name="municipio" type="text" class="form-control"  maxlength="100"  required>
            </div>   

            <div class="form-group col-md-4">
                <label for="password-confirm" >{{ __('Estado') }}</label>
               
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="estado" value="1" checked  >  
                    <label class="form-check-label" for="exampleRadios1">Activo</label>
                </div>
            </div>
        </div>
    </div>
        
     <div class="botones_atras_guardar">          
        <div class="botonatras">
            <a class="btn btn-outline-danger" href="{{  action('SucursalController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
        </div>

        <div>
            <button type="submit" class="btn btn-primary"> Guardar </button>
        </div>
    </div>
         
</form>
</div>                              
        
@endsection