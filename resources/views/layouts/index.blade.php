
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    

     <!-- CSRF Token -->
     <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIBELIS</title>
    <!-- mi estilo -->
    <link rel= "stylesheet" type="text/css" href= "{{ asset('css/estilo.css') }}"> 
      <!-- Fuentes -->
    <link  rel = " dns-prefetch "  href = " //fonts.gstatic.com " >
    <link  href = " https://fonts.googleapis.com/css?family=Nunito "  rel = " hoja de estilo ">

    <!-- Estilos -->
    <link  href = " {{ asset ( ' css / app.css ' ) }} "   rel = " hoja de estilo ">
   
    <script src="{{ asset('js/funciones.js') }}" ></script>
    
     <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <!-- para el select2-->   
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
   
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
  
   <!-- Para los iconos -->
    <script src="https://kit.fontawesome.com/02b49a5a66.js" crossorigin="anonymous"></script>
    
    <!-- datatables -->

 
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.0/css/jquery.dataTables.css">
 
 <link href="//datatables.net/download/build/nightly/jquery.dataTables.css" rel="stylesheet" type="text/css" />
<script src="//datatables.net/download/build/nightly/jquery.dataTables.js"></script>

    <!-- para el mapa  -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.2/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.0.2/dist/leaflet.js"></script>

    
    
</head>
<body class ="body">
    
