<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FacEnviada extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = "mensaje recibido";
    public $tipo_fac;
    public $archivosadjuntos;
    public $msj1;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tipo_fac)
    {
        //
        $this->tipo_fac = $tipo_fac;

        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $dato = auth()->user()->id;

        $s =  auth()->user()->id_sucursal;
        $pv = auth()->user()->punto_venta;

        $msj1 = [storage_path('facturas/'.$this->tipo_fac.$s.$pv.'.xml'),storage_path('facturas/factura'.$s.$pv.'.pdf')];
        
        $email = $this->view('email')->subject('Factura Enviada')
        // $archivosadjuntos es una matriz con rutas de archivos de archivos adjuntos
        //foreach($archivosadjuntos as $rutaArchivo){
            ->attach($msj1[0])
            ->attach($msj1[1]);
        //}
        //dd($email);

        return $email;


        //return $this->view('email');
    }
}
