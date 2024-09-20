<?php
include_once 'habitacionesGestor.php';
class Reserva {

    private $id;
    private $fechaInicio;
    private $fechaFin;
    private $habitacion;
    private $costo;
    private $usuarioDni;

    public function __construct($id, $fechaInicio, $fechaFin, $costo,$usuarioDni)
    {
        $this->id = $id;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->costo = $costo;
        $this->usuarioDni = $usuarioDni; 
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

   
    public function setUsuarioDni($dni) {
        $this->usuarioDni = $dni;
    }

    public function getUsuarioDni() {
        return $this->usuarioDni;
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

 
    /*function reservaToArray($reserva)    es necesaria?
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
      return "ID: {$this->id}, Fecha Inicio: {$this->fechaInicio}, Fecha Fin: {$this->fechaFin},Habitacion:{$this->habitacion}, Costo: $ . $this->costo, Reservado por dni:{$this->usuarioDni}";
    }
}

   


