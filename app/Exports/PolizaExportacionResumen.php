<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromView;

use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\FromQuery;

class PolizaExportacionResumen implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    private $consulta;
    private $año;
    private $consultadefinitivas;
    private $consultaritex;
    private $consulta_va;

    public function __construct($consulta1,$año1,$consultadefinitivas1,$consultaritex1,$consulta_va1)
    {
        $this->consulta = $consulta1;
        $this->año = $año1;
        $this->consultadefinitivas = $consultadefinitivas1;
        $this->consultaritex = $consultaritex1;
        $this->consulta_va = $consulta_va1;
    }

    
    public function view():View
    {
        return view('reportes.poliza_exportacion_resumen',['consulta'=>$this->consulta, 'año'=>$this->año, 'consultadefinitivas'=> $this->consultadefinitivas, 'consultaritex'=>$this->consultaritex, 'consulta_va'=>$this->consulta_va]);
    }

    public function collection()
    {
        //
    }
}
