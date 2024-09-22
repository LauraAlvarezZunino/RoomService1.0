<?php

require_once 'Habitacion.php';

class HabitacionGestor  {

    private $habitaciones = [];
    private $archivoJson = 'habitacion.json';
 
    // CRUD

    public function agregarHabitacion($habitacion)
    {
        $this->habitaciones[] = $habitacion;
        $this->guardarEnJSON();
      
    }

    public function obtenerHabitaciones()
    {
        return $this->habitaciones;
    }
    // Añadir días reservados

    public function buscarHabitacionPorNumero($numero)
    {
        foreach ($this->habitaciones as $habitacion) {
            if ($habitacion->getNumero() == $numero) {
                return $habitacion;
            }
        }
        return null; // Retorna null si no se encuentra la habitación
    }
public function agregarDiasReservados($diasReservado,$habitacion)
{
    $habitacion->setdiasReservados($diasReservado);
}


public function buscarPorDisponibilidadYTipo($disponibilidad, $tipo)
{
    $resultados = [];
    foreach ($this->habitaciones as $habitacion) {
        if ($habitacion->getDisponibilidad() == $disponibilidad && $habitacion->getTipo() == $tipo) {
            $resultados[] = $habitacion;
        }
    }
    return $resultados;
}




    public function actualizarHabitacion($numero, $nuevosDatos)
    {
        foreach ($this->habitaciones as &$habitacion) {
            if ($habitacion->getNumero() == $numero) {
                if (isset($nuevosDatos['tipo'])) {
                    $habitacion->setTipo($nuevosDatos['tipo']);
                } else {
                    $habitacion->setTipo($habitacion->getTipo());
                }

                if (isset($nuevosDatos['precio'])) {
                    $habitacion->setPrecio($nuevosDatos['precio']);
                } else {
                    $habitacion->setPrecio($habitacion->getPrecio());
                }

                if (isset($nuevosDatos['disponibilidad'])) {
                    $habitacion->setDisponibilidad($nuevosDatos['disponibilidad']);
                } else {
                    $habitacion->setDisponibilidad($habitacion->getDisponibilidad());
                }

                $this->guardarEnJSON();
                return true;
            }
        }
        return false;
    }



    public function eliminarHabitacion($numero)
    {
        $nuevasHabitaciones = [];

        foreach ($this->habitaciones as $habitacion) {
            if ($habitacion->getNumero() != $numero) {
                $nuevasHabitaciones[] = $habitacion; // Añade las habitaciones que no son la que queremos eliminar
            }
        }

        $this->habitaciones = $nuevasHabitaciones; 
        $this->guardarEnJSON();

        return true;
    }

    // Json

    function guardarEnJSON()
    {
        $habitacionesArray = [];

        foreach ($this->habitaciones as $habitacion) {
            $habitacionesArray[] = $this->habitacionToArray($habitacion);
        }

        $jsonHabitacion = json_encode(['habitacion' => $habitacionesArray], JSON_PRETTY_PRINT);
        file_put_contents($this->archivoJson, $jsonHabitacion);
    }


    function cargarDesdeJSON()
    {
        if (file_exists($this->archivoJson)) {
            $jsonHabitacion = file_get_contents($this->archivoJson);
            $habitacionesArray = json_decode($jsonHabitacion, true)['habitacion'] ?? [];
            $this->habitaciones = []; // Asegura que se vacie el array antes de cargar los datos
            foreach ($habitacionesArray as $habitacionData) {
                $habitacion = new Habitacion();
                $habitacion->setNumero($habitacionData['numero']);
                $habitacion->setTipo($habitacionData['tipo']);
                $habitacion->setPrecio($habitacionData['precio']);
                $habitacion->setDisponibilidad($habitacionData['disponibilidad']);
                $habitacion->setDiasReservados($habitacionData['diasReservado']);
                $this->habitaciones[] = $habitacion;
            }
        }
    }

    function habitacionToArray($habitacion)
    {
        return [
            'numero' => $habitacion->getNumero(),
            'tipo' => $habitacion->getTipo(),
            'precio' => $habitacion->getPrecio(),
            'disponibilidad' => $habitacion->getDisponibilidad(),
            'diasReservado'=>$habitacion->getDiasReservados()
        ];
    }
}
   
