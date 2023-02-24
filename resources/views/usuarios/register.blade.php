@extends('layouts.index')

@section('content')   
<div class ="divpagina">

<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos"> <i class="fas fa-user-edit"></i> Crear nuevo usuario</h3>
    </div>
   
<form method="POST" action="{{ url('usuarios') }}">
@csrf
    <div  class="divformulario"> 
        <div class="row">
            <div class="form-group col-md-4">
                <label for="name">{{ __('Nombre') }}</label>
                <input name="name" id="name" type="text" class="form-control" id="validationCustom01"  required>
            </div>                
        
            <div class="form-group col-md-4" > 
                <label for="apellido">{{ __('Apellido') }}</label>
                <input type="text" name="apellido" class="form-control" id="validationCustom01" required>
            </div>

            <div class="form-group col-md-4" > 
                <label for="cargo">{{ __('Cargo') }}</label>
                <select name ="rol" class="form-control " type="number" > 
                    <option value="1">Administrador</option>  
                    <option value="2">Operador</option>  
                </select>
            </div>
            <div class="form-group col-md-4" > 
                <label for="cargo">{{ __('Sucursal') }}</label>
                <select name ="id_sucursal" class="form-control " type="number" > 
                    @foreach($sucursal as $sucu)
                        <option value="{{$sucu->id}}">{{$sucu->descripcion}}</option>
                    @endforeach 
                </select>
            </div>
            <div class="form-group col-md-4" > 
                <label for="cargo">{{ __('Punto de Venta') }}</label>
                <select name ="punto_venta" class="form-control " type="number" > 
                    <option value="0"> 0</option>
                    <option value="1"> 1</option>
                    <option value="2"> 2</option>
                </select>
            </div>

            <div class="form-group col-md-4">
                <label for="email">{{ __('Correo Electronico') }}</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"  required autocomplete="email">
            </div>
                                
            <div class="form-group col-md-4">
                <label for="password">{{ __('Contraseña') }}</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password"  minlength="6">

                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
            </div>
                        
            <div class="form-group col-md-4">
                <label for="password-confirm" >{{ __('Confrimar Contraseña') }}</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
            </div>
            <div class="form-group col-md-2">
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
            <a class="btn btn-outline-danger" href="{{  action('UsuarioController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
        </div>

        <div>
            <button type="submit" class="btn btn-primary"> Guardar </button>
        </div>
    </div>
         
</form>
</div>                              
        
@endsection