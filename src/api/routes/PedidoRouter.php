<?php

namespace Api\Routes;

use Slim\App;
use Api\Controllers\PedidoController;
use Api\Middlewares\Pedido\ValidatePedidoBody;
use Api\Middlewares\Pedido\ValidatePedidoId;

/**
 * Classe responsável por registrar as rotas do recurso Pedido.
 *
 * Endpoints disponíveis:
 * - POST   /Pedidos
 * - PUT    /Pedidos/{idPedido}
 * - DELETE /Pedidos/{idPedido}
 * - GET    /Pedidos
 * - GET    /Pedidos/count
 * - GET    /Pedidos/{idPedido}
 */
class PedidoRouter
{
    /**
     * Instância principal da aplicação Slim.
     *
     * @var App
     */
    private App $app;

    /**
     * Controller responsável pelas regras de negócio.
     *
     * @var PedidoController
     */
    private PedidoController $controller;

    /**
     * Recebe as dependências necessárias.
     *
     * @param App $app Aplicação Slim.
     * @param PedidoController $controller Controller de Pedido.
     */
    public function __construct(App $app, PedidoController $controller)
    {
        $this->app = $app;
        $this->controller = $controller;
    }

    /**
     * Registra todas as rotas relacionadas ao recurso Pedido.
     *
     * IMPORTANTE:
     * No Slim Framework, os middlewares executam
     * na ordem inversa em que são adicionados.
     *
     * Exemplo:
     * ->add(A)->add(B)
     *
     * Ordem real:
     * 1. B
     * 2. A
     * 3. Controller
     *
     * @return void
     */
    public function setupRoutes(): void
    {
        /**
         * =====================================================
         * POST /Pedidos
         * =====================================================
         * Cria um novo Pedido.
         *
         * Body:
         * {
         *   "Pedido": {
         *     "nomePedido": "João",
         *     "email": "joao@email.com",
         *     "senha": "123456",
         *     "recebeValeTransporte": 1,
         *     "cargo": {
         *       "idCargo": 1
         *     }
         *   }
         * }
         *
         * Ordem de execução:
         * 1. ValidatePedidoBody
         * 2. createController
         */
        $this->app->post(
            '/Pedidos',
            [$this->controller, 'createController']
        )
        ->add(ValidatePedidoBody::class);
            
        /**
         * =====================================================
         * PUT /Pedidos/{idPedido}
         * =====================================================
         * Atualiza um Pedido existente.
         *
         * Ordem de execução:
         * 1. ValidatePedidoId
         * 2. ValidatePedidoBody
         * 3. updateController
         */
        $this->app->put(
            '/Pedidos/{idPedido}',
            [$this->controller, 'updateController']
        )
        ->add(ValidatePedidoBody::class)
        ->add(ValidatePedidoId::class);

        /**
         * =====================================================
         * DELETE /Pedidos/{idPedido}
         * =====================================================
         * Remove um Pedido pelo ID.
         *
         * Ordem de execução:
         * 1. ValidatePedidoId
         * 2. deleteController
         */
        $this->app->delete(
            '/Pedidos/{idPedido}',
            [$this->controller, 'deleteController']
        )
        ->add(ValidatePedidoId::class);

        /**
         * =====================================================
         * GET /Pedidos
         * =====================================================
         * Lista todos os Pedidos.
         *
         * Ordem de execução:
         * 1. findAllController
         */
        $this->app->get(
            '/Pedidos',
            [$this->controller, 'findAllController']
        );

        /**
         * =====================================================
         * GET /Pedidos/count
         * =====================================================
         * Retorna a quantidade total de Pedidos.
         *
         * Ordem de execução:
         * 1. countController
         */
        $this->app->get(
            '/Pedidos/count',
            [$this->controller, 'countController']
        );

        /**
         * =====================================================
         * GET /Pedidos/{idPedido}
         * =====================================================
         * Busca um Pedido pelo ID.
         *
         * Ordem de execução:
         * 1. ValidatePedidoId
         * 2. findByIdController
         */
        $this->app->get(
            '/Pedidos/{idPedido}',
            [$this->controller, 'findByIdController']
        )
        ->add(ValidatePedidoId::class);
    }
}