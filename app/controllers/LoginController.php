<?php

require_once './models/Usuario.php';
require_once './models/Login.php';
require_once './utils/AutentificadorJWT.php';
require '../vendor/autoload.php';

class LoginController
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
                    $payload = json_encode(array("mensaje" => "Logueo exitoso de $usuario->tipo, su registro ha sido auditado.", 'JWT de ingreso:' => $token));

                    LoginController::GenerarAuditoria($parametros['usuario'],$usuario->tipo,'Login');

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

    public static function GenerarAuditoria($usuario, $tipoUsuario, $accion){

        $logueo = new Login();
        $logueo->usuario = $usuario;
        $logueo->tipoUsuario = $tipoUsuario;
        $logueo->accion = $accion;
        $logueo->fechaAccion = date('Y-m-d H:i');
        $logueo->crearAccion();

    }

    public static function obtenerParametrosDelToken($request) {
        $token = $request->getHeaderLine('Authorization');

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }
        return (array) AutentificadorJWT::ObtenerData($token);
    }
}
