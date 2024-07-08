<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './middlewares/AuthPedidoMW.php';
require_once './models/Login.php';

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
  $group->get('/estadosmesa', \MesaController::class . ':TraerTodosSocio');
  $group->get('/cobrarcliente/{idmesa}', \MesaController::class . ':MozaCobraClienteController');
  $group->get('/cerrarmesa/{idmesa}', \MesaController::class . ':SocioCierraMesaController');
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
  $group->get('/pedidosmozo', \PedidoController::class . ':TraerTodosSolicitados');
  $group->get('/tomarpedidomozo/{id}', \PedidoController::class . ':TomarPedidoMozoController');
  $group->post('/cargarimagenmesa/{idpedido}', \PedidoController::class . ':CargarImagenMesaMozo');
  $group->get('/pedidoscocineros', \PedidoController::class . ':TraerTodosTomadosPorMozoYEnPreparacionComida');
  $group->get('/pedidosbartender', \PedidoController::class . ':TraerTodosTomadosPorMozoYEnPreparacionBartender');
  $group->get('/pedidoscerveceros', \PedidoController::class . ':TraerTodosTomadosPorMozoYEnPreparacionCervecero');
  $group->get('/tomarpedidoBartender/{id}', \PedidoController::class . ':TomarPedidoBartenderController');
  $group->get('/tomarpedidoCervecero/{id}', \PedidoController::class . ':TomarPedidoCerveceroController');
  $group->get('/tomarpedidococinero/{id}', \PedidoController::class . ':TomarPedidoCocineroController');
  $group->post('/traerpedidocliente', \PedidoController::class . ':TraerPedidoCliente');
  $group->get('/', \PedidoController::class . ':TraerTodosPedidosSocio');
  $group->get('/entregarpedidoBartender/{id}', \PedidoController::class . ':EntregarPedidoBartenderController');
  $group->get('/entregarpedidoCervecero/{id}', \PedidoController::class . ':EntregarPedidoCerveceroController');
  $group->get('/entregarpedidococinero/{id}', \PedidoController::class . ':EntregarPedidoCocineroController');
  $group->get('/pedidoslistos', \PedidoController::class . ':TraerTodosListos');
  $group->get('/entregaracliente/{id}', \PedidoController::class . ':EntregarPedidoAClienteController');


});


$app->get('[/]', function (Request $request, Response $response) {
  $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));

  $response->getBody()->write($payload);
  return $response->withHeader('Content-Type', 'application/json');
});


$app->run();
