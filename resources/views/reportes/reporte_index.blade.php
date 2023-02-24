@extends('layouts.index')

@section('content')

<div class="divpagina">

        <div class ="titulosagregar">
            <h3 class="titulos"> <i class="fas fa-angle-double-right"></i> REPORTES</h3>
        </div>
   
        <div class="divformulario  "> 
            <div class ="row">   
                <div class="form-group col-md-6">
                <button class="btn btn-outline-primary  col-md-12" type="button" data-toggle="collapse" data-target="#collapseE1" aria-expanded="false" aria-controls="multiCollapseExample2"> LIBRO DE VENTAS</button>
                  
                </div>

                <div class="form-group col-md-6">
                <button class="btn btn-outline-primary  col-md-12" type="button" data-toggle="collapse" data-target="#collapseE2" aria-expanded="false" aria-controls="multiCollapseExample2"> VENTAS POR LITROS</button>
                    
                </div> 
                
                <div class="form-group col-md-6">
                <button class="btn btn-outline-primary  col-md-12" type="button" data-toggle="collapse" data-target="#collapseE3" aria-expanded="false" aria-controls="multiCollapseExample2"> MOVIMIENTO DEL DÍA POR ITEM</button>
                 
                    
                </div> 

                
                <div class="form-group col-md-6">
                <button class="btn btn-outline-primary  col-md-12" type="button" data-toggle="collapse" data-target="#collapseE4" aria-expanded="false" aria-controls="multiCollapseExample2"> RESUMEN PRODUCTOS</button>
                
                </div> 

                <!-- <div class="form-group col-md-6">
                <button class="btn btn-outline-primary  col-md-12" type="button" data-toggle="collapse" data-target="#collapseE5" aria-expanded="false" aria-controls="multiCollapseExample2"> POLIZAS DE EXPORTACION</button>
                </div> 

                <div class="form-group col-md-6">
                <button class="btn btn-outline-primary  col-md-12" type="button" data-toggle="collapse" data-target="#collapseE6" aria-expanded="false" aria-controls="multiCollapseExample2"> POLIZAS DE EXPORTACION RESUMEN</button>
                </div> -->
            </div>
        </div>

<div class="collapse" id="collapseE1">
  <div class="card card-body">

    <h5  class = "resportestitulos"> Libro de Ventas</h5>
        <form method="POST" action="{{ url('reporte_libro_ventas') }}" autocomplete="off" id ="formulario1"  target="_blank">
            @csrf
            <div class ="row">
                <div class="form-group col-md-3 ">
                    <label  for="name">{{ __('Fecha Inicio') }}</label>
                    <input name="fechai1" id ="fechai1" type="date" class="form-control" value ="{{$fecha}}"  maxlength="50"  required>
                </div> 
                <div class="form-group col-md-3 ">
                    <label  for="name">{{ __('Fecha Fin') }}</label>
                    <input name="fechaf1" id ="fechaf1" type="date" class="form-control" value ="{{$fecha}}" maxlength="50"  required>
                </div>
                <div class="form-group col-md-4 ">
                <label  for="name">{{ __('Sucursal') }}</label>
                <select type= "number" name="sucursal1" class="form-control">
                    @foreach($sucursal as $c)
                        <option value="{{$c->nro_sucursal}}">{{$c->nro_sucursal}} - {{$c->descripcion}}</option>
                    @endforeach
                </select>
            </div> 
                <input type="hidden" name ="proceso1" id = "proceso1" value = "0">
                <div class=" col-md-3 flex ">
                    <label  for="name"> &nbsp;</label></br>
                    <!-- Button trigger modal -->
                    
                    <button type="button" class="btn btn-outline-danger" title = "PDF"  onclick="envio1();">
                        PDF &nbsp;<i class="far fa-file-pdf"></i>
                    </button>
            
                    &nbsp; 
                    <button type="button" class="btn btn-outline-success" title = "EXCEL" onclick="excel1();">
                    EXCEL &nbsp;<i class="far fa-file-excel"></i>
                    </button>
                
                </div> 
            </form>
        </div>
  </div>
