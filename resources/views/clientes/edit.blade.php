@extends('layouts.index')

@section('content')   
<div class ="divpagina">
    <div class ="titulosagregar">
        <h3 class="titulos"> EDITAR CLIENTE</h3>
    </div>
 
     
<form method="POST" action=" {{ url('/clientes/'.$cliente->id) }} " autocomplete="off">
@csrf
@method('PATCH')
<div  class="divformulario"> 
    <div  class="row">
    <div class="form-group col-md-3">
                <label for="name">{{ __('Descripcion:') }}</label>
                <input name="descripcion" type="text" class="form-control input_facturacion" value ="{{$cliente->descripcion}}" required>
            </div>

            <div class="form-group col-md-3">
                <label for="name">{{ __('Responsable:') }}</label>
                <input name="responsable" type="text" class="form-control input_facturacion" value ="{{$cliente->responsable}}"required>
            </div>
            
            <div class="form-group col-md-3">
                <label for="name">{{ __('Direccion:') }}</label>
                <input name="direccion" type="text" class="form-control input_facturacion" value ="{{$cliente->direccion}}"  required>
            </div>

            <div class="form-group col-md-3 labelliquidaciones">
                    <label for="name" >Email:</label>
                    <input id="email" id = "email" type="email" class="form-control @error('email') is-invalid @enderror input_facturacion" name="email" value ="{{$cliente->email}}" required autocomplete="email">
                </div>

            <div class="form-group col-md-3">
                   <label for="name">{{ __('Tipo Documento:') }}</label>
                   <select name="id_tipo_documento" id="id_tipo_documento" type = "number" class=" form-control input_facturacion" required>   
                        <option value=""></option>
                        @foreach($tipo_documento as $td)
                            @if($cliente->id_tipo_documento == $td->id)
                                <option value=" {{$td->id}}" selected> {{$td->descripcion}}  </option>
                                @else
                                <option value=" {{$td->id}}"> {{$td->descripcion}}  </option>
                            @endif
                            @endforeach
                    </select>
               </div>

               <div class="form-group col-md-2">
                   <label for="name">{{ __('Nro Documento:') }}</label>
                   <input name="nro_documento"  id="nro_documento" type="text" class="form-control input_facturacion" value ="{{$cliente->nro_documento}}" onblur="validarnit()"required>
                   <input type="text" id = "validanit" name = "validanit" value = "0" value ="{{$cliente->excepcion}}">
               </div> 

               <div class="form-group  col-md-1 labelliquidaciones">
                        <label for="name">&nbsp;</label>
                        <input name="complemento" type="text" class="form-control input_facturacion" id = "complemento"readonly placeholder ="Complemento"  value ="{{$cliente->complemento}}">
                </div>

                <div class="form-group col-md-3">
                    <label for="name">{{ __('Razon Social:') }}</label>
                    <input name="razon_social" type="text" class="form-control input_facturacion"  value ="{{$cliente->razon_social}}"required>
                </div>

               <div class="form-group col-md-2">
                   <label for="name">{{ __('Precio:') }}</label>
                   <select name="id_categoria_precio" id="codigo_impuestos" type = "number" class="form-control input_facturacion" required>                           
                   @if($cliente->id_categoria_precio == 1)
                        <option value=" 1" selected> Precio 1  </option>
                        <option value=" 2"> Precio 2  </option>
                        <option value=" 3"> Precio 3  </option>
                    @endif
                    @if($cliente->id_categoria_precio == 2)
                        <option value=" 1" > Precio 1  </option>
                        <option value=" 2"selected> Precio 2  </option>
                        <option value=" 3"> Precio 3  </option>
                    @endif
                    @if($cliente->id_categoria_precio == 3)
                        <option value=" 1" > Precio 1  </option>
                        <option value=" 2"> Precio 2  </option>
                        <option value=" 3" selected> Precio 3  </option>
                    @endif
                    </select>
                </div>
                <div class="form-group col-md-1">
                    <label for="password-confirm" >{{ __('Estado') }}</label>
                    @if($cliente->estado == 1 )
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
            <a class="btn btn-outline-danger" href="{{  action('ClientesController@index') }}" role="button">Cancelar &nbsp <i class="fas fa-times"></i></a>
        </div>

        <div>
            <button type="submit" class="btn btn-primary"> Guardar Cambios </button>
        </div>
    </div>       
</form>
</div>                              
        
@endsection

@section('js')
<script>



</script>
@endsection 