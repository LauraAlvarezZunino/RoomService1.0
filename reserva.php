<?php
include 'habitacion.php';
include 'usuario.php'; 

class Reserva {
    private $id;
    private $fecha_inicio;
    private $fecha_fin;
    private $habitacion;
    private $estado;
    private $costo;

    public function __construct($id, $fecha_inicio, $fecha_fin, $estado, $costo) {
        $this->id = $id;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->estado = $estado;
        $this->costo = $costo;
    }

    // Getters y Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getFechaInicio() {
        return $this->fecha_inicio;
    }

    public function setFechaInicio($fecha_inicio) {
        $this->fecha_inicio = $fecha_inicio;
    }

    public function getFechaFin() {
        return $this->fecha_fin;
    }

    public function setFechaFin($fecha_fin) {
        $this->fecha_fin = $fecha_fin;
    }

    public function getCosto() {
        return $this->costo;
    }

    public function setCosto($costo) {
        $this->costo = $costo;
    }

    public function getHabitacion() {
        return $this->habitacion;
    }

    public function setHabitacion($habitacion) {
        $this->habitacion = $habitacion;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function calcularCosto($precio_por_noche) {
        $inicio = new DateTime($this->fecha_inicio);
        $fin = new DateTime($this->fecha_fin);
        $diferencia = $inicio->diff($fin);
        $dias = $diferencia->days;
        $this->costo = $dias * $precio_por_noche;
    }

    public function mostrarCosto() {
         return "Costo de la reserva: $" . $this->costo;
    }

    public function mostrarEstado() {
        return "Estado de la reserva: " . $this->estado;
    }

    public function __toString() {
        return "ID: {$this->id}, Fecha Inicio: {$this->fecha_inicio}, Fecha Fin: {$this->fecha_fin}, Estado: {$this->estado}, Costo: $" . $this->costo;
    }
}
?>
