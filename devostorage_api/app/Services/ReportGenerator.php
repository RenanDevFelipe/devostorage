<?php namespace App\Services;

use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\MovimentacaoModel;
use App\Models\ProdutoModel;
use App\Models\UserModel;

class ReportGenerator
{
    protected $movimentacaoModel;
    protected $produtoModel;
    protected $userModel;

    public function __construct()
    {
        $this->movimentacaoModel = new MovimentacaoModel();
        $this->produtoModel = new ProdutoModel();
        $this->userModel = new UserModel();
    }

    /**
     * Gera relatório de movimentações em PDF
     * @param string|null $dataInicio
     * @param string|null $dataFim
     * @param string|null $produtoId
     * @return string Caminho do arquivo gerado
     */
    public function gerarPdfMovimentacoes($dataInicio = null, $dataFim = null, $produtoId = null)
    {
        $movimentacoes = $this->obterMovimentacoes($dataInicio, $dataFim, $produtoId);

        $html = $this->construirHtmlMovimentacoes($movimentacoes, $dataInicio, $dataFim);

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        $mpdf->WriteHTML($html);

        $nomeArquivo = 'relatorio_movimentacoes_' . date('Y-m-d_H-i-s') . '.pdf';
        $caminhoArquivo = 'uploads/' . $nomeArquivo;

        $mpdf->Output($caminhoArquivo, 'F');

        return $caminhoArquivo;
    }

    /**
     * Gera relatório de movimentações em Excel
     * @param string|null $dataInicio
     * @param string|null $dataFim
     * @param string|null $produtoId
     * @return string Caminho do arquivo gerado
     */
    public function gerarExcelMovimentacoes($dataInicio = null, $dataFim = null, $produtoId = null)
    {
        $movimentacoes = $this->obterMovimentacoes($dataInicio, $dataFim, $produtoId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Movimentações');

        // Estilo do cabeçalho
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '366092'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'border' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // Cabeçalhos
        $headers = ['ID', 'Data', 'Produto', 'Tipo', 'Quantidade', 'Usuário'];
        $sheet->fromArray([$headers], null, 'A1');

        // Aplicar estilo ao cabeçalho
        foreach (range('A', 'F') as $col) {
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
        }

        // Dados
        $row = 2;
        foreach ($movimentacoes as $mov) {
            $sheet->setCellValue('A' . $row, $mov['id']);
            $sheet->setCellValue('B' . $row, substr($mov['data'], 0, 10));
            $sheet->setCellValue('C' . $row, $mov['produto_nome'] ?? 'N/A');
            $sheet->setCellValue('D' . $row, ucfirst($mov['tipo']));
            $sheet->setCellValue('E' . $row, $mov['quantidade']);
            $sheet->setCellValue('F' . $row, $mov['usuario_nome'] ?? 'N/A');

            // Estilo das células
            $style = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'border' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ];
            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($style);
            $row++;
        }

        // Ajustar largura das colunas
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(20);

        // Salvar arquivo
        $nomeArquivo = 'relatorio_movimentacoes_' . date('Y-m-d_H-i-s') . '.xlsx';
        $caminhoArquivo = 'uploads/' . $nomeArquivo;

        $writer = new Xlsx($spreadsheet);
        $writer->save($caminhoArquivo);

        return $caminhoArquivo;
    }

    /**
     * Gera relatório de estoque em PDF
     * @return string Caminho do arquivo gerado
     */
    public function gerarPdfEstoque()
    {
        $produtos = $this->produtoModel->findAll();

        $html = $this->construirHtmlEstoque($produtos);

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        $mpdf->WriteHTML($html);

        $nomeArquivo = 'relatorio_estoque_' . date('Y-m-d_H-i-s') . '.pdf';
        $caminhoArquivo = 'uploads/' . $nomeArquivo;

        $mpdf->Output($caminhoArquivo, 'F');

        return $caminhoArquivo;
    }

    /**
     * Gera relatório de estoque em Excel
     * @return string Caminho do arquivo gerado
     */
    public function gerarExcelEstoque()
    {
        $produtos = $this->produtoModel->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Estoque');

        // Estilo do cabeçalho
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '27AE60'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'border' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // Cabeçalhos
        $headers = ['ID', 'Produto', 'Categoria', 'Quantidade', 'Preço Unit.', 'Valor Total'];
        $sheet->fromArray([$headers], null, 'A1');

        // Aplicar estilo ao cabeçalho
        foreach (range('A', 'F') as $col) {
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
        }

        // Dados
        $row = 2;
        $totalValor = 0;

        foreach ($produtos as $prod) {
            $valorTotal = $prod['quantidade'] * $prod['preco'];
            $totalValor += $valorTotal;

            $sheet->setCellValue('A' . $row, $prod['id']);
            $sheet->setCellValue('B' . $row, $prod['nome']);
            $sheet->setCellValue('C' . $row, $prod['categoria'] ?? 'S/C');
            $sheet->setCellValue('D' . $row, $prod['quantidade']);
            $sheet->setCellValue('E' . $row, $prod['preco']);
            $sheet->setCellValue('F' . $row, $valorTotal);

            // Formatar números como moeda
            $sheet->getStyle('E' . $row . ':F' . $row)->getNumberFormat()->setFormatCode('R$ #,##0.00');

            // Estilo das células
            $style = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'border' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ];
            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($style);
            $row++;
        }

