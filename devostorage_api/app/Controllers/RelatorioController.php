<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProdutoModel;
use App\Models\MovimentacaoModel;
use App\Services\ReportGenerator;

class RelatorioController extends ResourceController
{
    protected $format = 'json';
    protected $request;
    protected $reportGenerator;

    public function __construct()
    {
        $this->request = request();
        $this->reportGenerator = new ReportGenerator();
    }

    /**
     * Relatório completo de estoque
     * GET /relatorios/estoque
     */
    public function estoque()
    {
        $produtoModel = new ProdutoModel();

        $produtos = $produtoModel->findAll();

        if (empty($produtos)) {
            return $this->respond([
                'mensagem' => 'Nenhum produto cadastrado.'
            ]);
        }

        $totalItens = 0;
        $valorTotal = 0;

        foreach ($produtos as &$p) {
            $p['valor_total'] = $p['quantidade'] * $p['preco'];

            $totalItens += $p['quantidade'];
            $valorTotal += $p['valor_total'];
        }

        return $this->respond([
            'total_produtos' => count($produtos),
            'total_itens_estoque' => $totalItens,
            'valor_total_estoque' => $valorTotal,
            'produtos' => $produtos
        ]);
    }

    /**
     * Relatório de movimentações
     * GET /relatorios/movimentacoes?inicio=YYYY-mm-dd&fim=YYYY-mm-dd&produto_id=1
     */
    public function movimentacoes()
    {
        $inicio = $this->request->getGet('inicio');
        $fim    = $this->request->getGet('fim');
        $produtoId = $this->request->getGet('produto_id');

        $movModel = new MovimentacaoModel();

        $query = $movModel->comDetalhes();

        if ($inicio && $fim) {
            $query = $query
                ->where("movimentacoes.data >=", $inicio . " 00:00:00")
                ->where("movimentacoes.data <=", $fim . " 23:59:59");
        }

        if ($produtoId) {
            $query = $query->where('movimentacoes.produto_id', $produtoId);
        }

        $mov = $query
            ->orderBy('movimentacoes.data', 'DESC')
            ->findAll();

        return $this->respond([
            'periodo' => [
                'inicio' => $inicio,
                'fim'    => $fim
            ],
            'produto_id' => $produtoId,
            'total_registros' => count($mov),
            'movimentacoes' => $mov
        ]);
    }

    /**
     * Exporta relatório de movimentações em PDF
     * GET /relatorios/movimentacoes/pdf?inicio=YYYY-mm-dd&fim=YYYY-mm-dd&produto_id=1
     */
    public function movimentacoesPdf()
    {
        $inicio = $this->request->getGet('inicio');
        $fim    = $this->request->getGet('fim');
        $produtoId = $this->request->getGet('produto_id');

        try {
            $caminhoArquivo = $this->reportGenerator->gerarPdfMovimentacoes($inicio, $fim, $produtoId);

            return $this->respondCreated([
                'mensagem' => 'PDF gerado com sucesso.',
                'arquivo' => basename($caminhoArquivo),
                'url' => 'https://devotech.com.br/devostorange/devostorange_api/public/uploads/' . basename($caminhoArquivo)
            ]);
        } catch (\Exception $e) {
            return $this->fail('Erro ao gerar PDF: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Exporta relatório de movimentações em Excel
     * GET /relatorios/movimentacoes/excel?inicio=YYYY-mm-dd&fim=YYYY-mm-dd&produto_id=1
     */
    public function movimentacoesExcel()
    {
        $inicio = $this->request->getGet('inicio');
        $fim    = $this->request->getGet('fim');
        $produtoId = $this->request->getGet('produto_id');

        try {
            $caminhoArquivo = $this->reportGenerator->gerarExcelMovimentacoes($inicio, $fim, $produtoId);

            return $this->respondCreated([
                'mensagem' => 'Excel gerado com sucesso.',
                'arquivo' => basename($caminhoArquivo),
                'url' => 'https://devotech.com.br/devostorange/devostorange_api/public/uploads/' . basename($caminhoArquivo)
            ]);
        } catch (\Exception $e) {
            return $this->fail('Erro ao gerar Excel: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Exporta relatório de estoque em PDF
     * GET /relatorios/estoque/pdf
     */
    public function estoquePdf()
    {
        try {
            $caminhoArquivo = $this->reportGenerator->gerarPdfEstoque();

            return $this->respondCreated([
                'mensagem' => 'PDF de estoque gerado com sucesso.',
                'arquivo' => basename($caminhoArquivo),
                'url' => 'https://devotech.com.br/devostorange/devostorange_api/public/uploads/' . basename($caminhoArquivo)
            ]);
        } catch (\Exception $e) {
            return $this->fail('Erro ao gerar PDF: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Exporta relatório de estoque em Excel
     * GET /relatorios/estoque/excel
     */
    public function estoqueExcel()
    {
        try {
            $caminhoArquivo = $this->reportGenerator->gerarExcelEstoque();

            return $this->respondCreated([
                'mensagem' => 'Excel de estoque gerado com sucesso.',
                'arquivo' => basename($caminhoArquivo),
                'url' => 'https://devotech.com.br/devostorange/devostorange_api/public/uploads/' . basename($caminhoArquivo)
            ]);
        } catch (\Exception $e) {
            return $this->fail('Erro ao gerar Excel: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Relatório detalhado de movimentações de um produto específico
     * GET /relatorios/produto/:id/movimentacoes
     */
    public function movimentacoesProduto($produtoId = null)
    {
        if (!$produtoId) {
            return $this->fail('ID do produto é obrigatório.', 400);
        }

        $produtoModel = new ProdutoModel();
        $produto = $produtoModel->find($produtoId);

        if (!$produto) {
            return $this->failNotFound('Produto não encontrado.');
        }

        $movModel = new MovimentacaoModel();
        $movimentacoes = $movModel->porProduto($produtoId);

        $totalEntrada = 0;
        $totalSaida = 0;

        foreach ($movimentacoes as $mov) {
            if ($mov['tipo'] === 'entrada') {
                $totalEntrada += $mov['quantidade'];
            } else {
                $totalSaida += $mov['quantidade'];
            }
        }

        return $this->respond([
            'produto' => $produto,
            'resumo' => [
                'total_entradas' => $totalEntrada,
                'total_saidas' => $totalSaida,
                'saldo' => $totalEntrada - $totalSaida
            ],
            'movimentacoes' => $movimentacoes
        ]);
    }
}
