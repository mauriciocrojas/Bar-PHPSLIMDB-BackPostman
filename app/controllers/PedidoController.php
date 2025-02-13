<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
require_once './controllers/LoginController.php';


class PedidoController extends Pedido implements IApiUsable
{

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $idmesa = $parametros['idmesa'];
    $nombrecliente = $parametros['nombrecliente'];
    $productos = json_decode($parametros['productos'], true);

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

    $nuevoNombreImagen = date("d-m-Y") . ".jpg";

    $rutaImagen = "../ImagenesMesas2024/" . "IdPedido" . $idpedido . "_" . $nuevoNombreImagen;

    $imagenMesa->moveTo($rutaImagen);

    Pedido::GuardarImagenMesa($rutaImagen, $idpedido);

    $payload = json_encode(array("mensaje" => "Imagen cargada con exito, guardada en el servidor, y su ubicacion en la base de datos"));

    $parametros = LoginController::obtenerParametrosDelToken($request);
    LoginController::GenerarAuditoria($parametros['usuario'], $parametros['tipo'], 'Cargar imagen', 'Pedido', $idpedido);

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

    $listaOk = [];

    foreach ($lista as $pedido) {
      $idPedido = $pedido['IdPedido'];

      if (!isset($listaOk[$idPedido])) {
        $listaOk[$idPedido] = [
          'IdPedido' => $idPedido,
          'Mesa' => $pedido['Mesa'],
          'Cliente' => $pedido['Cliente'],
          'EstadoPedido' => $pedido['EstadoPedido'],
          'Productos' => ''
        ];
      }

      $listaOk[$idPedido]['Productos'] .= $pedido['Producto'] . ' (' . $pedido['Cantidad'] . '). ';
    }

