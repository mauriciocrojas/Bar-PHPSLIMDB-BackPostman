<?php

class Login
{

    public $usuario;
    public $tipoUsuario;
    public $accion;
    public $entidad;
    public $idEntidad;
    public $fechaAccion;


    public function crearAccion()
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO auditoria (accion, entidad, identidad, usuario, tipousuario, fechaaccion) 
        VALUES (:accion, :entidad, :identidad, :usuario, :tipousuario, :fechaaccion)");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':tipousuario', $this->tipoUsuario, PDO::PARAM_STR);
        $consulta->bindValue(':accion', $this->accion, PDO::PARAM_STR);
        $consulta->bindValue(':entidad', $this->entidad, PDO::PARAM_STR);
        $consulta->bindValue(':identidad', $this->idEntidad, PDO::PARAM_INT);
        $consulta->bindValue(':fechaaccion', $this->fechaAccion, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
}
