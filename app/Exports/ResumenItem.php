<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromView;

use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\FromQuery;

class ResumenItem implements FromView//FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    private $fi;
    private $ff;
    private $consulta;

    public function __construct($fi1,$ff1,$consulta1)
    {
        $this->fi = $fi1;
        $this->ff = $ff1;
        $this->consulta = $consulta1;
    }

    public function view():View
    {
        return view('reportes.resumen_item_excel',['consulta'=>$this->consulta,'fechaini'=>$this->fi, 'fechafin'=>$this->ff]);
    }


    public function collection()
    {
        //
    }
}
