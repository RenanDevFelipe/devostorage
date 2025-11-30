# Troubleshooting e FAQ - Sistema de Relatórios

## ❌ Problemas Comuns

### 1. "Undefined type 'Mpdf\Mpdf'" no Editor

**Problema**: VS Code mostra erro de tipo indefinido para as classes MPDF e PhpSpreadsheet

**Solução**: 
- Isso é apenas um erro do Intellisense do editor
- O código funciona normalmente em tempo de execução
- Para corrigir no editor, execute: `composer dump-autoload`

```bash
cd c:\xampp\htdocs\devostorange\devostorange_api
composer dump-autoload
```

---

### 2. "Classe não encontrada" em tempo de execução

**Erro**: 
```
Uncaught Exception: Undefined type 'App\Services\ReportGenerator'
```

**Solução**:
1. Verifique se o arquivo existe em `app/Services/ReportGenerator.php`
2. Execute: `composer dump-autoload -o`
3. Reinicie o servidor

```bash
composer dump-autoload -o
php spark serve
```

---

### 3. "Arquivo não encontrado" ao fazer download

**Erro**:
```
{"erro": "Arquivo não encontrado."}
```

**Solução**:
1. Verifique se a pasta `writable/uploads/` existe
2. Crie a pasta se necessária:
   ```bash
   mkdir writable\uploads
   ```
3. Verifique permissões de escrita:
   ```bash
   icacls writable\uploads /grant Everyone:F
   ```

---

### 4. PDF ou Excel não sendo gerado

**Erro**:
```
{"erro": "Erro ao gerar PDF: Class not found"}
```

**Solução**:
1. Verifique se as dependências estão instaladas:
   ```bash
   composer require --ignore-platform-reqs mpdf/mpdf phpoffice/phpspreadsheet
   ```

2. Se ainda não funcionar, atualize:
   ```bash
   composer update
   composer dump-autoload -o
   ```

---

### 5. Rotas retornam 404

**Erro**:
```
404 - Page Not Found
```

**Solução**:
1. Verifique se a rota está correta em `app/Config/Routes.php`
2. Execute para ver todas as rotas:
   ```bash
   php spark routes | findstr relatorio
   ```
3. Reinicie o servidor

---

### 6. Movimentações vazias no relatório

**Problema**: O relatório é gerado mas sem dados

**Solução**:
1. Verifique se há dados no banco:
   ```bash
   php spark tinker
   > DB().table('movimentacoes').countAllResults()
   ```

2. Verifique os filtros (data, produto_id)
3. Confirme que o período é válido

---

### 7. "Token inválido" ao tentar gerar relatório

**Erro**:
```
401 - Unauthorized
```

**Solução**:
1. Obtenha um token válido:
   ```bash
   curl -X POST "http://localhost/api/users/login" \
     -H "Content-Type: application/json" \
     -d '{"email":"seu@email.com","password":"sua_senha"}'
   ```

2. Use o token na header Authorization:
   ```bash
   Authorization: Bearer SEU_TOKEN_AQUI
   ```

---

### 8. Erro "Quantidade insuficiente no estoque"

**Erro**:
```
{"erro": "Quantidade insuficiente no estoque."}
```

**Solução**:
1. Verifique a quantidade atual:
   ```bash
   curl -X GET "http://localhost/api/relatorios/estoque"
   ```

2. Registre uma entrada antes de fazer a saída:
   ```bash
   curl -X POST "http://localhost/api/movimentacoes/entrada" \
     -d '{"produto_id":1,"quantidade":100}'
   ```

---

### 9. Relatório PDF com caracteres estranhos

**Problema**: Acentos e caracteres especiais aparecem incorretos

**Solução**: 
- Isso é raro, pois usamos encoding UTF-8
- Se ocorrer, atualize MPDF:
  ```bash
  composer update mpdf/mpdf
  ```

---

### 10. Arquivo Excel corrompido

**Problema**: Erro ao abrir arquivo .xlsx

**Solução**:
1. Atualize PhpSpreadsheet:
   ```bash
   composer update phpoffice/phpspreadsheet
   ```

2. Limpe a pasta de uploads:
   ```bash
   rmdir /s writable\uploads
   mkdir writable\uploads
   ```

3. Gere o relatório novamente

---

## ✅ Verificação de Funcionamento

