<?php
include_once 'reserva.php';
class ReservasGestor
{
    private $reservas = [];
    private $reservaJson = 'reservas.json';
    private $id = 1; // ID inicial

    public function __construct()
    {
        $this->cargarDesdeJSON();
    }
    public function generarNuevoId()
    {
        return $this->id++;
    }
    public function agregarReserva(Reserva $reserva, $habitacionesGestor)
    {
        // Obtenemos la habitación asociada a la reserva desde el archivo JSON
        $habitacion = $habitacionesGestor->buscarHabitacionPorNumero($reserva->getHabitacion()->getNumero());
        
        if ($habitacion && $habitacion->getDisponibilidad() === 'Disponible') {
            // Calcular los días entre la fecha de inicio y la fecha de fin
            $fechaInicio = new DateTime($reserva->getFechaInicio());
            $fechaFin = new DateTime($reserva->getFechaFin());
            $intervalo = new DateInterval('P1D');
            $periodo = new DatePeriod($fechaInicio, $intervalo, $fechaFin->modify('+1 day'));
    
            $diasReservados = [];
            foreach ($periodo as $fecha) {
                $diasReservados[] = $fecha->format('Y-m-d');
            }
    
            // Actualizar los días reservados y la disponibilidad en la habitación
            $habitacion->agregarDiasReservados($diasReservados);
            $habitacion->setDisponibilidad('No disponible');
    
            // Guardar la reserva y actualizar las habitaciones en el JSON
            $this->reservas[] = $reserva;
            $this->guardarEnJSON();
            $habitacionesGestor->actualizarHabitacionesEnJSON();
    
            echo "Reserva agregada exitosamente y la habitación ahora está no disponible.\n";
        } else {
            echo "La habitación ya está reservada o no está disponible.\n";
        }
    }
    
    


//   public function agregarReserva(Reserva $reserva)
  //  {
    //    $this->reservas[] = $reserva;
      //  $this->guardarEnJSON();
        //echo "Reserva agregada exitosamente.\n";
   // }
    public function obtenerReservas()
    {
        return $this->reservas;
    }
    //modifica una reserva existente por ID
    public function modificarReserva($id, $nuevaFechaInicio, $nuevaFechaFin, $nuevaHabitacion, $nuevoEstado, $nuevoCosto)
    {
        $reserva = $this->buscarReservaPorId($id);
        if ($reserva) {
            $reserva->setFechaInicio($nuevaFechaInicio);
            $reserva->setFechaFin($nuevaFechaFin);
            $reserva->setHabitacion($nuevaHabitacion);
            $reserva->setCosto($nuevoCosto);
            $this->guardarEnJSON();
        } else {
            echo "Reserva no encontrada.\n";
        }
    }

    //eliminar una reserva por ID
    public function eliminarReserva($id)
    {
        foreach ($this->reservas as $indice => $reserva) {
            if ($reserva->getId() == $id) {
                unset($this->reservas[$indice]);
                $this->reservas = array_values($this->reservas); // reindexamos el array
                $this->guardarEnJSON();
                return true;
            }
        }
        return false;
    }

   public function buscarReservaPorId($id)
    {
        foreach ($this->reservas as $reserva) {
            if ($reserva->getId() == $id) {
                return $reserva;
            }
        }
        return null;
    }

    // Guardar reservas en el archivo JSON
    private function guardarEnJSON()
    {
        $reservasArray = [];

        foreach ($this->reservas as $reserva) {
            $reservasArray[] = [
                'id' => $reserva->getId(),
                'fechaInicio' => $reserva->getFechaInicio(),
                'fechaFin' => $reserva->getFechaFin(),
                'habitacion' => $reserva->getHabitacion(),
                'costo' => $reserva->getCosto()
            ];
        }

        file_put_contents($this->reservaJson, json_encode($reservasArray, JSON_PRETTY_PRINT));
    }

    // Cargar reservas desde el archivo JSON
    private function cargarDesdeJSON()
    {
        if (file_exists($this->reservaJson)) {
            $json = file_get_contents($this->reservaJson);
            $reservasArray = json_decode($json, true);

            foreach ($reservasArray as $reservaData) {
                $reserva = new Reserva(
                    $reservaData['id'],
                    $reservaData['fechaInicio'],
                    $reservaData['fechaFin'],
                    $reservaData['habitacion'],
                    $reservaData['costo']
                );
                $this->reservas[] = $reserva;

                // Asegurar que el ID esté actualizado
                if ($this->id < $reserva->getId() + 1) {
                    $this->id = $reserva->getId() + 1;
                }
            }
        }
    }
}
