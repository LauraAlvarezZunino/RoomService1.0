<?php

class Usuarios
{
    private $usuarios = [];
    private $usuarioJson = 'usuario.json';

    public function __construct()
    {
        $this->cargarDesdeJSON();
    }

    // Cargar datos desde JSON
    private function cargarDesdeJSON()
    {
        if (file_exists($this->usuarioJson)) {
            $jsonUsuarios = file_get_contents($this->usuarioJson);

            $data = json_decode($jsonUsuarios, true);

            if (isset($data['usuarios'])) {  // Verifica si existe la clave 'usuarios'
                $usuariosArray = $data['usuarios'];

                foreach ($usuariosArray as $usuarioData) {
                    $usuario = new Usuario(
                        $usuarioData['id'],
                        $usuarioData['nombre'],
                        $usuarioData['dni'],
                        $usuarioData['email'],
                        $usuarioData['telefono']
                    );
                    $this->usuarios[] = $usuario;
                }
            } else {
                echo "No se encontró la clave 'usuarios' en el archivo JSON.\n";
            }
        } else {
            echo "No se encontró el archivo JSON.\n";
        }
    }

    // Guardar datos en JSON
    private function guardarEnJSON()
    {
        $usuariosArray = array_map([$this, 'usuarioToArray'], $this->usuarios);
        $jsonUsuario = json_encode(['usuarios' => $usuariosArray], JSON_PRETTY_PRINT);
        file_put_contents($this->usuarioJson, $jsonUsuario);
    }

    // Pasasr Usuario en array
    private function usuarioToArray($usuario)
    {
        return [
            'id' => $usuario->getId(),
            'nombre' => $usuario->getNombreApellido(),
            'dni' => $usuario->getDni(),
            'email' => $usuario->getEmail(),
            'telefono' => $usuario->getTelefono()
        ];
    }

    // Crear (Agregar) un nuevo usuario con ID autogenerado
    public function crearUsuario($nombreApellido, $dni, $email, $telefono)
    {
        $nuevoId = $this->generarNuevoId();
        $usuario = new Usuario($nuevoId, $nombreApellido, $dni, $email, $telefono);
        $this->usuarios[] = $usuario;
        $this->guardarEnJSON();
    }

    // Generar un nuevo ID basado en el último ID existente 
    private function generarNuevoId()
    {
        if (empty($this->usuarios)) {
            return 1; // Si no hay usuarios, el primer ID es 1
        } else {
            $ultimoUsuario = end($this->usuarios);
            return $ultimoUsuario->getId() + 1;
        }
    }


    // mostrar todos los usuarios
    public function obtenerUsuarios()
    {
        return $this->usuarios;
    }

    //  un usuario por ID para el menu de admin
    public function obtenerUsuarioPorId($id)
    {
        foreach ($this->usuarios as $usuario) {
            if ($usuario->getId() == $id) {
                return $usuario;
            }
        }
        return null;
    }
  
    
    public function obtenerUsuarioPorDni($dni)
    {
        foreach ($this->usuarios as $usuario) {
            if ($usuario->getDni() == $dni) {
                return $usuario;
            }
        }
        return null;
    }
    // Actualizar un usuario existente, el isset es para ver si existe el valor  
    public function actualizarUsuario($id, $nuevosDatos)
    {
        foreach ($this->usuarios as &$usuario) {
            if ($usuario->getId() == $id) {
                if (isset($nuevosDatos['nombre'])) {
                    $usuario->setNombreApellido($nuevosDatos['nombre']);
                } else {
                    $usuario->setNombreApellido($usuario->getNombreApellido());
                }
        
                if (isset($nuevosDatos['dni'])) {
                    $usuario->setDni($nuevosDatos['dni']);
                } else {
                    $usuario->setDni($usuario->getDni());
                }
        
                if (isset($nuevosDatos['email'])) {
                    $usuario->setEmail($nuevosDatos['email']);
                } else {
                    $usuario->setEmail($usuario->getEmail());
                }
        
                if (isset($nuevosDatos['telefono'])) {
                    $usuario->setTelefono($nuevosDatos['telefono']);
                } else {
                    $usuario->setTelefono($usuario->getTelefono());
                }
        
                $this->guardarEnJSON();
                return true;
            }
        }
        
        
        return false;
    }

    // Eliminar un usuario
    public function eliminarUsuario($id)
    {
        foreach ($this->usuarios as $idEliminar => $usuario) {
            if ($usuario->getId() == $id) {
                unset($this->usuarios[$idEliminar]); 
                $this->guardarEnJSON();  
                return true;
            }
        

            }
    
        return false;
    }
}

class Usuario
{
    private $id;
    private $nombreApellido;
    private $dni;
    private $email;
    private $telefono;

    public function __construct($id, $nombreApellido, $dni, $email, $telefono)
    {
        $this->id = $id;
        $this->nombreApellido = $nombreApellido;
        $this->dni = $dni;
        $this->email = $email;
        $this->telefono = $telefono;
    }

    public function __toString()
    {
        return "ID: " . $this->id . ", Nombre: " . $this->nombreApellido . ", DNI: " . $this->dni . ", Email: " . $this->email . ", Teléfono: " . $this->telefono;
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

    public function getNombreApellido()
    {
        return $this->nombreApellido;
    }

    public function setNombreApellido($nombreApellido)
    {
        $this->nombreApellido = $nombreApellido;
    }

    public function getDni()
    {
        return $this->dni;
    }

    public function setDni($dni)
    {
        $this->dni = $dni;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }
}
