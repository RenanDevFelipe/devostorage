# Exemplos Práticos - API de Movimentações e Relatórios

## 1. Registrar Movimentação de Entrada

```bash
curl -X POST "http://localhost/api/movimentacoes/entrada" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "produto_id": 1,
    "quantidade": 50
  }'
```

**Resposta Esperada:**
```json
{
    "mensagem": "Movimentação de entrada registrada com sucesso.",
    "estoque_atual": 150
}
```

---

## 2. Registrar Movimentação de Saída

```bash
curl -X POST "http://localhost/api/movimentacoes/saida" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "produto_id": 1,
    "quantidade": 10
  }'
```

**Resposta Esperada:**
```json
{
    "mensagem": "Movimentação de saida registrada com sucesso.",
    "estoque_atual": 140
}
```

---

## 3. Listar Todas as Movimentações com Detalhes

```bash
curl -X GET "http://localhost/api/movimentacoes" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Resposta Esperada:**
```json
[
    {
        "id": 1,
        "produto_id": 1,
        "usuario_id": 1,
        "tipo": "entrada",
        "quantidade": 50,
        "data": "2025-11-29 10:30:00",
        "produto_nome": "Notebook Dell",
        "categoria": "Eletrônicos",
        "usuario_nome": "João Silva"
    },
    {
        "id": 2,
        "produto_id": 1,
        "usuario_id": 1,
        "tipo": "saida",
        "quantidade": 10,
        "data": "2025-11-29 11:45:00",
        "produto_nome": "Notebook Dell",
        "categoria": "Eletrônicos",
        "usuario_nome": "João Silva"
    }
]
```

---

## 4. Relatório de Estoque (JSON)

```bash
curl -X GET "http://localhost/api/relatorios/estoque" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Resposta Esperada:**
```json
{
    "total_produtos": 5,
    "total_itens_estoque": 500,
    "valor_total_estoque": 45000.50,
    "produtos": [
        {
            "id": 1,
            "nome": "Notebook Dell",
            "categoria": "Eletrônicos",
            "quantidade": 140,
            "preco": "2500.00",
            "valor_total": 350000.00
        },
        {
            "id": 2,
            "nome": "Mouse Logitech",
            "categoria": "Periféricos",
            "quantidade": 360,
            "preco": "50.00",
            "valor_total": 18000.00
        }
    ]
}
```

---

## 5. Exportar Relatório de Estoque em PDF

```bash
curl -X GET "http://localhost/api/relatorios/estoque/pdf" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Resposta Esperada:**
```json
{
    "mensagem": "PDF de estoque gerado com sucesso.",
    "arquivo": "relatorio_estoque_2025-11-29_14-30-45.pdf",
    "url": "http://localhost/writable/uploads/relatorio_estoque_2025-11-29_14-30-45.pdf"
}
```

---

## 6. Exportar Relatório de Estoque em Excel

```bash
curl -X GET "http://localhost/api/relatorios/estoque/excel" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Resposta Esperada:**
```json
{
    "mensagem": "Excel de estoque gerado com sucesso.",
    "arquivo": "relatorio_estoque_2025-11-29_14-30-45.xlsx",
    "url": "http://localhost/writable/uploads/relatorio_estoque_2025-11-29_14-30-45.xlsx"
}
```

---

## 7. Relatório de Movimentações (JSON) - Período Específico

```bash
curl -X GET "http://localhost/api/relatorios/movimentacoes?inicio=2025-11-20&fim=2025-11-29" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Resposta Esperada:**
```json
{
    "periodo": {
        "inicio": "2025-11-20",
        "fim": "2025-11-29"
    },
    "produto_id": null,
    "total_registros": 10,
    "movimentacoes": [
        {
            "id": 1,
            "produto_id": 1,
            "usuario_id": 1,
            "tipo": "entrada",
            "quantidade": 50,
            "data": "2025-11-29 10:30:00",
            "produto_nome": "Notebook Dell",
            "categoria": "Eletrônicos",
            "usuario_nome": "João Silva"
        }
    ]
}
```

---

## 8. Exportar Movimentações em PDF por Período

```bash
curl -X GET "http://localhost/api/relatorios/movimentacoes/pdf?inicio=2025-11-20&fim=2025-11-29" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Resposta Esperada:**
```json
{
    "mensagem": "PDF gerado com sucesso.",
    "arquivo": "relatorio_movimentacoes_2025-11-29_14-30-45.pdf",
    "url": "http://localhost/writable/uploads/relatorio_movimentacoes_2025-11-29_14-30-45.pdf"
}
```

---

## 9. Exportar Movimentações em Excel de um Produto Específico

```bash
curl -X GET "http://localhost/api/relatorios/movimentacoes/excel?produto_id=1" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Resposta Esperada:**
```json
{
    "mensagem": "Excel gerado com sucesso.",
    "arquivo": "relatorio_movimentacoes_2025-11-29_14-30-45.xlsx",
    "url": "http://localhost/writable/uploads/relatorio_movimentacoes_2025-11-29_14-30-45.xlsx"
}
```

---

## 10. Relatório Detalhado de um Produto

```bash
curl -X GET "http://localhost/api/relatorios/produto/1/movimentacoes" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Resposta Esperada:**
```json
{
    "produto": {
        "id": 1,
        "nome": "Notebook Dell",
        "categoria": "Eletrônicos",
        "quantidade": 140,
        "preco": "2500.00"
    },
    "resumo": {
        "total_entradas": 250,
        "total_saidas": 110,
        "saldo": 140
    },
    "movimentacoes": [
        {
            "id": 1,
            "produto_id": 1,
            "usuario_id": 1,
            "tipo": "entrada",
            "quantidade": 50,
            "data": "2025-11-29 10:30:00",
            "produto_nome": "Notebook Dell",
            "categoria": "Eletrônicos",
            "usuario_nome": "João Silva"
        }
    ]
}
```

