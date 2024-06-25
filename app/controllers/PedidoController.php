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


  //Validar
  public function CargarImagenMesa($request, $response, $args)
  {
    //$parametros = $request->getParsedBody();

    $idpedido = $args['idpedido'];
    $archivosCargados = $request->getUploadedFiles();
    $imagenMesa = $archivosCargados['imagenmesa'];

    // Obtengo el nombre temporal del archivo
    $imagenMesaTmpName = $imagenMesa->getStream()->getMetadata('uri'); 

    // Nuevo nombre archivo
    $nuevoNombreImagen = date("d-m-Y") . ".jpg";

    // Ruta a la que mandaremos el archivo
    $rutaImagen = "../ImagenesMesas2024/" . "IdPedido" . $idpedido . "-" . $nuevoNombreImagen;

    // Guardo el archivo
    $imagenMesa->moveTo($rutaImagen);


    var_dump($imagenMesaTmpName);
    var_dump($imagenMesa->getClientFilename() . date("d-m-Y") . ".jpg");
    var_dump($rutaImagen);




    // Actualizamos el pedido
    // Pedido::GuardarImagenMesa();

    $payload = json_encode(array("mensaje" => "Imagen cargada con exito"));

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
