<?php

require_once './models/Usuario.php';
require_once './utils/AutentificadorJWT.php';
require '../vendor/autoload.php';

class Login
{


    public function ProcesoIngreso($request, $response)
    {
        $existeUsuario = false;
        $faltaParam = false;

        $parametros = $request->getParsedBody();
        if (isset($parametros['usuario'], $parametros['clave'])) {
            $listaUsuarios = Usuario::obtenerTodos();
            foreach ($listaUsuarios as $usuario) {
                if ($parametros['usuario'] == $usuario->nombre && $parametros['clave'] == $usuario->clave && $usuario->estado == 'Activo') {

                    $datos = array('usuario' => $usuario->nombre, 'tipo' => $usuario->tipo);
                    $token = AutentificadorJWT::CrearToken($datos);
                    $payload = json_encode(array("mensaje" => "Logueo exitoso de $usuario->tipo", 'jwt' => $token));
                    $existeUsuario = true;
                    $faltaParam = false;
                    break;
                } else if ($parametros['usuario'] == $usuario->nombre && $parametros['clave'] == $usuario->clave && $usuario->estado != 'Activo') {
                    $payload = json_encode(array("mensaje" => "El usuario ingresado no se encuentra Activo"));
                    $existeUsuario = true;
                    $faltaParam = false;
                    break;
                } else {
                    $existeUsuario = false;
                    $faltaParam = false;
                }
            }
        } else {
            $payload = json_encode(array("mensaje" => "Faltan setear credenciales"));
            $faltaParam = true;
        }

        if (!$existeUsuario && !$faltaParam) {
            $payload = json_encode(array("mensaje" => "Logueo invalido, datos erroneos"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
