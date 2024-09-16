<?php

class Habitacion
{

    private $habitaciones = [];
    private $archivoJson = 'habitacion.json';
    private $numero;
    private $tipo;
    private $precio;
    private $disponibilidad;


    public function __construct($numero = null, $tipo = null, $precio = null, $disponibilidad = null)
    {
        $this->numero = $numero;
        $this->tipo = $tipo;
        $this->precio = $precio;
        $this->disponibilidad = $disponibilidad;
    }

    // Getters y Setters

    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }

    public function getDisponibilidad()
    {
        return $this->disponibilidad;
    }

    public function setDisponibilidad($disponibilidad)
    {
        $this->disponibilidad = $disponibilidad;
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

    public function buscarPorDisponibilidad($disponibilidad)
    {
        $resultados = [];
        foreach ($this->habitaciones as $habitacion) {
            if ($habitacion->getDisponibilidad() == $disponibilidad) {
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
            $this->habitaciones = []; // Asegúrate de vaciar el array antes de cargar los datos
            foreach ($habitacionesArray as $habitacionData) {
                $habitacion = new Habitacion();
                $habitacion->setNumero($habitacionData['numero']);
                $habitacion->setTipo($habitacionData['tipo']);
                $habitacion->setPrecio($habitacionData['precio']);
                $habitacion->setDisponibilidad($habitacionData['disponibilidad']);
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
        ];
    }

    public function __toString()
    {
        return "Habitación Número: $this->numero, Tipo: $this->tipo, Precio: $this->precio";
    }
}
