<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $parametrosOk = false;
    $listaOk = false;

    $productosJson = $parametros['productos'] ?? null;
    $productos = json_decode($productosJson, true); // Convierto a array asociativo

    if (isset($parametros['idmesa']) && isset($parametros['nombrecliente'])) {
      $parametrosOk = true;
    } else {
      echo "Los parámetros del pedido no fueron seteados correctamente";
    }

    if (isset($productosJson)) {
      foreach ($productos as $producto) {
        if (isset($producto['idproducto']) && isset($producto['cantidad'])) {
          $listaOk = true;
        } else {
          $listaOk = false;
          echo "La lista de productos no fue seteada correctamente";
          break;
        }
      }
    } else {
      echo "La lista de productos no fue seteada";
      $listaOk = false;
    }

    if ($parametrosOk && $listaOk) {
      $idmesa = $parametros['idmesa'];
      $nombrecliente = $parametros['nombrecliente'];


      // Creamos el pedido
      $pedido = new Pedido();
      $pedido->idmesa = $idmesa;
      $pedido->nombrecliente = $nombrecliente;
      $pedido->productos = $productos;
      $pedido->crearPedido();

      $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    } else {
      $payload = json_encode(array("mensaje" => "El pedido no fue creado"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  public function TraerTodos($request, $response, $args)
  {
    $lista = Pedido::obtenerTodos();
    $payload = json_encode(array("listaPedidos" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos pedido por su id
    $idpedido = $args['idpedido'];
    $pedido = Pedido::obtenerPedido($idpedido);
    $payload = json_encode($pedido);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id = $args['id'];
    $estado = $parametros['estado'];
    Pedido::modificarPedido($id, $estado);

    $payload = json_encode(array("mensaje" => "Estado del pedido modificado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $id = $args['id'];
    Pedido::borrarPedido($id);

    $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
