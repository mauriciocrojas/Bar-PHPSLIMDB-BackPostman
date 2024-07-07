<?php

require_once './models/Usuario.php';

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
                    $payload = json_encode(array("mensaje" => "Logueo exitoso"));
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


    public static function CrearToken()
    {
        $datos = "Lo devuelto por getParsedBody";
        $ahora = time();

        $payload = array(
            'iat' => $ahora,
            'data' => $datos,
            'app' => 'API REST 20240'
        );

        //Codifico a JWT (payload, clave, algoritmo de codificación)
        $token = JWT::encode($payload, 'miClaveSecreta', 'HS256');

        $newResponse = $response->withStatus(200, 'Exito! Json enviado');

        //Genero el json a partir del array
        $newResponse->getBody()->write(json_encode($token));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    public static function VerificarToken()
    {
        $datos = "Lo devuelto por getParsedBody"; //debería ser del header
        $token = $datos['token'];

        $retorno = new stdClass();
        $status = 200;

        try {
            //Decodifico el token recibido
            JWT::decode(
                $token,                   //JWT
                'miClaveSecreta',         //Clave usada en la creación
                ['HS256']                 //Algoritmo de codificación
            );
        } catch (Exception $e) {
            $retorno->mensaje = "Token no valido! ---> " - $e->getMessage();
            $status = 500;
        }

        $newResponse = $response->withStatus($status);

        //Genero el json a partir del array
        $newResponse->getBody()->write(json_encode($retorno));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }
}
