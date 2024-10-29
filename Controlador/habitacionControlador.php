<?php

require_once 'Modelo/habitacion.php';

class HabitacionControlador
{
    private $habitaciones = [];

    private $archivoJson = 'habitacion.json';

    public function __construct()
    {
        $this->cargarDesdeJSON();
    }
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

    public function buscarHabitacionPorNumero($numero)
    {
        foreach ($this->habitaciones as $habitacion) {
            if ($habitacion->getNumero() == $numero) {
                return $habitacion;
            }
        }

        return null; // si no se encuentra la habitación
    }

    public function buscarPorTipo($tipo)
    {
        $resultados = [];
        foreach ($this->habitaciones as $habitacion) {
            if ($habitacion->getTipo() == $tipo) {
                $resultados[] = $habitacion;
            }
        }

        return $resultados;
    }

    public function actualizarHabitacion($numero, $nuevosDatos)
    {
        foreach ($this->habitaciones as &$habitacion) {
            if ($habitacion->getNumero() == $numero) {
                if (isset($nuevosDatos['tipo'])) { //isset chequea que no es nulo
                    $habitacion->setTipo($nuevosDatos['tipo']);
                } else {
                    $habitacion->setTipo($habitacion->getTipo());
                }

                if (isset($nuevosDatos['precio'])) {
                    $habitacion->setPrecio($nuevosDatos['precio']);
                } else {
                    $habitacion->setPrecio($habitacion->getPrecio());
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
                $nuevasHabitaciones[] = $habitacion; // Agrega las hab que no queremos borrar
            }
        }

        $this->habitaciones = $nuevasHabitaciones;
        $this->guardarEnJSON();

        return true;
    }

    // Json

    public function guardarEnJSON()
    {
        $habitacionesArray = [];

        foreach ($this->habitaciones as $habitacion) {
            $habitacionesArray[] = $this->habitacionToArray($habitacion);
        }

        $jsonHabitacion = json_encode(['habitacion' => $habitacionesArray], JSON_PRETTY_PRINT);
        file_put_contents($this->archivoJson, $jsonHabitacion);
    }

    public function habitacionToArray($habitacion)
    {
        return [
            'numero' => $habitacion->getNumero(),
            'tipo' => $habitacion->getTipo(),
            'precio' => $habitacion->getPrecio(),

        ];
    }

    public function cargarDesdeJSON()
    {
        if (file_exists($this->archivoJson)) { //existe?
            $jsonHabitacion = file_get_contents($this->archivoJson); //lo lee y lo guarda
            $habitacionesArray = json_decode($jsonHabitacion, true)['habitacion'];
            $this->habitaciones = []; // Asegura que se vacie el array antes de cargar los datos

            foreach ($habitacionesArray as $habitacionData) {
                $habitacion = new Habitacion;
                $habitacion->setNumero($habitacionData['numero']);
                $habitacion->setTipo($habitacionData['tipo']);
                $habitacion->setPrecio($habitacionData['precio']);
                $this->habitaciones[] = $habitacion;
            }
        }
    }
}
