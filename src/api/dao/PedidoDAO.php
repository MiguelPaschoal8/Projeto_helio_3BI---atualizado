<?php

namespace Api\DAO;

// Importações das classes necessárias
use Api\Models\Funcionario;               // Model/Entidade Pedido
use Api\Models\Mesa;               // Model/Entidade Pedido
use Api\Models\Cliente;                     // Model/Entidade Cliente (para relacionamento)
use Api\Models\Pedido;
use Api\Database\MysqlDatabase;           // Classe de conexão com o banco MySQL
use Exception;                            // Exceção genérica do PHP
use PDO;                                  // Classe nativa do PHP para acesso a banco

/**
 * Classe PedidoDAO (Data Access Object)
 * 
 * Responsável por realizar operações no banco de dados relacionadas à entidade Pedido.
 * Implementa o padrão DAO, separando a lógica de acesso a dados da lógica de negócio.
 * 
 * ARQUITETURA EM CAMADAS:
 * [Controller] → [Service] → [DAO] → [Banco de Dados]
 *                           ↑
 *                      (Esta classe)
 * 
 * COMPLEXIDADES DESTE DAO:
 * - Relacionamento com a tabela Cliente (JOIN)
 * - Hash de senha (segurança)
 * - Autenticação (login)
 * - Tratamento condicional no update (com/sem senha)
 */
class PedidoDAO
{
    /** 
     * Instância da conexão com o banco de dados
     * 
     * @var MysqlDatabase 
     */
    private MysqlDatabase $database;

    /**
     * Construtor do DAO, recebe a instância de MysqlDatabase via injeção de dependência
     *
     * @param MysqlDatabase $databaseInstance Instância da conexão com o banco
     */
    public function __construct(MysqlDatabase $databaseInstance)
    {
        // Log para debug - ⬆️ indica construção/instanciação
        error_log("⬆️  PedidoDAO::__construct()");

        $this->database = $databaseInstance;
    }

    /**
     * Cria um novo funcionário no banco de dados
     * 
     * Endpoint: POST /api/v1/Pedidos (chamado pelo Service)
     * 
     * DIFERENÇAS PARA O ClienteDAO::create:
     * - Mais campos (nome, email, senha, valeTransporte, Cliente_id)
     * - Hash da senha com bcrypt (segurança)
     * - Uso de placeholders "?" (posicional) em vez de named placeholders
     * 
     * SEGURANÇA:
     * - password_hash() gera hash seguro da senha
     * - cost=12 define o custo computacional (quanto maior, mais seguro)
     * 
     * @param Pedido $Pedido Objeto Pedido com todos os dados
     * @return int ID do funcionário criado
     * @throws Exception Se a inserção falhar
     */
    public function create(Pedido $Pedido): int
    {
        error_log("🟢 PedidoDAO::create()");
        // =============================================================
        // SQL com placeholders posicionais (?)
        // =============================================================
        $sql = "
            INSERT INTO Pedido 
            (statusPedido,dataPedido, idCliente, idFuncionario, idMesa) 
            VALUES (?, ?, ?, ?,?)
        ";

        // Array de parâmetros na mesma ordem dos placeholders
        $params = [
            $Pedido->getstatusPedido(),
            $Pedido->getdataPedido(),
            $Pedido->getCliente()->getIdCliente(), // Pega ID do Cliente associado
            $Pedido->getFuncionario()->getIdFuncionario(), // Pega ID do Funcionario associado
            $Pedido->getMesa()->getIdMesa(), // Pega ID do Funcionario associado
        ];

        // Obtém conexão PDO e executa
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Obtém o ID gerado (auto_increment)
        $insertId = $pdo->lastInsertId();

        if (!$insertId) {
            throw new Exception("Falha ao inserir pedido");
        }

        return (int) $insertId;
    }

