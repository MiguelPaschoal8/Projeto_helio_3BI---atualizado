<?php

namespace Api\Routes;

use Slim\App;
use Api\Controllers\ClienteController;
use Api\Middlewares\Cliente\ValidateClienteBody;
use Api\Middlewares\Cliente\ValidateClienteId;

/**
 * Classe responsável por registrar as rotas do recurso Cliente.
 *
 * Endpoints disponíveis:
 * - POST   /Clientes
 * - GET    /Clientes
 * - GET    /Clientes/count
 * - GET    /Clientes/{idCliente}
 * - PUT    /Clientes/{idCliente}
 * - DELETE /Clientes/{idCliente}
 */
class ClienteRouter
{
    /**
     * Instância da aplicação Slim.
     *
     * @var App
     */
    private App $app;

    /**
     * Recebe a instância principal da aplicação.
     *
     * @param App $app Aplicação Slim.
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Registra todas as rotas relacionadas ao recurso Cliente.
     *
     * Estrutura esperada do JSON:
     *
     * {
     *   "Cliente": {
     *     "nomeCliente": "teste"
     *   }
     * }
     *
     * IMPORTANTE:
     * No Slim Framework, os middlewares executam em ordem inversa
     * à ordem em que são adicionados com ->add().
     *
     * O último middleware adicionado executa primeiro.
     *
     * @return void
     */
    public function setupRoutes(): void
    {
        /**
         * =========================================================
         * POST /Clientes
         * =========================================================
         * Cria um novo Cliente.
         *
         * Body:
         * {
         *   "Cliente": {
         *     "nomeCliente": "teste"
         *   }
         * }
         *
         * Ordem de execução:
         * 1. ValidateClienteBody
         * 2. ClienteController::createController
         */
        $this->app->post(
            '/Clientes',
            [ClienteController::class, 'createController']
        )
            ->add(ValidateClienteBody::class);

        /**
         * =========================================================
         * GET /Clientes
         * =========================================================
         * Lista todos os Clientes.
         *
         * Ordem de execução:
         * 1. ClienteController::findAllController
         */
        $this->app->get(
            '/Clientes',
            [ClienteController::class, 'findAllController']
        );

        /**
         * =========================================================
         * GET /Clientes/count
         * =========================================================
         * Retorna a quantidade total de Clientes.
         *
         * Ordem de execução:
         * 1. ClienteController::countController
         */
        $this->app->get(
            '/Clientes/count',
            [ClienteController::class, 'countController']
        );

        /**
         * =========================================================
         * GET /Clientes/{idCliente}
         * =========================================================
         * Busca um Cliente pelo ID.
         *
         * Ordem de execução:
         * 1. ValidateClienteId
         * 2. ClienteController::findByIdController
         */
        $this->app->get(
            '/Clientes/{idCliente}',
            [ClienteController::class, 'findByIdController']
        )
            ->add(ValidateClienteId::class);

        /**
         * =========================================================
         * PUT /Clientes/{idCliente}
         * =========================================================
         * Atualiza um Cliente existente.
         *
         * Body:
         * {
         *   "Cliente": {
         *     "nomeCliente": "teste"
         *   }
         * }
         *
         * Ordem de execução:
         * 1. ValidateClienteId
         * 2. ValidateClienteBody
         * 3. ClienteController::updateController
         */
        $this->app->put(
            '/Clientes/{idCliente}',
            [ClienteController::class, 'updateController']
        )
            ->add(ValidateClienteBody::class)
            ->add(ValidateClienteId::class);

        /**
         * =========================================================
         * DELETE /Clientes/{idCliente}
         * =========================================================
         * Remove um Cliente pelo ID.
         *
         * Ordem de execução:
         * 1. ValidateClienteId
         * 2. ClienteController::deleteController
         */
        $this->app->delete(
            '/Clientes/{idCliente}',
            [ClienteController::class, 'deleteController']
        )
            ->add(ValidateClienteId::class);
    }
}