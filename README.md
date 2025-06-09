# Juros API

API simples em PHP para buscar a média da taxa SELIC via Banco Central e armazenar no banco de dados.

### 📦 Requisitos

- PHP 7.4+
- MySQL
- Servidor Apache ou PHP embutido

### 📁 Estrutura

- Endpoint: `PUT /juros`
- Entrada: JSON com `dataInicio` e `dataFinal`
- Validações:
  - `dataInicio >= 2010-01-01`
  - `dataInicio <= dataFinal`
  - `dataFinal <= hoje`

### 📌 Exemplo de Requisição

```bash
curl -X PUT http://localhost/juros \
     -H "Content-Type: application/json" \
     -d '{"dataInicio":"2024-01-01","dataFinal":"2024-01-31"}'
