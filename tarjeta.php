<?php
class Tarjeta extends Pago {
    private $numero_tarjeta;
    private $nombre_titular;
    private $fecha_expiracion;
    private $codigo_CVV;
    private $tipo;
    private $direccion_facturacion;
    private $banco_emisor;
 

    public function __construct($monto,$numero_tarjeta, $nombre_titular, $fecha_expiracion, $codigo_CVV, $tipo, $direccion_facturacion, $banco_emisor) {
        $this->numero_tarjeta = $numero_tarjeta;
        $this->nombre_titular = $nombre_titular;
        $this->fecha_expiracion = $fecha_expiracion;
        $this->codigo_CVV = $codigo_CVV;
        $this->tipo = $tipo;
        $this->direccion_facturacion = $direccion_facturacion;
        $this->banco_emisor = $banco_emisor;
        parent::__construct($monto);
    }

    public function getNumeroTarjeta() {
        return $this->numero_tarjeta;
    }

    public function setNumeroTarjeta($numero_tarjeta) {
        $this->numero_tarjeta = $numero_tarjeta;
    }

    public function getNombreTitular() {
        return $this->nombre_titular;
    }

    public function setNombreTitular($nombre_titular) {
        $this->nombre_titular = $nombre_titular;
    }

    public function getFechaExpiracion() {
        return $this->fecha_expiracion;
    }

    public function setFechaExpiracion($fecha_expiracion) {
        $this->fecha_expiracion = $fecha_expiracion;
    }

    public function getCodigoCVV() {
        return $this->codigo_CVV;
    }

    public function setCodigoCVV($codigo_CVV) {
        $this->codigo_CVV = $codigo_CVV;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function getDireccionFacturacion() {
        return $this->direccion_facturacion;
    }

    public function setDireccionFacturacion($direccion_facturacion) {
        $this->direccion_facturacion = $direccion_facturacion;
    }

    public function getBancoEmisor() {
        return $this->banco_emisor;
    }

    public function setBancoEmisor($banco_emisor) {
        $this->banco_emisor = $banco_emisor;
    }
  
    public function procesarPago() {
        echo "Pago con tarjeta del banco " . $this->banco_emisor . " del titular " . $this->nombre_titular . " con un monto de $" . $this->monto . " pesos procesado exitosamente. Gracias por utilizar Room Service.";
    }
}