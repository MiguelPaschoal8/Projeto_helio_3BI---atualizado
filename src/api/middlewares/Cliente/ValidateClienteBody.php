<?php

namespace Api\Middlewares\Cliente;

use Psr\Http\Message\ServerRequestInterface as Request;   // Interface do PSR-7 para requisições HTTP
use Psr\Http\Message\ResponseInterface as Response;       // Interface do PSR-7 para respostas HTTP
use Psr\Http\Server\RequestHandlerInterface as RequestHandler; // Interface que representa o próximo middleware/handler
use Psr\Http\Server\MiddlewareInterface;                  // Interface obrigatória para criar middlewares no PSR-15
use Api\Http\ErrorResponse;                               // Classe personalizada para padronizar erros da aplicação

/**
 * Middleware para validar o corpo de requisições que envolvem a entidade "Cliente".
 *
 * Este middleware intercepta requisições HTTP antes de chegar ao Controller,
 * garantindo que o corpo da requisição (JSON ou form data) contenha os campos
 * obrigatórios para criar ou atualizar um Cliente.
 *
 * @package Api\Middlewares\Cliente
 */
class ValidateClienteBody implements MiddlewareInterface
{
    /**
     * Método principal do middleware.
     *
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     * @throws ErrorResponse
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Lê o JSON bruto enviado no body
        $body = $request->getBody()->getContents();

        // Converte JSON para objeto stdClass
        $objPHP = json_decode($body);

        // -----------------------------------------------------------
        // Validação 1: verificar se o campo principal 'Cliente' existe
        // -----------------------------------------------------------
        if (!isset($objPHP->Cliente)) {
            throw new ErrorResponse(
                httpCode: 400,
                message: "Erro na validação de dados",
                error: [
                    "message" => "O campo 'Cliente' é obrigatório!"
                ]
            );
        }

        // Armazena objeto Cliente
        $Cliente = $objPHP->Cliente;

        // -----------------------------------------------------------
        // Validação 2: verificar se 'nomeCliente' existe e não está vazio
        // -----------------------------------------------------------
        if (!isset($Cliente->nomeCliente) || trim((string) $Cliente->nomeCliente) === "") {
            throw new ErrorResponse(
                httpCode: 400,
                message: "Erro na validação de dados",
                error: [
                    "message" => "O campo 'nomeCliente' é obrigatório!"
                ]
            );
        }

        // -----------------------------------------------------------
        // Se tudo estiver válido, segue fluxo da requisição
        // -----------------------------------------------------------
        return $handler->handle($request);
    }
}