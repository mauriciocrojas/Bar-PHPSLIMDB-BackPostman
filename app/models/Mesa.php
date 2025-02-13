<?php

class Mesa
{
    public $idmesa;
    public $estado;
    public $codigoidentificacion;

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesa (estado, codigoidentificacion) VALUES (:estado, :codigoidentificacion)");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':codigoidentificacion', $this->codigoidentificacion, PDO::PARAM_STR);

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

    public static function modificarMesa($idmesa, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET estado = :estado WHERE idmesa = :idmesa");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':idmesa', $idmesa, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarMesa($idmesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET estado = 'Baja' WHERE idmesa = :idmesa");

        $consulta->bindValue(':idmesa', $idmesa, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function obtenerTodosSocio()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT idmesa, estado, codigoidentificacion FROM mesa");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function MozaCobraCliente($idMesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET estado = 'Cliente pagando' WHERE idmesa = :idmesa");
        $consulta->bindValue(':idmesa', $idMesa, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function SocioCierraMesa($idMesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE mesa 
            SET estado = 'Cerrada' 
            WHERE idmesa = :idmesa"
        );
        $consulta->bindValue(':idmesa', $idMesa, PDO::PARAM_INT);
        $consulta->execute();
    }


    public static function MesaMasUsada()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDato->prepararConsulta(
            "SELECT idmesa
             FROM pedido
             GROUP BY idmesa
             ORDER BY COUNT(*) DESC
             LIMIT 1"
        );

        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
}
