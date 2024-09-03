<?php
class Transferencia extends Pago {
   
    public function __construct($monto) {
        parent::__construct($monto); 
   }
    
    public function procesarPago() {
        return " El monto de tu reserva es $" . $this->monto . "\n 
        Nuestro alias: habitacion.room.service\n 
        Una vez realizado el pago recibiras en tu correo electronico una confirmacion de tu reserva. \n 
        Gracias por utilizar Room Service.";
             
    }
    
}
?>