<?php

namespace Api\Routes;

use Slim\App;
use Api\Controllers\FuncionarioController;
use Api\Middlewares\Funcionario\ValidateFuncionarioBody;
use Api\Middlewares\Funcionario\ValidateFuncionarioId;

/**
 * Classe responsável por registrar as rotas do recurso Funcionario.
 *
 * Endpoints disponíveis:
 * - POST   /Funcionarios
 * - GET    /Funcionarios
 * - GET    /Funcionarios/count
 * - GET    /Funcionarios/{idFuncionario}
 * - PUT    /Funcionarios/{idFuncionario}
 * - DELETE /Funcionarios/{idFuncionario}
 */
class FuncionarioRouter
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
     * Registra todas as rotas relacionadas ao recurso Funcionario.
     *
     * Estrutura esperada do JSON:
     *
     * {
     *   "Funcionario": {
     *     "nomeFuncionario": "teste"
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
         * POST /Funcionarios
         * =========================================================
         * Cria um novo Funcionario.
         *
         * Body:
         * {
         *   "Funcionario": {
         *     "nomeFuncionario": "teste"
         *   }
         * }
         *
         * Ordem de execução:
         * 1. ValidateFuncionarioBody
         * 2. FuncionarioController::createController
         */
        $this->app->post(
            '/Funcionarios',
            [FuncionarioController::class, 'createController']
        )
            ->add(ValidateFuncionarioBody::class);

        /**
         * =========================================================
         * GET /Funcionarios
         * =========================================================
         * Lista todos os Funcionarios.
         *
         * Ordem de execução:
         * 1. FuncionarioController::findAllController
         */
        $this->app->get(
            '/Funcionarios',
            [FuncionarioController::class, 'findAllController']
        );

        /**
         * =========================================================
         * GET /Funcionarios/count
         * =========================================================
         * Retorna a quantidade total de Funcionarios.
         *
         * Ordem de execução:
         * 1. FuncionarioController::countController
         */
        $this->app->get(
            '/Funcionarios/count',
            [FuncionarioController::class, 'countController']
        );

        /**
         * =========================================================
         * GET /Funcionarios/{idFuncionario}
         * =========================================================
         * Busca um Funcionario pelo ID.
         *
         * Ordem de execução:
         * 1. ValidateFuncionarioId
         * 2. FuncionarioController::findByIdController
         */
        $this->app->get(
            '/Funcionarios/{idFuncionario}',
            [FuncionarioController::class, 'findByIdController']
        )
            ->add(ValidateFuncionarioId::class);

        /**
         * =========================================================
         * PUT /Funcionarios/{idFuncionario}
         * =========================================================
         * Atualiza um Funcionario existente.
         *
         * Body:
         * {
         *   "Funcionario": {
         *     "nomeFuncionario": "teste"
         *   }
         * }
         *
         * Ordem de execução:
         * 1. ValidateFuncionarioId
         * 2. ValidateFuncionarioBody
         * 3. FuncionarioController::updateController
         */
        $this->app->put(
            '/Funcionarios/{idFuncionario}',
            [FuncionarioController::class, 'updateController']
        )
            ->add(ValidateFuncionarioBody::class)
            ->add(ValidateFuncionarioId::class);

        /**
         * =========================================================
         * DELETE /Funcionarios/{idFuncionario}
         * =========================================================
         * Remove um Funcionario pelo ID.
         *
         * Ordem de execução:
         * 1. ValidateFuncionarioId
         * 2. FuncionarioController::deleteController
         */
        $this->app->delete(
            '/Funcionarios/{idFuncionario}',
            [FuncionarioController::class, 'deleteController']
        )
            ->add(ValidateFuncionarioId::class);
    }
}