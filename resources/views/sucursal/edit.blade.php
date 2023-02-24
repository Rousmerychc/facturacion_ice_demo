@extends('layouts.index')

@section('content')   
<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos"> EDITAR SUCURSAL</h3>
    </div>
 
     
<form method="POST" action=" {{ url('/sucursal/'.$sucursales->id) }} " autocomplete="off">
@csrf
@method('PATCH')
<div  class="divformulario"> 
    <div  class="row"> 
    <div class="form-group col-md-4">
                <label for="name">{{ __('Nro sucursal') }}</label>
                <select name="nro_sucursal" id="" class="form-control" disabled>
                @foreach($sucu as $su)
                        @if($su->nro_sucursal == 0 && $sucursales->nro_sucursal == $su->nro_sucursal)
                        <option value="{{$su->nro_sucursal}}" selected> {{$su->nro_sucursal}} - Casa Matriz</option>
                        @else
                            @if($su->nro_sucursal != 0 && $sucursales->nro_sucursal == $su->nro_sucursal)
                            <option value="{{$su->nro_sucursal}}" selected> {{$su->nro_sucursal}}</option>
                            @else
                            <option value="{{$su->nro_sucursal}}"> {{$su->nro_sucursal}}</option>
                            @endif
                        @endif
                        
                    @endforeach
                </select>
            </div>
         <div class="form-group col-md-4">
            <label for="name">{{ __('Descripcion') }}</label>
            <input name="descripcion" id="name" type="text" class="form-control" maxlength="100"  value="{{ $sucursales->descripcion }}" required>
        </div>
        <div class="form-group col-md-4">
            <label for="name">{{ __('Direccion') }}</label>
            <input name="direccion" id="name" type="text" class="form-control" maxlength="100"  value="{{ $sucursales->direccion }}" required>
        </div>
        <div class="form-group col-md-4">
            <label for="name">{{ __('Telefono') }}</label>
            <input name="telefono" id="name" type="text" class="form-control" maxlength="100"  value="{{ $sucursales->telefono }}" required>
        </div>
        <div class="form-group col-md-4">
            <label for="name">{{ __('Municipio') }}</label>
            <input name="municipio" id="name" type="text" class="form-control" maxlength="100"  value="{{ $sucursales->municipio }}" required>
        </div>

        <div class="form-group col-md-4">
                <label for="password-confirm" >{{ __('Estado') }}</label>
            @if($sucursales->estado == 1 )
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="estado" value ="1" checked>  
                    <label class="form-check-label" for="exampleRadios1">Activo</label>
                </div>
            @else    
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="estado" value="1">
                    <label class="form-check-label" for="exampleRadios2">Activo</label>
                </div>
            @endif    
        </div>        
    </div>
</div>
<div class="botones_atras_guardar">          
        <div class="botonatras">
            <a class="btn btn-outline-danger" href="{{  action('SucursalController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
        </div>

        <div>
            <button type="submit" class="btn btn-primary"> Guardar Cambios </button>
        </div>
    </div>       
</form>
</div>                              
        
@endsection