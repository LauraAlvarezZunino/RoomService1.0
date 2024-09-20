<?php 
class Habitacion
{
    protected $numero;
    protected $tipo;
    protected $precio;
    protected $disponibilidad;
    protected $diasReservado = [];

    public function __construct($numero = null, $tipo = null, $precio = null, $disponibilidad = null)
    {
        $this->numero = $numero;
        $this->tipo = $tipo;
        $this->precio = $precio;
        $this->disponibilidad = $disponibilidad;
        $this->diasReservado = [];
    }

    // Getters y Setters
    public function getNumero() { 
        return $this->numero; 
    }
    
    public function setNumero($numero) { 
        $this->numero = $numero; 
    }

    public function getTipo() { 
        return $this->tipo; 
    }
    
    public function setTipo($tipo) {
         $this->tipo = $tipo; 
        }

    public function getPrecio() { 
        return $this->precio; 
    }
    public function setPrecio($precio) {
        $this->precio = $precio; 
    }

    public function getDisponibilidad() { 
        return $this->disponibilidad; 
    }
    
    public function setDisponibilidad($disponibilidad) { 
        $this->disponibilidad = $disponibilidad; 
    }

    public function getDiasReservados() { 
        return $this->diasReservado; 
    }
    
    public function setDiasReservados(array $dias) { 
        $this->diasReservado = $dias; 
    }

    public function __toString()
    {
        return "Habitación Número: $this->numero, Tipo: $this->tipo, Precio: $this->precio, Disponibilidad: $this->disponibilidad, Días reservados: " . ($this->diasReservado ? implode(", ", $this->diasReservado) : "No reservada");
    }
}
