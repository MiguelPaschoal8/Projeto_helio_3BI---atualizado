<?php

namespace Api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Api\Services\FuncionarioService;

/**
 * Classe FuncionarioController
 *
 * Responsável pelos endpoints REST da entidade Funcionario.
 *
 * PADRÃO:
 * - Assinaturas em uma linha
 * - JSON convertido para stdClass
 * - Controller delega regras para Service
 */
class FuncionarioController
{
    /**
     * Serviço da entidade Funcionario.
     *
     * @var FuncionarioService
     */
    private FuncionarioService $FuncionarioService;

    /**
     * Injeção de dependência.
     *
     * @param FuncionarioService $FuncionarioServiceDependency
     */
    public function __construct(FuncionarioService $FuncionarioServiceDependency)
    {
        error_log("⬆️ FuncionarioController::__construct()");
        $this->FuncionarioService = $FuncionarioServiceDependency;
    }

    /**
     * Cria novo Funcionario.
     *
     * Endpoint:
     * POST /api/v1/Funcionarios
     *
     * JSON esperado:
     * {
     *   "Funcionario": {
     *      "nomeFuncionario": "Administrador"
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
        error_log("🔵 FuncionarioController::createController()");

        $body = $request->getBody()->getContents();
        $objPHP = json_decode($body);

        $novoFuncionario = $this->FuncionarioService->createService($objPHP);

        $resposta = [
            'success' => true,
            'message' => 'Cadastro realizado com sucesso',
            'data' => [
                'Funcionarios' => [
                    [
                        'idFuncionario' => $novoFuncionario->getIdFuncionario(),
                        'nomeFuncionario' => $novoFuncionario->getNomeFuncionario(),
                        'salario' => $novoFuncionario->getSalario(),
                        'Senha' => $novoFuncionario->getSenha(),
                        'Cargo' => $novoFuncionario->getCargo()
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
     * Lista todos os Funcionarios.
     *
     * Endpoint:
     * GET /api/v1/Funcionarios
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function findAllController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 FuncionarioController::findAllController()");

        $Funcionarios = $this->FuncionarioService->findAllService();

        $resposta = [
            'success' => true,
            'message' => 'Busca realizada com sucesso',
            'data' => [
                'Funcionarios' => $Funcionarios
            ]
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * Busca Funcionario por ID.
     *
     * Endpoint:
     * GET /api/v1/Funcionarios/{idFuncionario}
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function findByIdController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 FuncionarioController::findByIdController()");

        $idFuncionario = (int) $args['idFuncionario'];
        $Funcionario = $this->FuncionarioService->findByIdService($idFuncionario);

        $resposta = [
            'success' => true,
            'message' => 'Executado com sucesso',
            'data' => [
                'Funcionarios' => $Funcionario
            ]
        ];

        $response->getBody()->write(json_encode($resposta));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    /**
     * Atualiza Funcionario.
     *
     * Endpoint:
     * PUT /api/v1/Funcionarios/{idFuncionario}
     *
     * JSON esperado:
     * {
     *   "Funcionario": {
     *      "nomeFuncionario": "Novo Nome"
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
        error_log("🔵 FuncionarioController::updateController()");

        $idFuncionario = (int) $args['idFuncionario'];

        $body = $request->getBody()->getContents();
        $objPHP = json_decode($body);

        $nomeFuncionario = $objPHP->Funcionario->nomeFuncionario;
        $salario = $objPHP->Funcionario->salario;
        $senha = $objPHP->Funcionario->senha;
        $Cargo = $objPHP->Funcionario->Cargo;

        $this->FuncionarioService->updateService($idFuncionario, $nomeFuncionario, $salario, $senha, $Cargo);

        $resposta = [
            'success' => true,
            'message' => 'Atualizado com sucesso',
            'data' => [
                'Funcionarios' => [
                    [
                        'idFuncionario' => $idFuncionario,
                        'nomeFuncionario' => $nomeFuncionario,
                        'salario' => $salario,
                        'senha' => $senha,
                        'Cargo' => $Cargo
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
     * Exclui Funcionario.
     *
     * Endpoint:
     * DELETE /api/v1/Funcionarios/{idFuncionario}
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function deleteController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 FuncionarioController::deleteController()");

        $idFuncionario = (int) $args['idFuncionario'];

        $this->FuncionarioService->deleteService($idFuncionario);

        $resposta = [
            'success' => true,
            'message' => 'Excluído com sucesso',
            'data' => [
                'Funcionarios' => [
                    [
                        'idFuncionario' => $idFuncionario
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
     * Conta total de Funcionarios.
     *
     * Endpoint:
     * GET /api/v1/Funcionarios/count
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function countController(Request $request, Response $response, array $args): Response
    {
        error_log("🔵 FuncionarioController::countController()");

        $total = $this->FuncionarioService->countService();

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