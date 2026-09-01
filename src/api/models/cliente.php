<?php
namespace Api\Models;
use InvalidArgumentException;
use \JsonSerializable;

/**
 * Representa a entidade Cliente do sistema.
 *
 * Objetivo:
 * - Encapsular os dados de um Cliente.
 * - Garantir integridade dos atributos via getters e setters.
 */
class Cliente implements JsonSerializable
{
    /** @var int Identificador único do Cliente */
    private int $idCliente = 0;

    /** @var string|null Nome do Cliente */
    private string $nomeCliente = "";
    private string $telefone = "";
    private string $email = "";
    private string $cpf = "";

    public function __construct()
    {
        error_log("⬆️  Cliente::__construct()\n");
    }

    /**
     * Getter para idCliente
     * @return int|null Identificador único do Cliente
     */
    public function getIdCliente(): ?int
    {
        return $this->idCliente;
    }

    /**
     * Define o ID do Cliente.
     *
     * 🔹 Regra de domínio: garante que o ID seja sempre um número inteiro positivo.
     *
     * @param int $value Número inteiro positivo representando o ID do Cliente.
     * @throws InvalidArgumentException se o valor for inválido.
     */
    public function setIdCliente(int $value): void
    {
        if ($value <= 0) {
            throw new InvalidArgumentException("idCliente deve ser maior que zero.");
        }

        $this->idCliente = $value;
    }

    /**
     * Getter para nomeCliente
     * @return string|null Nome do Cliente
     */
    public function getNomeCliente(): ?string
    {
        return $this->nomeCliente;
    }

    /**
     * Define o nome do Cliente.
     *
     * 🔹 Regra de domínio: garante que o nome seja sempre uma string não vazia
     * e com pelo menos 3 caracteres e no máximo 64.
     *
     * @param string $value Nome do Cliente.
     * @throws InvalidArgumentException se o valor for inválido.
     */
    public function setNomeCliente(string $value): void
    {
        $nome = trim($value);

        if ($nome === '') {
            throw new InvalidArgumentException("nomeCliente não pode ser vazio.");
        }

        $len = mb_strlen($nome);

        if ($len < 3) {
            throw new InvalidArgumentException("nomeCliente deve ter pelo menos 3 caracteres.");
        }

        if ($len > 64) {
            throw new InvalidArgumentException("nomeCliente deve ter no máximo 64 caracteres.");
        }

        $this->nomeCliente = $nome;
    }

    public function gettelefone(): ?string
    {
        return $this->telefone;
    }

    public function settelefone(int $value): void
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException("telefone deve ser um número inteiro.");
        }

        if ($value <= 0) {
            throw new InvalidArgumentException("telefone deve ser maior que zero.");
        }

        $this->telefone = $value;
    }
    
    public function getemail(): ?string
    {
        return $this->email;
    }

    public function setemail(string $value): void
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException("email deve ser uma string.");
        }

        if ($value === '') {
            throw new InvalidArgumentException("email não pode ser vazio.");
        }

        $this->email = $value;
    }
    
    public function getcpf(): ?string
    {
        return $this->cpf;
    }

    public function setcpf(string $value): void
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException("cpf deve ser uma string.");
        }

        if ($value === '') {
            throw new InvalidArgumentException("cpf não pode ser vazio.");
        }

        $this->cpf = $value;
    }
        
    /**
     * Implementação da interface JsonSerializable
     *
     * Permite converter a entidade Cliente em formato JSON de forma segura e controlada.
     * Isso garante que apenas os atributos necessários sejam expostos ao cliente.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'idCliente' => $this->getIdCliente(),
            'nomeCliente' => $this->getNomeCliente(),
            'telefone' => $this->gettelefone(),
            'email' => $this->getemail(),
            'cpf' => $this->getcpf(),
        ];
    }
}
