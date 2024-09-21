<?php
include_once 'reserva.php';
include_once 'usuarios.php';
include_once 'habitacionesGestor.php';

class ReservasGestor
{
    private $reservas = [];
    private $reservaJson = 'reservas.json';
    private $id = 1; // ID inicial
    private $habitacionesGestor;

    public function __construct($habitacionesGestor)
    {
        $this->habitacionesGestor = $habitacionesGestor;
        $this->cargarDesdeJSON();
    }

    public function generarNuevoId()
    {
        return $this->id++;
    }

    public function agregarReserva(Reserva $reserva)
    {
        // Obtenemos la habitación asociada a la reserva desde el archivo JSON
        $habitacion = $this->habitacionesGestor->buscarHabitacionPorNumero($reserva->getHabitacion()->getNumero());

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
            $this->habitacionesGestor->agregarDiasReservados($diasReservados);
            $habitacion->setDisponibilidad('No disponible');

            // Guardar la reserva y actualizar las habitaciones en el JSON
            $this->reservas[] = $reserva;
            $this->guardarEnJSON();
            $this->habitacionesGestor ->actualizarHabitacion($habitacion,$diasReservados);

            echo "Reserva agregada exitosamente y la habitación ahora está no disponible.\n";
        } else {
            echo "La habitación ya está reservada o no está disponible.\n";
        }
    }

    public function obtenerReservas()
    {
        return $this->reservas;
    }

    public function modificarReserva($id, $nuevaFechaInicio, $nuevaFechaFin, $nuevaHabitacion, $nuevoCosto)
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
                'habitacion' => $reserva->getHabitacion(), // Guardamos solo el número de la habitación
                'costo' => $reserva->getCosto(),
                'usuarioDni' => $reserva->getUsuarioDni(), // Cambiado a 'usuarioDni'
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
            // Verificamos si la clave 'usuarioDni' existe antes de usarla
            $usuarioDni = isset($reservaData['usuarioDni']) ? $reservaData['usuarioDni'] : null;

            $habitacion = $this->habitacionesGestor->buscarHabitacionPorNumero($reservaData['habitacion']);
            $reserva = new Reserva(
                $reservaData['id'],
                $reservaData['fechaInicio'],
                $reservaData['fechaFin'],
                $habitacion, // Pasamos la habitación completa
                $reservaData['costo'],
                $usuarioDni // Asignamos el DNI del usuario o null si no existe
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