</div>
<div class="collapse" id="collapseE2">
  <div class="card card-body">
    <h5  class = "resportestitulos"> Ventas por Litros</h5>
    <form method="POST" action="{{ url('reporte_ventas_por_litros') }}" autocomplete="off" id ="formulario2"  target="_blank">     
    @csrf
        <div class ="row">
            <div class="form-group col-md-4 ">
                <label  for="name">{{ __('Sucursal') }}</label>
                <select type= "number" name="sucursal" class="form-control">
                @foreach($sucursal as $c)
                        <option value="{{$c->nro_sucursal}}">{{$c->nro_sucursal}} - {{$c->descripcion}}</option>
                    @endforeach
                </select>
            </div> 
          
            <div class="form-group col-md-3 ">
                <label  for="name">{{ __('Fecha Inicio') }}</label>
                <input name="fechai2" type="date" class="form-control" value ="{{$fecha}}"  maxlength="50"  required>
            </div> 
            <div class="form-group col-md-3 ">
                <label  for="name">{{ __('Fecha Fin') }}</label>
                <input name="fechaf2" type="date" class="form-control" value ="{{$fecha}}" maxlength="50"  required>
            </div> 
            <div class=" col-md-3 flex ">
                <input type="hidden" name ="proceso2" id = "proceso2" value = "0">
                <label  for="name"> &nbsp;</label></br>
                <button type="button" class="btn btn-outline-danger" title = "PDF"  onclick="envio2();">
                    PDF &nbsp;<i class="far fa-file-pdf"></i>
                </button>
        
                &nbsp; 
                <button type="button" class="btn btn-outline-success" title = "EXCEL" onclick="excel2();">
                EXCEL &nbsp;<i class="far fa-file-excel"></i>
                </button>
            </div>             
        </div>
    </form>
  </div>
  
</div>

<div class="collapse" id="collapseE3">
  <div class="card card-body">
    <h5  class = "resportestitulos"> Movimiento Día Por Item</h5>
    <form method="POST" action="{{ url('reporte_movimiento_diario_item') }}" autocomplete="off" id ="formulario3"  target="_blank">
    @csrf
        <div class ="row">
            <div class="form-group col-md-4 ">
                <label  for="name">{{ __('Producto') }}</label>
                <select type= "number" name="item" class="form-control">
                    @foreach($productos as $p)
                        <option value="{{$p->id}}"> {{$p->codigo_empresa}} - {{$p->descripcion}}</option>
                    @endforeach
                </select>
            </div> 
          
            <div class="form-group col-md-3 ">
                <label  for="name">{{ __('Fecha Inicio') }}</label>
                <input name="fechai3" type="date" class="form-control"  value ="{{$fecha}}"  maxlength="50"  required>
            </div> 
            <div class="form-group col-md-3 ">
                <label  for="name">{{ __('Fecha Fin') }}</label>
                <input name="fechaf3" type="date" class="form-control"  value ="{{$fecha}}"  maxlength="50"  required>
            </div> 
            <div class=" col-md-3 flex ">
                <input type="hidden" name ="proceso3" id = "proceso3" value = "0">
                <label  for="name"> &nbsp;</label>
                <button type="button" class="btn btn-outline-danger" title = "PDF"  onclick="envio3();">
                    PDF &nbsp;<i class="far fa-file-pdf"></i>
                </button>
        
                &nbsp; 
                <button type="button" class="btn btn-outline-success" title = "EXCEL" onclick="excel3();">
                EXCEL &nbsp;<i class="far fa-file-excel"></i>
                </button>
               
            </div> 
            
        </div>
   </form>
  </div>
  
</div>


