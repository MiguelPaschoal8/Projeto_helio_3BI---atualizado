<?php

namespace Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Api\Services\MesaService;

/**
 * Classe MesaController
 *
 * Responsável pelos endpoints REST da entidade Mesa.
 *
 * PADRÃO:
 * - Assinaturas em uma linha
 * - JSON convertido para stdClass
 * - Controller delega regras para Service
 */
class MesaController
{
    /**
     * Serviço da entidade Mesa.
     *
     * @var MesaService
     */
    private MesaService $MesaService;

    /**
     * Injeção de dependência.
     *
     * @param MesaService $MesaServiceDependency
     */
    public function __construct(MesaService $MesaServiceDependency)
    {
        error_log("⬆️ MesaController::__construct()");
        $this->MesaService = $MesaServiceDependency;
    }

    /**
     * Cria novo Mesa.
     *
     * Endpoint:
     * POST /api/v1/Mesas
     *
     * JSON esperado:
     * {
     *   "Mesa": {
     *      "NumeroMesa": "Administrador"
     *   }
     * }
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function createController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 MesaController::createController()");

        $body = $request->getBody()->getContents();
        $objPHP = json_decode($body);

        $novoMesa = $this->MesaService->createService($objPHP);

        $resposta = [
            'success' => true,
            'message' => 'Cadastro realizado com sucesso',
            'data' => [
                'Mesas' => [
                    [
                        'idMesa' => $novoMesa->getIdMesa(),
                        'numeroMesa' => $novoMesa->getNumeroMesa(),
                        'capacidade' => $novoMesa->getCapacidade(),
                        'statusMesa' => $novoMesa->getStatusMesa()
                    ]
                ]
            ]
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    /**
     * Lista todos os Mesas.
     *
     * Endpoint:
     * GET /api/v1/Mesas
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function findAllController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 MesaController::findAllController()");

        $Mesas = $this->MesaService->findAllService();

        $resposta = [
            'success' => true,
            'message' => 'Busca realizada com sucesso',
            'data' => [
                'Mesas' => $Mesas
            ]
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * Busca Mesa por ID.
     *
     * Endpoint:
     * GET /api/v1/Mesas/{idMesa}
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function findByIdController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 MesaController::findByIdController()");

        $idMesa = (int) $args['idMesa'];
        $Mesa = $this->MesaService->findByIdService($idMesa);

        $resposta = [
            'success' => true,
            'message' => 'Executado com sucesso',
            'data' => [
                'Mesas' => $Mesa
            ]
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * Atualiza Mesa.
     *
     * Endpoint:
     * PUT /api/v1/Mesas/{idMesa}
     *
     * JSON esperado:
     * {
     *   "Mesa": {
     *      "NumeroMesa": "Novo Nome"
     *   }
     * }
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function updateController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 MesaController::updateController()");

        $idMesa = (int) $args['idMesa'];

        $body = $request->getBody()->getContents();
        $objPHP = json_decode($body);

        $numeroMesa = $objPHP->Mesa->numeroMesa;
        $capacidade = $objPHP->Mesa->capacidade;
        $statusMesa = $objPHP->Mesa->statusMesa;

        $this->MesaService->updateService($idMesa, $numeroMesa, $capacidade, $statusMesa);

        $resposta = [
            'success' => true,
            'message' => 'Atualizado com sucesso',
            'data' => [
                'Mesas' => [
                    [
                        'idMesa' => $idMesa,
                        'numeroMesa' => $numeroMesa,
                        'capacidade' => $capacidade,
                        'statusMesa' => $statusMesa
                    ]
                ]
            ]
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * Exclui Mesa.
     *
     * Endpoint:
     * DELETE /api/v1/Mesas/{idMesa}
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function deleteController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 MesaController::deleteController()");

        $idMesa = (int) $args['idMesa'];

        $this->MesaService->deleteService($idMesa);

        $resposta = [
            'success' => true,
            'message' => 'Excluído com sucesso',
            'data' => [
                'Mesas' => [
                    [
                        'idMesa' => $idMesa
                    ]
                ]
            ]
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * Conta total de Mesas.
     *
     * Endpoint:
     * GET /api/v1/Mesas/count
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function countController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 MesaController::countController()");

        $total = $this->MesaService->countService();

        $resposta = [
            'success' => true,
            'message' => 'Executado com sucesso',
            'data' => [
                'count' => $total
            ]
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}