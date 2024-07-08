<?php

use Firebase\JWT\JWT;

require __DIR__ . '/../vendor/autoload.php';


class AutentificadorJWT
{
    private static $claveSecreta = 'ABC123';
    private static $tipoEncriptacion = ['HS256']; // El tipo de encriptación como array
    public static function CrearToken($datos)
    {
        $ahora = time();

        $payload = array(
            'iat' => $ahora,
            'exp' => $ahora + (60000),
            'aud' => self::Aud(),
            'data' => $datos,
            'app' => 'Test JWT'
        );

        return JWT::encode($payload, self::$claveSecreta);
    }

    public static function VerificarToken($token)
    {
        $tipoEncriptacion = ['HS256']; // Debe ser un array

        if (empty($token)) {
            throw new Exception("El token está vacío");
        }

        try {
            //Decodifico el token recibido
            $decodificado = JWT::decode(
                $token,                   //JWT
                self::$claveSecreta,      //Clave usada en la creación
                self::$tipoEncriptacion    // Algoritmo de codificación como array
            );
        } catch (Exception $e) {
            throw $e;
        }
        if ($decodificado->aud !== self::Aud()) {
            throw new Exception("No es el usuario valido");
        }
    }


    public static function ObtenerPayLoad($token)
    {
        $tipoEncriptacion = ['HS256']; // Debe ser un array

        if (empty($token)) {
            throw new Exception("El token está vacío.");
        }
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion    // Algoritmo de codificación como array
        );
    }

    public static function ObtenerData($token)
    {
        $tipoEncriptacion = ['HS256']; // Debe ser un array
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion   // Algoritmo de codificación como array
        )->data;
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}
