<?php

namespace Api\DAO;

use Api\Models\Mesa;
use Api\Database\MysqlDatabase;
use Exception;

/**
 * Classe responsável pelo acesso aos dados da entidade Mesa.
 *
 * Camadas:
 * Controller -> Service -> DAO -> Banco de Dados
 *
 * Objetivo:
 * Centralizar todas as operações SQL relacionadas à tabela Mesa.
 */
class MesaDAO
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

        error_log("⬆️ MesaDAO::__construct()");
    }

    /**
     * Insere um novo Mesa no banco.
     *
     * @param Mesa $objMesa
     * @return Mesa gerado
     * @throws Exception
     */
    public function create(Mesa $objMesa): Mesa
    {
        error_log("🟢 MesaDAO::create()");

        /**
         * SQL de inserção.
         */
        $sql = "
            INSERT INTO Mesa (capacidade, numeroMesa, statusMesa)
            VALUES (:capacidade, :numeroMesa, :statusMesa)
        ";

        /**
         * Valores da query.
         */
        $parametros = [
            ':capacidade' => $objMesa->getCapacidade(),
            ':numeroMesa' => $objMesa->getnumeroMesa(),
            ':statusMesa' => $objMesa->getstatusMesa()
        ];

        /**
         * Prepara e executa.
         */
        $stmt = $this->database->getConnection()->prepare($sql);

        if (!$stmt->execute($parametros)) {
            throw new Exception("Erro ao cadastrar Mesa.");
        }

        /**
         * Retorna ID criado.
         */
        $novoID = (int) $this->database->getConnection()->lastInsertId();
        $objMesa->setIdMesa($novoID);
        return $objMesa;
    }

    /**
     * Remove um Mesa pelo ID.
     *
     * @param Mesa $objMesaModel
     * @return bool
     */
    public function delete(Mesa $objMesaModel): bool
    {
        error_log("🟢 MesaDAO::delete()");

        /**
         * SQL de exclusão.
         */
        $sql = "
            DELETE FROM Mesa
            WHERE idMesa = :idMesa
        ";

        /**
         * Valores da query.
         */
        $parametros = [
            ':idMesa' => $objMesaModel->getIdMesa()
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
     * Atualiza um Mesa existente.
     *
     * @param Mesa $objMesaModel
     * @return bool
     */
    public function update(Mesa $objMesaModel): bool
    {
        error_log("🟢 MesaDAO::update()");

        /**
         * SQL de atualização.
         */
        $sql = "
            UPDATE Mesa
            SET capacidade = :capacidade, numeroMesa = :numeroMesa, statusMesa = :statusMesa
            WHERE idMesa = :idMesa
        ";

        /**
         * Valores da query.
         */
        $parametros = [
            ':capacidade' => $objMesaModel->getCapacidade(),
            ':numeroMesa' => $objMesaModel->getnumeroMesa(),
            ':statusMesa' => $objMesaModel->getstatusMesa(),
            ':idMesa' => $objMesaModel->getIdMesa()
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
     * Retorna todos os Mesas cadastrados.
     *
     * @return array
     */
    public function findAll(): array
    {
        error_log("🟢 MesaDAO::findAll()");

        /**
         * Consulta todos os registros.
         */
        $sql = "SELECT * FROM Mesa";

        /**
         * Executa consulta.
         */
        $stmt = $this->database->getConnection()->query($sql);

        /**
         * Matriz de arrays.
         */
        $matrizArrays = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        /**
         * Lista final de objetos Mesa.
         */
        $Mesas = [];

        /**
         * Converte cada linha em objeto Mesa.
         */
        foreach ($matrizArrays as $linhaMatriz) {
            $Mesa = new Mesa();

            $Mesa->setIdMesa((int) $linhaMatriz['idMesa']);
            $Mesa->setCapacidade($linhaMatriz['capacidade']);
            $Mesa->setnumeroMesa($linhaMatriz['numeroMesa']);
            $Mesa->setstatusMesa($linhaMatriz['statusMesa']);

            $Mesas[] = $Mesa;
        }

        /**
         * Retorna lista pronta.
         */
        return $Mesas;
    }

    /**
     * Retorna total de Mesas cadastrados.
     *
     * @return int
     */
    public function count(): int
    {
        error_log("🟢 MesaDAO::count()");

        /**
         * SQL de contagem.
         */
        $sql = "SELECT COUNT(*) AS qtd FROM Mesa";

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
     * Busca Mesa pelo ID.
     *
     * @param int $idMesa
     * @return Mesa|null
     */
    public function findById(int $idMesa): ?Mesa
    {
        error_log("🟢 MesaDAO::findById()");

        /**
         * Busca reutilizando método genérico.
         */
        $resultado = $this->findByField('idMesa', $idMesa);

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
        error_log("🟢 MesaDAO::findByField()");

        /**
         * Campos permitidos.
         */
        $camposPermitidos = [
            'idMesa',
            'numeroMesa',
            'capacidade',
            'statusMesa'
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
        $sql = "SELECT * FROM Mesa WHERE $field = :value";

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
         * Lista final de objetos Mesa.
         */
        $Mesas = [];

        /**
         * Converte linhas em objetos.
         */
        foreach ($matrizArrays as $linhaMatriz) {
            $Mesa = new Mesa();

            $Mesa->setIdMesa((int) $linhaMatriz['idMesa']);
            $Mesa->setCapacidade($linhaMatriz['capacidade']);
            $Mesa->setnumeroMesa($linhaMatriz['numeroMesa']);
            $Mesa->setstatusMesa($linhaMatriz['statusMesa']);

            $Mesas[] = $Mesa;
        }

        /**
         * Retorna lista.
         */
        return $Mesas;
    }
}