<div class="divloginpagina" >
    
    <div class = "divmenuloginpatallachicaylogoconpatallagrande">

        <div class ="desplegable_menu">  
            <input type="checkbox" id="abrir-cerrar" name="abrir-cerrar" value="" onchange="javascript:showContent()">
            <label for="abrir-cerrar">&#9776; <span class="abrir">Abrir</span><span class="cerrar">Cerrar</span></label>
               
            <div id="sidebar" class="sidebar">

                <div class="logo1">
                    <h5>SIBELIS</h5>  
                </div>
                <ul>
                    <li><a href="{{  action('IndexController@index') }}" > <i class="fas fa-home"></i>  Inicio</a></li>
                    @auth 
                        @if(1 == auth()->user()->rol)
                        <li><a href="#usuario1" data-toggle="collapse" aria-expanded="false"  aria-controls="collapseExample">
                            <div class ="menuenlaces">
                                <div><i class="fas fa-user"></i></div>
                                <div class ="padding_a_menu"> Usuario</div>
                                <div class = "flechamenu"><i class="fas fa-chevron-down"></i></div>
                            </div>
                        </a>                                                                                                                                                                                                                                                     
                        <ul class="collapse " id="usuario1">
                                <li><a href="{{  action('UsuarioController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-user"></i></div>
                                        <div class ="padding_a_menu">Usuarios</div>
                                    </div>    
                                 </a></li>
                                <li><a href="{{  action('UsuarioController@create') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-user-edit"></i></div>
                                        <div class ="padding_a_menu">Registrar Usuarios</div>
                                    </div>   
                                </a></li>
                            </ul>
                        </li>
                        @endif                  
                    @endauth 
                        <li><a href="#codificacion1" data-toggle="collapse" aria-expanded="false">
                                <div class ="menuenlaces">
                                    <div><i class="far fa-edit"></i></i></div>
                                    <div class ="padding_a_menu"> Codificacion</div>
                                    <div class = "flechamenu"><i class="fas fa-chevron-down"></i></div>
                                </div>
                            </a>                                                                                                                                                                                                                                                     
                            <ul class="collapse " id="codificacion1">
                            @auth 
                                @if(1 == auth()->user()->rol)
                                <li>
                                    <a href="{{  action('SucursalController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Sucursal</div>
                                    </div>   
                                    </a>
                                </li>
                                <li>
                                    <a href="{{  action('GruposController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Grupo ICE Y Porcentual</div>
                                    </div>   
                                    </a>
                                </li>
                                <li>
                                    <a href="{{  action('Grupo2Controller@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Grupo Producto</div>
                                    </div>   
                                    </a>
                                </li>
                                 <li>
                                    <a href="{{  action('ProductosController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Productos </div>
                                    </div>   
                                    </a>
                                </li>
                                <li>
                                    <a href="{{  action('UnidadMedidaController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Unidad Medida</div>
                                    </div>   
                                    </a>
                                </li> 
                                @endif                  
                            @endauth 
                                <li><a href="{{  action('ClientesController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Clientes</div>
                                    </div>   
                                    </a>
                                </li>  
                                <!--<li>
                                    <a href="{{  action('ProductosTasaCeroController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Productos Alquiler de Equipo</div>
                                    </div>   
                                    </a>
                                </li> -->
                               
                                <!-- <li>
                                    <a href="{{  action('ProveedorController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Proveedores</div>
                                    </div>   
                                    </a>
                                </li>    -->
                            </ul>                                    
                        </li>

                        <li><a href="#movimientos1" data-toggle="collapse" aria-expanded="false">
                                <div class ="menuenlaces">
                                    <div><i class="fas fa-exchange-alt"></i></div>
                                    <div class ="padding_a_menu"> Movimientos</div>
                                    <div class = "flechamenu"><i class="fas fa-chevron-down"></i></div>
                                </div>
                            </a>
                                                                                                                                                                                                                                                                           
                            <ul class="collapse " id="movimientos1">
                            @auth 
                                @if(1 == auth()->user()->rol)     
                                <li><a href="{{  action('IngresosController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Ingresos</div>
                                    </div>   
                                </a></li>
                                @endif                  
                            @endauth 
                                <li><a href="{{  action('FacturacionController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Facturacion</div>
                                    </div>   
                                </a></li>
                                <li><a href="{{  action('NotaVentaController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Nota de Entrega</div>
                                    </div>   
                                </a></li>
                            </ul> 
                        </li>
                        @auth 
                        @if(1 == auth()->user()->rol)  
                        <li>
                            <a href="{{  action('ReportesController@index') }}">
                            <div class ="menuenlaces">
                                <div><i class="far fa-file-alt"></i></div>
                                <div class ="padding_a_menu">Reportes</div>
                            </div>   
                            </a>
                        </li>  
                        <!-- <li>
                        <a href="{{  action('SincronizacionController@index') }}">
                            <div class ="menuenlaces">
                                <div><i class="fa-sharp fa-solid fa-pen"></i></div>
                                <div class ="padding_a_menu">Actualiza Catalogos</div>
                            </div>   
                            </a>
                        </li> -->
                        @endif                  
                            @endauth                        
                </ul>
            </div>
        </div> 
        
        <div class="logo">
             <h5>SIBELIS</h5>  
        </div>
        <div class ="div_login"  >
            <ul >
                <!-- Authentication Links -->
                @auth
                    <li >
                        <a  role="button" data-toggle="dropdown" strong>
                        <i class="fas fa-user"></i> &nbsp; Sucursal:{{ auth()->user()->descripcion_sucursal}} Usuario: {{ Auth::user()->name }}
                        </a>
                        <div class = " dropdown-menu dropdown-menu-right ">
                            <a class = " dropdown-item "  href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                {{ __('Cerrar Sesion') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endauth
                </ul>
        </div>
            
    </div>
        
    <div class = "divmenupagina">
       
        <div class= "overflow-auto contenedor_menu " >
                    
            <div class="menu1 ">
                <ul>
                    <li><a href="{{  action('IndexController@index') }}" > <i class="fas fa-home"></i>  Inicio</a></li>
                    @auth 
                        @if(1 == auth()->user()->rol)
                        <li><a href="#usuario" data-toggle="collapse" aria-expanded="false"  aria-controls="collapseExample">
                            <div class ="menuenlaces">
                                <div><i class="fas fa-user"></i></div>
                                <div class ="padding_a_menu"> Usuario</div>
                                <div class = "flechamenu"><i class="fas fa-chevron-down"></i></div>
                            </div>
                        </a>                                                                                                                                                                                                                                                     
                        <ul class="collapse " id="usuario">
                                <li><a href="{{  action('UsuarioController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-user"></i></div>
                                        <div class ="padding_a_menu">Usuarios</div>
                                    </div>    
                                 </a></li>
                                <li><a href="{{  action('UsuarioController@create') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-user-edit"></i></div>
                                        <div class ="padding_a_menu">Registrar Usuarios</div>
                                    </div>   
                                </a></li>
                            </ul>
                        </li>
                
                        @endif                  
                    @endauth
                    
                    <li><a href="#codificacion" data-toggle="collapse" aria-expanded="false">
                            <div class ="menuenlaces">
                                <div><i class="far fa-edit"></i></div>
                                <div class ="padding_a_menu"> Codificacion</div>
                                <div class = "flechamenu"><i class="fas fa-chevron-down"></i></div>
                            </div>
                        </a>                                                                                                                                                                                                                                                     
                        <ul class="collapse " id="codificacion">
                        @auth 
                        @if(1 == auth()->user()->rol)
                            <li>
                                <a href="{{  action('SucursalController@index') }}">
                                <div class ="menuenlaces">
                                    <div><i class="fas fa-angle-double-right"></i></div>
                                    <div class ="padding_a_menu">Sucursal</div>
                                </div>   
                                </a>
                            </li>
                            <li>
                                <a href="{{  action('GruposController@index') }}">
                                <div class ="menuenlaces">
                                    <div><i class="fas fa-angle-double-right"></i></div>
                                    <div class ="padding_a_menu">Grupo ICE Y Porcentual</div>
                                </div>   
                                </a>
                                </li>
                                <li>
                                    <a href="{{  action('Grupo2Controller@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Grupo Producto</div>
                                    </div>   
                                    </a>
                                </li>
                             <li><a href="{{  action('ProductosController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Productos</div>
                                    </div>   
                                </a>
                            </li>
                            <li>
                                    <a href="{{  action('UnidadMedidaController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Unidad Medida</div>
                                    </div>   
                                    </a>
                                </li>  
                            @endif                  
                            @endauth 
                            <li><a href="{{  action('ClientesController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Clientes</div>
                                    </div>   
                                </a>
                            </li>  
                            <!--<li>
                                    <a href="{{  action('ProductosTasaCeroController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Productos Alquiler de Equipo</div>
                                    </div>   
                                    </a>
                                </li> -->
                               
                                <!-- <li>
                                    <a href="{{  action('ProveedorController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Proveedores</div>
                                    </div>   
                                    </a>
                                </li>   -->
                        </ul>    
                     </li>
                    
                     <li><a href="#movimientos" data-toggle="collapse" aria-expanded="false">
                            <div class ="menuenlaces">
                                <div><i class="fas fa-exchange-alt"></i></div>
                                <div class ="padding_a_menu"> Movimientos</div>
                                <div class = "flechamenu"><i class="fas fa-chevron-down"></i></div>
                            </div>
                        </a>                                                                                                                                                                                                                                                     
                        <ul class="collapse " id="movimientos">
                        @auth 
                        @if(1 == auth()->user()->rol)
                            <li><a href="{{  action('IngresosController@index') }}">
                                <div class ="menuenlaces">
                                    <div><i class="fas fa-angle-double-right"></i></div>
                                    <div class ="padding_a_menu">Ingresos</div>
                                </div>   
                            </a></li>
                            @endif                  
                            @endauth
                            <li><a href="{{  action('FacturacionController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Facturacion</div>
                                    </div>   
                                </a></li>
                                <li><a href="{{  action('NotaVentaController@index') }}">
                                    <div class ="menuenlaces">
                                        <div><i class="fas fa-angle-double-right"></i></div>
                                        <div class ="padding_a_menu">Nota de Entrega</div>
                                    </div>   
                                </a></li>
                        </ul> 
                    </li>
                    @auth 
                        @if(1 == auth()->user()->rol)
                    <li>
                        <a href="{{  action('ReportesController@index') }}">
                            <div class ="menuenlaces">
                                <div><i class="far fa-file-alt"></i></div>
                                <div class ="padding_a_menu">Reportes</div>
                            </div>   
                            </a>
                        </li>
                        <!-- <li>
                        <a href="{{  action('SincronizacionController@index') }}">
                            <div class ="menuenlaces">
                                <div><i class="fa fa-solid fa-pen"></i></div>
                                <div class ="padding_a_menu">Actualiza Catalogos</div>
                            </div>   
                            </a>
                        </li>  -->
                        @endif                  
                            @endauth                      
                </ul>
            </div>
        
        </div> 

        <div id ="paginaindex" class ="paginaindex" >
        @yield('content')
            
        </div>  
    </div>    
    
</div>
     <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

    <!--datatables -->
    <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
           <!--para los botones de datatables  -->
           <!-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> //libreria choca con select 2-->
           <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
           <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
           <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
           <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
           <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
           <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
           <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>

           <!-- para propios botones -->
     
@yield('js')
<script>
$(document).ready(function() {
$('.select2').select2({
closeOnSelect: false
});
});

function showContent() {
        element = document.getElementById('paginaindex');
        check = document.getElementById('abrir-cerrar');
        if (check.checked) {
          
            element.style.pointerEvents = "none";
            element.style.opacity = "0.5";  
        }
        else {
            element.style.pointerEvents = "auto";
            element.style.opacity = "1";   
        }
    }

</script>

</body>
</html>
