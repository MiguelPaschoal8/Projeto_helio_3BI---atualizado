<?php

namespace Api\Services;

use Api\DAO\ClienteDAO;
use Api\Models\Cliente;
use Api\DAO\FuncionarioDAO;
use Api\Models\Funcionario;
use Api\DAO\MesaDAO;
use Api\Models\Mesa;
use Api\DAO\PedidoDAO;
use Api\Models\Pedido;
use Api\Http\ErrorResponse;
use stdClass;

/**
 * Camada de regra de negócio da entidade Pedido.
 *
 * Fluxo:
 * Controller -> Service -> DAO -> Banco
 */
class PedidoService
{

    private PedidoDAO $PedidoDAO;

    private ClienteDAO $ClienteDAO;

    private FuncionarioDAO $FuncionarioDAO;

    private MesaDAO $MesaDAO;

    /**
     * Injeção de dependência.
     *
     * @param PedidoDAO $PedidoDAODependency
     * @param ClienteDAO $ClienteDAODependency
     */
    public function __construct(
        PedidoDAO $PedidoDAODependency,
        ClienteDAO $ClienteDAODependency,
        FuncionarioDAO $FuncionarioDAODependency,
        MesaDAO $MesaDAODependency
    ) {
        error_log("⬆️ PedidoService::__construct()");

        $this->PedidoDAO = $PedidoDAODependency;
        $this->ClienteDAO = $ClienteDAODependency;
        $this->FuncionarioDAO = $FuncionarioDAODependency;
        $this->MesaDAO = $MesaDAODependency;
    }

    /**
     * Cria novo Pedido.
     *
     * Regras:
     * - Cliente informado deve existir.
     * - Funcionario informado deve existir.
     * - Email não pode estar duplicado.
     *
     * @param stdClass $jsonPedido
     * @return Pedido
     * @throws ErrorResponse
     */
    public function createService(stdClass $jsonPedido): Pedido
    {
        error_log("🟣 PedidoService::createService()");

        $Pedido = new Pedido();
        /**
         * Cliente informado.
         */

        //echo json_encode($jsonPedido);

        $Cliente = new Cliente();
        $Cliente->setIdCliente($jsonPedido->Pedido->Cliente->idCliente);

        /**
         * Verifica se Cliente existe.
         */
        $ClienteExiste = $this->ClienteDAO->findById($Cliente->getIdCliente());

        if (!$ClienteExiste) {
            throw new ErrorResponse(
                404,
                "Cliente não encontrado",
                [
                    "message" =>
                        "Não existe Cliente com id {$Cliente->getIdCliente()}"
                ]
            );
        }

        $Funcionario = new Funcionario();
        $Funcionario->setIdFuncionario($jsonPedido->Pedido->Funcionario->idFuncionario);

        /**
         * Verifica se Funcionario existe.
         */
        $FuncionarioExiste = $this->FuncionarioDAO->findById($Funcionario->getIdFuncionario());

        if (!$FuncionarioExiste) {
            throw new ErrorResponse(
                404,
                "Funcionario não encontrado",
                [
                    "message" =>
                        "Não existe Funcionario com id {$Funcionario->getIdFuncionario()}"
                ]
            );
        }

        $Mesa = new Mesa();
        $Mesa->setIdMesa($jsonPedido->Pedido->Mesa->idMesa);

        /**
         * Verifica se Mesa existe.
         */
        $MesaExiste = $this->MesaDAO->findById($Mesa->getIdMesa());

        if (!$MesaExiste) {
            throw new ErrorResponse(
                404,
                "Mesa não encontrado",
                [
                    "message" =>
                        "Não existe Mesa com id {$Mesa->getIdMesa()}"
                ]
            );
        }
        /**
         * Pedido
         */
        $Pedido = new Pedido();
        $Pedido->setStatusPedido($jsonPedido->Pedido->statusPedido);
        $Pedido->setdataPedido($jsonPedido->Pedido->dataPedido);
        $Pedido->setCliente($ClienteExiste);
        $Pedido->setFuncionario($FuncionarioExiste);
        $Pedido->setMesa($MesaExiste);


    /** 
     * Salva.
     */
         $idCriado = $this->PedidoDAO->create(
            $Pedido
        );

        $Pedido->setIdPedido($idCriado);

        return $Pedido;

}