        // Totalizador
        $sheet->setCellValue('B' . $row, 'TOTAL');
        $sheet->getStyle('B' . $row)->getFont()->setBold(true);
        $sheet->setCellValue('F' . $row, $totalValor);
        $sheet->getStyle('F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('R$ #,##0.00');

        // Ajustar largura das colunas
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);

        // Salvar arquivo
        $nomeArquivo = 'relatorio_estoque_' . date('Y-m-d_H-i-s') . '.xlsx';
        $caminhoArquivo = 'uploads/' . $nomeArquivo;

        $writer = new Xlsx($spreadsheet);
        $writer->save($caminhoArquivo);

        return $caminhoArquivo;
    }

    /**
     * Obtém movimentações com filtros
     */
    private function obterMovimentacoes($dataInicio = null, $dataFim = null, $produtoId = null)
    {
        $query = $this->movimentacaoModel;

        if ($dataInicio && $dataFim) {
            $query = $query
                ->where("data >=", $dataInicio . " 00:00:00")
                ->where("data <=", $dataFim . " 23:59:59");
        }

        if ($produtoId) {
            $query = $query->where('produto_id', $produtoId);
        }

        $movimentacoes = $query
            ->orderBy('data', 'DESC')
            ->findAll();

        // Enrichir dados com informações de produto e usuário
        foreach ($movimentacoes as &$mov) {
            $produto = $this->produtoModel->find($mov['produto_id']);
            $mov['produto_nome'] = $produto['nome'] ?? 'N/A';

            $usuario = $this->userModel->find($mov['usuario_id']);
            $mov['usuario_nome'] = $usuario['nome'] ?? 'N/A';
        }

        return $movimentacoes;
    }

    /**
     * Constrói HTML para relatório de movimentações
     */
    private function construirHtmlMovimentacoes($movimentacoes, $dataInicio, $dataFim)
    {
        $dataAtual = date('d/m/Y H:i:s');
        $periodo = '';

        if ($dataInicio && $dataFim) {
            $periodo = "Período: {$dataInicio} até {$dataFim}";
        } else {
            $periodo = "Todas as movimentações";
        }

        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { text-align: center; color: #366092; font-size: 20px; margin-bottom: 5px; }
            .info { text-align: center; font-size: 10px; color: #666; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th { background-color: #366092; color: white; padding: 8px; text-align: center; font-weight: bold; }
            td { padding: 7px; border: 1px solid #ddd; text-align: center; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .footer { margin-top: 20px; font-size: 9px; color: #666; }
        </style>
        <h1>Relatório de Movimentações</h1>
        <div class="info">
            <p>' . $periodo . '</p>
            <p>Gerado em: ' . $dataAtual . '</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Produto</th>
                    <th>Tipo</th>
                    <th>Quantidade</th>
                    <th>Usuário</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($movimentacoes as $mov) {
            $tipo = strtoupper($mov['tipo']);
            $dataBr = date('d/m/Y H:i', strtotime($mov['data']));
            $html .= '
                <tr>
                    <td>' . $mov['id'] . '</td>
                    <td>' . $dataBr . '</td>
                    <td>' . $mov['produto_nome'] . '</td>
                    <td><strong>' . $tipo . '</strong></td>
                    <td>' . $mov['quantidade'] . '</td>
                    <td>' . $mov['usuario_nome'] . '</td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>
        <div class="footer">
            <p>Total de registros: ' . count($movimentacoes) . '</p>
        </div>';

        return $html;
    }

    /**
     * Constrói HTML para relatório de estoque
     */
    private function construirHtmlEstoque($produtos)
    {
        $dataAtual = date('d/m/Y H:i:s');
        $totalItens = 0;
        $valorTotal = 0;

        foreach ($produtos as $p) {
            $totalItens += $p['quantidade'];
            $valorTotal += $p['quantidade'] * $p['preco'];
        }

        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { text-align: center; color: #27AE60; font-size: 20px; margin-bottom: 5px; }
            .info { text-align: center; font-size: 10px; color: #666; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th { background-color: #27AE60; color: white; padding: 8px; text-align: center; font-weight: bold; }
            td { padding: 7px; border: 1px solid #ddd; text-align: center; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .footer { margin-top: 20px; font-size: 9px; color: #666; }
        </style>
        <h1>Relatório de Estoque</h1>
        <div class="info">
            <p>Gerado em: ' . $dataAtual . '</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th>Qtd.</th>
                    <th>Preço Unit.</th>
                    <th>Valor Total</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($produtos as $p) {
            $valorTotal = $p['quantidade'] * $p['preco'];
            $precoFormatado = number_format($p['preco'], 2, ',', '.');
            $valorTotalFormatado = number_format($valorTotal, 2, ',', '.');

            $html .= '
                <tr>
                    <td>' . $p['id'] . '</td>
                    <td>' . $p['nome'] . '</td>
                    <td>' . ($p['categoria'] ?? 'S/C') . '</td>
                    <td>' . $p['quantidade'] . '</td>
                    <td>R$ ' . $precoFormatado . '</td>
                    <td>R$ ' . $valorTotalFormatado . '</td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>
        <div class="footer">
            <p>Total de produtos: ' . count($produtos) . ' | Total de itens: ' . $totalItens . '</p>
            <p>Valor total do estoque: R$ ' . number_format($valorTotal, 2, ',', '.') . '</p>
        </div>';

        return $html;
    }
}
