<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();


    $idmesa = $parametros['idmesa'];
    $nombrecliente = $parametros['nombrecliente'];
    $productos = json_decode($parametros['productos'], true);

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
  }


  public function CargarImagenMesaMozo($request, $response, $args)
  {
    $idpedido = $args['idpedido'];
    $archivosCargados = $request->getUploadedFiles();
    $imagenMesa = $archivosCargados['imagenmesa'];

    // Nuevo nombre archivo
    $nuevoNombreImagen = date("d-m-Y") . ".jpg";

    // Ruta a la que mandaremos el archivo
    $rutaImagen = "../ImagenesMesas2024/" . "IdPedido" . $idpedido . "_" . $nuevoNombreImagen;

    // Guardo el archivo
    $imagenMesa->moveTo($rutaImagen);

    // Guadamos la ubicación de la imagen en la base de datos
    Pedido::GuardarImagenMesa($rutaImagen, $idpedido);

    $payload = json_encode(array("mensaje" => "Imagen cargada con exito, guardada en el servidor, y su ubicacion en la base de datos"));

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

  public function TraerTodosSolicitados($request, $response, $args)
  {
    $lista = Pedido::obtenerTodosSolicitados();
    $payload = json_encode(array("PendientesMozo" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodosListos($request, $response, $args)
  {
    $lista = Pedido::obtenerTodosListosParaServir();
    $payload = json_encode(array("Listos para servir" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodosTomadosPorMozoYEnPreparacionComida($request, $response, $args)
  {
    $lista = Pedido::obtenerTodosTomadosPorMozoYEnPreparacionComida();
    $payload = json_encode(array("PendientesCocinero" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodosTomadosPorMozoYEnPreparacionBartender($request, $response, $args)
  {
    $lista = Pedido::obtenerTodosTomadosPorMozoYEnPreparacionBartender();
    $payload = json_encode(array("PendientesBartender" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodosTomadosPorMozoYEnPreparacionCervecero($request, $response, $args)
  {
    $lista = Pedido::obtenerTodosTomadosPorMozoYEnPreparacionCervecero();
    $payload = json_encode(array("PendientesCervecero" => $lista));

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


  public function TomarPedidoMozoController($request, $response, $args)
  {

    $id = $args['id'];
    $codigoPedido = Pedido::TomarPedidoMozo($id);

    $payload = json_encode(array("mensaje" => "Pedido tomado por el mozo y actualizado su estado, codigo pedido: $codigoPedido, pedido id: $id"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TomarPedidoCocineroController($request, $response, $args)
  {

    $id = $args['id'];
    Pedido::TomarPedidoCocinero($id);

    $payload = json_encode(array("mensaje" => "Pedido tomado por el cocinero, actualizado su tiempo de preparacion y estado, pedido id: $id"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TomarPedidoBartenderController($request, $response, $args)
  {

    $id = $args['id'];
    Pedido::TomarPedidoBartender($id);

    $payload = json_encode(array("mensaje" => "Pedido tomado por el bartender, actualizado su tiempo de preparacion y estado, pedido id: $id"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TomarPedidoCerveceroController($request, $response, $args)
  {

    $id = $args['id'];
    Pedido::TomarPedidoCervecero($id);

    $payload = json_encode(array("mensaje" => "Pedido tomado por el cervecero, actualizado su tiempo de preparacion y estado, pedido id: $id"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerPedidoCliente($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $idmesa = $parametros['idmesa'];
    $numeropedido = $parametros['codigopedido'];

    $pedido = Pedido::obtenerPedidoCliente($numeropedido, $idmesa);

    if ($pedido) {
      $tiempoPreparacion = $pedido['TiempoDePreparacion'];
      $mensaje = "El tiempo de demora de su pedido es de $tiempoPreparacion minutos";
  } else {
      $mensaje = "No se encontró el pedido con el código proporcionado y la mesa especificada.";
  }

  $payload = json_encode(["mensaje" => $mensaje]);
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodosPedidosSocio($request, $response, $args)
  {
    $lista = Pedido::obtenerTodosPedidosSocio();
    $payload = json_encode(array("listaPedidos" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function EntregarPedidoBartenderController($request, $response, $args)
  {

    $id = $args['id'];
    Pedido::EntregarPedidoBartender($id);

    $payload = json_encode(array("mensaje" => "El bartender dejó el trago listo para servir"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function EntregarPedidoCerveceroController($request, $response, $args)
  {

    $id = $args['id'];
    Pedido::EntregarPedidoCervecero($id);

    $payload = json_encode(array("mensaje" => "El cervecero dejó la cerveza lista para servir"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  public function EntregarPedidoCocineroController($request, $response, $args)
  {

    $id = $args['id'];
    Pedido::EntregarPedidoCocinero($id);

    $payload = json_encode(array("mensaje" => "El cocinero dejó la comida lista para servir"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function EntregarPedidoAClienteController($request, $response, $args)
  {

    $id = $args['id'];
    Pedido::EntregarPedidoACliente($id);

    $payload = json_encode(array("mensaje" => "El mozo entregó el pedido al cliente"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


}
