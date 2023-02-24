@extends('layouts.index')

@section('content')   
<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos"> <i class="fas fa-user"></i> CREAR NUEVO USUSARIO</h3>
    </div>
 
     
<form method="POST" action="{{ url('usuarios') }}">
@csrf
    <div  class="row"> 
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
            <select name ="rol" class="form-control" type= "number" required> 
                <option></option> 
                <option value="1">Administrador</option> 
                <option value="2">Operador</option>     
            </select>
        </div>
        <div class="form-group col-md-4" > 
            <label for="cargo">{{ __('Sucursal') }}</label>
            <select name ="rol" class="form-control "> 
                @foreach($sucursales as $s)
                    <option  value="'{{$s->id}}'">{{ $s-> descrip }} </option>  
                @endforeach</option>     
            </select>
        </div>
                                
        <div class="form-group col-md-4">
            <label for="email">{{ __('Correo Electronico') }}</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"  required autocomplete="email">
        </div>
                            
        <div class="form-group col-md-4">
            <label for="password">{{ __('Contraseña') }}</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

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
    </div>

    <div class="form-group row p-3">
            <div class="col-1">
                <button type="submit" class="btn btn-primary">
                    {{ __('Registrarse') }}
                </button>
            </div>
         </div>         
</form>
</div>                              
        
@endsection