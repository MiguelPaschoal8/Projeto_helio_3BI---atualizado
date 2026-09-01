<?php

namespace Api\Controllers;

// Importações das classes necessárias
use Psr\Http\Message\ResponseInterface as Response; // Interface para respostas HTTP PSR-7
use Psr\Http\Message\ServerRequestInterface as Request; // Interface para requisições HTTP PSR-7
use Api\Services\PagamentoService; // Serviço de negócio para operações com Pagamentos

/**
 * Classe PagamentoController
 *
 * Responsável por controlar os endpoints da API REST para a entidade Pagamento.
 * Implementa o padrão Controller da arquitetura MVC, atuando como intermediário
 * entre as rotas (Router) e a camada de serviço (Service).
 *
 * FLUXO DE UMA REQUISIÇÃO:
 * 1. Rota chama o método do controller
 * 2. Controller extrai dados da requisição
 * 3. Controller chama métodos do Service
 * 4. Controller formata resposta padronizada
 * 5. Retorna Response com JSON e status HTTP
 *
 * PADRÃO ADOTADO:
 * - Uso de stdClass com json_decode($body)
 * - Acesso via objeto: $objPHP->Pagamento->email
 * - Assinaturas em uma única linha
 */
class PagamentoController
{
    /**
     * Serviço responsável pelas regras de negócio.
     *
     * @var PagamentoService
     */
    private PagamentoService $PagamentoService;

    /**
     * Construtor com injeção de dependência.
     *
     * @param PagamentoService $PagamentoService
     */
    public function __construct(PagamentoService $PagamentoService)
    {
        error_log("⬆️ PagamentoController::__construct()");
        $this->PagamentoService = $PagamentoService;
    }

    /**
     * Cria novo Pagamento.
     *
     * Endpoint: POST /api/v1/Pagamentos
     *
     * @return Response
     */
    public function createController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 PagamentoController::createController()");

        // Lê JSON bruto e converte para stdClass
        $body = $request->getBody()->getContents();
        $objPHP = json_decode($body);

        // Service recebe objeto Pagamento
        $resultado = $this->PagamentoService->createService($objPHP);

        $resposta = [
            'success' => true,
            'message' => 'Cadastro realizado com sucesso',
            'data' => [
                'Pagamentos' => [
                    $resultado
                ]
            ]
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    /**
     * Lista todos os Pagamentos.
     *
     * Endpoint: GET /api/v1/Pagamentos
     *
     * @return Response
     */
    public function findAllController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 PagamentoController::findAllController()");

        $lista = $this->PagamentoService->findAll();

        $resposta = [
            'success' => true,
            'message' => 'Executado com sucesso',
            'data' => [
                'Pagamentos' => $lista
            ]
        ];

        $response->getBody()->write(
            json_encode($resposta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * Busca Pagamento por ID.
     *
     * Endpoint: GET /api/v1/Pagamentos/{idPagamento}
     *
     * @return Response
     */
    public function findByidController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 PagamentoController::findByidController()");

        $id = (int) $args['idPagamento'];

        $Pagamento = $this->PagamentoService->findByIdService($id);

        $resposta = [
            'success' => true,
            'message' => 'Executado com sucesso',
            'data' => [
                'Pagamentos' => [
                    $Pagamento
                ]
            ]
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * Retorna contagem total.
     *
     * Endpoint: GET /api/v1/Pagamentos/count
     *
     * @return Response
     */
    public function countController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 PagamentoController::countController()");

        $qtd = $this->PagamentoService->countService();

        $resposta = [
            'success' => true,
            'message' => 'Executado com sucesso',
            'data' => [
                'count' => $qtd
            ]
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * Atualiza Pagamento existente.
     *
     * Endpoint: PUT /api/v1/Pagamentos/{idPagamento}
     *
     * @return Response
     */
    public function updateController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 PagamentoController::updateController()");

        $id = (int) $args['idPagamento'];

        // Lê JSON bruto e converte para stdClass
        $body = $request->getBody()->getContents();
        $objPHP = json_decode($body);

        /**
         * CORREÇÃO:
         * Seu Service ainda espera ARRAY no segundo parâmetro:
         * updateService(int $idPagamento, array $requestBody)
         *
         * Então convertemos stdClass para array associativo.
         */
        $arrayPHP = json_decode($body, true);

        $resultado = $this->PagamentoService->updateService($id, $arrayPHP);

        $resposta = [
            'success' => true,
            'message' => 'Atualizado com sucesso',
            'data' => [
                'Pagamentos' => [
                    $resultado
                ]
            ]
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * Remove Pagamento.
     *
     * Endpoint: DELETE /api/v1/Pagamentos/{idPagamento}
     *
     * @return Response
     */
    public function deleteController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 PagamentoController::deleteController()");

        $id = (int) $args['idPagamento'];

        $excluiu = $this->PagamentoService->deleteService($id);

        $status = $excluiu ? 200 : 404;
        $mensagem = $excluiu
            ? 'Excluído com sucesso'
            : 'Pagamento não encontrado';

        $resposta = [
            'success' => $excluiu,
            'message' => $mensagem
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}