# Guia de Relatórios e Exportações - DevOS ToRange API

## Visão Geral

O sistema foi atualizado com funcionalidades completas de:
1. **Movimentações vinculadas a Produtos** - Todas as movimentações agora incluem informações do produto
2. **Gerador de Relatórios** - Exportação de relatórios em PDF e Excel

## Rotas de Relatórios

### 1. Relatório de Estoque (JSON)
```
GET /api/relatorios/estoque
```
Retorna o estoque completo com:
- Total de produtos
- Total de itens em estoque
- Valor total do estoque
- Lista de produtos com detalhes

### 2. Relatório de Estoque (PDF)
```
GET /api/relatorios/estoque/pdf
```
Gera um PDF com relatório de estoque e retorna:
```json
{
    "mensagem": "PDF de estoque gerado com sucesso.",
    "arquivo": "relatorio_estoque_2025-11-29_12-30-45.pdf",
    "url": "http://localhost/writable/uploads/relatorio_estoque_2025-11-29_12-30-45.pdf"
}
```

### 3. Relatório de Estoque (Excel)
```
GET /api/relatorios/estoque/excel
```
Gera um arquivo Excel com relatório de estoque.

### 4. Relatório de Movimentações (JSON)
```
GET /api/relatorios/movimentacoes?inicio=YYYY-mm-dd&fim=YYYY-mm-dd&produto_id=1
```
Parâmetros opcionais:
- `inicio` - Data inicial (formato: YYYY-mm-dd)
- `fim` - Data final (formato: YYYY-mm-dd)
- `produto_id` - Filtrar por produto específico

Retorna:
- Período da busca
- Total de registros
- Lista de movimentações com dados do produto e usuário

### 5. Relatório de Movimentações (PDF)
```
GET /api/relatorios/movimentacoes/pdf?inicio=YYYY-mm-dd&fim=YYYY-mm-dd&produto_id=1
```
Gera PDF com as movimentações filtradas.

### 6. Relatório de Movimentações (Excel)
```
GET /api/relatorios/movimentacoes/excel?inicio=YYYY-mm-dd&fim=YYYY-mm-dd&produto_id=1
```
Gera Excel com as movimentações filtradas.

### 7. Movimentações de um Produto Específico
```
GET /api/relatorios/produto/:id/movimentacoes
```
Retorna todas as movimentações de um produto com resumo:
- Total de entradas
- Total de saídas
- Saldo (entradas - saídas)

## Endpoints de Movimentações Atualizados

### Listar Movimentações
```
GET /api/movimentacoes
```
Agora retorna com informações do produto e usuário:
```json
[
    {
        "id": 1,
        "produto_id": 5,
        "usuario_id": 1,
        "tipo": "entrada",
        "quantidade": 10,
        "data": "2025-11-29 10:30:00",
        "produto_nome": "Produto A",
        "categoria": "Eletrônicos",
        "usuario_nome": "João Silva"
    }
]
```

### Registrar Entrada
```
POST /api/movimentacoes/entrada
Content-Type: application/json

{
    "produto_id": 5,
    "quantidade": 10
}
```

### Registrar Saída
```
POST /api/movimentacoes/saida
Content-Type: application/json

{
    "produto_id": 5,
    "quantidade": 3
}
```

## Download de Arquivos

### Download HTTP
```
GET /download/relatorio_estoque_2025-11-29_12-30-45.pdf
```

### Download via API JSON
```
GET /api/download/relatorio_estoque_2025-11-29_12-30-45.pdf
```

Retorna:
```json
{
    "mensagem": "Arquivo pronto para download.",
    "arquivo": "relatorio_estoque_2025-11-29_12-30-45.pdf",
    "url": "http://localhost/download/relatorio_estoque_2025-11-29_12-30-45.pdf",
    "tamanho": "45230 bytes",
    "criado_em": "29/11/2025 12:30:45"
}
```

## Exemplos de Uso

### Exemplo 1: Gerar relatório de estoque em Excel
```bash
curl -X GET "http://localhost/api/relatorios/estoque/excel" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Exemplo 2: Gerar relatório de movimentações por período em PDF
```bash
curl -X GET "http://localhost/api/relatorios/movimentacoes/pdf?inicio=2025-11-20&fim=2025-11-29" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Exemplo 3: Gerar relatório de movimentações de um produto específico em Excel
```bash
curl -X GET "http://localhost/api/relatorios/movimentacoes/excel?produto_id=5" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Exemplo 4: Obter movimentações de um produto
```bash
curl -X GET "http://localhost/api/relatorios/produto/5/movimentacoes" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

## Estrutura de Classes

### ReportGenerator
Localização: `app/Services/ReportGenerator.php`

Métodos principais:
- `gerarPdfMovimentacoes()` - Gera PDF de movimentações
- `gerarExcelMovimentacoes()` - Gera Excel de movimentações
- `gerarPdfEstoque()` - Gera PDF de estoque
- `gerarExcelEstoque()` - Gera Excel de estoque

### MovimentacaoModel (Atualizado)
Métodos auxiliares adicionados:
- `comProduto()` - Join com dados do produto
- `comDetalhes()` - Join com produto e usuário
- `porProduto($id)` - Movimentações de um produto
- `porPeriodo($inicio, $fim)` - Movimentações por período
- `porTipo($tipo)` - Movimentações por tipo (entrada/saída)

## Dependências Instaladas

```json
{
    "mpdf/mpdf": "^8.2",
    "phpoffice/phpspreadsheet": "^5.3"
}
```

## Diretório de Upload

Os arquivos gerados são salvos em:
```
writable/uploads/
```

Certifique-se que a pasta existe e tem permissão de escrita.

## Notas Importantes

1. **Autenticação**: Todos os endpoints de relatório e download via API requerem autenticação JWT
2. **Nomes de Arquivo**: Os arquivos são gerados com timestamp para evitar conflitos
3. **Localização do Banco**: Os relatórios consultam o banco de dados em tempo real
4. **Formatação**: 
   - PDFs usam MPDF
   - Excels usam PHPSpreadsheet
   - Moeda é formatada como R$ (Real brasileiro)

## Troubleshooting

### Erro ao gerar PDF/Excel
- Verifique se a pasta `writable/uploads` existe
- Verifique permissões de escrita no diretório
- Verifique se as dependências estão instaladas: `composer require --ignore-platform-reqs mpdf/mpdf phpoffice/phpspreadsheet`

### Arquivo não encontrado no download
- Verifique o nome exato do arquivo retornado na resposta
- O arquivo é armazenado em `writable/uploads/`

### Dados vazios nos relatórios
- Verifique se os filtros estão corretos
- Confirme que há registros no banco de dados para o período/produto solicitado
