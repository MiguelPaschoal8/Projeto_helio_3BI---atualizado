<?php

namespace Api\Services;

use Api\Models\Mesa;
use Api\DAO\MesaDAO;
use Api\Http\ErrorResponse;
use stdClass;

/**
 * Camada de regra de negócio da entidade Mesa.
 *
 * Fluxo:
 * Controller -> Service -Mesa> DAO -> Banco
 */
class MesaService
{
    /**
     * DAO responsável pelo acesso aos dados.
     *
     * @var MesaDAO
     */
    private MesaDAO $MesaDAO;

    /**
     * Injeção de dependência.
     *
     * @param MesaDAO $MesaDAODependency
     */
    public function __construct(MesaDAO $MesaDAODependency)
    {
        error_log("⬆️ MesaService::__construct()");
        $this->MesaDAO = $MesaDAODependency;
    }

    /**
     * Cria um novo Mesa.
     *
     * Regras:
     * - Não permite nome duplicado.
     *
     * @param stdClass $objPHP
     * @return Mesa
     * @throws ErrorResponse
     */
    public function createService(stdClass $objPHP): Mesa
    {
        error_log("🟣 MesaService::createService()");

        $Mesa = new Mesa();
        $Mesa->setNumeroMesa($objPHP->Mesa->numeroMesa);
        $Mesa->setCapacidade($objPHP->Mesa->capacidade);
        $Mesa->setStatusMesa($objPHP->Mesa->statusMesa);

        /**
         * Verifica duplicidade.
         */
        $resultado = $this->MesaDAO->findByField(
            'numeroMesa',
            $Mesa->getNumeroMesa()
        );

        if (count($resultado) > 0) {
            throw new ErrorResponse(
                400,
                "Mesa já existe",
                [
                    "message" =>
                        "O Mesa {$Mesa->getNumeroMesa()} já existe"
                ]
            );
        }

        return $this->MesaDAO->create($Mesa);
    }

    /**
     * Retorna quantidade total.
     *
     * @return int
     */
    public function countService(): int
    {
        error_log("🟣 MesaService::countService()");
        return $this->MesaDAO->count();
    }

    /**
     * Lista todos os Mesas.
     *
     * @return array
     */
    public function findAllService(): array
    {
        error_log("🟣 MesaService::findAllService()");
        return $this->MesaDAO->findAll();
    }

    /**
     * Busca Mesa por ID.
     *
     * @param int $idMesa
     * @return Mesa|null
     */
    public function findByIdService(int $idMesa): ?Mesa
    {
        error_log("🟣 MesaService::findByIdService()");

        $Mesa = new Mesa();
        $Mesa->setIdMesa($idMesa);

        return $this->MesaDAO->findById(
            $Mesa->getIdMesa()
        );
    }

    /**
     * Atualiza Mesa existente.
     *
     * Regras:
     * - O Mesa precisa existir.
     * - Se não existir, lança erro 404.
     *
     * @param int $idMesa
     * @param string $numeroMesa
     * @param string $capacidade
     * @param string $statusMesa
     * @return bool
     * @throws ErrorResponse
     */
    public function updateService(int $idMesa, string $numeroMesa, string $capacidade, string $statusMesa): bool
    {
        error_log("🟣 MesaService::updateService()");

        /**
         * Verifica existência.
         */
        $MesaExistente = $this->MesaDAO->findById($idMesa);

        if (!$MesaExistente) {
            throw new ErrorResponse(
                404,
                "Mesa não encontrado",
                [
                    "message" =>
                        "Não existe Mesa com id {$idMesa}"
                ]
            );
        }

        /**
         * Monta objeto atualizado.
         */
        $Mesa = new Mesa();
        $Mesa->setIdMesa($idMesa);
        $Mesa->setNumeroMesa($numeroMesa);
        $Mesa->setCapacidade($capacidade);
        $Mesa->setStatusMesa($statusMesa);

        return $this->MesaDAO->update($Mesa);
    }

    /**
     * Remove Mesa existente.
     *
     * Regras:
     * - O Mesa precisa existir.
     * - Se não existir, lança erro 404.
     *
     * @param int $idMesa
     * @return bool
     * @throws ErrorResponse
     */
    public function deleteService(int $idMesa): bool
    {
        error_log("🟣 MesaService::deleteService()");

        /**
         * Verifica existência.
         */
        $MesaExistente = $this->MesaDAO->findById($idMesa);

        if (!$MesaExistente) {
            throw new ErrorResponse(
                404,
                "Mesa não encontrado",
                [
                    "message" =>
                        "Não existe Mesa com id {$idMesa}"
                ]
            );
        }

        /**
         * Monta objeto para exclusão.
         */
        $Mesa = new Mesa();
        $Mesa->setIdMesa($idMesa);

        return $this->MesaDAO->delete($Mesa);
    }
}