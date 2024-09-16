<?php
include_once 'habitaciones.php';
class Reserva {

    private $id;
    private $fechaInicio;
    private $fechaFin;
    private $habitacion;
    private $estado;
    private $costo;

    public function __construct($id, $fechaInicio, $fechaFin, $estado, $costo)
    {
        $this->id = $id;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->estado = $estado;
        $this->costo = $costo;
    }

    // Getters y Setters
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio($fecha_inicio)
    {
        $this->fechaInicio = $fecha_inicio;
    }

    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;
    }

    public function getCosto()
    {
        return $this->costo;
    }

    public function setCosto($costo)
    {
        $this->costo = $costo;
    }

    public function getHabitacion()
    {
        return $this->habitacion;
    }

   
    public function setHabitacion(Habitacion $habitacion) {
        $this->habitacion = $habitacion;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function calcularCosto($precioPorNoche) {
        $inicio = new DateTime($this->fechaInicio);
        $fin = new DateTime($this->fechaFin);

        // Calcular la diferencia en días
        $diferencia = $inicio->diff($fin);
        $dias = $diferencia->days;

        // Calcular el costo total
        $this->costo = $dias * $precioPorNoche;
    }

 
    /*function reservaToArray($reserva)
    {
        return [
            'id' => $reserva->getId(),
            'Fecha inicio' => $reserva->getFechaInicio(),
            'Fecha fin' => $reserva->getFechaFin(),
            'Estado' => $reserva->getEstado(),
            'Habitacion'=>$reserva->getHabitacion(),
            'Costo'=>$reserva->getCosto()
        ];
    }

*/
    public function __toString()
    {
      return "ID: {$this->id}, Fecha Inicio: {$this->fechaInicio}, Fecha Fin: {$this->fechaFin}, Estado: {$this->estado},Habitacion:{$this->habitacion}, Costo: $" . $this->costo;
    }
}

   


