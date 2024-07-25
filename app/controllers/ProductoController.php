<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{

  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $descripcion = $parametros['descripcion'];
    $tipo = $parametros['tipo'];
    $tiempopreparacion = $parametros['tiempopreparacion'];
    $precio = $parametros['precio'];

    // Creamos el usuario
    $producto = new Producto();
    $producto->descripcion = $descripcion;
    $producto->tipo = $tipo;
    $producto->tiempopreparacion = $tiempopreparacion;
    $producto->precio = $precio;
    $producto->crearProducto();

    $payload = json_encode(array("mensaje" => "Producto creado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  public function TraerTodos($request, $response, $args)
  {
    $lista = Producto::obtenerTodos();
    $payload = json_encode(array("listaProductos" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos producto por su descripcion
    $descripcionProducto = $args['descripcionProducto'];
    $producto = Producto::obtenerProducto($descripcionProducto);
    $payload = json_encode($producto);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id = $args['id'];
    $estado = $parametros['estado'];
    Producto::modificarProducto($id, $estado);

    $payload = json_encode(array("mensaje" => "Estado del producto modificado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $id = $args['id'];
    Producto::borrarProducto($id);

    $payload = json_encode(array("mensaje" => "Producto borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  public function DescargarProductosCSV($request, $response, $args)
  {
    $listaProductos = Producto::obtenerTodos();
    $rutaArchivo = '../ArchivosCSV/productos.csv';

    $archivo = fopen($rutaArchivo, 'w');

    fputcsv($archivo, ['descripcion', 'tipo', 'tiempopreparacion', 'precio']);

    foreach ($listaProductos as $producto) {
      fputcsv($archivo, [
        $producto->descripcion,
        $producto->tipo,
        $producto->tiempopreparacion,
        $producto->precio
      ]);
    }

    fclose($archivo);

    $csv = file_get_contents($rutaArchivo);

    $response->getBody()->write("Los datos descargardos del archivo CSV son los siguientes:\n\n" . $csv);
    return $response
      ->withHeader('Content-Type', 'text/csv')
      ->withHeader('Content-Disposition', 'attachment; filename="productos.csv"');
  }

  public function CargarProductoDesdeCSV($request, $response, $args)
  {
    $rutaArchivo = '../ArchivosCSV/productos.csv';
    $archivo = fopen($rutaArchivo, 'r');

    fgetcsv($archivo);

    while (($datos = fgetcsv($archivo, 1000, ',')) !== FALSE) {
      $descripcion = $datos[0];
      $tipo = $datos[1];
      $tiempopreparacion = $datos[2];
      $precio = $datos[3];

      $producto = new Producto();
      $producto->descripcion = $descripcion;
      $producto->tipo = $tipo;
      $producto->tiempopreparacion = $tiempopreparacion;
      $producto->precio = $precio;
      $producto->crearProducto();
    }

    fclose($archivo);

    $payload = json_encode(array("mensaje" => "Productos cargados desde el archivo CSV con éxito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
}
