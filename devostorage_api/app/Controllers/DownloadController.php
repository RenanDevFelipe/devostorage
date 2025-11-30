<?php namespace App\Controllers;

use CodeIgniter\Controller;

class DownloadController extends Controller
{
    /**
     * Faz download de um arquivo de relatório
     * GET /download/:filename
     */
    public function arquivo($filename = null)
    {
        if (!$filename) {
            return redirect()->back()->with('error', 'Arquivo não especificado.');
        }

        // Validar nome do arquivo para prevenir directory traversal
        $filename = basename($filename);
        $filepath = 'uploads/' . $filename;

        // Verificar se o arquivo existe
        if (!file_exists($filepath)) {
            return redirect()->back()->with('error', 'Arquivo não encontrado.');
        }

        // Definir headers para download
        return $this->response
            ->download($filepath, null);
    }

    /**
     * API para fazer download via JSON
     * GET /api/download/:filename
     */
    public function downloadJson($filename = null)
    {
        if (!$filename) {
            return response()->setJSON([
                'erro' => 'Arquivo não especificado.'
            ])->setStatusCode(400);
        }

        // Validar nome do arquivo
        $filename = basename($filename);
        $filepath = 'uploads/' . $filename;

        // Verificar se o arquivo existe
        if (!file_exists($filepath)) {
            return response()->setJSON([
                'erro' => 'Arquivo não encontrado.'
            ])->setStatusCode(404);
        }

        // Retornar URL para download
        return response()->setJSON([
            'mensagem' => 'Arquivo pronto para download.',
            'arquivo' => $filename,
            'url' => 'https://devotech.com.br/devostorange/devostorange_api/public/download/' . $filename,
            'tamanho' => filesize($filepath) . ' bytes',
            'criado_em' => date('d/m/Y H:i:s', filemtime($filepath))
        ])->setStatusCode(200);
    }

    /**
     * Lista todos os arquivos de relatório gerados
     * GET /api/downloads
     */
    public function listar()
    {
        $uploadDir = 'uploads/';

        // Verificar se a pasta existe
        if (!is_dir($uploadDir)) {
            return response()->setJSON([
                'mensagem' => 'Nenhum arquivo encontrado.',
                'arquivos' => []
            ])->setStatusCode(200);
        }

        // Listar apenas arquivos (não diretórios)
        $files = array_filter(scandir($uploadDir), function ($file) use ($uploadDir) {
            return is_file($uploadDir . $file) && $file !== 'index.html';
        });

        $arquivos = [];
        foreach ($files as $file) {
            $filepath = $uploadDir . $file;
            $arquivos[] = [
                'nome' => $file,
                'tamanho' => filesize($filepath),
                'tamanho_formatado' => $this->formatarTamanho(filesize($filepath)),
                'criado_em' => date('d/m/Y H:i:s', filemtime($filepath)),
                'url_download' => 'https://devotech.com.br/devostorange/devostorange_api/public/download/' . $file,
                'url_info' => 'http://devotech.com.br/devostorange/devostorange_api/public/api/download/' . $file
            ];
        }

        // Ordenar por data (mais recentes primeiro)
        usort($arquivos, function ($a, $b) {
            return strtotime($b['criado_em']) - strtotime($a['criado_em']);
        });

        return response()->setJSON([
            'mensagem' => 'Arquivos de relatório disponíveis.',
            'total' => count($arquivos),
            'arquivos' => $arquivos
        ])->setStatusCode(200);
    }

    /**
     * Formata tamanho de arquivo em bytes para formato legível
     */
    private function formatarTamanho($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
