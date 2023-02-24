@extends('layouts.index')

@section('content')   

<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos"> CREAR NUEVO GRUPO CON ICE Y PORCENTUAL</h3>
    </div>
   
    <form method="POST" action="{{ url('grupos') }}" autocomplete="off">
    @csrf
        <div  class="divformulario"> 
            <div class="row">
               
                <div class="form-group col-md-6">
                    <label for="name">{{ __('Descripcion:') }}</label>
                    <input name="descripcion" type="text" class="form-control"  maxlength="100"  required>
                </div>
            
                <div class="form-group col-md-3">
                    <label for="name">{{ __('Ice Prcentual:') }}</label>
                    <input name="ice_porcentual" type="text" class="form-control"  onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"  required>
                </div> 

                <div class="form-group col-md-3">
                    <label for="name">{{ __('Ice Especifico:') }}</label>
                    <input name="ice_especifico" type="text" class="form-control"  onkeypress="return (event.charCode >= 46 && event.charCode <= 57)"  required>
                </div>  

            </div>
        </div>
            
        <div class="botones_atras_guardar">
            <div class="botonatras">
                <a class="btn btn-outline-danger" href="{{  action('GruposController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
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