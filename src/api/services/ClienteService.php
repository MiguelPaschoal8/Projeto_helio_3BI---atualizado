<?php

namespace Api\Services;

use Api\Models\Cliente;
use Api\DAO\ClienteDAO;
use Api\Http\ErrorResponse;
use stdClass;

/**
 * Camada de regra de negócio da entidade Cliente.
 *
 * Fluxo:
 * Controller -> Service -> DAO -> Banco
 */
class ClienteService
{
    /**
     * DAO responsável pelo acesso aos dados.
     *
     * @var ClienteDAO
     */
    private ClienteDAO $ClienteDAO;

    /**
     * Injeção de dependência.
     *
     * @param ClienteDAO $ClienteDAODependency
     */
    public function __construct(ClienteDAO $ClienteDAODependency)
    {
        error_log("⬆️ ClienteService::__construct()");
        $this->ClienteDAO = $ClienteDAODependency;
    }

    /**
     * Cria um novo Cliente.
     *
     * Regras:
     * - Não permite nome duplicado.
     *
     * @param stdClass $objPHP
     * @return Cliente
     * @throws ErrorResponse
     */
    public function createService(stdClass $objPHP): Cliente
    {
        error_log("🟣 ClienteService::createService()");

        $Cliente = new Cliente();
        $Cliente->setNomeCliente($objPHP->Cliente->nomeCliente);
        $Cliente->setTelefone($objPHP->Cliente->telefone);
        $Cliente->setEmail($objPHP->Cliente->email);
        $Cliente->setCpf($objPHP->Cliente->cpf);

        /**
         * Verifica duplicidade.
         */
        $resultado = $this->ClienteDAO->findByField(
            'nomeCliente',
            $Cliente->getNomeCliente()
        );

        if (count($resultado) > 0) {
            throw new ErrorResponse(
                400,
                "Cliente já existe",
                [
                    "message" =>
                        "O Cliente {$Cliente->getNomeCliente()} já existe"
                ]
            );
        }

        return $this->ClienteDAO->create($Cliente);
    }

    /**
     * Retorna quantidade total.
     *
     * @return int
     */
    public function countService(): int
    {
        error_log("🟣 ClienteService::countService()");
        return $this->ClienteDAO->count();
    }

    /**
     * Lista todos os Clientes.
     *
     * @return array
     */
    public function findAllService(): array
    {
        error_log("🟣 ClienteService::findAllService()");
        return $this->ClienteDAO->findAll();
    }

    /**
     * Busca Cliente por ID.
     *
     * @param int $idCliente
     * @return Cliente|null
     */
    public function findByIdService(int $idCliente): ?Cliente
    {
        error_log("🟣 ClienteService::findByIdService()");

        $Cliente = new Cliente();
        $Cliente->setIdCliente($idCliente);

        return $this->ClienteDAO->findById(
            $Cliente->getIdCliente()
        );
    }

    /**
     * Atualiza Cliente existente.
     *
     * Regras:
     * - O Cliente precisa existir.
     * - Se não existir, lança erro 404.
     *
     * @param int $idCliente
     * @param string $nomeCliente
     * @return bool
     * @throws ErrorResponse
     */
    public function updateService(int $idCliente, string $nomeCliente, string $telefone, string $email, string $cpf): bool
    {
        error_log("🟣 ClienteService::updateService()");

        /**
         * Verifica existência.
         */
        $ClienteExistente = $this->ClienteDAO->findById($idCliente);

        if (!$ClienteExistente) {
            throw new ErrorResponse(
                404,
                "Cliente não encontrado",
                [
                    "message" =>
                        "Não existe Cliente com id {$idCliente}"
                ]
            );
        }

        /**
         * Monta objeto atualizado.
         */
        $Cliente = new Cliente();
        $Cliente->setIdCliente($idCliente);
        $Cliente->setNomeCliente($nomeCliente);
        $Cliente->setTelefone($telefone);
        $Cliente->setEmail($email);
        $Cliente->setCpf($cpf);

        return $this->ClienteDAO->update($Cliente);
    }

    /**
     * Remove Cliente existente.
     *
     * Regras:
     * - O Cliente precisa existir.
     * - Se não existir, lança erro 404.
     *
     * @param int $idCliente
     * @return bool
     * @throws ErrorResponse
     */
    public function deleteService(int $idCliente): bool
    {
        error_log("🟣 ClienteService::deleteService()");

        /**
         * Verifica existência.
         */
        $ClienteExistente = $this->ClienteDAO->findById($idCliente);

        if (!$ClienteExistente) {
            throw new ErrorResponse(
                404,
                "Cliente não encontrado",
                [
                    "message" =>
                        "Não existe Cliente com id {$idCliente}"
                ]
            );
        }

        /**
         * Monta objeto para exclusão.
         */
        $Cliente = new Cliente();
        $Cliente->setIdCliente($idCliente);

        return $this->ClienteDAO->delete($Cliente);
    }
}