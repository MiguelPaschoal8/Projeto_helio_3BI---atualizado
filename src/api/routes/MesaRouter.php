<?php

namespace Api\Routes;

use Slim\App;
use Api\Controllers\MesaController;
use Api\Middlewares\Mesa\ValidateMesaBody;
use Api\Middlewares\Mesa\ValidateMesaId;

/**
 * Classe responsável por registrar as rotas do recurso Mesa.
 *
 * Endpoints disponíveis:
 * - POST   /Mesas
 * - GET    /Mesas
 * - GET    /Mesas/count
 * - GET    /Mesas/{idMesa}
 * - PUT    /Mesas/{idMesa}
 * - DELETE /Mesas/{idMesa}
 */
class MesaRouter
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
     * Registra todas as rotas relacionadas ao recurso Mesa.
     *
     * Estrutura esperada do JSON:
     *
     * {
     *   "Mesa": {
     *     "nomeMesa": "teste"
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
         * POST /Mesas
         * =========================================================
         * Cria um novo Mesa.
         *
         * Body:
         * {
         *   "Mesa": {
         *     "nomeMesa": "teste"
         *   }
         * }
         *
         * Ordem de execução:
         * 1. ValidateMesaBody
         * 2. MesaController::createController
         */
        $this->app->post(
            '/Mesas',
            [MesaController::class, 'createController']
        )
            ->add(ValidateMesaBody::class);

        /**
         * =========================================================
         * GET /Mesas
         * =========================================================
         * Lista todos os Mesas.
         *
         * Ordem de execução:
         * 1. MesaController::findAllController
         */
        $this->app->get(
            '/Mesas',
            [MesaController::class, 'findAllController']
        );

        /**
         * =========================================================
         * GET /Mesas/count
         * =========================================================
         * Retorna a quantidade total de Mesas.
         *
         * Ordem de execução:
         * 1. MesaController::countController
         */
        $this->app->get(
            '/Mesas/count',
            [MesaController::class, 'countController']
        );

        /**
         * =========================================================
         * GET /Mesas/{idMesa}
         * =========================================================
         * Busca um Mesa pelo ID.
         *
         * Ordem de execução:
         * 1. ValidateMesaId
         * 2. MesaController::findByIdController
         */
        $this->app->get(
            '/Mesas/{idMesa}',
            [MesaController::class, 'findByIdController']
        )
            ->add(ValidateMesaId::class);

        /**
         * =========================================================
         * PUT /Mesas/{idMesa}
         * =========================================================
         * Atualiza um Mesa existente.
         *
         * Body:
         * {
         *   "Mesa": {
         *     "nomeMesa": "teste"
         *   }
         * }
         *
         * Ordem de execução:
         * 1. ValidateMesaId
         * 2. ValidateMesaBody
         * 3. MesaController::updateController
         */
        $this->app->put(
            '/Mesas/{idMesa}',
            [MesaController::class, 'updateController']
        )
            ->add(ValidateMesaBody::class)
            ->add(ValidateMesaId::class);

        /**
         * =========================================================
         * DELETE /Mesas/{idMesa}
         * =========================================================
         * Remove um Mesa pelo ID.
         *
         * Ordem de execução:
         * 1. ValidateMesaId
         * 2. MesaController::deleteController
         */
        $this->app->delete(
            '/Mesas/{idMesa}',
            [MesaController::class, 'deleteController']
        )
            ->add(ValidateMesaId::class);
    }
}