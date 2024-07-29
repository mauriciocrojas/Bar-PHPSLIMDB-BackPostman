<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $estado = $parametros['estado'];
    $codigoidentificacion = $parametros['codigoidentificacion'];

    // Creamos el usuario
    $producto = new Mesa();
    $producto->estado = $estado;
    $producto->codigoidentificacion = $codigoidentificacion;

    $producto->crearMesa();

    $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  public function TraerTodos($request, $response, $args)
  {
    $lista = Mesa::obtenerTodos();
    $payload = json_encode(array("listaMesas" => $lista));

    echo "Entré al TraerTodos de Mesas\n";

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos mesa por su id
    $idmesa = $args['id'];
    $mesa = Mesa::obtenerMesa($idmesa);
    $payload = json_encode($mesa);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id = $args['id'];
    $estado = $parametros['estado'];
    Mesa::modificarMesa($id, $estado);

    $payload = json_encode(array("mensaje" => "Estado de la mesa modificado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $id = $args['id'];
    Mesa::borrarMesa($id);

    $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodosSocio($request, $response, $args)
  {
    $lista = Mesa::obtenerTodosSocio();
    $payload = json_encode(array("listaMesas" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function MozaCobraClienteController($request, $response, $args)
  {

    $idmesa = $args['idmesa'];
    Mesa::MozaCobraCliente($idmesa);

    $payload = json_encode(array("mensaje" => "El mozo cobró al cliente"));

    $parametros = LoginController::obtenerParametrosDelToken($request);
    LoginController::GenerarAuditoria($parametros['usuario'], $parametros['tipo'], 'Cobrar mesa', 'Mesa');


    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function SocioCierraMesaController($request, $response, $args)
  {

    $idmesa = $args['idmesa'];
    Mesa::SocioCierraMesa($idmesa);

    $payload = json_encode(array("mensaje" => "El socio cerró la mesa"));

    $parametros = LoginController::obtenerParametrosDelToken($request);
    LoginController::GenerarAuditoria($parametros['usuario'], $parametros['tipo'], 'Cerrar mesa', 'Mesa');

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function MesaMasUsadaController($request, $response, $args)
  {
      $mesaMasUsada = Mesa::MesaMasUsada();
  
      if ($mesaMasUsada) {
          $idmesa = $mesaMasUsada['idmesa'];
          $mensaje = "La mesa más usada fue la: $idmesa";
      } else {
          $mensaje = "No se encontró ninguna mesa.";
      }
  
      $payload = json_encode(array("mensaje" => $mensaje));
  
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
  }
  
}
