# 🚀 Guia de Início Rápido - Sistema de Movimentações e Relatórios

## ✅ O que foi implementado

### 1. **Movimentações Vinculadas ao Produto** ✨
- Todas as movimentações agora incluem dados do produto (nome, categoria)
- Relacionamento via chave estrangeira já estava configurado
- Novos métodos no model para facilitar consultas relacionadas

### 2. **Gerador de Relatórios em PDF e Excel** 📄
- Exportação de estoque em **PDF** e **Excel**
- Exportação de movimentações em **PDF** e **Excel**
- Filtros por período e produto
- Formatação profissional com cores, bordas e moeda formatada

---

## 🔧 Instalação (Se ainda não fez)

### Passo 1: Instalar Dependências
```bash
cd c:\xampp\htdocs\devostorange\devostorange_api
composer require --ignore-platform-reqs mpdf/mpdf phpoffice/phpspreadsheet
```

### Passo 2: Criar Pasta de Uploads
```bash
mkdir writable\uploads
```

### Passo 3: Atualizar Banco de Dados
```bash
php spark migrate
```

---

## 📝 Endpoints Disponíveis

### 🔐 Autenticação (sem JWT)
```
POST /api/users/login
  Body: { "email": "seu@email.com", "password": "senha" }
  Response: { "access_token": "...", "token_type": "Bearer" }
```

### 📦 Movimentações (com JWT)
```
GET  /api/movimentacoes              - Listar todas
POST /api/movimentacoes/entrada      - Registrar entrada
POST /api/movimentacoes/saida        - Registrar saída
```

### 📊 Relatórios (com JWT)
```
GET /api/relatorios/estoque                      - JSON
GET /api/relatorios/estoque/pdf                  - PDF
GET /api/relatorios/estoque/excel                - Excel

GET /api/relatorios/movimentacoes                - JSON (com filtros)
GET /api/relatorios/movimentacoes/pdf            - PDF (com filtros)
GET /api/relatorios/movimentacoes/excel          - Excel (com filtros)

GET /api/relatorios/produto/:id/movimentacoes    - Detalhes do produto
```

### 💾 Download
```
GET /download/:filename               - Download direto (sem JWT)
GET /api/download/:filename           - Info do arquivo (com JWT)
```

---

## 🎯 Exemplos Rápidos

### 1️⃣ Fazer Login
```bash
curl -X POST "http://localhost/api/users/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@email.com","password":"password"}'
```

Salve o `access_token` retornado como `TOKEN`

### 2️⃣ Registrar Entrada
```bash
curl -X POST "http://localhost/api/movimentacoes/entrada" \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"produto_id":1,"quantidade":50}'
```

### 3️⃣ Listar Movimentações
```bash
curl -X GET "http://localhost/api/movimentacoes" \
  -H "Authorization: Bearer TOKEN"
```

### 4️⃣ Exportar Estoque em Excel
```bash
curl -X GET "http://localhost/api/relatorios/estoque/excel" \
  -H "Authorization: Bearer TOKEN"
```

Resposta:
```json
{
  "mensagem": "Excel de estoque gerado com sucesso.",
  "arquivo": "relatorio_estoque_2025-11-29_14-30-45.xlsx",
  "url": "http://localhost/writable/uploads/relatorio_estoque_2025-11-29_14-30-45.xlsx"
}
```

### 5️⃣ Exportar Movimentações em PDF por Período
```bash
curl -X GET "http://localhost/api/relatorios/movimentacoes/pdf?inicio=2025-11-20&fim=2025-11-29" \
  -H "Authorization: Bearer TOKEN"
```

### 6️⃣ Obter Movimentações de um Produto
```bash
curl -X GET "http://localhost/api/relatorios/produto/1/movimentacoes" \
  -H "Authorization: Bearer TOKEN"
```

---

## 📚 Documentação Completa

| Arquivo | Descrição |
|---------|-----------|
| **RELATORIOS.md** | Documentação técnica de todos os endpoints |
| **EXEMPLOS_PRATICOS.md** | 15 exemplos práticos de uso com curl e JavaScript |
| **ARQUITETURA.md** | Diagramas e fluxos de dados |
| **TROUBLESHOOTING.md** | Resolução de problemas e FAQ |
| **IMPLEMENTACAO_RESUMO.md** | Resumo das mudanças realizadas |

---

## 🗂️ Arquivos Modificados/Criados

