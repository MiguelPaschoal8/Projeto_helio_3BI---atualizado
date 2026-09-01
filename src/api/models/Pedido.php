<?php

namespace Api\Models; // Ajuste conforme sua estrutura de pastas

use Api\Models;
use JsonSerializable;

class Pedido implements JsonSerializable
{
    // Atributos privados
    private int $idPedido;
    private string $dataPedido = "";
    private string $statusPedido = "Em preparo";
    private Cliente $Cliente;
    private Funcionario $Funcionario;
    private Mesa $Mesa;

    public function __construct()
    {
        // error_log("⬆️  Pedido::__construct()\n");
        $this->Cliente = new Cliente();
        $this->Funcionario = new Funcionario();
        $this->Mesa = new Mesa();
    }

    // Getter e Setter para idPedido
    public function getIdPedido(): int
    {
        return $this->idPedido;
    }

    public function setIdPedido(int $valor): void
    {
        if ($valor <= 0) {
            throw new \Exception("idPedido deve ser um número maior que zero.");
        }
        $this->idPedido = intval($valor);
    }

    // Getter e Setter para Cliente
    public function getCliente(): Cliente
    {
        return $this->Cliente;
    }

    public function setCliente(Cliente $Cliente): void
    {
        if (!($Cliente instanceof Cliente)) {
            throw new \Exception("Cliente deve ser uma instância válida de Cliente.");
        }
        $this->Cliente = $Cliente;
    }

        // Getter e Setter para Funcionario
    public function getFuncionario(): Funcionario
    {
        return $this->Funcionario;
    }

    public function setFuncionario(Funcionario $Fun): void
    {
        if (!($Fun instanceof Funcionario)) {
            throw new \Exception("Funcionario deve ser uma instância válida de Funcionario.");
        }
        $this->Funcionario = $Fun;
    }

        // Getter e Setter para Funcionario
    public function getMesa(): Mesa
    {
        return $this->Mesa;
    }

    public function setMesa(Mesa $Mesa): void
    {
        if (!($Mesa instanceof Mesa)) {
            throw new \Exception("Mesa deve ser uma instância válida de Mesa.");
        }
        $this->Mesa = $Mesa;
    }

    // Getter e Setter para dataPedido
    public function getdataPedido(): string
    {
        return $this->dataPedido;
    }

    public function setdataPedido(string $data): void
    {
        if ($data === "") {
        $this->dataPedido = date("Y-m-d H:i:s"); 
        }
        $this->dataPedido = $data;
    }

    // Getter e Setter para statusPedido
    public function getstatusPedido(): string
    {
        return $this->statusPedido;
    }

    public function setStatusPedido(string $status): void
    {
        if ($status != "Em preparo" && $status != "Entregue" && $status != "Cancelado") {
            throw new \Exception("statusPedido deve ser um dos valores válidos.");
        }
        $this->statusPedido = $status;
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
            'idPedido' => $this->getIdPedido(),
            'Cliente' => $this->getCliente() ? $this->getCliente() : null,
            'Funcionario' => $this->getFuncionario() ? $this->getFuncionario() : null,
            'Mesa' => $this->getMesa() ? $this->getMesa() : null,
            'statusPedido' => $this->getstatusPedido(),
            'dataPedido' => $this->getdataPedido()
        ];
    }
}
