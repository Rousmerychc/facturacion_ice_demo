/*funcion de prueba en index items*/
function mostrarMensaje1(){
    alert('Bienvenido al curso JavaScript de aprenderaprogramar.com');
}

/*validacion para cobros, garantiza que almenos un campo este lleno*/
function validacion(){
    valor = document.getElementById("totalgeneral").value;
    if( valor == null || valor.length == 0 || /^\s+$/.test(valor) ) {
    alert("No hay monto a pagar");
    return false;
    }
} 
    
/**suma de los inputs importe en la vista crobros create  */   
function sumaimporte(f) {
    //alert("hola");
  
    var total=0;
      for(var x=0;x< f.length ;x++){//recorremos los campos dentro del form
  
          if(f[x].name.indexOf('importe')!=-1){//si el nombre campo contiene la palabra 'aporte'
              total+=Number(f[x].value);//sumamos, convirtiendo el contenido del campo a número
          }
         
      }
    //console.log(total);
      $('#totalgeneral').val(total);
      compara(f);
      
  }
/** funcion llamada por suma importe */  
function compara(f){
    var imp = document.getElementsByName("importe[]");
    var sal = document.getElementsByName("saldo[]");
    var iimp = 0;
    var isal = 0;
    for(var j=0; j<imp.length;j++){
        //console.log(imp[j].value+j);
        //console.log(sal[j].value);
        iimp = parseFloat( imp[j].value);
        isal = parseFloat( sal[j].value);
        if(iimp > isal){
            console.log('importe mayor');
            $('#'+j).val("");
            sumaimporte(f);
    
        }
    }
}

//---------------------------------------------------------------------------
//PARA PRESICION DE DECIMALES
function decimalAdjust(type, value, exp) {
    // Si el exp no está definido o es cero...
    if (typeof exp === 'undefined' || +exp === 0) {
      return Math[type](value);
    }
    value = +value;
    exp = +exp;
    // Si el valor no es un número o el exp no es un entero...
    if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
      return NaN;
    }
    // Shift
    value = value.toString().split('e');
    value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
    // Shift back
    value = value.toString().split('e');
    return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
  }

  // Decimal round
  if (!Math.round10) {
    Math.round10 = function(value, exp) {
      return decimalAdjust('round', value, exp);
    };
  }
  // Decimal floor
  if (!Math.floor10) {
    Math.floor10 = function(value, exp) {
      return decimalAdjust('floor', value, exp);
    };
  }
  // Decimal ceil
  if (!Math.ceil10) {
    Math.ceil10 = function(value, exp) {
      return decimalAdjust('ceil', value, exp);
    };
  }
  //--------------------------------------------------------------------------------