### Checklist de Instalação

- [ ] Dependências instaladas: `composer require --ignore-platform-reqs mpdf/mpdf phpoffice/phpspreadsheet`
- [ ] Pasta `writable/uploads/` criada e com permissão de escrita
- [ ] Arquivo `app/Services/ReportGenerator.php` existe
- [ ] Controllers atualizados (Relatorio, Download, Movimentacao)
- [ ] Rotas atualizadas em `app/Config/Routes.php`
- [ ] Models atualizados (MovimentacaoModel)
- [ ] Banco de dados migrado: `php spark migrate`
- [ ] Há produtos cadastrados: `curl http://localhost/api/produtos`
- [ ] Há usuários cadastrados: `curl http://localhost/api/users`

---

## 🧪 Testes Práticos

### Teste 1: Verificar Instalação de Dependências

```bash
cd c:\xampp\htdocs\devostorange\devostorange_api
php -r "require 'vendor/autoload.php'; echo 'Autoload OK';"
```

**Resultado esperado**: `Autoload OK`

---

### Teste 2: Verificar Rotas

```bash
php spark routes | findstr "relatorio\|download"
```

**Resultado esperado**: Deve listar todas as rotas de relatório

---

### Teste 3: Verificar Banco de Dados

```bash
php spark tinker
> DB().table('movimentacoes').countAllResults()
```

**Resultado esperado**: Número inteiro >= 0

---

### Teste 4: Teste de API Manual

1. Abra Postman ou Insomnia
2. Faça login para obter JWT token:
   ```
   POST http://localhost/api/users/login
   Body: {"email":"seu@email.com","password":"sua_senha"}
   ```

3. Copie o token retornado

4. Teste um endpoint de relatório:
   ```
   GET http://localhost/api/relatorios/estoque
   Header: Authorization: Bearer SEU_TOKEN
   ```

5. Deve retornar dados de estoque em JSON

---

## 📋 Perguntas Frequentes

### P: Por que os PDFs ficam muito grandes?

**R**: PDFs com imagens e muito conteúdo podem ser grandes. Use compressão:
```php
$mpdf = new Mpdf(['compress' => true]);
```

---

### P: Posso customizar as cores dos relatórios?

**R**: Sim! Edite `app/Services/ReportGenerator.php` e altere os valores RGB:
```php
'startColor' => ['rgb' => '366092'], // Altere para sua cor
```

---

### P: Os relatórios suportam gráficos?

**R**: PDFs sim (via MPDF HTML), Excel também (via PhpSpreadsheet). Seria necessário adicionar bibliotecas como Chart.js para gráficos mais avançados.

---

### P: Como faço backup dos relatórios gerados?

**R**: Os arquivos estão em `writable/uploads/`. Faça backup dessa pasta regularmente.

---

### P: Posso agendar a geração automática de relatórios?

**R**: Sim, usando Cron Jobs (Linux) ou Task Scheduler (Windows) para chamar uma rota via curl.

---

### P: Como exporto dados para outros formatos como CSV?

**R**: PhpSpreadsheet suporta CSV. Adicione método similar a `gerarExcelEstoque()` mas use:
```php
$writer = new Csv($spreadsheet);
```

---

## 🔧 Logs e Debugging

### Ativar Logs Detalhados

Edite `.env`:
```env
CI_ENVIRONMENT = development
```

### Ver Logs

```bash
cat writable/logs/log-*.log
```

### Debug de Query

```bash
php spark tinker
> $mov = DB().table('movimentacoes')
>   .join('produtos', 'produtos.id = movimentacoes.produto_id')
>   .get()
>   .getResult()
```

---

## 📞 Suporte Técnico

Se o problema persistir:

1. **Verifique versão do PHP**: `php -v` (deve ser >= 8.1)
2. **Verifique versão do CodeIgniter**: `php spark version`
3. **Limpe cache**: `php spark cache:clear`
4. **Regenere autoload**: `composer dump-autoload -o`
5. **Reinicie o servidor**: `php spark serve`

---

## 📝 Exemplo de Curl com Debugging

```bash
curl -X GET "http://localhost/api/relatorios/estoque/pdf" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -v \
  -w "\nStatus Code: %{http_code}\n"
```

O `-v` mostra headers de request e response, o `-w` mostra o código HTTP.

