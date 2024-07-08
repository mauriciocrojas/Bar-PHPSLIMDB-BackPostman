<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AutLoggerMW
{

    public static function ValidarToken(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try {
            AutentificadorJWT::VerificarToken($token);
            $response = $handler->handle($request);
        } catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el token'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }


    public static function VerificarTipoEmpleadoMozo($request, $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $response = new Response();


        $parametros = (array) AutentificadorJWT::ObtenerData($token);

        echo "Estás intentanto ingresar con los datos de: " . $parametros["usuario"] . ", perfil: " . $parametros["tipo"] . "\n";

        $tipoEmpleado = $parametros['tipo'];

        if ($tipoEmpleado == 'Mozo') {
            $response = $handler->handle($request);
        } else {
            $payload = json_encode(array('mensaje' => 'No sos mozo'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