---

## 11. Download Direto de Arquivo PDF

```bash
curl -X GET "http://localhost/download/relatorio_estoque_2025-11-29_14-30-45.pdf" \
  --output "relatorio.pdf"
```

---

## 12. Informações sobre Arquivo via API JSON

```bash
curl -X GET "http://localhost/api/download/relatorio_estoque_2025-11-29_14-30-45.pdf" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

**Resposta Esperada:**
```json
{
    "mensagem": "Arquivo pronto para download.",
    "arquivo": "relatorio_estoque_2025-11-29_14-30-45.pdf",
    "url": "http://localhost/download/relatorio_estoque_2025-11-29_14-30-45.pdf",
    "tamanho": "45230 bytes",
    "criado_em": "29/11/2025 14:30:45"
}
```

---

## 13. Filtros Combinados - Movimentações com Período e Produto

```bash
curl -X GET "http://localhost/api/relatorios/movimentacoes?inicio=2025-11-20&fim=2025-11-29&produto_id=1" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

---

## 14. Exemplo com JavaScript/Fetch

```javascript
// Exportar estoque em Excel
async function exportarEstoque() {
  const token = localStorage.getItem('jwt_token');
  
  try {
    const response = await fetch('http://localhost/api/relatorios/estoque/excel', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });
    
    const data = await response.json();
    
    if (response.ok) {
      console.log('Excel gerado:', data.arquivo);
      // Redirecionar para download
      window.location.href = data.url;
    } else {
      console.error('Erro:', data.message);
    }
  } catch (error) {
    console.error('Erro na requisição:', error);
  }
}

// Gerar movimentação de entrada
async function registrarEntrada(produtoId, quantidade) {
  const token = localStorage.getItem('jwt_token');
  
  try {
    const response = await fetch('http://localhost/api/movimentacoes/entrada', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        produto_id: produtoId,
        quantidade: quantidade
      })
    });
    
    const data = await response.json();
    
    if (response.ok) {
      console.log('Entrada registrada:', data);
    } else {
      console.error('Erro:', data.message);
    }
  } catch (error) {
    console.error('Erro na requisição:', error);
  }
}

// Obter relatório de movimentações de um produto
async function obterMovimentacoesProduto(produtoId) {
  const token = localStorage.getItem('jwt_token');
  
  try {
    const response = await fetch(`http://localhost/api/relatorios/produto/${produtoId}/movimentacoes`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });
    
    const data = await response.json();
    
    if (response.ok) {
      console.log('Produto:', data.produto);
      console.log('Resumo:', data.resumo);
      console.log('Movimentações:', data.movimentacoes);
    } else {
      console.error('Erro:', data.message);
    }
  } catch (error) {
    console.error('Erro na requisição:', error);
  }
}
```

---

## 15. Exemplo com Python/Requests

```python
import requests
from datetime import datetime, timedelta

# Configurações
BASE_URL = 'http://localhost'
API_URL = f'{BASE_URL}/api'
TOKEN = 'seu_token_jwt_aqui'
HEADERS = {
    'Authorization': f'Bearer {TOKEN}',
    'Content-Type': 'application/json'
}

# Registrar entrada
def registrar_entrada(produto_id, quantidade):
    url = f'{API_URL}/movimentacoes/entrada'
    data = {
        'produto_id': produto_id,
        'quantidade': quantidade
    }
    response = requests.post(url, json=data, headers=HEADERS)
    return response.json()

# Exportar estoque em Excel
def exportar_estoque_excel():
    url = f'{API_URL}/relatorios/estoque/excel'
    response = requests.get(url, headers=HEADERS)
    return response.json()

# Obter movimentações do mês atual
def movimentacoes_mes_atual():
    hoje = datetime.now()
    inicio = hoje.replace(day=1).strftime('%Y-%m-%d')
    fim = hoje.strftime('%Y-%m-%d')
    
    url = f'{API_URL}/relatorios/movimentacoes?inicio={inicio}&fim={fim}'
    response = requests.get(url, headers=HEADERS)
    return response.json()

# Gerar PDF de movimentações do período
def gerar_pdf_movimentacoes(inicio, fim, produto_id=None):
    url = f'{API_URL}/relatorios/movimentacoes/pdf?inicio={inicio}&fim={fim}'
    if produto_id:
        url += f'&produto_id={produto_id}'
    
    response = requests.get(url, headers=HEADERS)
    return response.json()

# Exemplo de uso
if __name__ == '__main__':
    # Registrar entrada
    print("Registrando entrada...")
    resultado = registrar_entrada(1, 50)
    print(resultado)
    
    # Exportar estoque
    print("\nExportando estoque...")
    excel = exportar_estoque_excel()
    print(f"Arquivo gerado: {excel['arquivo']}")
    
    # Movimentações do mês
    print("\nMovimentações do mês atual...")
    movs = movimentacoes_mes_atual()
    print(f"Total: {movs['total_registros']} registros")
```

---

## 🔑 Notas Importantes

1. **Token JWT**: Substitua `YOUR_JWT_TOKEN` pelo seu token válido obtido em `/api/users/login`
2. **Data**: Use o formato `YYYY-mm-dd` para os parâmetros de data
3. **Produto ID**: Valores numéricos válidos que existem no banco
4. **Quantidade**: Deve ser um número inteiro maior que 0
5. **URLs de Download**: Os arquivos podem ser baixados diretamente pela URL retornada

