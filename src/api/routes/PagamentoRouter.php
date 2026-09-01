<?php

namespace Api\Routes;

use Slim\App;
use Api\Controllers\PagamentoController;
use Api\Middlewares\Pagamento\ValidatePagamentoBody;
use Api\Middlewares\Pagamento\ValidatePagamentoId;

/**
 * Classe responsável por registrar as rotas do recurso Pagamento.
 *
 * Endpoints disponíveis:
 * - POST   /Pagamentos
 * - PUT    /Pagamentos/{idPagamento}
 * - DELETE /Pagamentos/{idPagamento}
 * - GET    /Pagamentos
 * - GET    /Pagamentos/count
 * - GET    /Pagamentos/{idPagamento}
 */
class PagamentoRouter
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
     * @var PagamentoController
     */
    private PagamentoController $controller;

    /**
     * Recebe as dependências necessárias.
     *
     * @param App $app Aplicação Slim.
     * @param PagamentoController $controller Controller de Pagamento.
     */
    public function __construct(App $app, PagamentoController $controller)
    {
        $this->app = $app;
        $this->controller = $controller;
    }

    /**
     * Registra todas as rotas relacionadas ao recurso Pagamento.
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
         * POST /Pagamentos
         * =====================================================
         * Cria um novo Pagamento.
         *
         * Body:
         * {
         *   "Pagamento": {
         *     "nomePagamento": "João",
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
         * 1. ValidatePagamentoBody
         * 2. createController
         */
        $this->app->post(
            '/Pagamentos',
            [$this->controller, 'createController']
        )
        ->add(ValidatePagamentoBody::class);
            
        /**
         * =====================================================
         * PUT /Pagamentos/{idPagamento}
         * =====================================================
         * Atualiza um Pagamento existente.
         *
         * Ordem de execução:
         * 1. ValidatePagamentoId
         * 2. ValidatePagamentoBody
         * 3. updateController
         */
        $this->app->put(
            '/Pagamentos/{idPagamento}',
            [$this->controller, 'updateController']
        )
        ->add(ValidatePagamentoBody::class)
        ->add(ValidatePagamentoId::class);

        /**
         * =====================================================
         * DELETE /Pagamentos/{idPagamento}
         * =====================================================
         * Remove um Pagamento pelo ID.
         *
         * Ordem de execução:
         * 1. ValidatePagamentoId
         * 2. deleteController
         */
        $this->app->delete(
            '/Pagamentos/{idPagamento}',
            [$this->controller, 'deleteController']
        )
        ->add(ValidatePagamentoId::class);

        /**
         * =====================================================
         * GET /Pagamentos
         * =====================================================
         * Lista todos os Pagamentos.
         *
         * Ordem de execução:
         * 1. findAllController
         */
        $this->app->get(
            '/Pagamentos',
            [$this->controller, 'findAllController']
        );

        /**
         * =====================================================
         * GET /Pagamentos/count
         * =====================================================
         * Retorna a quantidade total de Pagamentos.
         *
         * Ordem de execução:
         * 1. countController
         */
        $this->app->get(
            '/Pagamentos/count',
            [$this->controller, 'countController']
        );

        /**
         * =====================================================
         * GET /Pagamentos/{idPagamento}
         * =====================================================
         * Busca um Pagamento pelo ID.
         *
         * Ordem de execução:
         * 1. ValidatePagamentoId
         * 2. findByIdController
         */
        $this->app->get(
            '/Pagamentos/{idPagamento}',
            [$this->controller, 'findByIdController']
        )
        ->add(ValidatePagamentoId::class);
    }
}