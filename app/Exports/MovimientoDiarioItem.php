<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromView;

use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\FromQuery;

class MovimientoDiarioItem implements FromView //FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    private $fi;
    private $ff;
    private $detalle;
    private $datos_item;

    public function __construct($fi1,$ff1,$detalle1, $datos_item1)
    {
        $this->fi = $fi1;
        $this->ff = $ff1;
        $this->detalle = $detalle1;
        $this->datos_item = $datos_item1;
    }

    public function view():View
    {
        return view('reportes.movimiento_diario_item_excel',['detalle'=>$this->detalle,'fechaini'=>$this->fi, 'fechafin'=>$this->ff, 'datos_item'=>$this->datos_item]);
    }


    public function collection()
    {
        //
    }
}
