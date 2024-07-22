<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');

// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require '../vendor/autoload.php';

require_once './db/AccesoDatos.php';

require_once './models/Login.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';

require_once './middlewares/AuthPedidoMW.php';
require_once './middlewares/AutLoggerMW.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes Usuario
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/{nombre}', \UsuarioController::class . ':TraerUno');
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
  $group->put('/modificarestado/{id}', \UsuarioController::class . ':ModificarUno');
  $group->delete('/eliminarusuario/{id}', \UsuarioController::class . ':BorrarUno');
});

// Routes Producto
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{descripcionProducto}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
  $group->put('/modificarestado/{id}', \ProductoController::class . ':ModificarUno');
  $group->delete('/eliminarproducto/{id}', \ProductoController::class . ':BorrarUno');
});

// Routes Mesa
$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->post('[/]', \MesaController::class . ':CargarUno');
  $group->put('/modificarestado/{id}', \MesaController::class . ':ModificarUno');
  $group->delete('/eliminarmesa/{id}', \MesaController::class . ':BorrarUno');

  $group->get('/estadosmesa', \MesaController::class . ':TraerTodosSocio')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoSocio')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/cobrarcliente/{idmesa}', \MesaController::class . ':MozaCobraClienteController')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoMozo')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/cerrarmesa/{idmesa}', \MesaController::class . ':SocioCierraMesaController')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoSocio')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/mesamasusada', \MesaController::class . ':MesaMasUsadaController')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoSocio')->add(\AutLoggerMW::class . ':ValidarToken');
});


// Routes Pedido
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
  $group->get('/{idpedido}', \PedidoController::class . ':TraerUno');
  $group->post('[/]', \PedidoController::class . ':CargarUno')->add(\AuthPedidoMW::class . ':ValidarParamsPedido');
  $group->put('/modificarestado/{id}', \PedidoController::class . ':ModificarUno');
  $group->delete('/eliminarpedido/{id}', \PedidoController::class . ':BorrarUno');
});

// Routes Log
$app->group('/log', function (RouteCollectorProxy $group) {
  $group->post('[/]', \Login::class . ':ProcesoIngreso');
});



// Routes PedidoAccion
$app->group('/pedidoaccion', function (RouteCollectorProxy $group) {

  $group->get('/pedidosmozo', \PedidoController::class . ':TraerTodosSolicitados')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoMozo')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/tomarpedidomozo/{id}', \PedidoController::class . ':TomarPedidoMozoController')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoMozo')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->post('/cargarimagenmesa/{idpedido}', \PedidoController::class . ':CargarImagenMesaMozo')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoMozo')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/pedidoscocineros', \PedidoController::class . ':TraerTodosTomadosPorMozoYEnPreparacionComida')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoCocinero')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/pedidosbartender', \PedidoController::class . ':TraerTodosTomadosPorMozoYEnPreparacionBartender')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoBartender')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/pedidoscerveceros', \PedidoController::class . ':TraerTodosTomadosPorMozoYEnPreparacionCervecero')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoCervecero')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/tomarpedidoBartender/{id}', \PedidoController::class . ':TomarPedidoBartenderController')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoBartender')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/tomarpedidoCervecero/{id}', \PedidoController::class . ':TomarPedidoCerveceroController')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoCervecero')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/tomarpedidococinero/{id}', \PedidoController::class . ':TomarPedidoCocineroController')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoCocinero')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->post('/traerpedidocliente', \PedidoController::class . ':TraerPedidoCliente');

  $group->get('/traerpedidossocio', \PedidoController::class . ':TraerTodosPedidosSocio')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoSocio')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/traerpedidosdemoradossocio', \PedidoController::class . ':TraerTodosPedidosDemoradosSocio')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoSocio')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/traerproductosdemoradossocio', \PedidoController::class . ':TraerTodosProductosDemoradosSocio')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoSocio')->add(\AutLoggerMW::class . ':ValidarToken');



  $group->get('/entregarpedidoBartender/{id}', \PedidoController::class . ':EntregarPedidoBartenderController')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoBartender')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/entregarpedidoCervecero/{id}', \PedidoController::class . ':EntregarPedidoCerveceroController')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoCervecero')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/entregarpedidococinero/{id}', \PedidoController::class . ':EntregarPedidoCocineroController')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoCocinero')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/pedidoslistos', \PedidoController::class . ':TraerTodosListos')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoMozo')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->get('/entregaracliente/{id}', \PedidoController::class . ':EntregarPedidoAClienteController')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoMozo')->add(\AutLoggerMW::class . ':ValidarToken');

  $group->post('/realizarencuesta', \PedidoController::class . ':AltaEncuestaController');

  $group->get('/mejorescomentarios', \PedidoController::class . ':MejoresComentariosController')
    ->add(\AutLoggerMW::class . ':VerificarTipoEmpleadoSocio')->add(\AutLoggerMW::class . ':ValidarToken');
});


$app->get('[/]', function (Request $request, Response $response) {
  $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));

  $response->getBody()->write($payload);
  return $response->withHeader('Content-Type', 'application/json');
});


$app->run();
