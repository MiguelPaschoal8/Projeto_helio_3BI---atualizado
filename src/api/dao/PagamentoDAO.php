<?php

namespace Api\DAO;

// Importações das classes necessárias
use Api\Models\Pedido;
use Api\Models\Cliente;
use Api\Models\Funcionario;
use Api\Models\Mesa;
use Api\Models\Pagamento;
use Api\Database\MysqlDatabase;           // Classe de conexão com o banco MySQL
use Exception;                            // Exceção genérica do PHP
use PDO;                                  // Classe nativa do PHP para acesso a banco

/**
 * Classe PagamentoDAO (Data Access Object)
 * 
 * Responsável por realizar operações no banco de dados relacionadas à entidade Pagamento.
 * Implementa o padrão DAO, separando a lógica de acesso a dados da lógica de negócio.
 * 
 * ARQUITETURA EM CAMADAS:
 * [Controller] → [Service] → [DAO] → [Banco de Dados]
 *                           ↑
 *                      (Esta classe)
 * 
 * COMPLEXIDADES DESTE DAO:
 * - Relacionamento com a tabela Pedido (JOIN)
 * - Hash de senha (segurança)
 * - Autenticação (login)
 * - Tratamento condicional no update (com/sem senha)
 */
class PagamentoDAO
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
        error_log("⬆️  PagamentoDAO::__construct()");

        $this->database = $databaseInstance;
    }

    /**
     * Cria um novo Pedido no banco de dados
     * 
     * Endpoint: POST /api/v1/Pagamentos (chamado pelo Service)
     * 
     * DIFERENÇAS PARA O PedidoDAO::create:
     * - Mais campos (nome, email, senha, valeTransporte, Pedido_id)
     * - Hash da senha com bcrypt (segurança)
     * - Uso de placeholders "?" (posicional) em vez de named placeholders
     * 
     * SEGURANÇA:
     * - password_hash() gera hash seguro da senha
     * - cost=12 define o custo computacional (quanto maior, mais seguro)
     * 
     * @param Pagamento $Pagamento Objeto Pagamento com todos os dados
     * @return int ID do Pedido criado
     * @throws Exception Se a inserção falhar
     */
    public function create(Pagamento $Pagamento): int
    {
        error_log("🟢 PagamentoDAO::create()");
        // =============================================================
        // SQL com placeholders posicionais (?)
        // =============================================================
        $sql = "
            INSERT INTO Pagamento 
            (formaPagamento, statusPagamento, valor, dataPagamento, idPedido) 
            VALUES (?, ?, ?, ?, ?)
        ";

        // Array de parâmetros na mesma ordem dos placeholders
        $params = [
            $Pagamento->getFormaPagamento(),
            $Pagamento->getStatusPagamento(),
            $Pagamento->getValorPagamento(),
            $Pagamento->getDataPagamento(),
            $Pagamento->getPedido()->getIdPedido(), // Pega ID do Pedido associado
        ];

        // Obtém conexão PDO e executa
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Obtém o ID gerado (auto_increment)
        $insertId = $pdo->lastInsertId();

        if (!$insertId) {
            throw new Exception("Falha ao inserir Pagamento.");
        }

        return (int) $insertId;
    }

    /**
     * Remove um Pedido pelo ID
     * 
     * Endpoint: DELETE /api/v1/Pagamentos/{idPagamento}
     * 
     * @param Pagamento $Pagamento Objeto Pagamento com ID definido
     * @return bool True se excluiu, False se não encontrou
     */
    public function delete(Pagamento $Pagamento): bool
    {
        error_log("🟢 PagamentoDAO::delete()");

        $sql = "DELETE FROM Pagamento WHERE idPagamento = ?";

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$Pagamento->getIdPagamento()]);

        // rowCount() indica quantas linhas foram afetadas
        return $stmt->rowCount() > 0;
    }

    /**
     * Atualiza os dados de um Pedido
     * 
     * Endpoint: PUT /api/v1/Pagamentos/{idPagamento}
     * 
     * COMPLEXIDADE:
     * - Se a senha for fornecida (não vazia), atualiza também a senha
     * - Se a senha não for fornecida, mantém a senha atual
     * 
     * Isso permite que o frontend não precise enviar a senha
     * em toda atualização (apenas quando quer mudar)
     *
     * @param Pagamento $Pagamento Objeto Pagamento com dados atualizados
     * @return bool True se atualizou, False se não encontrou
     */
    public function update(Pagamento $Pagamento): bool
    {
        error_log("🟢 PagamentoDAO::update()");

        $pdo = $this->database->getConnection();

            $sql = "
                UPDATE Pagamento 
                SET formaPagamento=?, statusPagamento=?, valor=?, dataPagamento=?, idPedido=? 
                WHERE idPagamento=?
            ";
            $params = [
                $Pagamento->getFormaPagamento(),
                $Pagamento->getStatusPagamento(),
                $Pagamento->getValorPagamento(),
                $Pagamento->getDataPagamento(),
                $Pagamento->getPedido()->getIdPedido(),     $Pagamento->getIdPagamento()
            ];
        

        // Executa a query apropriada
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
    }

    /**
     * Retorna todos os Pedidos com seus respectivos Pedidos
     * 
     * Endpoint: GET /api/v1/Pagamentos
     * 
     * DIFERENÇA PARA O PedidoDAO::findAll:
     * - Faz JOIN com a tabela Pedido para trazer o nome do Pedido
     * - Converte os resultados em objetos (não arrays)
     * 
     * @return array Array de objetos Pagamento (cada um com seu Pedido)
     */
    public function findAll(): array
    {
        error_log("🟢 PagamentoDAO::findAll()");

        // =============================================================
        // JOIN para trazer dados relacionados
        // =============================================================
        $sql = "
            SELECT Pagamento.idPagamento, Pagamento.formaPagamento, Pagamento.dataPagamento, Pagamento.statusPagamento, Pagamento.valor, 
            
            Pedido.idPedido, Pedido.dataPedido,
            
            Cliente.idCliente, Cliente.nomeCliente,
            Cliente.telefone, Cliente.email, Cliente.cpf,

            Funcionario.idFuncionario, Funcionario.nomeFuncionario, Funcionario.Cargo, Funcionario.salario,

            Mesa.idMesa, Mesa.numeroMesa, Mesa.capacidade

            FROM Pagamento
            JOIN Pedido ON Pagamento.idPedido = Pedido.idPedido
            JOIN Cliente ON Pedido.idCliente = Cliente.idCliente
            JOIN Funcionario ON Pedido.idFuncionario = Funcionario.idFuncionario
            JOIN Mesa ON Pedido.idMesa = Mesa.idMesa
        ";

        $pdo = $this->database->getConnection();
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // =============================================================
        // Converte cada linha em objetos Pedido e Pagamento
        // =============================================================
        $result = [];
        foreach ($rows as $row) {
            // Cria objeto Pedido com os dados do JOIN
            $Pedido = new Pedido();
            $Pedido->setIdPedido((int) $row['idPedido']);
            $Pedido->setDataPedido($row['dataPedido']);
            
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
            $Funcionario->setSalario($row['salario']);

            $Mesa = new Mesa();
            $Mesa->setIdMesa((int) $row['idMesa']);
            $Mesa->setNumeroMesa($row['numeroMesa']);
            $Mesa->setCapacidade($row['capacidade']);

            // Cria objeto Pagamento com os dados da tabela
            $Pagamento = new Pagamento();
            $Pagamento->setIdPagamento((int) $row['idPagamento']);
            $Pagamento->setFormaPagamento($row['formaPagamento']);
            $Pagamento->setValorPagamento($row['valor']);
            $Pagamento->setStatusPagamento($row['statusPagamento']);
            $Pagamento->setDataPagamento($row['dataPagamento']);

            // Associa os relacionamentos ao Pedido 
            $Pedido->setCliente($Cliente); 
            $Pedido->setFuncionario($Funcionario); 
            $Pedido->setMesa($Mesa);

            // Associa o Pedido ao Pedido
            $Pagamento->setPedido($Pedido);

            $result[] = $Pagamento;
        }

        return $result;
    }

    /**
     * Retorna a quantidade total de Pedidos
     * 
     * Endpoint: GET /api/v1/Pagamentos/count
     * 
     * @return int Número total de Pedidos
     */
    public function count(): int
    {
        error_log("🟢 PagamentoDAO::count()");

        $sql = "SELECT COUNT(*) AS qtd FROM Pagamento";

        $pdo = $this->database->getConnection();
        $stmt = $pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $row['qtd'];
    }

    /**
     * Busca um Pedido específico pelo ID
     * 
     * Endpoint: GET /api/v1/Pagamentos/{idPagamento}
     * 
     * REUTILIZAÇÃO:
     * - Aproveita o método genérico findByField
     * - Retorna o primeiro resultado ou null
     *
     * @param int $idPagamento ID do Pedido
     * @return Pagamento|null Objeto Pagamento ou null se não encontrado
     */
    public function findById(int $idPagamento): ?Pagamento
    {
        $result = $this->findByField('idPagamento', $idPagamento);
        return $result[0] ?? null; // Retorna primeiro ou null
    }

    /**
     * Busca Pedidos por um campo específico (método genérico)
     * 
     * @param string $field Nome do campo (deve estar em $allowedFields)
     * @param mixed $value Valor a ser buscado
     * @return array Array de objetos Pagamento
     * @throws Exception Se o campo não for permitido
     */
    public function findByField(string $field, $value): array
    {
        error_log("🟢 PagamentoDAO::findByField() - Campo: $field, Valor: $value");

        // =============================================================
        // SEGURANÇA: Whitelist de campos permitidos
        // =============================================================
        $allowedFields = ['idPagamento', 'formaPagamento', 'statusPagamento', 'valor','dataPagamento', 'idPedido'];
        if (!in_array($field, $allowedFields)) {
            throw new Exception("Campo inválido para busca");
        }

        // =============================================================
        // Busca apenas na tabela Pagamento (sem JOIN)
        // =============================================================
        $sql = "SELECT * FROM Pagamento WHERE $field = ?";

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$value]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // =============================================================
        // Converte cada linha em objeto Pagamento
        // =============================================================
        $result = [];
        foreach ($rows as $row) {
            // Cria objeto Pedido apenas com o ID (sem nome)
            $Pedido = new Pedido();
            $Pedido->setIdPedido((int) $row['idPedido']);
            // NOTA: O nome do Pedido não é carregado aqui (falta JOIN)

            // Cria objeto Pagamento
            $Pagamento = new Pagamento();
            $Pagamento->setIdPagamento((int) $row['idPagamento']);
            $Pagamento->setFormaPagamento($row['formaPagamento']);
            $Pagamento->setStatusPagamento($row['statusPagamento']);
            $Pagamento->setValorPagamento($row['valor']);
            $Pagamento->setDataPagamento($row['dataPagamento']);
            $Pagamento->setPedido($Pedido);

            $result[] = $Pagamento;
        }

        return $result;
    }

    /**
     * Autentica um Pedido (verifica email e senha)
     * 
     * Endpoint: POST /api/v1/Pagamentos/login (PÚBLICO)
     * 
     * FLUXO DE AUTENTICAÇÃO:
     * 1. Busca Pedido pelo email
     * 2. Se não encontrar → retorna null
     * 3. Se encontrar → verifica a senha com password_verify()
     * 4. Se senha válida → retorna objeto Pagamento (sem a senha)
     * 5. Se senha inválida → retorna null
     * 
     * SEGURANÇA:
     * - password_verify() compara a senha fornecida com o hash
     * - Nunca retorna a senha (nem o hash)
     * - Timing attack: mesmo tempo para usuário existente ou não
     *
     * @param Pagamento $Pagamento Objeto com email e senha
     * @return Pagamento|null Objeto Pagamento se autenticado, null caso contrário
     */
    public function findStatusPagamento(Pagamento $Pagamento): ?Pagamento
    {
        error_log("🟢 PagamentoDAO::login()");

        // =============================================================
        // Busca Pedido pelo email (com JOIN para trazer o Pedido)
        // =============================================================
        $sql = "
            SELECT idPagamento, dataPagamento, statusPagamento, idPedido, numeroPedido
            FROM Pagamento
            JOIN Pedido ON Pedido.idPedido = Pagamento.idPedido
            WHERE statusPagamento = ?
        ";

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$Pagamento->getStatusPagamento()]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // =============================================================
        // Caso 1: Email não encontrado
        // =============================================================
        if (!$row) {
            error_log("❌ Pagamento não encontrado");
            return null;
        }

        // =============================================================
        // Caso 3: Senha válida - monta objeto de retorno
        // =============================================================
        $Pedido = new Pedido();
        $Pedido->setIdPedido((int) $row['idPedido']);
        $Pedido->setDataPedido($row['dataPedido']);

        $Pedi = new Pagamento();
        $Pagamento->setIdPagamento((int) $row['idPagamento']);
        $Pagamento->setFormaPagamento($row['formaPagamento']);
        $Pagamento->setStatusPagamento($row['statusPagamento']);
        $Pagamento->setValorPagamento($row['valor']);
        $Pagamento->setDataPagamento($row['dataPagamento']);
        $Pagamento->setPedido($Pedido);

        return $Pedi;
    }
}