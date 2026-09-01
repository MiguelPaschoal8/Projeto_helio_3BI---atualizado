<?php

namespace Api\Services;

use Api\Models\Funcionario;
use Api\DAO\FuncionarioDAO;
use Api\Http\ErrorResponse;
use stdClass;

/**
 * Camada de regra de negócio da entidade Funcionario.
 *
 * Fluxo:
 * Controller -> Service -Funcionario> DAO -> Banco
 */
class FuncionarioService
{
    /**
     * DAO responsável pelo acesso aos dados.
     *
     * @var FuncionarioDAO
     */
    private FuncionarioDAO $FuncionarioDAO;

    /**
     * Injeção de dependência.
     *
     * @param FuncionarioDAO $FuncionarioDAODependency
     */
    public function __construct(FuncionarioDAO $FuncionarioDAODependency)
    {
        error_log("⬆️ FuncionarioService::__construct()");
        $this->FuncionarioDAO = $FuncionarioDAODependency;
    }

    /**
     * Cria um novo Funcionario.
     *
     * Regras:
     * - Não permite nome duplicado.
     *
     * @param stdClass $objPHP
     * @return Funcionario
     * @throws ErrorResponse
     */
    public function createService(stdClass $objPHP): Funcionario
    {
        error_log("🟣 FuncionarioService::createService()");

        $Funcionario = new Funcionario();
        $Funcionario->setNomeFuncionario($objPHP->Funcionario->nomeFuncionario);
        $Funcionario->setSalario($objPHP->Funcionario->salario);
        $Funcionario->setSenha($objPHP->Funcionario->senha);
        $Funcionario->setCargo($objPHP->Funcionario->Cargo);

        /**
         * Verifica duplicidade.
         */
        $resultado = $this->FuncionarioDAO->findByField(
            'nomeFuncionario',
            $Funcionario->getNomeFuncionario()
        );

        if (count($resultado) > 0) {
            throw new ErrorResponse(
                400,
                "Funcionario já existe",
                [
                    "message" =>
                        "O Funcionario {$Funcionario->getNomeFuncionario()} já existe"
                ]
            );
        }

        return $this->FuncionarioDAO->create($Funcionario);
    }

    /**
     * Retorna quantidade total.
     *
     * @return int
     */
    public function countService(): int
    {
        error_log("🟣 FuncionarioService::countService()");
        return $this->FuncionarioDAO->count();
    }

    /**
     * Lista todos os Funcionarios.
     *
     * @return array
     */
    public function findAllService(): array
    {
        error_log("🟣 FuncionarioService::findAllService()");
        return $this->FuncionarioDAO->findAll();
    }

    /**
     * Busca Funcionario por ID.
     *
     * @param int $idFuncionario
     * @return Funcionario|null
     */
    public function findByIdService(int $idFuncionario): ?Funcionario
    {
        error_log("🟣 FuncionarioService::findByIdService()");

        $Funcionario = new Funcionario();
        $Funcionario->setIdFuncionario($idFuncionario);

        return $this->FuncionarioDAO->findById(
            $Funcionario->getIdFuncionario()
        );
    }

    /**
     * Atualiza Funcionario existente.
     *
     * Regras:
     * - O Funcionario precisa existir.
     * - Se não existir, lança erro 404.
     *
     * @param int $idFuncionario
     * @param string $nomeFuncionario
     * @return bool
     * @throws ErrorResponse
     */
    public function updateService(int $idFuncionario, string $nomeFuncionario, string $Salario, string $Senha, string $Cargo): bool
    {
        error_log("🟣 FuncionarioService::updateService()");

        /**
         * Verifica existência.
         */
        $FuncionarioExistente = $this->FuncionarioDAO->findById($idFuncionario);

        if (!$FuncionarioExistente) {
            throw new ErrorResponse(
                404,
                "Funcionario não encontrado",
                [
                    "message" =>
                        "Não existe Funcionario com id {$idFuncionario}"
                ]
            );
        }

        /**
         * Monta objeto atualizado.
         */
        $Funcionario = new Funcionario();
        $Funcionario->setIdFuncionario($idFuncionario);
        $Funcionario->setNomeFuncionario($nomeFuncionario);
        $Funcionario->setSalario($Salario);
        $Funcionario->setSenha($Senha);
        $Funcionario->setCargo($Cargo);

        return $this->FuncionarioDAO->update($Funcionario);
    }

    /**
     * Remove Funcionario existente.
     *
     * Regras:
     * - O Funcionario precisa existir.
     * - Se não existir, lança erro 404.
     *
     * @param int $idFuncionario
     * @return bool
     * @throws ErrorResponse
     */
    public function deleteService(int $idFuncionario): bool
    {
        error_log("🟣 FuncionarioService::deleteService()");

        /**
         * Verifica existência.
         */
        $FuncionarioExistente = $this->FuncionarioDAO->findById($idFuncionario);

        if (!$FuncionarioExistente) {
            throw new ErrorResponse(
                404,
                "Funcionario não encontrado",
                [
                    "message" =>
                        "Não existe Funcionario com id {$idFuncionario}"
                ]
            );
        }

        /**
         * Monta objeto para exclusão.
         */
        $Funcionario = new Funcionario();
        $Funcionario->setIdFuncionario($idFuncionario);

        return $this->FuncionarioDAO->delete($Funcionario);
    }
}