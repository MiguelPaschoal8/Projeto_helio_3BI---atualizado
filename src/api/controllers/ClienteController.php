<?php

namespace Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Api\Services\ClienteService;

/**
 * Classe ClienteController
 *
 * Responsável pelos endpoints REST da entidade Cliente.
 *
 * PADRÃO:
 * - Assinaturas em uma linha
 * - JSON convertido para stdClass
 * - Controller delega regras para Service
 */
class ClienteController
{
    /**
     * Serviço da entidade Cliente.
     *
     * @var ClienteService
     */
    private ClienteService $ClienteService;

    /**
     * Injeção de dependência.
     *
     * @param ClienteService $ClienteServiceDependency
     */
    public function __construct(ClienteService $ClienteServiceDependency)
    {
        error_log("⬆️ ClienteController::__construct()");
        $this->ClienteService = $ClienteServiceDependency;
    }

    /**
     * Cria novo Cliente.
     *
     * Endpoint:
     * POST /api/v1/Clientes
     *
     * JSON esperado:
     * {
     *   "Cliente": {
     *      "nomeCliente": "Administrador"
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
        error_log("🔵 ClienteController::createController()");

        $body = $request->getBody()->getContents();
        $objPHP = json_decode($body);

        $novoCliente = $this->ClienteService->createService($objPHP);

        $resposta = [
            'success' => true,
            'message' => 'Cadastro realizado com sucesso',
            'data' => [
                'Clientes' => [
                    [
                        'idCliente' => $novoCliente->getIdCliente(),
                        'nomeCliente' => $novoCliente->getNomeCliente(),
                        'telefone' => $novoCliente->getTelefone(),
                        'email' => $novoCliente->getEmail(),
                        'cpf' => $novoCliente->getCpf()
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
     * Lista todos os Clientes.
     *
     * Endpoint:
     * GET /api/v1/Clientes
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function findAllController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 ClienteController::findAllController()");

        $Clientes = $this->ClienteService->findAllService();

        $resposta = [
            'success' => true,
            'message' => 'Busca realizada com sucesso',
            'data' => [
                'Clientes' => $Clientes
            ]
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * Busca Cliente por ID.
     *
     * Endpoint:
     * GET /api/v1/Clientes/{idCliente}
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function findByIdController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 ClienteController::findByIdController()");

        $idCliente = (int) $args['idCliente'];
        $Cliente = $this->ClienteService->findByIdService($idCliente);

        $resposta = [
            'success' => true,
            'message' => 'Executado com sucesso',
            'data' => [
                'Clientes' => $Cliente
            ]
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * Atualiza Cliente.
     *
     * Endpoint:
     * PUT /api/v1/Clientes/{idCliente}
     *
     * JSON esperado:
     * {
     *   "Cliente": {
     *      "nomeCliente": "Novo Nome"
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
        error_log("🔵 ClienteController::updateController()");

        $idCliente = (int) $args['idCliente'];

        $body = $request->getBody()->getContents();
        $objPHP = json_decode($body);

        $nomeCliente = $objPHP->Cliente->nomeCliente;
        $telefone = $objPHP->Cliente->telefone;
        $email = $objPHP->Cliente->email;
        $cpf = $objPHP->Cliente->cpf;

        $this->ClienteService->updateService($idCliente, $nomeCliente, $telefone, $email, $cpf);

        $resposta = [
            'success' => true,
            'message' => 'Atualizado com sucesso',
            'data' => [
                'Clientes' => [
                    [
                        'idCliente' => $idCliente,
                        'nomeCliente' => $nomeCliente,
                        'telefone' => $telefone,
                        'email' => $email,
                        'cpf' => $cpf
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
     * Exclui Cliente.
     *
     * Endpoint:
     * DELETE /api/v1/Clientes/{idCliente}
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function deleteController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 ClienteController::deleteController()");

        $idCliente = (int) $args['idCliente'];

        $this->ClienteService->deleteService($idCliente);

        $resposta = [
            'success' => true,
            'message' => 'Excluído com sucesso',
            'data' => [
                'Clientes' => [
                    [
                        'idCliente' => $idCliente
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
     * Conta total de Clientes.
     *
     * Endpoint:
     * GET /api/v1/Clientes/count
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function countController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 ClienteController::countController()");

        $total = $this->ClienteService->countService();

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