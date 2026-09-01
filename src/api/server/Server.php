<?php

namespace Api\Server;

use Slim\App;                          // Aplicação Slim para gerenciar rotas e middlewares
use Psr\Http\Message\ServerRequestInterface;  // Interface para requisições HTTP
use Api\Http\ErrorResponse;              // Classe personalizada para respostas de erro
use Api\Routes\ClienteRouter;              // Router para rotas de Clientes
use Api\Routes\FuncionarioRouter;        // Router para rotas de funcionários
use Api\Routes\MesaRouter;        // Router para rotas de mesa
use Api\Routes\PedidoRouter;        // Router para rotas de pedido
use Api\Routes\PagamentoRouter;        // Router para rotas de pagamento

class Server
{

    private App $app;

    private ClienteRouter $ClienteRouter;

    private FuncionarioRouter $funcionarioRouter;

    private MesaRouter $MesaRouter;

    private PedidoRouter $PedidoRouter;

    private PagamentoRouter $PagamentoRouter;

    public function __construct(
        App $app,
        ClienteRouter $ClienteRouter,
        FuncionarioRouter $funcionarioRouter,
        MesaRouter $MesaRouter,
        PedidoRouter $PedidoRouter,
        PagamentoRouter $PagamentoRouter
    ) {
        $this->app = $app;
        $this->ClienteRouter = $ClienteRouter;
        $this->funcionarioRouter = $funcionarioRouter;
        $this->MesaRouter = $MesaRouter;
        $this->PedidoRouter = $PedidoRouter;
        $this->PagamentoRouter = $PagamentoRouter;

        $this->setupMiddlewares();      // Configura os middlewares da aplicação
        $this->setupRoutes();           // Configura as rotas da API
        $this->setupErrorHandling();    // Configura o tratamento de erros
    }

    private function setupMiddlewares(): void
    {

        $this->app->addBodyParsingMiddleware();

        $this->app->add(function ($request, $handler) {

            $response = $handler->handle($request);

            return $response

                ->withHeader('Access-Control-Allow-Origin', '*')

                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')

                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        });
    }

    private function setupRoutes(): void
    {

        $this->ClienteRouter->setupRoutes();  // Rotas para /Clientes/*

        $this->funcionarioRouter->setupRoutes();  // Rotas para /funcionarios/*

        $this->MesaRouter->setupRoutes();  // Rotas para /Mesas/*

        $this->PedidoRouter->setupRoutes();// Rotas para /Pedidos/*
        
        $this->PagamentoRouter->setupRoutes();

        $this->app->get('/', function ($request, $response) {

            return $response
                ->withHeader('Location', '/login.html')  // Define o header de redirecionamento
                ->withStatus(302);                        // Define o status HTTP 302
        });

    }

    private function setupErrorHandling(): void
    {

        $errorMiddleware = $this->app->addErrorMiddleware(true, true, true);

        $errorMiddleware->setDefaultErrorHandler(
            function (ServerRequestInterface $request, \Throwable $exception)  {

                  $response = new \Slim\Psr7\Response();  

                $status = 500;

                if ($exception instanceof ErrorResponse) {
                    // Formata resposta para erros esperados
                    $payload = [
                        'success' => false,                           // Indica falha na operação
                        'message' => $exception->getMessage(),        // Mensagem amigável para o usuário
                        'error' => $exception->getError() ?? (object) [], // Detalhes adicionais do erro
                    ];
                    $status = $exception->getHttpCode();              // Status HTTP específico (ex: 404, 400, 403)
                }

                else {

                    $payload = [
                        'success' => false,                           // Indica falha na operação
                        'message' => $exception->getMessage(),        // Mensagem de erro técnica
                        'error' => [
                            'code' => $exception->getCode(),          // Código do erro
                            'sack' => $exception->getTrace(),
                            'file' => $exception->getFile(),          // Arquivo onde ocorreu
                            'line' => $exception->getLine(),          // Linha do erro
                        ],
                    ];

                }

                $response->getBody()->write(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                return $response
                    ->withHeader('Content-Type', 'application/json')  // Indica que resposta é JSON
                    ->withStatus($status);                             // Define o status HTTP
            }
        );
    }

    public function run(): void
    {

        $this->app->run();
    }
}