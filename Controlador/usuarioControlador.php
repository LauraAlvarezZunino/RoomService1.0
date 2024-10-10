<?php
require_once 'Modelo/usuario.php';
class UsuarioControlador {

    private $usuarios = [];
    private $usuarioJson = 'Modelo/usuario.json';

    public function __construct()
    {
        $this->cargarDesdeJSON();
    }

   

    

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

// Guardar datos en JSON
    private function guardarEnJSON()
    {
        $usuariosArray = array_map([$this, 'usuarioToArray'], $this->usuarios);//aplica la funcion en el arreglo sin usar for array map
        $jsonUsuario = json_encode(['usuarios' => $usuariosArray], JSON_PRETTY_PRINT);
        file_put_contents($this->usuarioJson, $jsonUsuario);
    }


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

    private function cargarDesdeJSON()
    {
        if (file_exists($this->usuarioJson)) {
            $jsonUsuarios = file_get_contents($this->usuarioJson);
    
            // Decodificamos el JSON en un array a
            $data = json_decode($jsonUsuarios, true);
    
            // Verificamos si el JSON tiene la clave 'usuarios'
            if (isset($data['usuarios'])) {
                $usuariosArray = $data['usuarios'];
    
                // Recorremos el array de usuarios y creamos instancias de Usuario
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
            }
        }
    }
    
}    