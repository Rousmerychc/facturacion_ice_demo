<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromView;

use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\FromQuery;


class ProductoExcel implements  FromView // FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    private $producto;
    

    public function __construct($producto1)
    {
        $this->producto = $producto1;
       
    }

    public function view():View
    {
        return view('producto_excel',['producto'=>$this->producto]);
    }
    public function collection()
    {
        //
    }
}
