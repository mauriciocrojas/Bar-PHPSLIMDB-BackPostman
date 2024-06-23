<?php

class Pedido
{
    public $idpedido;
    public $idmesa;
    //public $idproducto;
    public $estado;
    public $nombrecliente;
    public $nombreimagen;
    public $tiempoestimado;

    protected $productos = [];




    public function crearPedido()
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedido (idmesa, nombrecliente) VALUES (:idmesa, :nombrecliente)");
        $consulta->bindValue(':idmesa', $this->idmesa, PDO::PARAM_INT);
        $consulta->bindValue(':nombrecliente', $this->nombrecliente, PDO::PARAM_STR);
        $consulta->execute();

        $consultaPedidoProducto = $objAccesoDatos->prepararConsulta("INSERT INTO pedidoproducto (idpedido, idproducto, cantidad) VALUES (:idpedido, :idproducto, :cantidad)");

        $idpedido = $objAccesoDatos->obtenerUltimoId();

        foreach ($this->productos as $producto) {
            $consultaPedidoProducto->bindValue(':idpedido', $idpedido, PDO::PARAM_INT);
            $consultaPedidoProducto->bindValue(':idproducto', $producto['idproducto'], PDO::PARAM_INT);
            $consultaPedidoProducto->bindValue(':cantidad', $producto['cantidad'], PDO::PARAM_INT);
            $consultaPedidoProducto->execute();
        }

        return $objAccesoDatos->obtenerUltimoId();
    }




    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pe.idpedido, pe.idmesa, pe.estado, pe.nombrecliente, pe.tiempoestimado, pp.idproducto, pp.cantidad FROM pedido pe inner join pedidoproducto pp 
        on pe.idpedido = pp.idpedido");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pe.idpedido, pe.idmesa, pe.estado, pe.nombrecliente, pe.tiempoestimado, pp.idproducto, pp.cantidad FROM pedido pe inner join pedidoproducto pp 
        on pe.idpedido = pp.idpedido
        WHERE pp.idpedido = :idpedido");
        $consulta->bindValue(':idpedido', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function modificarPedido($id, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET estado = :estado WHERE idpedido = :idpedido");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':idpedido', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarPedido($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET estado = 'Baja' WHERE idpedido = :idpedido");

        $consulta->bindValue(':idpedido', $id, PDO::PARAM_INT);
        $consulta->execute();
    }
}
