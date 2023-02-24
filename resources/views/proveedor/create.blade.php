@extends('layouts.index')

@section('content')   

<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos"> CREAR NUEVO PROVEEDOR</h3>
    </div>
   
    <form method="POST" action="{{ url('proveedor') }}" autocomplete="off">
    @csrf
        <div  class="divformulario"> 
            <div class="row">
               
                <div class="form-group col-md-7">
                    <label for="name">{{ __('Descripcion') }}</label>
                    <input name="descripcion" type="text" class="form-control"  maxlength="80"  required>
                </div>                                    
            </div>
        </div>
            
        <div class="botones_atras_guardar">
            <div class="botonatras">
                <a class="btn btn-outline-danger" href="{{  action('ProveedorController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
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