    $payload = json_encode(array("PendientesMozo" => array_values($listaOk)));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodosListos($request, $response, $args)
  {
    $lista = Pedido::obtenerTodosListosParaServir();
    
    $listaOk = [];

    foreach ($lista as $pedido) {
      $idPedido = $pedido['IdPedido'];

      if (!isset($listaOk[$idPedido])) {
        $listaOk[$idPedido] = [
          'IdPedido' => $idPedido,
          'Mesa' => $pedido['Mesa'],
          'Cliente' => $pedido['Cliente'],
          'EstadoPedido' => $pedido['EstadoPedido'],
          'Productos' => ''
        ];
      }

      $listaOk[$idPedido]['Productos'] .= $pedido['Producto'] . ' (' . $pedido['Cantidad'] . '). ';
    }

    $payload = json_encode(array("ListosParaServir" => array_values($listaOk)));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerPendientesCocinero($request, $response, $args)
  {
    $lista = Pedido::obtenerPendientesCocinero();

    $listaOk = [];

    foreach ($lista as $pedido) {
      $idPedido = $pedido['IdPedido'];

      if (!isset($listaOk[$idPedido])) {
        $listaOk[$idPedido] = [
          'IdPedido' => $idPedido,
          'Mesa' => $pedido['Mesa'],
          'EstadoPedido' => $pedido['EstadoPedido'],
          'Productos' => ''
        ];
      }

      $listaOk[$idPedido]['Productos'] .= $pedido['Producto'] . ' (' . $pedido['Cantidad'] . '). ';
    }

    $payload = json_encode(array("PendientesCocinero" => array_values($listaOk)));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }



  public function TraerPendientesBartender($request, $response, $args)
  {
    $lista = Pedido::ObtenerPendientesBartender();
   
    $listaOk = [];

    foreach ($lista as $pedido) {
      $idPedido = $pedido['IdPedido'];

      if (!isset($listaOk[$idPedido])) {
        $listaOk[$idPedido] = [
          'IdPedido' => $idPedido,
          'Mesa' => $pedido['Mesa'],
          'EstadoPedido' => $pedido['EstadoPedido'],
          'Productos' => ''
        ];
      }

      $listaOk[$idPedido]['Productos'] .= $pedido['Producto'] . ' (' . $pedido['Cantidad'] . '). ';
    }

    $payload = json_encode(array("PendientesBartender" => array_values($listaOk)));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerPendientesCervecero($request, $response, $args)
  {
    $lista = Pedido::ObtenerPendientesCervecero();
    $listaOk = [];

    foreach ($lista as $pedido) {
      $idPedido = $pedido['IdPedido'];

      if (!isset($listaOk[$idPedido])) {
        $listaOk[$idPedido] = [
          'IdPedido' => $idPedido,
          'Mesa' => $pedido['Mesa'],
          'EstadoPedido' => $pedido['EstadoPedido'],
          'Productos' => ''
        ];
      }

      $listaOk[$idPedido]['Productos'] .= $pedido['Producto'] . ' (' . $pedido['Cantidad'] . '). ';
    }

    $payload = json_encode(array("PendientesCervecero" => array_values($listaOk)));

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

    $parametros = LoginController::obtenerParametrosDelToken($request);
    LoginController::GenerarAuditoria($parametros['usuario'], $parametros['tipo'], 'Tomar pedido', 'Pedido', $id);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TomarPedidoCocineroController($request, $response, $args)
  {

    $id = $args['id'];
    Pedido::TomarPedidoCocinero($id);

    $payload = json_encode(array("mensaje" => "Pedido tomado por el cocinero, actualizado su tiempo de preparacion y estado, pedido id: $id"));

    $parametros = LoginController::obtenerParametrosDelToken($request);
    LoginController::GenerarAuditoria($parametros['usuario'], $parametros['tipo'], 'Tomar pedido', 'Pedido', $id);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TomarPedidoBartenderController($request, $response, $args)
  {

    $id = $args['id'];
    Pedido::TomarPedidoBartender($id);

    $payload = json_encode(array("mensaje" => "Pedido tomado por el bartender, actualizado su tiempo de preparacion y estado, pedido id: $id"));

    $parametros = LoginController::obtenerParametrosDelToken($request);
    LoginController::GenerarAuditoria($parametros['usuario'], $parametros['tipo'], 'Tomar pedido', 'Pedido', $id);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TomarPedidoCerveceroController($request, $response, $args)
  {

    $id = $args['id'];
    Pedido::TomarPedidoCervecero($id);

    $payload = json_encode(array("mensaje" => "Pedido tomado por el cervecero, actualizado su tiempo de preparacion y estado, pedido id: $id"));

    $parametros = LoginController::obtenerParametrosDelToken($request);
    LoginController::GenerarAuditoria($parametros['usuario'], $parametros['tipo'], 'Tomar pedido', 'Pedido', $id);

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

  public function TraerTodosPedidosDemoradosSocio($request, $response, $args)
  {
    $lista = Pedido::obtenerTodosPedidosDemoradosSocio();
    $payload = json_encode(array("listaPedidosDemorados" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodosProductosDemoradosSocio($request, $response, $args)
  {
    $lista = Pedido::obtenerTodosProductosDemoradosSocio();
    $payload = json_encode(array("listaProductosDemorados" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }



  public function EntregarPedidoBartenderController($request, $response, $args)
  {

    $id = $args['id'];
    Pedido::EntregarPedidoBartender($id);

    $payload = json_encode(array("mensaje" => "El bartender dejó el trago listo para servir"));

    $parametros = LoginController::obtenerParametrosDelToken($request);
    LoginController::GenerarAuditoria($parametros['usuario'], $parametros['tipo'], 'Entregar pedido', 'Pedido', $id);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function EntregarPedidoCerveceroController($request, $response, $args)
  {

    $id = $args['id'];
    Pedido::EntregarPedidoCervecero($id);

    $payload = json_encode(array("mensaje" => "El cervecero dejó la cerveza lista para servir"));

    $parametros = LoginController::obtenerParametrosDelToken($request);
    LoginController::GenerarAuditoria($parametros['usuario'], $parametros['tipo'], 'Entregar pedido', 'Pedido', $id);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function EntregarPedidoCocineroController($request, $response, $args)
  {

    $id = $args['id'];
    Pedido::EntregarPedidoCocinero($id);

    $payload = json_encode(array("mensaje" => "El cocinero dejó la comida lista para servir"));

    $parametros = LoginController::obtenerParametrosDelToken($request);
    LoginController::GenerarAuditoria($parametros['usuario'], $parametros['tipo'], 'Entregar pedido', 'Pedido', $id);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function EntregarPedidoAClienteController($request, $response, $args)
  {

    $id = $args['id'];
    Pedido::EntregarPedidoACliente($id);

    $payload = json_encode(array("mensaje" => "El mozo entregó el pedido al cliente"));

    $parametros = LoginController::obtenerParametrosDelToken($request);
    LoginController::GenerarAuditoria($parametros['usuario'], $parametros['tipo'], 'Entregar pedido', 'Pedido', $id);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  public function AltaEncuestaController($request, $response, $args)
  {
    $parametros = $request->getParsedBody();


    $codigoMesa = $parametros['codigomesa'];
    $codigoPedido = $parametros['codigopedido'];
    $puntajeRestaurante = $parametros['puntajerestaurante'];
    $puntajeMesa = $parametros['puntajeMesa'];
    $puntajeMozo = $parametros['puntajeMozo'];
    $puntajeCocinero = $parametros['puntajeCocinero'];
    $comentario = $parametros['comentarios'];

    Pedido::AltaEncuesta($codigoMesa, $codigoPedido, $puntajeRestaurante, $puntajeMesa, $puntajeMozo, $puntajeCocinero, $comentario);
    $payload = json_encode(array("mensaje" => "Encuesta realizada con éxito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function MejoresComentariosController($request, $response, $args)
  {
    $mejoresComentarios = Pedido::MejoresComentarios();
    $payload = json_encode(array("Mejores comentarios" => $mejoresComentarios));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function GenerarPDF($request, $response, $args)
  {
    $pdf = new FPDF();
    $pdf->AddPage();

    //Logo
    $pdf->Image('../img/logo.png', 170, 10, 30);

    //Titulo
    $pdf->SetFont('Helvetica', 'B', 15);
    $pdf->Ln(20);
    $pdf->Cell(0, 10, 'Listado de pedidos', 0, 1, 'C');
    $pdf->Line(80, 38, 130, 38);
    $pdf->Ln(10);


    //Cabecera tabla
    $pdf->SetX(32);
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->Cell(14, 10, 'IdPed', 1);
    $pdf->Cell(16, 10, 'CodPed', 1);
    $pdf->Cell(11, 10, 'Mesa', 1);
    $pdf->Cell(16, 10, 'Cliente', 1);
    $pdf->Cell(22, 10, 'Tiempo Est', 1);
    $pdf->Cell(24, 10, 'Tiempo Prep', 1);
    $pdf->Cell(35, 10, 'Estado', 1);
    $pdf->Ln();

    $datos = Pedido::obtenerTodosPedidos();

    //Detalle tabla
    $pdf->SetFont('Helvetica', '', 10);
    foreach ($datos as $fila) {
      $pdf->SetX(32);
      $pdf->Cell(14, 10, $fila->IdPedido, 1);
      $pdf->Cell(16, 10, $fila->CodigoPedido, 1);
      $pdf->Cell(11, 10, $fila->Mesa, 1);
      $pdf->Cell(16, 10, $fila->Cliente, 1);
      $pdf->Cell(22, 10, $fila->TiempoEstimado, 1);
      $pdf->Cell(24, 10, $fila->TiempoPreparacion, 1);
      $pdf->Cell(35, 10, $fila->EstadoPedido, 1);
      $pdf->Ln();
    }

    $pdf->Output('D', 'Pedidos.pdf');
  }
}
