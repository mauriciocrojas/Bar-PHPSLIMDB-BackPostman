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
        pe.tiempoestimado TiempoDePreparacion, pr.descripcion Producto , pp.cantidad Cantidad, pp.estadoproducto EstadoProducto, pe.ubicacionimagen UbicacionImagen, pe.codigopedido CodigoPedido FROM pedido pe 
        inner join pedidoproducto pp on pe.idpedido = pp.idpedido
        inner join producto pr on pp.idproducto = pr.idproducto");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerTodosSolicitados()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pe.idpedido IdPedido, pe.idmesa Mesa, pe.nombrecliente Cliente, pe.estado EstadoPedido, 
        pe.tiempoestimado TiempoDePreparacion, pr.descripcion Producto , pp.cantidad Cantidad, pe.codigopedido CodigoPedido FROM pedido pe 
        inner join pedidoproducto pp on pe.idpedido = pp.idpedido
        inner join producto pr on pp.idproducto = pr.idproducto
        WHERE pe.estado = 'Solicitado'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerTodosListosParaServir()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pe.idpedido IdPedido, pe.idmesa Mesa, pe.nombrecliente Cliente, pe.estado EstadoPedido, 
        pe.tiempoestimado TiempoDePreparacion, pr.descripcion Producto , pp.cantidad Cantidad, pe.codigopedido CodigoPedido FROM pedido pe 
        inner join pedidoproducto pp on pe.idpedido = pp.idpedido
        inner join producto pr on pp.idproducto = pr.idproducto
        WHERE pe.estado = 'Listo para servir'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerTodosTomadosPorMozoYEnPreparacionComida()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pe.idpedido IdPedido, pe.idmesa Mesa, pe.estado EstadoPedido, 
        pe.tiempoestimado TiempoDePreparacion, pr.descripcion Producto, pp.cantidad Cantidad, pp.estadoproducto EstadoProducto, pe.codigopedido CodigoPedido FROM pedido pe 
        inner join pedidoproducto pp on pe.idpedido = pp.idpedido
        inner join producto pr on pp.idproducto = pr.idproducto
        WHERE pe.estado in ('Tomado por mozo','En preparacion') AND pr.tipo = 'Comida'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerTodosTomadosPorMozoYEnPreparacionBartender()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pe.idpedido IdPedido, pe.idmesa Mesa, pe.estado EstadoPedido, 
        pe.tiempoestimado TiempoDePreparacion, pr.descripcion Producto, pp.cantidad Cantidad, pp.estadoproducto EstadoProducto, pe.codigopedido CodigoPedido FROM pedido pe 
        inner join pedidoproducto pp on pe.idpedido = pp.idpedido
        inner join producto pr on pp.idproducto = pr.idproducto
        WHERE pe.estado in ('Tomado por mozo','En preparacion') AND pr.tipo = 'Trago'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerTodosTomadosPorMozoYEnPreparacionCervecero()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pe.idpedido IdPedido, pe.idmesa Mesa, pe.estado EstadoPedido, 
        pe.tiempoestimado TiempoDePreparacion, pr.descripcion Producto, pp.cantidad Cantidad, pp.estadoproducto EstadoProducto, pe.codigopedido CodigoPedido FROM pedido pe 
        inner join pedidoproducto pp on pe.idpedido = pp.idpedido
        inner join producto pr on pp.idproducto = pr.idproducto
        WHERE pe.estado in ('Tomado por mozo','En preparacion') AND pr.tipo = 'Cerveza'");
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

        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidoproducto SET estadoproducto = 'Tomado por mozo' WHERE idpedido = :idpedido");
        $consulta->bindValue(':idpedido', $id, PDO::PARAM_INT);
        $consulta->execute();

        $consulta = $objAccesoDato->prepararConsulta("SELECT idmesa FROM pedido WHERE idpedido = :idpedido");
        $consulta->bindValue(':idpedido', $id, PDO::PARAM_INT);
        $consulta->execute();
        $idMesa = $consulta->fetch(PDO::FETCH_COLUMN);

        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET estado = 'Cliente esperando pedido' WHERE idmesa = :idmesa");
        $consulta->bindValue(':idmesa', $idMesa, PDO::PARAM_INT);
        $consulta->execute();

        return $codigoPedido;
    }

    public static function TomarPedidoCocinero($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedido 
            SET estado = 'En preparacion', tiempoestimado = (SELECT MAX(pr.tiempopreparacion) 
        FROM pedidoproducto pepr 
        INNER JOIN producto pr ON pepr.idproducto = pr.idproducto
        INNER JOIN pedido pe ON pe.idpedido = pepr.idpedido
        WHERE pr.tipo = 'Comida' AND pepr.idpedido = pe.idpedido
        AND pe.idpedido = :idpedidosub),
        fechacomienzo = :fechacomienzo
        WHERE idpedido = :idpedidomain"
        );

        $consulta->bindValue(':fechacomienzo', date('Y-m-d H:i'), PDO::PARAM_STR);
        $consulta->bindValue(':idpedidosub', $id, PDO::PARAM_INT);
        $consulta->bindValue(':idpedidomain', $id, PDO::PARAM_INT);
        $consulta->execute();

        $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedidoproducto pp
            INNER JOIN producto p ON pp.idproducto = p.idproducto
            SET pp.estadoproducto = 'En preparacion',
                pp.tiempoestimado = (
                    SELECT MAX(pr.tiempopreparacion) 
                    FROM pedidoproducto pepr 
                    INNER JOIN producto pr ON pepr.idproducto = pr.idproducto
                    INNER JOIN pedido pe ON pe.idpedido = pepr.idpedido
                    WHERE pr.tipo = 'Comida' 
                    AND pepr.idpedido = pe.idpedido
                    AND pe.idpedido = :idpedidosub
                ),
                fechacomienzo = :fechacomienzo
            WHERE p.tipo = 'Comida' AND pp.idpedido = :idpedidomain"
        );

        $consulta->bindValue(':fechacomienzo', date('Y-m-d H:i'), PDO::PARAM_STR);
        $consulta->bindValue(':idpedidosub', $id, PDO::PARAM_INT);
        $consulta->bindValue(':idpedidomain', $id, PDO::PARAM_INT);
        $consulta->execute();
    }


    public static function TomarPedidoBartender($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedido SET estado = 'En preparacion', tiempoestimado = (SELECT MAX(pr.tiempopreparacion) 
        FROM pedidoproducto pepr 
        INNER JOIN producto pr ON pepr.idproducto = pr.idproducto
        INNER JOIN pedido pe ON pe.idpedido = pepr.idpedido
        WHERE pr.tipo = 'Trago' AND pepr.idpedido = pe.idpedido
        AND pe.idpedido = :idpedidosub),
        fechacomienzo = :fechacomienzo 
        WHERE idpedido = :idpedidomain AND tiempoestimado IS NULL"
        );

        $consulta->bindValue(':fechacomienzo', date('Y-m-d H:i'), PDO::PARAM_STR);
        $consulta->bindValue(':idpedidosub', $id, PDO::PARAM_INT);
        $consulta->bindValue(':idpedidomain', $id, PDO::PARAM_INT);
        $consulta->execute();

        $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedidoproducto pp
            INNER JOIN producto p ON pp.idproducto = p.idproducto
            SET pp.estadoproducto = 'En preparacion',
                pp.tiempoestimado = (
                    SELECT MAX(pr.tiempopreparacion) 
                    FROM pedidoproducto pepr 
                    INNER JOIN producto pr ON pepr.idproducto = pr.idproducto
                    INNER JOIN pedido pe ON pe.idpedido = pepr.idpedido
                    WHERE pr.tipo = 'Trago' 
                    AND pepr.idpedido = pe.idpedido
                    AND pe.idpedido = :idpedidosub
                ),
                fechacomienzo = :fechacomienzo
            WHERE p.tipo = 'Trago' AND pp.idpedido = :idpedidomain"
        );

        $consulta->bindValue(':fechacomienzo', date('Y-m-d H:i'), PDO::PARAM_STR);
        $consulta->bindValue(':idpedidosub', $id, PDO::PARAM_INT);
        $consulta->bindValue(':idpedidomain', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function TomarPedidoCervecero($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedido
             SET estado = 'En preparacion', tiempoestimado = (SELECT MAX(pr.tiempopreparacion) 
        FROM pedidoproducto pepr 
        INNER JOIN producto pr ON pepr.idproducto = pr.idproducto
        INNER JOIN pedido pe ON pe.idpedido = pepr.idpedido
        WHERE pr.tipo = 'Cerveza' AND pepr.idpedido = pe.idpedido
        AND pe.idpedido = :idpedidosub),
        fechacomienzo = :fechacomienzo
        WHERE idpedido = :idpedidomain AND tiempoestimado IS NULL"
        );

        $consulta->bindValue(':fechacomienzo', date('Y-m-d H:i'), PDO::PARAM_STR);
        $consulta->bindValue(':idpedidosub', $id, PDO::PARAM_INT);
        $consulta->bindValue(':idpedidomain', $id, PDO::PARAM_INT);
        $consulta->execute();

        $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedidoproducto pp
            INNER JOIN producto p ON pp.idproducto = p.idproducto
            SET pp.estadoproducto = 'En preparacion',
                pp.tiempoestimado = (
                    SELECT MAX(pr.tiempopreparacion) 
                    FROM pedidoproducto pepr 
                    INNER JOIN producto pr ON pepr.idproducto = pr.idproducto
                    INNER JOIN pedido pe ON pe.idpedido = pepr.idpedido
                    WHERE pr.tipo = 'Cerveza' 
                    AND pepr.idpedido = pe.idpedido
                    AND pe.idpedido = :idpedidosub
                ),
                fechacomienzo = :fechacomienzo
            WHERE p.tipo = 'Cerveza' AND pp.idpedido = :idpedidomain"
        );

        $consulta->bindValue(':fechacomienzo', date('Y-m-d H:i'), PDO::PARAM_STR);
        $consulta->bindValue(':idpedidosub', $id, PDO::PARAM_INT);
        $consulta->bindValue(':idpedidomain', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function generarCodigoAlfanumerico()
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($caracteres), 0, 5);
    }

    public static function obtenerPedidoCliente($numeropedido, $idmesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT tiempoestimado TiempoDePreparacion FROM pedido WHERE codigopedido = :codigopedido AND idmesa = :idmesa");
        $consulta->bindValue(':codigopedido', $numeropedido, PDO::PARAM_STR);
        $consulta->bindValue(':idmesa', $idmesa, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerTodosPedidosSocio()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT idpedido IdPedido, idmesa Mesa, estado EstadoPedido, 
        tiempoestimado Demora FROM pedido");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function EntregarPedidoCocinero($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedido SET estado = 'Listo para servir',
            fechafinalizacion = :fechafinalizacion
        WHERE idpedido = :idpedido"
        );

        $consulta->bindValue(':fechafinalizacion', date('Y-m-d H:i'), PDO::PARAM_STR);
        $consulta->bindValue(':idpedido', $id, PDO::PARAM_INT);
        $consulta->execute();

        $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedidoproducto pp
            INNER JOIN producto p ON pp.idproducto = p.idproducto
            SET pp.estadoproducto = 'Listo para servir',
            fechafinalizacion = :fechafinalizacion
            WHERE p.tipo = 'Comida' AND pp.idpedido = :idpedido"
        );

        $consulta->bindValue(':fechafinalizacion', date('Y-m-d H:i'), PDO::PARAM_STR);
        $consulta->bindValue(':idpedido', $id, PDO::PARAM_INT);
        $consulta->execute();
    }


    public static function EntregarPedidoBartender($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedidoproducto pp
            INNER JOIN producto p ON pp.idproducto = p.idproducto
            SET pp.estadoproducto = 'Listo para servir',
            fechafinalizacion = :fechafinalizacion
            WHERE p.tipo = 'Trago' AND pp.idpedido = :idpedido"
        );

        $consulta->bindValue(':fechafinalizacion', date('Y-m-d H:i'), PDO::PARAM_STR);
        $consulta->bindValue(':idpedido', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function EntregarPedidoCervecero($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedidoproducto pp
            INNER JOIN producto p ON pp.idproducto = p.idproducto
            SET pp.estadoproducto = 'Listo para servir',
            fechafinalizacion = :fechafinalizacion
            WHERE p.tipo = 'Cerveza' AND pp.idpedido = :idpedido"
        );

        $consulta->bindValue(':fechafinalizacion', date('Y-m-d H:i'), PDO::PARAM_STR);
        $consulta->bindValue(':idpedido', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function EntregarPedidoACliente($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedido SET estado = 'Entregado'
        WHERE idpedido = :idpedido"
        );

        $consulta->bindValue(':idpedido', $id, PDO::PARAM_INT);
        $consulta->execute();

        $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedidoproducto 
            SET estadoproducto = 'Entregado'
            WHERE idpedido = :idpedido"
        );
        $consulta->bindValue(':idpedido', $id, PDO::PARAM_INT);
        $consulta->execute();

        $consulta = $objAccesoDato->prepararConsulta("SELECT idmesa FROM pedido WHERE idpedido = :idpedido");
        $consulta->bindValue(':idpedido', $id, PDO::PARAM_INT);
        $consulta->execute();
        $idMesa = $consulta->fetch(PDO::FETCH_COLUMN);

        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa SET estado = 'Cliente comiendo' WHERE idmesa = :idmesa");
        $consulta->bindValue(':idmesa', $idMesa, PDO::PARAM_INT);
        $consulta->execute();
    }

    public function AltaEncuesta($codigoMesa, $codigoPedido, $puntajeRestaurante, $puntajeMesa, $puntajeMozo, $puntajeCocinero, $comentario)
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuesta 
        (codigomesa, codigopedido, puntajerestaurante, puntajemesa, puntajemozo, puntajecocinero, comentario) 
        VALUES 
        (:codigoMesa, :codigoPedido, :puntajeRestaurante, :puntajeMesa, :puntajeMozo, :puntajeCocinero, :comentario)");
        $consulta->bindValue(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
        $consulta->bindValue(':puntajeRestaurante', $puntajeRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeMesa', $puntajeMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeMozo', $puntajeMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeCocinero', $puntajeCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':comentario', $comentario, PDO::PARAM_STR);
        $consulta->execute();


        return $objAccesoDatos->obtenerUltimoId();
    }


    public static function MejoresComentarios()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDato->prepararConsulta(
            "SELECT (puntajeRestaurante + puntajeMesa + puntajeMozo + puntajeCocinero) / 4 AS promedio, comentario
            FROM encuesta
            HAVING promedio >= 8
            ORDER BY promedio DESC
            LIMIT 3;"
        );

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}
