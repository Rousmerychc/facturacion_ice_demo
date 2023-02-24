<?php

namespace App\Exports;


use App\ProveedoresCorporativas;
use App\Socios;
use App\Minerales;

use App\RecepcionMineralContador;
use App\RecepcionMineral;
use App\RecepcionMineralDetalle;


use Illuminate\Support\Facades\DB;



use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\Exportable;

class RecepcionMineralesExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;
    private $fi;
    private $ff;
    private $mineral;
   

    public function __construct($fi1,$ff1,$min1)
    {
        $this->fi= $fi1;
        $this->ff= $ff1;
        $this->mineral= $min1;
        
        //dd($this->fi, $this->ff, $this->mineral, $this->proveedor);
    }

    public function view():View
    {
       
        $min = DB::table('minerales')
        ->where('minerales.id','=',1)
        ->first();

        $consulta = DB::table('recepcion_mineral')
        ->join('recepcion_mineral_detalle','recepcion_mineral_detalle.id_recepcion_mineral','=','recepcion_mineral.id')
        ->join('provedores_corporativas','provedores_corporativas.id','recepcion_mineral.id_proveedor')
        ->where('recepcion_mineral.id_mineral','=', $this->mineral)
        ->where('recepcion_mineral.anulada','=',0)
        ->whereDate('recepcion_mineral.fecha','>=',$this->fi)
        ->whereDate('recepcion_mineral.fecha','<=',$this->ff)
        ->select('recepcion_mineral.fecha','provedores_corporativas.descrip','recepcion_mineral_detalle.lote','recepcion_mineral_detalle.peso_bruto','recepcion_mineral_detalle.peso_pallet','recepcion_mineral_detalle.cant_saquillos','recepcion_mineral_detalle.peso_saquillos_va','recepcion_mineral_detalle.tara','recepcion_mineral_detalle.total_peso_neto')
        ->orderBy('recepcion_mineral.fecha', 'asc')
        ->get();
        //dd($consulta, $min);
        return view('reportes.excelrdiario',['consulta'=>$consulta,'min'=>$min,'fi'=>$this->fi, 'ff'=>$this->ff]);
    }

    public function collection()
    {
        //
    }
}
