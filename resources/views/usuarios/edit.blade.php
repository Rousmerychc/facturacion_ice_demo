@extends('layouts.index')

@section('content')   
<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos"> EDITAR USUSARIO</h3>
    </div>
<form method="POST" action=" {{ url('/usuarios/'.$usuarios->id) }} ">
@csrf
@method('PATCH')
<div  class="divformulario"> 
    <div  class="row"> 
         <div class="form-group col-md-4">
            <label for="name">{{ __('Nombre') }}</label>
            <input name="name" id="name" type="text" class="form-control" id="validationCustom01"  value="{{ $usuarios->name }}" required>
        </div>                
    
        <div class="form-group col-md-4" > 
            <label for="apellido">{{ __('Apellido') }}</label>
            <input type="text" name="apellido" class="form-control" id="validationCustom01"  value="{{ $usuarios->apellido }}" required>
        </div>

        <div class="form-group col-md-4" > 
            <label for="cargo">{{ __('Cargo') }}</label>
            <select name ="rol" class="form-control ">
                @if($usuarios->rol == 1)
                <option value="1" selected> Administrador</option>
                <option value="2">Operador</option>
                @else
                <option value="1" > Administrador</option>
                <option value="2" selected>Operador</option>
                @endif
            </select>
        </div>
        <div class="form-group col-md-4" > 
            <label for="cargo">{{ __('Sucursal') }}</label>
            <select name ="id_sucursal" class="form-control " type="number" > 
                @foreach($sucursal as $sucu)
                    @if($sucu->nro_sucursal == $usuarios->id_sucursal)
                        <option value="{{$sucu->id}}" selected>{{$sucu->descripcion}}</option>
                    @else
                        <option value="{{$sucu->id}}">{{$sucu->descripcion}}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-4" > 
            <label for="cargo">{{ __('Punto de Venta') }}</label>
            <select name ="punto_venta" class="form-control " type="number" > 
                @if($usuarios->punto_venta == 0)
                    <option value="0" selected> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                @endif
                @if($usuarios->punto_venta == 1)
                    <option value="0"> 0</option>
                    <option value="1"selected> 1</option>
                    <option value="2"> 2</option>
                @endif
                @if($usuarios->punto_venta == 2)
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2" selected> 2</option>
                @endif
            </select>
        </div>
                            
        <div class="form-group col-md-4">
            <label for="email">Correo Electronico</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"   value="{{ $usuarios->email }}" required >
        </div>
                            
        <div class="form-group col-md-4">
            <label for="password-confirm" >{{ __('Estado') }}</label>
            @if($usuarios->estado == 1 )
                
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
        <div class="form-group col-md-4">
            <label for="password-confirm" >{{ __('Estado Descuento') }}</label>
            @if($usuarios->estado_descuento == 1 )
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="estado_descuento" value ="1" checked>  
                    <label class="form-check-label" for="exampleRadios1">Activo</label>
                </div>
            @else    
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="estado_descuento" value="1">
                    <label class="form-check-label" for="exampleRadios2">Activo</label>
                </div>
            @endif    
        </div>       
    </div>
</div>  
<div class="botones_atras_guardar">          
        <div class="botonatras">
            <a class="btn btn-outline-danger" href="{{  action('UsuarioController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
        </div>

        <div>
            <button type="submit" class="btn btn-primary"> Guardar Cambios </button>
        </div>
    </div>        
</form>
</div>                             
        
@endsection