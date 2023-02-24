<?php

namespace App\Exports;

use App\User;
use App\ProveedoresCorporativas;
use App\Socios;
use App\Minerales;
use App\Liquidaciones;
use App\Retenciones;


use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromView;

use Illuminate\Contracts\View\View;


use App\Moneda;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;


class LiquidacionesExport implements FromView   //FromQuery //FromCollection  //
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    private $fi;
    private $ff;
    private $mineral;
    private $proveedor;

      public function __construct($fi1,$ff1,$min1,$pro1,$id_moneda1)
     {
         $this->fi= $fi1;
         $this->ff= $ff1;
         $this->mineral= $min1;
         $this->proveedor= $pro1;
    
         $this->id_moneda = $id_moneda1;
         //dd($this->fi, $this->ff, $this->mineral, $this->proveedor, $this->$id_moneda);
     }

    public function view():View
    {
//     
        $min = DB::table('minerales')
        ->where('minerales.id','=',$this->mineral)
        ->first();
        if($this->proveedor == 0){
            $prov = DB::table('provedores_corporativas')
            ->where('provedores_corporativas.id','=',$this->proveedor)
            ->get();
            $consulta = DB::table('liquidaciones')
            ->join('provedores_corporativas','provedores_corporativas.id','=','liquidaciones.id_proveedor')
            ->join('liquidaciones_laboratorio','liquidaciones_laboratorio.id_liquidacion','=','liquidaciones.id')
            ->where('liquidaciones.id_mineral','=',$this->mineral)
            ->where('liquidaciones.anulada','=',0)
            ->whereDate('liquidaciones.fecha','>=',$this->fi)
            ->whereDate('liquidaciones.fecha','<=',$this->ff)
            ->select('provedores_corporativas.descrip','liquidaciones_laboratorio.h2o','liquidaciones.fecha','liquidaciones.lote','liquidaciones.ley_calculo','liquidaciones.kb','liquidaciones.tara','liquidaciones.knh',
            'liquidaciones.humedad','liquidaciones.kns','liquidaciones.kf','liquidaciones.rm','liquidaciones.valor_bruto','liquidaciones.valor_final','liquidaciones.total_retenciones','liquidaciones.liquido_pagable',
            'liquidaciones.moneda','liquidaciones.liquido_pagable_bs','liquidaciones.dolar')
            ->orderBy('liquidaciones.fecha')
            ->get();
        }else{

            $prov = DB::table('provedores_corporativas')
            ->where('provedores_corporativas.id','=',$this->proveedor)
            ->first();

            $consulta = DB::table('liquidaciones')
            ->join('provedores_corporativas','provedores_corporativas.id','=','liquidaciones.id_proveedor')
            ->join('liquidaciones_laboratorio','liquidaciones_laboratorio.id_liquidacion','=','liquidaciones.id')
            ->where('liquidaciones.id_mineral','=',$this->mineral)
            ->where('liquidaciones.id_proveedor','=',$this->proveedor)
            ->where('liquidaciones.anulada','=',0)
            ->whereDate('liquidaciones.fecha','>=',$this->fi)
            ->whereDate('liquidaciones.fecha','<=',$this->ff)
            ->select('provedores_corporativas.descrip','liquidaciones_laboratorio.h2o','liquidaciones.fecha','liquidaciones.lote','liquidaciones.ley_calculo','liquidaciones.kb','liquidaciones.tara','liquidaciones.knh',
            'liquidaciones.humedad','liquidaciones.kns','liquidaciones.kf','liquidaciones.rm','liquidaciones.valor_bruto','liquidaciones.valor_final','liquidaciones.total_retenciones','liquidaciones.liquido_pagable',
            'liquidaciones.moneda','liquidaciones.liquido_pagable_bs','liquidaciones.dolar')
            ->orderBy('liquidaciones.fecha')
            ->get();
        }

        foreach($consulta as $c ){
            if($c->moneda != $this->id_moneda){
                if($this->id_moneda == 1){
                    $c->rm = round($c->rm*$c->dolar, 2);
                    $c->valor_bruto = round($c->valor_bruto*$c->dolar, 2);
                    $c->valor_final = round($c->valor_final*$c->dolar, 2);
                    $c->total_retenciones = round($c->total_retenciones*$c->dolar, 2);

                }else{
                    $c->rm = round($c->rm/$c->dolar, 2);
                    $c->valor_bruto = round($c->valor_bruto/$c->dolar, 2);
                    $c->valor_final = round($c->valor_final/$c->dolar, 2);
                    $c->total_retenciones = round($c->total_retenciones/$c->dolar, 2);
                    
                }
            }
        }
        $moneda = Moneda::where('id','=',$this->id_moneda)->first();

        return view('reporte_liquidaciones.prueba',['consulta'=>$consulta,'min'=>$min,'prov'=>$prov,'moneda'=>$moneda,'fi'=>$this->fi, 'ff'=>$this->ff]);

    }

    public function collection()
    {
        //
        return Liquidaciones::all();
    }

    private $f;
    public function fecha($f){
        $this->f = $f;
        return $this;
    }

    public function query(){
        return Liquidaciones::query()
        ->join('socios','socios.id','=','liquidaciones.id_socio')
        ->whereDate('fecha','=',$this->fi);
    }
}
