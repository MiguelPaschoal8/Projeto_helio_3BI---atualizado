<?php

namespace Api\Models; // Ajuste conforme sua estrutura de pastas

use Api\Models;
use JsonSerializable;

class Pagamento implements JsonSerializable
{
    // Atributos privados
    private int $idPagamento;
    private string $formaPagamento = "Pendente";
    private float $valor = 0.00;
    private string $statusPagamento = "Pendente";
    private string $dataPagamento = "";
    private Pedido $Pedido;

    public function __construct()
    {
        // error_log("⬆️  Pagamento::__construct()\n");
        $this->Pedido = new Pedido();
    }

    // Getter e Setter para idPagamento
    public function getIdPagamento(): int
    {
        return $this->idPagamento;
    }

    public function setIdPagamento(int $valor): void
    {
        if (!is_numeric($valor) || intval($valor) != $valor) {
            throw new \Exception("idPagamento deve ser um número inteiro.");
        }
        if ($valor <= 0) {
            throw new \Exception("idPagamento deve ser um número maior que zero.");
        }
        $this->idPagamento = intval($valor);
    }

    // Getter e Setter para Pedido
    public function getPedido(): Pedido
    {
        return $this->Pedido;
    }

    public function setPedido(Pedido $Pedido): void
    {
        if (!($Pedido instanceof Pedido)) {
            throw new \Exception("Pedido deve ser uma instância válida de Pedido.");
        }
        
        $this->Pedido = $Pedido;
    }

    // Getter e Setter para formaPagamento
    public function getFormaPagamento(): string
    {
        return $this->formaPagamento;
    }

    public function setFormaPagamento(string $forma): void
    {
        if ($forma != "Pendente" && $forma != "Dinheiro" && $forma != "Cartão" && $forma != "PIX") {
            throw new \Exception("formaPagamento deve ser um dos valores válidos.");
        }
        $this->formaPagamento = $forma;
    }

    // Getter e Setter para valor
    public function getValorPagamento(): float
    {
        return $this->valor;
    }

    public function setValorPagamento(float $valor): void
    {
        $valor = trim($valor);
        if (!filter_var($valor, FILTER_VALIDATE_FLOAT)) {
            throw new \Exception("valor em formato inválido.");
        }
        if ($valor <= 0) {
            throw new \Exception("valor deve ser um número maior que zero.");
        }
        $this->valor = $valor;
    }

    // Getter e Setter para statusPagamento
    public function getStatusPagamento(): string
    {
        return $this->statusPagamento;
    }

    public function setStatusPagamento(string $statusPagamento): void
    {
        if ($statusPagamento != "Pendente" && $statusPagamento != "Pago") {
            throw new \Exception("statusPagamento deve ser um dos valores válidos.");
        }
        $this->statusPagamento = $statusPagamento;
    }

        // Getter e Setter para statusPagamento
    public function getDataPagamento(): string
    {
        return $this->dataPagamento;
    }

    public function setDataPagamento(string $dataPagamento): void
    {
        if ($dataPagamento === "") {
            $this->dataPagamento = date("Y-m-d H:i:s");
        } else {
            $this->dataPagamento = $dataPagamento;
        }
    }

    /**
     * Implementação da interface JsonSerializable
     * 
     * Permite que objetos da classe sejam convertidos corretamente
     * para JSON ao using json_encode(), exibindo apenas dados relevantes.
     */
    public function jsonSerialize(): array
    {
        return [
            'idPagamento' => $this->getIdPagamento(),
            'Pedido' => $this->getPedido() ? $this->getPedido() : null,
            'formaPagamento' => $this->getFormaPagamento(),
            'valor' => $this->getValorPagamento(),
            'statusPagamento' => $this->getStatusPagamento(),
            'dataPagamento' => $this->getDataPagamento()
        ];
    }
}