    /**
     * Remove um funcionário pelo ID
     * 
     * Endpoint: DELETE /api/v1/Pedidos/{idPedido}
     * 
     * @param Pedido $Pedido Objeto Pedido com ID definido
     * @return bool True se excluiu, False se não encontrou
     */
    public function delete(Pedido $Pedido): bool
    {
        error_log("🟢 PedidoDAO::delete()");

        $sql = "DELETE FROM Pedido WHERE idPedido = ?";

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$Pedido->getIdPedido()]);

        // rowCount() indica quantas linhas foram afetadas
        return $stmt->rowCount() > 0;
    }

    /**
     * Atualiza os dados de um funcionário
     * 
     * Endpoint: PUT /api/v1/Pedidos/{idPedido}
     * 
     * COMPLEXIDADE:
     * - Se a senha for fornecida (não vazia), atualiza também a senha
     * - Se a senha não for fornecida, mantém a senha atual
     * 
     * Isso permite que o frontend não precise enviar a senha
     * em toda atualização (apenas quando quer mudar)
     *
     * @param Pedido $Pedido Objeto Pedido com dados atualizados
     * @return bool True se atualizou, False se não encontrou
     */
    public function update(Pedido $Pedido): bool
    {
        error_log("🟢 PedidoDAO::update()");

        $pdo = $this->database->getConnection();

            $sql = "
                UPDATE Pedido 
                SET dataPedido=?, statusPedido=?, idCliente=?, idFuncionario=?, idMesa=? 
                WHERE idPedido=?
            ";
            $params = [
                $Pedido->getdataPedido(),
                $Pedido->getstatusPedido(),
                $Pedido->getCliente()->getIdCliente(),
                $Pedido->getFuncionario()->getIdFuncionario(),
                $Pedido->getMesa()->getIdMesa(),
                $Pedido->getIdPedido(),
            ];
        

        // Executa a query apropriada
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }

    /**
     * Retorna todos os funcionários com seus respectivos Clientes
     * 
     * Endpoint: GET /api/v1/Pedidos
     * 
     * DIFERENÇA PARA O ClienteDAO::findAll:
     * - Faz JOIN com a tabela Cliente para trazer o nome do Cliente
     * - Converte os resultados em objetos (não arrays)
     * 
     * @return array Array de objetos Pedido (cada um com seu Cliente)
     */
    public function findAll(): array
    {
        error_log("🟢 PedidoDAO::findAll()");

        // =============================================================
        // JOIN para trazer dados relacionados
        // =============================================================
$sql = "
    SELECT 
        Pedido.idPedido,
        Pedido.dataPedido,
        Pedido.statusPedido,

        Cliente.idCliente,
        Cliente.nomeCliente,
        Cliente.telefone,
        Cliente.email,
        Cliente.cpf,

        Funcionario.idFuncionario,
        Funcionario.nomeFuncionario,
        Funcionario.Cargo,
        Funcionario.salario,

        Mesa.idMesa,
        Mesa.numeroMesa,
        Mesa.capacidade

    FROM Pedido

    JOIN Cliente 
        ON Pedido.idCliente = Cliente.idCliente

    JOIN Funcionario 
        ON Pedido.idFuncionario = Funcionario.idFuncionario

    JOIN Mesa 
        ON Pedido.idMesa = Mesa.idMesa
";

        $pdo = $this->database->getConnection();
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // =============================================================
        // Converte cada linha em objetos Cliente e Pedido
        // =============================================================
        $result = [];
        foreach ($rows as $row) {
            // Cria objeto Cliente com os dados do JOIN
            $Cliente = new Cliente();
            $Cliente->setIdCliente((int) $row['idCliente']);
            $Cliente->setNomeCliente($row['nomeCliente']);
            $Cliente->setTelefone($row['telefone']);
            $Cliente->setEmail($row['email']);
            $Cliente->setCpf($row['cpf']);
            $Funcionario = new Funcionario();
            $Funcionario->setIdFuncionario((int) $row['idFuncionario']);
            $Funcionario->setNomeFuncionario($row['nomeFuncionario']);
            $Funcionario->setCargo($row['Cargo']);
            $Funcionario->setSalario((float) $row['salario']);

            $Mesa = new Mesa();
            $Mesa->setIdMesa((int) $row['idMesa']);
            $Mesa->setnumeroMesa($row['numeroMesa']);
            $Mesa->setCapacidade((int) $row['capacidade']);

            // Cria objeto Pedido com os dados da tabela
            $Pedido = new Pedido();
            $Pedido->setIdPedido((int) $row['idPedido']);
            $Pedido->setdataPedido($row['dataPedido']);
            $Pedido->setStatusPedido($row['statusPedido']);

            // Associa o Cliente ao funcionário
            $Pedido->setCliente($Cliente);
            $Pedido->setFuncionario($Funcionario);
            $Pedido->setMesa($Mesa);

            $result[] = $Pedido;
        }

        return $result;
    }

    /**
     * Retorna a quantidade total de funcionários
     * 
     * Endpoint: GET /api/v1/Pedidos/count
     * 
     * @return int Número total de funcionários
     */
    public function count(): int
    {
        error_log("🟢 PedidoDAO::count()");

        $sql = "SELECT COUNT(*) AS qtd FROM Pedido";

        $pdo = $this->database->getConnection();
        $stmt = $pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $row['qtd'];
    }

    /**
     * Busca um funcionário específico pelo ID
     * 
     * Endpoint: GET /api/v1/Pedidos/{idPedido}
     * 
     * REUTILIZAÇÃO:
     * - Aproveita o método genérico findByField
     * - Retorna o primeiro resultado ou null
     *
     * @param int $idPedido ID do funcionário
     * @return Pedido|null Objeto Pedido ou null se não encontrado
     */
    public function findById(int $idPedido): ?Pedido
    {
        $result = $this->findByField('idPedido', $idPedido);
        return $result[0] ?? null; // Retorna primeiro ou null
    }

    /**
     * Busca funcionários por um campo específico (método genérico)
     * 
     * @param string $field Nome do campo (deve estar em $allowedFields)
     * @param mixed $value Valor a ser buscado
     * @return array Array de objetos Pedido
     * @throws Exception Se o campo não for permitido
     */
    public function findByField(string $field, $value): array
    {
        error_log("🟢 PedidoDAO::findByField() - Campo: $field, Valor: $value");

        // =============================================================
        // SEGURANÇA: Whitelist de campos permitidos
        // =============================================================
        $allowedFields = ['idPedido', 'dataPedido','statusPedido', 'Cliente_idCliente', 'Funcionario_idFuncionario', 'Mesa_idMesa'];
        if (!in_array($field, $allowedFields)) {
            throw new Exception("Campo inválido para busca");
        }

        // =============================================================
        // Busca apenas na tabela Pedido (sem JOIN)
        // =============================================================
        $sql = "SELECT * FROM Pedido WHERE $field = ?";

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$value]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // =============================================================
        // Converte cada linha em objeto Pedido
        // =============================================================
        $result = [];
        foreach ($rows as $row) {
            // Cria objeto Cliente apenas com o ID (sem nome)
            $Cliente = new Cliente();
            $Cliente->setIdCliente((int) $row['idCliente']);
            // NOTA: O nome do Cliente não é carregado aqui (falta JOIN)
            $Funcionario = new Funcionario();
            $Funcionario->setIdFuncionario((int) $row['idFuncionario']);

            $Mesa = new Mesa();
            $Mesa->setIdMesa((int) $row['idMesa']);
            // NOTA: O nome do Funcionario não é carregado aqui (falta JOIN)

            // Cria objeto Pedido
            $Pedido = new Pedido();
            $Pedido->setIdPedido((int) $row['idPedido']);
            $Pedido->setdataPedido($row['dataPedido']);
            $Pedido->setStatusPedido($row['statusPedido']);
            $Pedido->setCliente($Cliente);
            $Pedido->setFuncionario($Funcionario);
            $Pedido->setMesa($Mesa);

            $result[] = $Pedido;
        }

        return $result;
    }
}