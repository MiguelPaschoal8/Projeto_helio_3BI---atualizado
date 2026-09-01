<?php

namespace Api\DAO;

use Api\Models\Funcionario;
use Api\Database\MysqlDatabase;
use Exception;

/**
 * Classe responsável pelo acesso aos dados da entidade Funcionario.
 *
 * Camadas:
 * Controller -> Service -> DAO -> Banco de Dados
 *
 * Objetivo:
 * Centralizar todas as operações SQL relacionadas à tabela Funcionario.
 */
class FuncionarioDAO
{
    /**
     * Instância de conexão com banco de dados.
     *
     * @var MysqlDatabase
     */
    private MysqlDatabase $database;

    /**
     * Recebe a conexão via injeção de dependência.
     *
     * @param MysqlDatabase $databaseInstance
     */
    public function __construct(MysqlDatabase $databaseInstance)
    {
        $this->database = $databaseInstance;

        error_log("⬆️ FuncionarioDAO::__construct()");
    }

    /**
     * Insere um novo Funcionario no banco.
     *
     * @param Funcionario $objFuncionario
     * @return Funcionario gerado
     * @throws Exception
     */
    public function create(Funcionario $objFuncionario): Funcionario
    {
        error_log("🟢 FuncionarioDAO::create()");

        /**
         * SQL de inserção.
         */
        $sql = "
            INSERT INTO Funcionario (nomeFuncionario, Cargo, Senha, salario)
            VALUES (:nomeFuncionario, :Cargo, :Senha, :salario)
        ";

        /**
         * Valores da query.
         */
        $parametros = [
            ':nomeFuncionario' => $objFuncionario->getNomeFuncionario(),
            ':Cargo' => $objFuncionario->getCargo(),
            ':Senha' => $objFuncionario->getSenha(),
            ':salario' => $objFuncionario->getsalario()
        ];

        /**
         * Prepara e executa.
         */
        $stmt = $this->database->getConnection()->prepare($sql);

        if (!$stmt->execute($parametros)) {
            throw new Exception("Erro ao cadastrar Funcionario.");
        }

        /**
         * Retorna ID criado.
         */
        $novoID = (int) $this->database->getConnection()->lastInsertId();
        $objFuncionario->setIdFuncionario($novoID);
        return $objFuncionario;
    }

    /**
     * Remove um Funcionario pelo ID.
     *
     * @param Funcionario $objFuncionarioModel
     * @return bool
     */
    public function delete(Funcionario $objFuncionarioModel): bool
    {
        error_log("🟢 FuncionarioDAO::delete()");

        /**
         * SQL de exclusão.
         */
        $sql = "
            DELETE FROM Funcionario
            WHERE idFuncionario = :idFuncionario
        ";

        /**
         * Valores da query.
         */
        $parametros = [
            ':idFuncionario' => $objFuncionarioModel->getIdFuncionario()
        ];

        /**
         * Executa exclusão.
         */
        $stmt = $this->database->getConnection()->prepare($sql);
        $stmt->execute($parametros);

        /**
         * True se removeu registro.
         */
        return $stmt->rowCount() > 0;
    }

    /**
     * Atualiza um Funcionario existente.
     *
     * @param Funcionario $objFuncionarioModel
     * @return bool
     */
    public function update(Funcionario $objFuncionarioModel): bool
    {
        error_log("🟢 FuncionarioDAO::update()");

        /**
         * SQL de atualização.
         */
        $sql = "
            UPDATE Funcionario
            SET nomeFuncionario = :nomeFuncionario, Cargo = :Cargo, senha = :senha, salario = :salario
            WHERE idFuncionario = :idFuncionario
        ";

        /**
         * Valores da query.
         */
        $parametros = [
            ':nomeFuncionario' => $objFuncionarioModel->getNomeFuncionario(),
            ':Cargo' => $objFuncionarioModel->getCargo(),
            ':senha' => $objFuncionarioModel->getSenha(),
            ':salario' => $objFuncionarioModel->getsalario(),
            ':idFuncionario' => $objFuncionarioModel->getIdFuncionario()
        ];

        /**
         * Executa atualização.
         */
        $stmt = $this->database->getConnection()->prepare($sql);
        $stmt->execute($parametros);

        /**
         * True se alterou registro.
         */
        return $stmt->rowCount() > 0;
    }