    public function findAll(): array
    {
        error_log("🟣 PedidoService::findAll()");
        return $this->PedidoDAO->findAll();
    }

    /**
     * Busca Pedido por ID.
     *
     * @param int $idPedido
     * @return Pedido
     * @throws ErrorResponse
     */
    public function findByIdService(
        int $idPedido
    ): Pedido {
        error_log("🟣 PedidoService::findByIdService()");

        $Pedido = $this->PedidoDAO->findById(
            $idPedido
        );

        if (!$Pedido) {
            throw new ErrorResponse(
                404,
                "Pedido não encontrado",
                [
                    "message" =>
                        "Não existe Pedido com id {$idPedido}"
                ]
            );
        }

        return $Pedido;
    }

    /**
     * Atualiza Pedido existente.
     *
     * Regras:
     * - Pedido deve existir.
     * - Cliente informado deve existir.
     *
     * @param int $idPedido
     * @param array $requestBody
     * @return bool
     * @throws ErrorResponse
     */
    public function updateService(
        int $idPedido,
        array $requestBody
    ): bool {
        error_log("🟣 PedidoService::updateService()");

        /**
         * Verifica Pedido.
         */
        $PedidoExiste =
            $this->PedidoDAO->findById(
                $idPedido
            );

        if (!$PedidoExiste) {
            throw new ErrorResponse(
                404,
                "Pedido não encontrado",
                [
                    "message" =>
                        "Não existe Pedido com id {$idPedido}"
                ]
            );
        }

        $jsonPedido =
            $requestBody['Pedido'];

        /**
         * Verifica Cliente.
         */
        $Cliente = $this->ClienteDAO->findById(
            $jsonPedido['Cliente']['idCliente']
        );

        if (!$Cliente) {
            throw new ErrorResponse(
                404,
                "Cliente não encontrado",
                [
                    "message" =>
                        "Cliente informado não existe"
                ]
            );
        }

                $Funcionario = $this->FuncionarioDAO->findById(
            $jsonPedido['Funcionario']['idFuncionario']
        );

        if (!$Funcionario) {
            throw new ErrorResponse(
                404,
                "Funcionario não encontrado",
                [
                    "message" =>
                        "Funcionario informado não existe"
                ]
            );
        }

        
        $Mesa = $this->MesaDAO->findById(
            $jsonPedido['Mesa']['idMesa']
        );

        if (!$Mesa) {
            throw new ErrorResponse(
                404,
                "Mesa não encontrado",
                [
                    "message" =>
                        "Mesa informado não existe"
                ]
            );
        }

        
        /**
         * Monta objeto atualizado.
         */
        $Pedido = new Pedido();
        $Pedido->setIdPedido(
            $idPedido
        );
        $Pedido->setDataPedido(
            $jsonPedido['dataPedido']
        );
        $Pedido->setStatusPedido(
            $jsonPedido['statusPedido']
        );
        $Pedido->setCliente($Cliente);
        $Pedido->setFuncionario($Funcionario);
        $Pedido->setMesa($Mesa);

        return $this->PedidoDAO->update(
            $Pedido
        );
    }

    /**
     * Remove Pedido existente.
     *
     * Regras:
     * - Pedido deve existir.
     *
     * @param int $idPedido
     * @return bool
     * @throws ErrorResponse
     */
    public function deleteService(
        int $idPedido
    ): bool {
        error_log("🟣 PedidoService::deleteService()");

        $PedidoExiste =
            $this->PedidoDAO->findById(
                $idPedido
            );

        if (!$PedidoExiste) {
            throw new ErrorResponse(
                404,
                "Pedido não encontrado",
                [
                    "message" =>
                        "Não existe Pedido com id {$idPedido}"
                ]
            );
        }

        $Pedido = new Pedido();
        $Pedido->setIdPedido(
            $idPedido
        );

        return $this->PedidoDAO->delete(
            $Pedido
        );
    }

    /**
     * Retorna total de Pedidos.
     *
     * @return int
     */
    public function countService(): int
    {
        error_log("🟣 PedidoService::countService()");
        return $this->PedidoDAO->count();
    }
}