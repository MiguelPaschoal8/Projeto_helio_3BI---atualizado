<?php

namespace Api\Models; // Ajuste conforme sua estrutura de pastas

use Api\Models;
use JsonSerializable;

class Funcionario implements JsonSerializable
{
    // Atributos privados
    private int $idFuncionario = 0;
    private string $nomeFuncionario = "";
    private string $Cargo = "";
    private string $senha = "";
    private float $salario = 0.0; 

    public function __construct()
    {
        error_log("⬆️  Funcionario::__construct()\n");

    }

    // Getter e Setter para idFuncionario
    public function getIdFuncionario(): int
    {
        return $this->idFuncionario;
    }

    public function setIdFuncionario(int $valor): void
    {
        if ($valor <= 0) {
            throw new \Exception("idFuncionario deve ser maior que zero.");
        }
        $this->idFuncionario = intval($valor);
    }


    // Getter e Setter para nomeFuncionario
    public function getNomeFuncionario(): string
    {
        return $this->nomeFuncionario;
    }

    public function setNomeFuncionario(string $nome): void
    {
        $nome = trim($nome);
        if (strlen($nome) < 3) {
            throw new \Exception("nomeFuncionario deve ter pelo menos 3 caracteres.");
        }
        $this->nomeFuncionario = $nome;
    }

    // Getter e Setter para cargo
    public function getCargo(): string
    {
        return $this->Cargo;
    }

    public function setCargo(string $car): void
    {
        if ($car != "Garçom" && $car != "Caixa" && $car != "Cozinheiro" && $car != "Gerente") {
            throw new \Exception("cargo deve ser um dos valores válidos.");
        }
        $this->Cargo = $car;
    }

    // Getter e Setter para salario
    public function getSalario(): float 
    {
        return $this->salario;
    }

    public function setSalario(string $salario): void
    {
        $salario = trim($salario);
        if (!is_numeric($salario)) {
            throw new \Exception("salario deve ser um número.");
        }
        if ($salario <= 0) {
            throw new \Exception("salario deve ser um número positivo.");
        }
        $this->salario = $salario;
    }

    // Getter e Setter para senha
    public function getSenha(): string
    {
        return $this->senha;
    }

    public function setSenha(string $senha): void
    {
        $senha = trim($senha);
        if (strlen($senha) < 6) {
            throw new \Exception("senha deve ter pelo menos 6 caracteres.");
        }
        if (!preg_match("/[A-Z]/", $senha)) {
            throw new \Exception("senha deve conter pelo menos uma letra maiúscula.");
        }
        if (!preg_match("/[0-9]/", $senha)) {
            throw new \Exception("senha deve conter pelo menos um número.");
        }
        if (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $senha)) {
            throw new \Exception("senha deve conter pelo menos um caractere especial.");
        }
        $this->senha = $senha;
    }

    /**
     * Implementação da interface JsonSerializable
     * 
     * Permite que objetos da classe sejam convertidos corretamente
     * para JSON ao usar json_encode(), exibindo apenas dados relevantes.
     */
    public function jsonSerialize(): array
    {
        return [
            'idFuncionario' => $this->getIdFuncionario(),
            'Cargo' => $this->getCargo(),
            'nomeFuncionario' => $this->getNomeFuncionario(),
            'salario' => $this->getSalario()
            // ⚠️ senha não deve ser exposta em respostas JSON por segurança
        ];
    }
}
