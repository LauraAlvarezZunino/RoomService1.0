<?php
include_once 'Modelo/reserva.php';
include_once 'usuarioControlador.php';
include_once 'habitacionControlador.php';

class ReservaControlador
{
    private $reservas = [];
    private $reservaJson = 'Modelo/reservas.json';
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

       // if ($habitacion && $habitacion->getDisponibilidad() === 'Disponible') {
         
         //   $fechaInicio = new DateTime($reserva->getFechaInicio());//datetime representacion de fechas 
           // $fechaFin = new DateTime($reserva->getFechaFin());
         
            //$diasReservados = [];
            //$diasReservados[] = ["Reservada ",$fechaInicio,"a",$fechaFin] ;
            // Actualizar los días reservados y la disponibilidad en la habitación
           // $this->habitacionesGestor->agregarDiasReservados($diasReservados,$habitacion);
            //$habitacion->setDisponibilidad('Reservada');
            // Guardar la reserva y actualizar las habitaciones en el JSON
            $this->reservas[] = $reserva;
            $this->guardarEnJSON();
            //$this->habitacionesGestor ->actualizarHabitacion($habitacion,$diasReservados);
        //// else {
          //  echo "La habitación ya está reservada o no está disponible.\n";
         //}
    }

    public function obtenerReservas()
    {
        return $this->reservas;
    }

    public function modificarReserva($id, $nuevaFechaInicio, $nuevaFechaFin, $nuevaHabitacion, $nuevoCosto){
        $reserva = $this->buscarReservaPorId($id); //pendienteeeeee 
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
                $this->reservas = array_values($this->reservas); // reposicionamos el array para que no quede un lugar vacio
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
                'habitacion' => $reserva->getHabitacion()->getNumero(), // Guardamos solo el número de la habitación
                'costo' => $reserva->getCosto(),
                'usuarioDni' => $reserva->getUsuarioDni(), // Cambiado a 'usuarioDni'
            ];
        }

        // Envolver el array de reservas en un objeto
        $dataToSave = ['reservas' => $reservasArray];
        file_put_contents($this->reservaJson, json_encode($dataToSave, JSON_PRETTY_PRINT));

        //file_put_contents($this->reservaJson, json_encode($reservasArray, JSON_PRETTY_PRINT));
    }

    // Cargar reservas desde el archivo JSON
    private function cargarDesdeJSON()
{
    /*if (file_exists($this->reservaJson)) {
        $json = file_get_contents($this->reservaJson);
        $reservasArray = json_decode($json, true)['reservas'];

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
    }*/
    if (file_exists($this->reservaJson)) {
        $json = file_get_contents($this->reservaJson);
        $data = json_decode($json, true);

        if (isset($data['reservas'])) {
            $reservasArray = $data['reservas'];
        } else {
            $reservasArray = []; // Inicializa como un array vacío si no existe
        }

        foreach ($reservasArray as $reservaData) {
            $usuarioDni = isset($reservaData['usuarioDni']) ? $reservaData['usuarioDni'] : null;

            // Verificamos si 'habitacion' es un array con la información completa
            if (is_array($reservaData['habitacion'])) {
                // Creamos la instancia de Habitacion usando los datos dentro del array
                $habitacionData = $reservaData['habitacion'];
                $habitacion = new Habitacion(
                    $habitacionData['numero'],
                    $habitacionData['tipo'],
                    $habitacionData['precio']
                );
            } else {
                // En caso contrario, asumimos que es un número y buscamos la habitación
                $habitacion = $this->habitacionesGestor->buscarHabitacionPorNumero($reservaData['habitacion']);
            }

            // Verificamos si se encontró o creó la habitación
            if ($habitacion === null) {
                echo "Advertencia: La habitación número {$reservaData['habitacion']} no fue encontrada. Se omitirá esta reserva.\n";
                continue;
            }

            $reserva = new Reserva(
                $reservaData['id'],
                $reservaData['fechaInicio'],
                $reservaData['fechaFin'],
                $habitacion, 
                $reservaData['costo'],
                $usuarioDni
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