<div class="collapse" id="collapseE4">
  <div class="card card-body">
    <h5  class = "resportestitulos"> Resumen Productos</h5>
    <form method="POST" action="{{ url('reporte_resumen_item') }}" autocomplete="off" id ="formulario4"  target="_blank">
         @csrf
        <div class ="row">
        
            <div class="form-group col-md-3 ">
                <label  for="name">{{ __('Fecha Inicio') }}</label>
                <input name="fechai4" type="date" class="form-control"  value ="{{$fecha}}"   maxlength="50"  required>
            </div> 
            <div class="form-group col-md-3 ">
                <label  for="name">{{ __('Fecha Fin') }}</label>
                <input name="fechaf4" type="date" class="form-control"  value ="{{$fecha}}" maxlength="50"  required>
            </div> 
            <div class=" col-md-3 flex ">
            <input type="hidden" name ="proceso4" id = "proceso4" value = "0">
                <label  for="name"> &nbsp;</label></br>
                <button type="button" class="btn btn-outline-danger" title = "PDF"  onclick="envio4();">
                    PDF &nbsp;<i class="far fa-file-pdf"></i>
                </button>
        
                &nbsp; 
                <button type="button" class="btn btn-outline-success" title = "EXCEL" onclick="excel4();">
                EXCEL &nbsp;<i class="far fa-file-excel"></i>
                </button>      
            </div>  
        </div>
    </form>
  </div>
</div>

<div class="collapse" id="collapseE5">
  <div class="card card-body">
    <h5  class = "resportestitulos"> Poliza De Exportación</h5>
    <form method="POST" action="{{ url('poliza/exportacion') }}" autocomplete="off" id ="formulario5"  target="_blank">
         @csrf
        <div class ="row">
        
            <div class="form-group col-md-3 ">
                <label  for="name">{{ __('Fecha Inicio') }}</label>
                <input name="fechai5" type="date" class="form-control"  value ="{{$fecha}}"   maxlength="50"  required>
            </div> 
            <div class="form-group col-md-3 ">
                <label  for="name">{{ __('Fecha Fin') }}</label>
                <input name="fechaf5" type="date" class="form-control"  value ="{{$fecha}}" maxlength="50"  required>
            </div> 
            <div class=" col-md-3 flex ">
            <label  for="name"> &nbsp;</label></br>
                <button type="sumbit" class="btn btn-outline-success" title = "EXCEL">
                EXCEL &nbsp;<i class="far fa-file-excel"></i>
                </button>      
            </div>  
        </div>
    </form>
  </div>
</div>
 

<div class="collapse" id="collapseE6">
  <div class="card card-body">
    <h5  class = "resportestitulos"> Poliza De Exportación Resumen</h5>
    <form method="POST" action="{{ url('poliza/exportacion/resumen') }}" autocomplete="off" id ="formulario5"  target="_blank">
         @csrf
        <div class ="row">
        
            <div class="form-group col-md-3 ">
                <label  for="name">{{ __('Año Gestion') }}</label>
                <select name="año" class="form-control">
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                    <option value="2027">2027</option>
                    <option value="2028">2028</option>
                    <option value="2029">2029</option>
                    <option value="2030">2030</option>
                </select>      
            </div>
            <div>
            <label  for="name"> &nbsp;</label></br>
                <button type="sumbit" class="btn btn-outline-success" title = "EXCEL">
                EXCEL &nbsp;<i class="far fa-file-excel"></i> 
                </button>
            </div>  
        </div>
    </form>
  </div>
</div>

</div>

@endsection

@section('js')
<script>

function excel1(){

    $("#proceso1").val(1);
    form1= document.getElementById("formulario1");
    form1.submit();
}
    
function envio1(){
    
    $("#proceso1").val(0);
    form1= document.getElementById("formulario1");
    form1.submit();
}

    
function excel2(){
$("#proceso2").val(1);
form2= document.getElementById("formulario2");
form2.submit();
}

function envio2(){
$("#proceso2").val(0);
form2= document.getElementById("formulario2");
form2.submit();
}

function excel3(){
$("#proceso3").val(1);
form3= document.getElementById("formulario3");
form3.submit();
}

function envio3(){
$("#proceso3").val(0);
form3= document.getElementById("formulario3");
form3.submit();
}

function excel4(){
$("#proceso4").val(1);
form4= document.getElementById("formulario4");
form4.submit();
}

function envio4(){
$("#proceso4").val(0);
form4= document.getElementById("formulario4");
form4.submit();
}
</script>


@endsection 