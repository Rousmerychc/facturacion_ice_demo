<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromView;

use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\FromQuery;

class MovimientoDiarioCliente implements FromView //FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    private $fi;
    private $ff;
    private $factura;
    private $detalle;
    private $cliente_datos;

    public function __construct($fi1,$ff1,$factura1,$detalle1, $cliente_datos1)
    {
        $this->fi = $fi1;
        $this->ff = $ff1;
        $this->factura = $factura1;
        $this->detalle = $detalle1;
        $this->cliente_datos = $cliente_datos1;
    }

    public function view():View
    {
   
        return view('reportes.movimiento_diario_cliente_excel',['factura'=>$this->factura,'detalle'=>$this->detalle,'fechaini'=>$this->fi, 'fechafin'=>$this->ff, 'cliente_datos'=>$this->cliente_datos]);
    }


    public function collection()
    {
        //
    }
}
