<!-- <form method='POST' action =" {{ url('/almacen/'.$id) }} ">
    @csrf
    @method('DELETE') -->
    <a class="btn btn-outline-primary btn-sm" id="btneditar" href="{{ url('/clientes/'.$id).'/edit' }}"><i class="fas fa-pencil-alt"></i></a>
    
    <!-- <button tye="submit"  class="btn btn-outline-danger" onclick="return confirm('¿Estas Seguro?')";> <i class="fas fa-trash-alt"></i></button>
</form> -->
 