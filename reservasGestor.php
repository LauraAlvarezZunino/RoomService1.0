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

    // Generar un nuevo ID automáticamente
    public function generarNuevoId()
    {
        return $this->id++;
    }

    // Agrega una nueva reserva pero hay que ver porque no esta modficando la disponibilidad de ;a habiracion cuando 
    public function agregarReserva(Reserva $reserva)
    {
        $this->reservas[] = $reserva;
        $this->guardarEnJSON();
        echo "Reserva agregada exitosamente.\n";
    }

    // Obtener todas las reservas
    public function obtenerReservas()
    {
        return $this->reservas;
    }

    // Modificar una reserva existente por ID
    public function modificarReserva($id, $nuevaFechaInicio, $nuevaFechaFin, $nuevaHabitacion, $nuevoEstado, $nuevoCosto)
    {
        $reserva = $this->buscarReservaPorId($id);
        if ($reserva) {
            $reserva->setFechaInicio($nuevaFechaInicio);
            $reserva->setFechaFin($nuevaFechaFin);
            $reserva->setHabitacion($nuevaHabitacion);
            $reserva->setEstado($nuevoEstado);
            $reserva->setCosto($nuevoCosto);
            $this->guardarEnJSON();
        } else {
            echo "Reserva no encontrada.\n";
        }
    }

    // Eliminar una reserva por ID
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

    // Buscar una reserva por su ID
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
                'fecha_inicio' => $reserva->getFechaInicio(),
                'fecha_fin' => $reserva->getFechaFin(),
                'habitacion' => $reserva->getHabitacion(),
                'estado' => $reserva->getEstado(),
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
                    $reservaData['fecha_inicio'],
                    $reservaData['fecha_fin'],
                    $reservaData['habitacion'],
                    $reservaData['estado'],
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
