<?php

namespace Api\Controllers;

// Importações das classes necessárias
use Psr\Http\Message\ResponseInterface as Response; // Interface para respostas HTTP PSR-7
use Psr\Http\Message\ServerRequestInterface as Request; // Interface para requisições HTTP PSR-7
use Api\Services\PedidoService; // Serviço de negócio para operações com Pedidos

/**
 * Classe PedidoController
 *
 * Responsável por controlar os endpoints da API REST para a entidade Pedido.
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
 * - Acesso via objeto: $objPHP->Pedido->email
 * - Assinaturas em uma única linha
 */
class PedidoController
{
    /**
     * Serviço responsável pelas regras de negócio.
     *
     * @var PedidoService
     */
    private PedidoService $PedidoService;

    /**
     * Construtor com injeção de dependência.
     *
     * @param PedidoService $PedidoService
     */
    public function __construct(PedidoService $PedidoService)
    {
        error_log("⬆️ PedidoController::__construct()");
        $this->PedidoService = $PedidoService;
    }

    /**
     * Cria novo Pedido.
     *
     * Endpoint: POST /api/v1/Pedidos
     *
     * @return Response
     */
    public function createController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 PedidoController::createController()");

        // Lê JSON bruto e converte para stdClass
        $body = $request->getBody()->getContents();
        $objPHP = json_decode($body);

        // Service recebe objeto Pedido
        $resultado = $this->PedidoService->createService($objPHP);

        $resposta = [
            'success' => true,
            'message' => 'Cadastro realizado com sucesso',
            'data' => [
                'Pedidos' => [
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
     * Lista todos os Pedidos.
     *
     * Endpoint: GET /api/v1/Pedidos
     *
     * @return Response
     */
    public function findAllController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 PedidoController::findAllController()");

        $lista = $this->PedidoService->findAll();

        $resposta = [
            'success' => true,
            'message' => 'Executado com sucesso',
            'data' => [
                'Pedidos' => $lista
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
     * Busca Pedido por ID.
     *
     * Endpoint: GET /api/v1/Pedidos/{idPedido}
     *
     * @return Response
     */
    public function findByidController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 PedidoController::findByidController()");

        $id = (int) $args['idPedido'];

        $Pedido = $this->PedidoService->findByIdService($id);

        $resposta = [
            'success' => true,
            'message' => 'Executado com sucesso',
            'data' => [
                'Pedidos' => [
                    $Pedido
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
     * Endpoint: GET /api/v1/Pedidos/count
     *
     * @return Response
     */
    public function countController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 PedidoController::countController()");

        $qtd = $this->PedidoService->countService();

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
     * Atualiza Pedido existente.
     *
     * Endpoint: PUT /api/v1/Pedidos/{idPedido}
     *
     * @return Response
     */
    public function updateController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 PedidoController::updateController()");

        $id = (int) $args['idPedido'];

        // Lê JSON bruto e converte para stdClass
        $body = $request->getBody()->getContents();
        $objPHP = json_decode($body);

        /**
         * CORREÇÃO:
         * Seu Service ainda espera ARRAY no segundo parâmetro:
         * updateService(int $idPedido, array $requestBody)
         *
         * Então convertemos stdClass para array associativo.
         */
        $arrayPHP = json_decode($body, true);

        $resultado = $this->PedidoService->updateService($id, $arrayPHP);

        $resposta = [
            'success' => true,
            'message' => 'Atualizado com sucesso',
            'data' => [
                'Pedidos' => [
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
     * Remove Pedido.
     *
     * Endpoint: DELETE /api/v1/Pedidos/{idPedido}
     *
     * @return Response
     */
    public function deleteController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 PedidoController::deleteController()");

        $id = (int) $args['idPedido'];

        $excluiu = $this->PedidoService->deleteService($id);

        $status = $excluiu ? 200 : 404;
        $mensagem = $excluiu
            ? 'Excluído com sucesso'
            : 'Pedido não encontrado';

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