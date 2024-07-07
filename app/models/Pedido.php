<?php

class Pedido
{
    protected $idpedido;
    protected $idmesa;
    protected $estado;
    protected $nombrecliente;
    protected $ubicacionimagen;
    protected $tiempoestimado;
    protected $codigopedido;

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

    public static function GuardarImagenMesa($ubicacionImagen, $idpedido)
    {
        //Insert a la base
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedido SET ubicacionimagen = :ubicacionimagen WHERE idpedido = :idpedido");
        $consulta->bindValue(':ubicacionimagen', $ubicacionImagen, PDO::PARAM_STR);
        $consulta->bindValue(':idpedido', $idpedido, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pe.idpedido IdPedido, pe.idmesa Mesa, pe.nombrecliente Cliente, pe.estado EstadoPedido, 
        pe.tiempoestimado TiempoDePreparacion, pr.descripcion Producto , pp.cantidad Cantidad, pe.ubicacionimagen UbicacionImagen, pe.codigopedido CodigoPedido FROM pedido pe 
        inner join pedidoproducto pp on pe.idpedido = pp.idpedido
        inner join producto pr on pp.idproducto = pr.idproducto");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerTodosSolicitados()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pe.idpedido IdPedido, pe.idmesa Mesa, pe.nombrecliente Cliente, pe.estado EstadoPedido, 
        pe.tiempoestimado TiempoDePreparacion, pr.descripcion Producto , pp.cantidad Cantidad, pe.ubicacionimagen UbicacionImagen, pe.codigopedido CodigoPedido FROM pedido pe 
        inner join pedidoproducto pp on pe.idpedido = pp.idpedido
        inner join producto pr on pp.idproducto = pr.idproducto
        WHERE pe.estado = 'Solicitado'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerTodosTomadosPorMozoYEnPreparacionComida()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pe.idpedido IdPedido, pe.idmesa Mesa, pe.nombrecliente Cliente, pe.estado EstadoPedido, 
        pe.tiempoestimado TiempoDePreparacion, pr.descripcion Producto , pp.cantidad Cantidad, pe.ubicacionimagen UbicacionImagen, pe.codigopedido CodigoPedido FROM pedido pe 
        inner join pedidoproducto pp on pe.idpedido = pp.idpedido
        inner join producto pr on pp.idproducto = pr.idproducto
        WHERE pe.estado in ('Tomado por mozo','En preparacion') AND pr.tipo = 'Comida'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerTodosTomadosPorMozoYEnPreparacionBebida()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pe.idpedido IdPedido, pe.idmesa Mesa, pe.nombrecliente Cliente, pe.estado EstadoPedido, 
        pe.tiempoestimado TiempoDePreparacion, pr.descripcion Producto , pp.cantidad Cantidad, pe.ubicacionimagen UbicacionImagen, pe.codigopedido CodigoPedido FROM pedido pe 
        inner join pedidoproducto pp on pe.idpedido = pp.idpedido
        inner join producto pr on pp.idproducto = pr.idproducto
        WHERE pe.estado in ('Tomado por mozo','En preparacion') AND pr.tipo = 'Bebida'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }
    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pe.idpedido IdPedido, pe.idmesa Mesa, pe.nombrecliente Cliente, pe.estado EstadoPedido, 
        pe.tiempoestimado TiempoDePreparacion, pr.descripcion Producto , pp.cantidad Cantidad, pe.codigopedido CodigoPedido FROM pedido pe 
        inner join pedidoproducto pp on pe.idpedido = pp.idpedido
        inner join producto pr on pp.idproducto = pr.idproducto
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

    public static function TomarPedidoMozo($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET estado = 'Tomado por mozo', codigopedido = :codigo WHERE idpedido = :idpedido");
       
        $codigoPedido = Pedido::generarCodigoAlfanumerico();
       
        $consulta->bindValue(':codigo', $codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idpedido', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $codigoPedido;
    }

    public static function TomarPedidoCocinero($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta(
        "UPDATE pedido SET estado = 'En preparacion', tiempoestimado = (SELECT MAX(pr.tiempopreparacion) 
        FROM pedidoproducto pepr 
        INNER JOIN producto pr ON pepr.idproducto = pr.idproducto
        INNER JOIN pedido pe ON pe.idpedido = pepr.idpedido
        WHERE pr.tipo = 'Comida' AND pepr.idpedido = pe.idpedido
        AND pe.idpedido = :idpedido)
        WHERE idpedido = :idpedido");
    
        $consulta->bindValue(':idpedido', $id, PDO::PARAM_INT);
        $consulta->execute();
    }


    public static function TomarPedidoBartenderCervecero($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta(
        "UPDATE pedido SET estado = 'En preparacion', tiempoestimado = (SELECT MAX(pr.tiempopreparacion) 
        FROM pedidoproducto pepr 
        INNER JOIN producto pr ON pepr.idproducto = pr.idproducto
        INNER JOIN pedido pe ON pe.idpedido = pepr.idpedido
        WHERE pr.tipo = 'Bebida' AND pepr.idpedido = pe.idpedido
        AND pe.idpedido = :idpedido) 
        WHERE idpedido = :idpedido AND tiempoestimado IS NULL");
    
        $consulta->bindValue(':idpedido', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function generarCodigoAlfanumerico() {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($caracteres), 0, 5);
    }
}
