<?php

require_once './models/Usuario.php';

class Login
{
    public function ProcesoIngreso($request, $response)
    {
        $existeUsuario = false;

        $parametros = $request->getParsedBody();
        if (isset($parametros['usuario'], $parametros['clave'])) {
            $listaUsuarios = Usuario::obtenerTodos();
            foreach ($listaUsuarios as $usuario) {
                if ($parametros['usuario'] == $usuario->nombre && $parametros['clave'] == $usuario->clave && $usuario->estado == 'Activo') {
                    $payload = json_encode(array("mensaje" => "Logueo exitoso"));
                    $existeUsuario = true;
                    break;
                } else if ($parametros['usuario'] == $usuario->nombre && $parametros['clave'] == $usuario->clave && $usuario->estado != 'Activo') {
                    $payload = json_encode(array("mensaje" => "El usuario ingresado no se encuentra Activo"));
                    $existeUsuario = true;
                    break;
                } else {
                    $existeUsuario = false;
                }
            }
        } else {
            $payload = json_encode(array("mensaje" => "Faltan setear credenciales"));
        }

        if (!$existeUsuario) {
            $payload = json_encode(array("mensaje" => "Logueo invalido, datos erroneos"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public static function CrearToken(){
        $datos = "Lo devuelto por getParsedBody";
        $ahora = time();

        $payload = array (
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

    public static function VerificarToken(){
        $datos = "Lo devuelto por getParsedBody";//debería ser del header
        $token = $datos['token'];

        $retorno = new stdClass();
        $status = 200;

        try{
            //Decodifico el token recibido
            JWT::decode (
              $token,                   //JWT
              'miClaveSecreta',         //Clave usada en la creación
              ['HS256']                 //Algoritmo de codificación
            );
        }
        catch(Exception $e){
            $retorno->mensaje = "Token no valido! ---> " - $e->getMessage();
            $status = 500;
        }

        $newResponse = $response->withStatus($status);

        //Genero el json a partir del array
        $newResponse->getBody()->write(json_encode($retorno));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesa (estado, codigoidentificacion) VALUES (:estado, :codigoidentificacion)");
        // $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        // $consulta->bindValue(':codigoidentificacion', $this->codigoidentificacion, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }


    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT idmesa, estado, codigoidentificacion FROM mesa");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesa($idmesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT idmesa, estado, codigoidentificacion FROM mesa WHERE idmesa = :idmesa");
        $consulta->bindValue(':idmesa', $idmesa, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }
}
