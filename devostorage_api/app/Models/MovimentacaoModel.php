<?php namespace App\Models;

use CodeIgniter\Model;

class MovimentacaoModel extends Model
{
    protected $table      = 'movimentacoes';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'produto_id', 'usuario_id', 'tipo', 'quantidade', 'data'
    ];

    protected $useTimestamps = true;

    protected $validationRules = [
        'produto_id' => 'required|integer|is_natural_no_zero',
        'usuario_id' => 'required|integer|is_natural_no_zero',
        'tipo'       => 'required|in_list[entrada,saida]',
        'quantidade' => 'required|integer|greater_than_equal_to[1]',
    ];

    protected $validationMessages = [
        'produto_id' => [
            'required' => 'O produto é obrigatório.',
            'integer' => 'O produto deve ser um número inteiro.',
            'is_natural_no_zero' => 'O produto deve ser um ID válido.'
        ],
        'usuario_id' => [
            'required' => 'O usuário é obrigatório.',
            'integer' => 'O usuário deve ser um número inteiro.',
            'is_natural_no_zero' => 'O usuário deve ser um ID válido.'
        ],
        'tipo' => [
            'required' => 'O tipo de movimentação é obrigatório.',
            'in_list' => 'O tipo deve ser "entrada" ou "saida".'
        ],
        'quantidade' => [
            'required' => 'A quantidade é obrigatória.',
            'integer' => 'A quantidade deve ser um número inteiro.',
            'greater_than_equal_to' => 'A quantidade deve ser maior que 0.'
        ]
    ];

    /**
     * Retorna as movimentações com dados do produto
     */
    public function comProduto()
    {
        return $this->select('movimentacoes.*, produtos.nome as produto_nome, produtos.categoria')
            ->join('produtos', 'produtos.id = movimentacoes.produto_id');
    }

    /**
     * Retorna as movimentações com dados do produto e usuário
     */
    public function comDetalhes()
    {
        return $this->select('movimentacoes.*, produtos.nome as produto_nome, produtos.categoria, users.nome as usuario_nome')
            ->join('produtos', 'produtos.id = movimentacoes.produto_id')
            ->join('users', 'users.id = movimentacoes.usuario_id');
    }

    /**
     * Obtém movimentações por produto
     */
    public function porProduto($produtoId)
    {
        return $this->comDetalhes()
            ->where('movimentacoes.produto_id', $produtoId)
            ->orderBy('movimentacoes.data', 'DESC')
            ->findAll();
    }

    /**
     * Obtém movimentações por período
     */
    public function porPeriodo($dataInicio, $dataFim)
    {
        return $this->comDetalhes()
            ->where("movimentacoes.data >=", $dataInicio . " 00:00:00")
            ->where("movimentacoes.data <=", $dataFim . " 23:59:59")
            ->orderBy('movimentacoes.data', 'DESC')
            ->findAll();
    }

    /**
     * Obtém movimentações por tipo
     */
    public function porTipo($tipo)
    {
        return $this->comDetalhes()
            ->where('movimentacoes.tipo', $tipo)
            ->orderBy('movimentacoes.data', 'DESC')
            ->findAll();
    }
}
