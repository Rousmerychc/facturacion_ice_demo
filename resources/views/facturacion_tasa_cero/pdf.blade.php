<!-- <a class="btn btn-outline-danger btn-sm" id="btneditar" title = "PDF" href="{{ url('/pdf_factura/'.$id) }}" target="_blank"><i class="far fa-file-pdf"></i></a> -->
<a class="btn btn-outline-danger btn-sm" id="btneditar" title = "PDF" href="{{ url('/pdf_clientesT/'.$id) }}" target="_blank"><i class="far fa-file-pdf"></i></a>

<!-- <Button trigger modal> -->
<button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal1" title = "QR" onclick="qr( {{ $id }});">
    <i class="fas fa-qrcode"></i>
</button>

<!-- <a class="btn btn-outline-dark btn-sm" id="btneditar" title = "QR" href="{{ url('/codigoQR/'.$id) }}" target="_blank"><i class="fas fa-qrcode"></i></a> -->