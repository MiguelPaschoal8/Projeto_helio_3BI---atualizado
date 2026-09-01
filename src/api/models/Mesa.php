<?php

namespace Api\Models; // Ajuste conforme sua estrutura de pastas

use Api\Models;
use JsonSerializable;

class Mesa implements JsonSerializable
{
    // Atributos privados
    private int $idMesa = 0;
    private int $numeroMesa = 0;
    private int $Capacidade = 0;
    private string $statusMesa = "Livre";

    public function __construct()
    {
        error_log("⬆️  Mesa::__construct()\n");

    }

    // Getter e Setter para idMesa
    public function getIdMesa(): int
    {
        return $this->idMesa;
    }

    public function setIdMesa(int $valor): void
    {
        if ($valor <= 0) {
            throw new \Exception("idMesa deve ser um número inteiro positivo.");
        }
        $this->idMesa = intval($valor);
    }


    // Getter e Setter para numeroMesa
    public function getnumeroMesa(): int
    {
        return $this->numeroMesa;
    }

    public function setnumeroMesa(int $numero): void
    {
        if ($numero <= 0 ) {
            throw new \Exception("O número da mesa maior que zero.");
        }
        $this->numeroMesa = $numero;
    }

    // Getter e Setter para capacidade
    public function getcapacidade(): int
    {
        return $this->Capacidade;
    }

    public function setcapacidade(int $novaCapacidade): void
    {
        if ($novaCapacidade <= 0 ) {
            throw new \Exception("capacidade deve ser um número maior que zero.");
        }
        $this->Capacidade = $novaCapacidade;
    }

    // Getter e Setter para statusMesa
    public function getstatusMesa(): string
    {
        return $this->statusMesa;
    }

    public function setstatusMesa(string $status): void
    {
        if ($status != "Livre" && $status != "Reservada" && $status != "Ocupada") {
            throw new \Exception("statusMesa deve ser um dos valores válidos.");
        }
        $this->statusMesa = $status;
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
            'idMesa' => $this->getIdMesa(),
            'capacidade' => $this->getcapacidade(),
            'numeroMesa' => $this->getnumeroMesa(),
            'statusMesa' => $this->getstatusMesa()
        ];
    }
}
