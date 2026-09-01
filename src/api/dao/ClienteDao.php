<?php

namespace Api\DAO;

use Api\Models\Cliente;
use Api\Database\MysqlDatabase;
use Exception;

/**
 * Classe responsável pelo acesso aos dados da entidade Cliente.
 *
 * Camadas:
 * Controller -> Service -> DAO -> Banco de Dados
 *
 * Objetivo:
 * Centralizar todas as operações SQL relacionadas à tabela Cliente.
 */
class ClienteDAO
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

        error_log("⬆️ ClienteDAO::__construct()");
    }

    /**
     * Insere um novo Cliente no banco.
     *
     * @param Cliente $objCliente
     * @return Cliente gerado
     * @throws Exception
     */
    public function create(Cliente $objCliente): Cliente
    {
        error_log("🟢 ClienteDAO::create()");

        /**
         * SQL de inserção.
         */
        $sql = "
            INSERT INTO Cliente (nomeCliente, telefone, email, cpf)
            VALUES (:nomeCliente, :telefone, :email, :cpf)
        ";

        /**
         * Valores da query.
         */
        $parametros = [
            ':nomeCliente' => $objCliente->getNomeCliente(),
            ':telefone' => $objCliente->getTelefone(),
            ':email' => $objCliente->getEmail(),
            ':cpf' => $objCliente->getCpf()
        ];

        /**
         * Prepara e executa.
         */
        $stmt = $this->database->getConnection()->prepare($sql);

        if (!$stmt->execute($parametros)) {
            throw new Exception("Erro ao cadastrar Cliente.");
        }

        /**
         * Retorna ID criado.
         */
        $novoID = (int) $this->database->getConnection()->lastInsertId();
        $objCliente->setIdCliente($novoID);
        return $objCliente;
    }

    /**
     * Remove um Cliente pelo ID.
     *
     * @param Cliente $objClienteModel
     * @return bool
     */
    public function delete(Cliente $objClienteModel): bool
    {
        error_log("🟢 ClienteDAO::delete()");

        /**
         * SQL de exclusão.
         */
        $sql = "
            DELETE FROM Cliente
            WHERE idCliente = :idCliente
        ";

        /**
         * Valores da query.
         */
        $parametros = [
            ':idCliente' => $objClienteModel->getIdCliente()
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
     * Atualiza um Cliente existente.
     *
     * @param Cliente $objClienteModel
     * @return bool
     */
    public function update(Cliente $objClienteModel): bool
    {
        error_log("🟢 ClienteDAO::update()");

        /**
         * SQL de atualização.
         */
        $sql = "
            UPDATE Cliente
            SET nomeCliente = :nomeCliente, telefone = :telefone, email = :email, cpf = :cpf
            WHERE idCliente = :idCliente
        ";

        /**
         * Valores da query.
         */
        $parametros = [
            ':nomeCliente' => $objClienteModel->getNomeCliente(),
            ':telefone' => $objClienteModel->getTelefone(),
            ':email' => $objClienteModel->getEmail(),
            ':cpf' => $objClienteModel->getCpf(),
            ':idCliente' => $objClienteModel->getIdCliente()
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
     * Retorna todos os Clientes cadastrados.
     *
     * @return array
     */
    public function findAll(): array
    {
        error_log("🟢 ClienteDAO::findAll()");

        /**
         * Consulta todos os registros.
         */
        $sql = "SELECT * FROM Cliente";

        /**
         * Executa consulta.
         */
        $stmt = $this->database->getConnection()->query($sql);

        /**
         * Matriz de arrays.
         */
        $matrizArrays = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        /**
         * Lista final de objetos Cliente.
         */
        $Clientes = [];

        /**
         * Converte cada linha em objeto Cliente.
         */
        foreach ($matrizArrays as $linhaMatriz) {
            $Cliente = new Cliente();

            $Cliente->setIdCliente((int) $linhaMatriz['idCliente']);
            $Cliente->setNomeCliente($linhaMatriz['nomeCliente']);
            $Cliente->setTelefone($linhaMatriz['telefone']);
            $Cliente->setEmail($linhaMatriz['email']);
            $Cliente->setCpf($linhaMatriz['cpf']);

            $Clientes[] = $Cliente;
        }

        /**
         * Retorna lista pronta.
         */
        return $Clientes;
    }

    /**
     * Retorna total de Clientes cadastrados.
     *
     * @return int
     */
    public function count(): int
    {
        error_log("🟢 ClienteDAO::count()");

        /**
         * SQL de contagem.
         */
        $sql = "SELECT COUNT(*) AS qtd FROM Cliente";

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
     * Busca Cliente pelo ID.
     *
     * @param int $idCliente
     * @return Cliente|null
     */
    public function findById(int $idCliente): ?Cliente
    {
        error_log("🟢 ClienteDAO::findById()");

        /**
         * Busca reutilizando método genérico.
         */
        $resultado = $this->findByField('idCliente', $idCliente);

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
        error_log("🟢 ClienteDAO::findByField()");

        /**
         * Campos permitidos.
         */
        $camposPermitidos = [
            'idCliente',
            'nomeCliente'
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
        $sql = "SELECT * FROM Cliente WHERE $field = :value";

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
         * Lista final de objetos Cliente.
         */
        $Clientes = [];

        /**
         * Converte linhas em objetos.
         */
        foreach ($matrizArrays as $linhaMatriz) {
            $Cliente = new Cliente();

            $Cliente->setIdCliente((int) $linhaMatriz['idCliente']);
            $Cliente->setNomeCliente($linhaMatriz['nomeCliente']);
            $Cliente->setTelefone($linhaMatriz['telefone']);
            $Cliente->setEmail($linhaMatriz['email']);
            $Cliente->setCpf($linhaMatriz['cpf']);

            $Clientes[] = $Cliente;
        }

        /**
         * Retorna lista.
         */
        return $Clientes;
    }
}