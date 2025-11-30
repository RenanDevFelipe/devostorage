<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\MovimentacaoModel;
use App\Models\ProdutoModel;

class MovimentacaoController extends ResourceController
{
    protected $modelName = MovimentacaoModel::class;
    protected $format = 'json';
    protected $request;

    public function __construct()
    {
        $this->request = request();
    }

    // LISTA TODAS AS MOVIMENTAÇÕES COM DETALHES DOS PRODUTOS
    public function index()
    {
        return $this->respond($this->model
            ->comDetalhes()
            ->orderBy('movimentacoes.data', 'DESC')
            ->findAll());
    }

    // REGISTRAR ENTRADA
    public function entrada()
    {
        return $this->registrarMovimentacao('entrada');
    }

    // REGISTRAR SAÍDA
    public function saida()
    {
        return $this->registrarMovimentacao('saida');
    }

    // MÉTODO CENTRAL
    private function registrarMovimentacao($tipo)
    {
        $dados = $this->request->getJSON(true);

        // Verifica payload básico
        if (empty($dados) || empty($dados['produto_id']) || empty($dados['quantidade'])) {
            return $this->failValidationErrors('Parâmetros inválidos. Informe produto_id e quantidade.');
        }

        $usuarioId = service('authUser')->id();
        if (!$usuarioId) {
            return $this->failUnauthorized('Usuário não autenticado.');
        }

        $produtoModel = new ProdutoModel();
        $produto = $produtoModel->find($dados['produto_id']);

        if (!$produto) {
            return $this->failNotFound('Produto não encontrado.');
        }

        // Verifica estoque em caso de saída
        if ($tipo === 'saida' && $produto['quantidade'] < $dados['quantidade']) {
            return $this->fail('Quantidade insuficiente no estoque.');
        }

        // Calcula nova quantidade
        $novaQtd = ($tipo === 'entrada')
            ? $produto['quantidade'] + $dados['quantidade']
            : $produto['quantidade'] - $dados['quantidade'];

        // Dados da movimentação
        $dadosMov = [
            'produto_id' => $produto['id'],
            'usuario_id' => $usuarioId,
            'tipo'       => $tipo,
            'quantidade' => $dados['quantidade'],
            'data'       => date('Y-m-d H:i:s'),
        ];

        // Executa em transação: atualiza produto e insere movimentação
        $db = \Config\Database::connect();
        $db->transStart();

        $produtoModel->update($produto['id'], ['quantidade' => $novaQtd]);

        $insertId = $this->model->insert($dadosMov);

        $db->transComplete();

        if ($db->transStatus() === false || !$insertId) {
            // Tenta recuperar mensagens de erro do model
            $errors = $this->model->errors();

            // Garante que estoque volte ao valor anterior caso a transação tenha falhado
            try {
                $produtoModel->update($produto['id'], ['quantidade' => $produto['quantidade']]);
            } catch (\Exception $e) {
                // ignorar, já estamos em erro
            }

            if (!empty($errors)) {
                return $this->failValidationErrors($errors);
            }

            return $this->fail('Erro ao registrar movimentação. Tente novamente.');
        }

        return $this->respondCreated([
            'mensagem' => "Movimentação de {$tipo} registrada com sucesso.",
            'estoque_atual' => $novaQtd,
            'movimentacao_id' => $insertId,
        ]);
    }
}
