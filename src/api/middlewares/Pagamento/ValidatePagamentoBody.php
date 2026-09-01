<?php

namespace Api\Middlewares\Pagamento;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use Api\Http\ErrorResponse;

/**
 * Middleware responsável por validar o corpo (body)
 * das requisições relacionadas ao recurso Pagamento.
 *
 * Objetivo:
 * Garantir que os dados mínimos necessários estejam presentes
 * antes de a requisição chegar ao Controller.
 *
 * Se algum campo estiver inválido ou ausente,
 * uma exceção ErrorResponse será lançada com HTTP 400.
 *
 * Estrutura esperada do JSON:
 *
 * {
 *   "funcionario": {
 *     "nomeFuncionario": "João Silva",
 *     "email": "joao@email.com",
 *     "senha": "123456",
 *     "recebeValeTransporte": 1,
 *     "cargo": {
 *       "idCargo": 2
 *     }
 *   }
 * }
 */
class ValidatePagamentoBody implements MiddlewareInterface
{
    /**
     * Método executado automaticamente pelo Slim
     * antes da requisição seguir para o próximo middleware
     * ou para o Controller.
     *
     * Fluxo:
     * 1. Lê o body enviado na requisição
     * 2. Valida estrutura principal
     * 3. Valida campos obrigatórios
     * 4. Valida regras específicas
     * 5. Libera continuidade da execução
     *
     * @param Request $request Requisição HTTP recebida
     * @param RequestHandler $handler Próximo item da fila
     *
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        /**
         * Lê o JSON bruto enviado no body
         * e converte para objeto stdClass.
         *
         * Exemplo:
         * $objPHP->funcionario->nomeFuncionario
         */
        $body = $request->getBody()->getContents();
        $objPHP = json_decode($body);

        /**
         * Verifica se o objeto principal "funcionario" existe.
         */
        if (!isset($objPHP->Pagamento)) {
            throw new ErrorResponse(
                httpCode: 400,
                message: "Erro na validação de dados",
                error: [
                    "message" => "O campo 'Pagamento' é obrigatório!"
                ]
            );
        }

        /**
         * Armazena os dados internos do funcionário
         * para facilitar leitura do código.
         */
        $Pagamento = $objPHP->Pagamento;

        /**
         * Lista de campos obrigatórios.
         */
        $camposObrigatorios = [
            "valor",
            "statusPagamento",
            "dataPagamento",
            "formaPagamento"
        ];

        /**
         * Percorre cada campo obrigatório.
         */
        foreach ($camposObrigatorios as $campo) {
            if (
                !isset($Pagamento->$campo) ||
                $Pagamento->$campo === "" ||
                $Pagamento->$campo === null
            ) {
                throw new ErrorResponse(
                    httpCode: 400,
                    message: "Erro na validação de dados",
                    error: [
                        "message" => "O campo '{$campo}' é obrigatório!"
                    ]
                );
            }
        }

        /**
         * Valida vínculo com Pedido.
         */
        if (
            !isset($Pagamento->Pedido) ||
            !isset($Pagamento->Pedido->idPedido) ||
            !is_int($Pagamento->Pedido->idPedido) ||
            $Pagamento->Pedido->idPedido <= 0
        ) {
            throw new ErrorResponse(
                httpCode: 400,
                message: "Erro na validação de dados",
                error: [
                    "message" =>
                        "O campo 'idPedido' deve ser um número maior que zero."
                ]
            );
        }
        

        /**
         * Se chegou aqui, está válido.
         */
        return $handler->handle($request);
    }
}