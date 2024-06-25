<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthPedidoMW
{



  public function ValidarParamsPedido (Request $request, RequestHandler $handler)
  {

    $params = $request->getParsedBody();

    $parametrosOk = false;
    $listaOk = false;
    $response = new Response();


    if (isset($params["idmesa"], $params["nombrecliente"])) {
      $parametrosOk = true;
    } else {
      $response->getBody()->write(json_encode(array("error" => "MW: Id Mesa o Nombre Cliente no fueron seteados")));
    }


    if (isset($params['productos'])) {

      $productos = json_decode($params['productos'], true);

      foreach ($productos as $producto) {
        if (isset($producto['idproducto'], $producto['cantidad'])) {
          $listaOk = true;
        } else {
          $listaOk = false;
          $response->getBody()->write(json_encode(array("error" => "MW: La lista de productos no fue seteada correctamente")));
          break;
        }
      }
    } else {
      $response->getBody()->write(json_encode(array("error" => "MW: La lista de productos no fue seteada")));
      $listaOk = false;
    }


    if ($parametrosOk && $listaOk) {
      $response = $handler->handle($request);
    }

    return $response;
  }
}