    /**
     * Retorna todos os Funcionarios cadastrados.
     *
     * @return array
     */
    public function findAll(): array
    {
        error_log("🟢 FuncionarioDAO::findAll()");

        /**
         * Consulta todos os registros.
         */
        $sql = "SELECT * FROM Funcionario";

        /**
         * Executa consulta.
         */
        $stmt = $this->database->getConnection()->query($sql);

        /**
         * Matriz de arrays.
         */
        $matrizArrays = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        /**
         * Lista final de objetos Funcionario.
         */
        $Funcionarios = [];

        /**
         * Converte cada linha em objeto Funcionario.
         */
        foreach ($matrizArrays as $linhaMatriz) {
            $Funcionario = new Funcionario();

            $Funcionario->setIdFuncionario((int) $linhaMatriz['idFuncionario']);
            $Funcionario->setNomeFuncionario($linhaMatriz['nomeFuncionario']);
            $Funcionario->setCargo($linhaMatriz['Cargo']);
            $Funcionario->setSenha($linhaMatriz['senha']);
            $Funcionario->setsalario($linhaMatriz['salario']);

            $Funcionarios[] = $Funcionario;
        }

        /**
         * Retorna lista pronta.
         */
        return $Funcionarios;
    }

    /**
     * Retorna total de Funcionarios cadastrados.
     *
     * @return int
     */
    public function count(): int
    {
        error_log("🟢 FuncionarioDAO::count()");

        /**
         * SQL de contagem.
         */
        $sql = "SELECT COUNT(*) AS qtd FROM Funcionario";

        /**
         * Executa consulta.
         */
        $stmt = $this->database->getConnection()->query($sql);

        /**
         * Resultado único.
         */
        $linhaMatriz = $stmt->fetch(\PDO::FETCH_ASSOC);

        /**
         * Retorna total.
         */
        return (int) $linhaMatriz['qtd'];
    }

    /**
     * Busca Funcionario pelo ID.
     *
     * @param int $idFuncionario
     * @return Funcionario|null
     */
    public function findById(int $idFuncionario): ?Funcionario
    {
        error_log("🟢 FuncionarioDAO::findById()");

        /**
         * Busca reutilizando método genérico.
         */
        $resultado = $this->findByField('idFuncionario', $idFuncionario);

        /**
         * Se encontrou registro.
         */
        if (!empty($resultado)) {
            return $resultado[0];
        }

        /**
         * Não encontrado.
         */
        return null;
    }

    /**
     * Busca por campo específico.
     *
     * @param string $field
     * @param mixed $value
     * @return array
     * @throws Exception
     */
    public function findByField(string $field, $value): array
    {
        error_log("🟢 FuncionarioDAO::findByField()");

        /**
         * Campos permitidos.
         */
        $camposPermitidos = [
            'idFuncionario',
            'nomeFuncionario'
        ];

        /**
         * Valida campo informado.
         */
        if (!in_array($field, $camposPermitidos)) {
            throw new Exception("Campo inválido.");
        }

        /**
         * SQL dinâmica segura.
         */
        $sql = "SELECT * FROM Funcionario WHERE $field = :value";

        /**
         * Prepara consulta.
         */
        $stmt = $this->database->getConnection()->prepare($sql);

        /**
         * Executa busca.
         */
        $stmt->execute([
            ':value' => $value
        ]);

        /**
         * Matriz retornada.
         */
        $matrizArrays = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        /**
         * Lista final de objetos Funcionario.
         */
        $Funcionarios = [];

        /**
         * Converte linhas em objetos.
         */
        foreach ($matrizArrays as $linhaMatriz) {
            $Funcionario = new Funcionario();

            $Funcionario->setIdFuncionario((int) $linhaMatriz['idFuncionario']);
            $Funcionario->setNomeFuncionario($linhaMatriz['nomeFuncionario']);
            $Funcionario->setCargo($linhaMatriz['Cargo']);
            $Funcionario->setsalario($linhaMatriz['salario']);

            $Funcionarios[] = $Funcionario;
        }

        /**
         * Retorna lista.
         */
        return $Funcionarios;
    }
}