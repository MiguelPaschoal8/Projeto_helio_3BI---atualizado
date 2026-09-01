<?php

namespace Api\Services;

use Api\DAO\PedidoDAO;
use Api\Models\Pedido;
use Api\DAO\PagamentoDAO;
use Api\Models\Pagamento;
use Api\Http\ErrorResponse;
use stdClass;

/**
 * Camada de regra de negócio da entidade Pagamento.
 *
 * Fluxo:
 * Controller -> Service -> DAO -> Banco
 */
class PagamentoService
{
    /**
     * DAO de Pagamento.
     *
     * @var PagamentoDAO
     */
    private PagamentoDAO $PagamentoDAO;

    /**
     * DAO de Pedido.
     *
     * @var PedidoDAO
     */
    private PedidoDAO $PedidoDAO;

    /**
     * Injeção de dependência.
     *
     * @param PagamentoDAO $PagamentoDAODependency
     * @param PedidoDAO $PedidoDAODependency
     */
    public function __construct(
        PagamentoDAO $PagamentoDAODependency,
        PedidoDAO $PedidoDAODependency,
    ) {
        error_log("⬆️ PagamentoService::__construct()");

        $this->PagamentoDAO = $PagamentoDAODependency;
        $this->PedidoDAO = $PedidoDAODependency;
    }

    /**
     * Cria novo Pagamento.
     *
     * Regras:
     * - Pedido informado deve existir.
     * - Funcionario informado deve existir.
     * - Email não pode estar duplicado.
     *
     * @param stdClass $jsonPagamento
     * @return Pagamento
     * @throws ErrorResponse
     */
    public function createService(stdClass $jsonPagamento): Pagamento
    {
        error_log("🟣 PagamentoService::createService()");

        $Pagamento = new Pagamento();

        /**
         * Pedido informado.
         */

        //echo json_encode($jsonPagamento);

        $Pedido = new Pedido();
        $Pedido->setIdPedido($jsonPagamento->Pagamento->Pedido->idPedido);

        /**
         * Verifica se Pedido existe.
         */
        $PedidoExiste = $this->PedidoDAO->findById($Pedido->getIdPedido());

        if (!$PedidoExiste) {
            throw new ErrorResponse(
                404,
                "Pedido não encontrado",
                [
                    "message" =>
                        "Não existe Pedido com id {$Pedido->getIdPedido()}"
                ]
            );
        }
        /**
         * Pagamento
         */
        $Pagamento = new Pagamento();
        $Pagamento->setFormaPagamento($jsonPagamento->Pagamento->formaPagamento);
        $Pagamento->setValorPagamento($jsonPagamento->Pagamento->valor);
        $Pagamento->setStatusPagamento($jsonPagamento->Pagamento->statusPagamento);
        $Pagamento->setDataPagamento($jsonPagamento->Pagamento->dataPagamento);
        $Pagamento->setPedido($PedidoExiste);

        /**
         * Verifica email duplicado.
         */

        /**
         * Salva.
         */
        $idCriado = $this->PagamentoDAO->create(
            $Pagamento
        );

        $Pagamento->setIdPagamento($idCriado);

        return $Pagamento;
    }

    public function findAll(): array
    {
        error_log("🟣 PagamentoService::findAll()");
        return $this->PagamentoDAO->findAll();
    }

    /**
     * Busca Pagamento por ID.
     *
     * @param int $idPagamento
     * @return Pagamento
     * @throws ErrorResponse
     */
    public function findByIdService(
        int $idPagamento
    ): Pagamento {
        error_log("🟣 PagamentoService::findByIdService()");

        $Pagamento = $this->PagamentoDAO->findById(
            $idPagamento
        );

        if (!$Pagamento) {
            throw new ErrorResponse(
                404,
                "Pagamento não encontrado",
                [
                    "message" =>
                        "Não existe Pagamento com id {$idPagamento}"
                ]
            );
        }

        return $Pagamento;
    }

    /**
     * Atualiza Pagamento existente.
     *
     * Regras:
     * - Pagamento deve existir.
     * - Pedido informado deve existir.
     *
     * @param int $idPagamento
     * @param array $requestBody
     * @return bool
     * @throws ErrorResponse
     */
    public function updateService(
        int $idPagamento,
        array $requestBody
    ): bool {
        error_log("🟣 PagamentoService::updateService()");

        /**
         * Verifica Pagamento.
         */
        $PagamentoExiste =
            $this->PagamentoDAO->findById(
                $idPagamento
            );

        if (!$PagamentoExiste) {
            throw new ErrorResponse(
                404,
                "Pagamento não encontrado",
                [
                    "message" =>
                        "Não existe Pagamento com id {$idPagamento}"
                ]
            );
        }

        $jsonPagamento =
            $requestBody['Pagamento'];

        /**
         * Verifica Pedido.
         */
        $Pedido = $this->PedidoDAO->findById(
            $jsonPagamento['Pedido']['idPedido']
        );

        if (!$Pedido) {
            throw new ErrorResponse(
                404,
                "Pedido não encontrado",
                [
                    "message" =>
                        "Pedido informado não existe"
                ]
            );
        }


        
        /**
         * Monta objeto atualizado.
         */
        $Pagamento = new Pagamento();
        $Pagamento->setIdPagamento(
            $idPagamento
        );
        $Pagamento->setDataPagamento(
            $jsonPagamento['dataPagamento']
        );
        $Pagamento->setStatusPagamento(
            $jsonPagamento['statusPagamento']
        );
        $Pagamento->setValorPagamento(
            floatval($jsonPagamento['valor'])
        );
        $Pagamento->setFormaPagamento(
            $jsonPagamento['formaPagamento']
        );
        $Pagamento->setPedido($Pedido);

        return $this->PagamentoDAO->update(
            $Pagamento
        );
    }

    /**
     * Remove Pagamento existente.
     *
     * Regras:
     * - Pagamento deve existir.
     *
     * @param int $idPagamento
     * @return bool
     * @throws ErrorResponse
     */
    public function deleteService(
        int $idPagamento
    ): bool {
        error_log("🟣 PagamentoService::deleteService()");

        $PagamentoExiste =
            $this->PagamentoDAO->findById(
                $idPagamento
            );

        if (!$PagamentoExiste) {
            throw new ErrorResponse(
                404,
                "Pagamento não encontrado",
                [
                    "message" =>
                        "Não existe Pagamento com id {$idPagamento}"
                ]
            );
        }

        $Pagamento = new Pagamento();
        $Pagamento->setIdPagamento(
            $idPagamento
        );

        return $this->PagamentoDAO->delete(
            $Pagamento
        );
    }

    /**
     * Retorna total de Pagamentos.
     *
     * @return int
     */
    public function countService(): int
    {
        error_log("🟣 PagamentoService::countService()");
        return $this->PagamentoDAO->count();
    }
}