### ✨ Novos Arquivos
- `app/Services/ReportGenerator.php` - Gerador de relatórios
- `app/Controllers/DownloadController.php` - Controle de downloads
- `RELATORIOS.md` - Documentação de relatórios
- `EXEMPLOS_PRATICOS.md` - Exemplos de uso
- `TROUBLESHOOTING.md` - Guia de troubleshooting
- `ARQUITETURA.md` - Diagramas de arquitetura
- `IMPLEMENTACAO_RESUMO.md` - Resumo de implementação

### ✏️ Arquivos Atualizados
- `app/Controllers/MovimentacaoController.php` - Melhorado com detalhes
- `app/Controllers/RelatorioController.php` - Novos endpoints de exportação
- `app/Models/MovimentacaoModel.php` - Novos métodos e relacionamentos
- `app/Config/Routes.php` - Novas rotas

---

## 🎨 Recursos do Gerador de Relatórios

### PDF
- ✅ Cabeçalhos com cores personalizadas
- ✅ Tabelas formatadas
- ✅ Moeda em Real Brasileiro (R$)
- ✅ Data e hora de geração
- ✅ Totalizadores automáticos

### Excel
- ✅ Cabeçalhos com fundo colorido
- ✅ Bordas e formatação de células
- ✅ Moeda formatada (R$)
- ✅ Cálculos automáticos
- ✅ Ajuste automático de largura de colunas

---

## 🔐 Segurança

- ✅ Todos os endpoints de relatório requerem JWT
- ✅ Downloads protegidos com validação de nome de arquivo
- ✅ Validações de dados nas movimentações
- ✅ Relacionamentos de chave estrangeira no banco

---

## 📊 Estrutura do Banco

### Tabela: movimentacoes
```sql
CREATE TABLE movimentacoes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  produto_id INT NOT NULL,        -- FK para produtos
  usuario_id INT NOT NULL,        -- FK para users
  tipo ENUM('entrada', 'saida'),
  quantidade INT,
  data DATETIME,
  created_at DATETIME,
  updated_at DATETIME,
  FOREIGN KEY (produto_id) REFERENCES produtos(id),
  FOREIGN KEY (usuario_id) REFERENCES users(id)
);
```

---

## 🧪 Teste Rápido

1. **Inicie o servidor**
   ```bash
   cd c:\xampp\htdocs\devostorange\devostorange_api
   php spark serve
   ```

2. **Em outro terminal, teste a API**
   ```bash
   # Fazer login
   curl -X POST http://localhost:8080/api/users/login \
     -H "Content-Type: application/json" \
     -d '{"email":"admin@email.com","password":"password"}'
   
   # Listar movimentações
   curl -X GET http://localhost:8080/api/movimentacoes \
     -H "Authorization: Bearer SEU_TOKEN"
   ```

---

## 📲 Usando em Frontend

### JavaScript/React
```javascript
const token = localStorage.getItem('token');

// Exportar estoque
const response = await fetch('/api/relatorios/estoque/excel', {
  headers: { 'Authorization': `Bearer ${token}` }
});
const data = await response.json();
window.open(data.url);
```

### Vue.js
```javascript
async exportarEstoque() {
  const response = await this.$axios.get('/api/relatorios/estoque/excel');
  window.location.href = response.data.url;
}
```

---

## 🚀 Próximos Passos

1. **Testes Automatizados** - Criar testes unitários para os endpoints
2. **Cache** - Implementar cache para relatórios frequentes
3. **Email** - Enviar relatórios por email automaticamente
4. **Agendamento** - Cron jobs para gerar relatórios diários/mensais
5. **Gráficos** - Adicionar gráficos aos relatórios PDF/Excel
6. **Dashboard** - Criar dashboard em tempo real

---

## 💡 Dicas Úteis

- Os arquivos PDF/Excel são salvos em `writable/uploads/`
- Use timestamps nos nomes para evitar conflitos
- Filtre por produto_id para relatórios específicos
- Combine inicio e fim para períodos específicos
- Use `/api/relatorios/produto/:id/movimentacoes` para análise detalhada

---

## 📞 Suporte

Se encontrar problemas:
1. Consulte `TROUBLESHOOTING.md`
2. Verifique os logs em `writable/logs/`
3. Rode `php spark routes` para verificar rotas
4. Use `composer dump-autoload -o` para regenerar autoload

---

**Desenvolvido com ❤️ usando CodeIgniter 4**
