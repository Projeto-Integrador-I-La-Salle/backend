# Documentação da API - MotoShop Backend

Esta é a documentação para os endpoints de autenticação da API.

**URL Base Local:** `http://127.0.0.1:8000/api`

---

### 1. Registrar Novo Usuário

Cria uma nova conta de cliente no sistema.

* **Método:** `POST`
* **Endpoint:** `/auth/register`
* **URL Completa:** `http://127.0.0.1:8000/api/auth/register`

#### Corpo da Requisição (Body - JSON)
```json
{
    "name": "Nome do Usuario",
    "email": "email.unico@exemplo.com",
    "password": "senha_forte_min_8_chars",
    "telefone": "51999998888"
}
```

#### Resposta de Sucesso (Status 201)
```json
{
    "message": "Usuário registrado com sucesso!",
    "user": {
        "id_publico": "uuid-gerado-aqui",
        "name": "Nome do Usuario",
        "email": "email.unico@exemplo.com",
        "telefone": "51999998888",
        "updated_at": "2025-08-30T23:00:00.000000Z",
        "created_at": "2025-08-30T23:00:00.000000Z",
        "id": 1
    }
}
```

---

### 2. Autenticar Usuário (Login)

Autentica um usuário existente e retorna um token de acesso.

* **Método:** `POST`
* **Endpoint:** `/auth/login`
* **URL Completa:** `http://127.0.0.1:8000/api/auth/login`

#### Corpo da Requisição (Body - JSON)
```json
{
    "email": "email.unico@exemplo.com",
    "password": "senha_forte_min_8_chars"
}
```

#### Resposta de Sucesso (Status 200)
**Atenção:** O `access_token` deve ser salvo pelo frontend para ser usado em requisições futuras.
```json
{
    "message": "Login bem-sucedido!",
    "access_token": "SEU_TOKEN_SECRETO_APARECERA_AQUI",
    "token_type": "Bearer",
    "user": { ... }
}
```

---

### 3. Buscar Dados do Usuário Logado

Retorna os detalhes do usuário autenticado. Requer o envio do token.

* **Método:** `GET`
* **Endpoint:** `/user`
* **URL Completa:** `http://127.0.0.1:8000/api/user`

#### Autenticação (Header)
É necessário enviar um Header de `Authorization` com o token recebido no login.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_SECRETO_APARECERA_AQUI`

#### Resposta de Sucesso (Status 200)
```json
{
    "id": 1,
    "id_publico": "uuid-gerado-aqui",
    "name": "Nome do Usuario",
    "email": "email.unico@exemplo.com",
    // ...demais dados do usuário
}
```

## Endpoints de Produtos

Endpoints para visualizar e gerenciar o catálogo de produtos.

### 4. Listar Todos os Produtos

Retorna uma lista paginada de todos os produtos disponíveis no catálogo.

* **Método:** `GET`
* **Endpoint:** `/produtos`
* **URL Completa:** `http://127.0.0.1:8000/api/produtos`

#### Resposta de Sucesso (Status 200)
```json
{
    "current_page": 1,
    "data": [
        {
            "id_produto": 1,
            "id_publico": "seu-uuid-aqui",
            "id_categoria": 1,
            "nome": "Capacete Pro Tork R8",
            "descricao": "Capacete integral com design aerodinâmico",
            "preco": "350.99",
            "qtd_estoque": 15,
            "created_at": "...",
            "updated_at": "...",
            "categoria": { ... },
            "imagens": [ ... ]
        }
    ],
    // ...outras informações de paginação...
}
```

---

### 5. Buscar um Produto Específico

Retorna os detalhes de um único produto, buscando pelo seu ID público (UUID).

* **Método:** `GET`
* **Endpoint:** `/produtos/{uuid}`
* **URL Completa:** `http://127.0.0.1:8000/api/produtos/seu-uuid-aqui`

#### Resposta de Sucesso (Status 200)
```json
{
    "id_produto": 1,
    "id_publico": "seu-uuid-aqui",
    "id_categoria": 1,
    "nome": "Capacete Pro Tork R8",
    "descricao": "Capacete integral com design aerodinâmico",
    "preco": "350.99",
    "qtd_estoque": 15,
    "created_at": "...",
    "updated_at": "...",
    "categoria": { ... },
    "imagens": [ ... ]
}
```

---

### 6. Criar um Novo Produto (Requer Admin)

Cria um novo produto no catálogo.

* **Método:** `POST`
* **Endpoint:** `/produtos`
* **URL Completa:** `http://127.0.0.1:8000/api/produtos`

#### Autenticação (Header)
É necessário enviar um Header de `Authorization` com o **token de um usuário administrador**.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_DE_ADMIN_AQUI`

#### Corpo da Requisição (Body - JSON)
```json
{
    "nome": "Óleo Mobil Super Moto 20W-50",
    "descricao": "Óleo mineral de alta performance para motores de motocicletas 4 tempos.",
    "preco": 35.50,
    "qtd_estoque": 50,
    "id_categoria": 1
}
```

#### Resposta de Sucesso (Status 201)
```json
{
    "id_produto": 2,
    "id_publico": "novo-uuid-gerado-aqui",
    "id_categoria": 1,
    "nome": "Óleo Mobil Super Moto 20W-50",
    // ...demais dados do produto criado
}
```

---

### 7. Atualizar um Produto (Requer Admin)

Atualiza as informações de um produto existente. Você só precisa enviar os campos que deseja alterar.

* **Método:** `PUT` ou `PATCH`
* **Endpoint:** `/produtos/{uuid}`
* **URL Completa:** `http://127.0.0.1:8000/api/produtos/seu-uuid-aqui`

#### Autenticação (Header)
Requer o token de um usuário **administrador**.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_DE_ADMIN_AQUI`

#### Corpo da Requisição (Body - JSON)
```json
{
    "preco": 42.90,
    "qtd_estoque": 45
}
```

#### Resposta de Sucesso (Status 200)
Retorna o JSON completo do produto com as informações atualizadas.

---

### 8. Deletar um Produto (Requer Admin)

Remove um produto do catálogo.

* **Método:** `DELETE`
* **Endpoint:** `/produtos/{uuid}`
* **URL Completa:** `http://127.0.0.1:8000/api/produtos/seu-uuid-aqui`

#### Autenticação (Header)
Requer o token de um usuário **administrador**.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_DE_ADMIN_AQUI`

#### Resposta de Sucesso (Status 204)
A resposta não contém corpo (`No Content`), indicando que a deleção foi bem-sucedida.

---

## Endpoints de Carrinho de Compras

Endpoints para gerenciar o carrinho de compras de um usuário logado. Todas as rotas deste grupo exigem autenticação via Bearer Token.

### 9. Ver Carrinho de Compras

Retorna o carrinho de compras e todos os produtos contidos nele para o usuário autenticado.

* **Método:** `GET`
* **Endpoint:** `/carrinho`
* **URL Completa:** `http://127.0.0.1:8000/api/carrinho`

#### Autenticação (Header)
É necessário enviar um Header de `Authorization` com o token do usuário.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_DE_USUARIO_AQUI`

#### Resposta de Sucesso (Status 200)
```json
{
    "id_carrinho_compras": 1,
    "id_user": 1,
    "created_at": "...",
    "updated_at": "...",
    "produtos": [
        {
            "id_produto": 1,
            "nome": "Capacete Pro Tork R8",
            "preco": "350.99",
            // ...demais dados do produto
            "pivot": {
                "id_carrinho_compras": 1,
                "id_produto": 1,
                "quantidade": 2
            }
        }
    ]
}
```

---

### 10. Adicionar Produto ao Carrinho

Adiciona um produto específico ao carrinho do usuário. Se o produto já existir no carrinho, a quantidade é somada.

* **Método:** `POST`
* **Endpoint:** `/carrinho/produtos`
* **URL Completa:** `http://127.0.0.1:8000/api/carrinho/produtos`

#### Autenticação (Header)
Requer o token de um usuário.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_DE_USUARIO_AQUI`

#### Corpo da Requisição (Body - JSON)
```json
{
    "id_produto": 2,
    "quantidade": 1
}
```

#### Resposta de Sucesso (Status 200)
Retorna o estado atualizado do carrinho de compras.
```json
{
    "message": "Produto adicionado ao carrinho com sucesso!",
    "carrinho": {
        // ...objeto completo do carrinho atualizado...
    }
}
```

---

### 11. Atualizar Quantidade de um Produto no Carrinho

Altera a quantidade de um produto que já está no carrinho.

* **Método:** `PUT`
* **Endpoint:** `/carrinho/produtos/{id_produto}`
* **URL Completa:** `http://127.0.0.1:8000/api/carrinho/produtos/2`

#### Autenticação (Header)
Requer o token de um usuário.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_DE_USUARIO_AQUI`

#### Corpo da Requisição (Body - JSON)
```json
{
    "quantidade": 5
}
```

#### Resposta de Sucesso (Status 200)
Retorna o JSON completo do carrinho com a quantidade do produto atualizada.

---

### 12. Remover Produto do Carrinho

Remove um item específico do carrinho de compras do usuário.

* **Método:** `DELETE`
* **Endpoint:** `/carrinho/produtos/{id_produto}`
* **URL Completa:** `http://127.0.0.1:8000/api/carrinho/produtos/2`

#### Autenticação (Header)
Requer o token de um usuário.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_DE_USUARIO_AQUI`

#### Resposta de Sucesso (Status 200)
Retorna o JSON completo do carrinho, agora sem o produto que foi removido.

---

## Endpoints de Lista de Desejos

Endpoints para gerenciar a lista de desejos de um usuário logado. Todas as rotas deste grupo exigem autenticação via Bearer Token.

### 13. Ver Lista de Desejos

Retorna a lista de desejos e todos os produtos contidos nela para o usuário autenticado.

* **Método:** `GET`
* **Endpoint:** `/lista-desejos`
* **URL Completa:** `http://127.0.0.1:8000/api/lista-desejos`

#### Autenticação (Header)
É necessário enviar um Header de `Authorization` com o token do usuário.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_DE_USUARIO_AQUI`

#### Resposta de Sucesso (Status 200)
```json
{
    "id_lista_desejos": 1,
    "id_user": 1,
    "created_at": "...",
    "updated_at": "...",
    "produtos": [
        {
            "id_produto": 3,
            "nome": "Óleo Mobil Super Moto 20W-50",
            "preco": "35.50",
            // ...demais dados do produto
            "pivot": {
                "id_lista_desejos": 1,
                "id_produto": 3
            }
        }
    ]
}
```
*Se a lista estiver vazia, a resposta será `{"message": "Sua lista de desejos está vazia."}`.*

---

### 14. Adicionar Produto à Lista de Desejos

Adiciona um produto específico à lista de desejos do usuário. Se o produto já existir, nada acontece (não há duplicação).

* **Método:** `POST`
* **Endpoint:** `/lista-desejos/produtos`
* **URL Completa:** `http://127.0.0.1:8000/api/lista-desejos/produtos`

#### Autenticação (Header)
Requer o token de um usuário.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_DE_USUARIO_AQUI`

#### Corpo da Requisição (Body - JSON)
```json
{
    "id_produto": 3
}
```

#### Resposta de Sucesso (Status 200)
Retorna o estado atualizado da lista de desejos.
```json
{
    "message": "Produto adicionado à lista de desejos!",
    "lista_desejos": {
        // ...objeto completo da lista de desejos atualizada...
    }
}
```

---

### 15. Remover Produto da Lista de Desejos

Remove um item específico da lista de desejos do usuário.

* **Método:** `DELETE`
* **Endpoint:** `/lista-desejos/produtos/{id_produto}`
* **URL Completa:** `http://127.0.0.1:8000/api/lista-desejos/produtos/3`

#### Autenticação (Header)
Requer o token de um usuário.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_DE_USUARIO_AQUI`

#### Resposta de Sucesso (Status 200)
Retorna o JSON completo da lista de desejos, agora sem o produto que foi removido.
---

## Endpoints de Descontos (Admin)

Endpoints para gerenciar os descontos aplicáveis aos produtos. **Todas as rotas deste grupo exigem autenticação de um usuário com permissão de `admin`.**

### 16. Listar Todos os Descontos

Retorna uma lista paginada de todos os descontos cadastrados.

* **Método:** `GET`
* **Endpoint:** `/descontos`
* **URL Completa:** `http://127.0.0.1:8000/api/descontos`

#### Autenticação (Header)
É necessário enviar um Header de `Authorization` com o **token de um usuário administrador**.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_DE_ADMIN_AQUI`

#### Resposta de Sucesso (Status 200)
```json
{
    "current_page": 1,
    "data": [
        {
            "id_descontos": 1,
            "nome": "Promoção de Fim de Semana",
            "data_inicio": "2025-10-17T00:00:00.000000Z",
            "data_fim": "2025-10-19T23:59:59.000000Z",
            "porcentagem": "15.50",
            "created_at": "...",
            "updated_at": "..."
        }
    ],
    // ...outras informações de paginação...
}
```

---

### 17. Criar um Novo Desconto

Cria um novo desconto no sistema.

* **Método:** `POST`
* **Endpoint:** `/descontos`
* **URL Completa:** `http://127.0.0.1:8000/api/descontos`

#### Autenticação (Header)
Requer o token de um usuário **administrador**.

#### Corpo da Requisição (Body - JSON)
```json
{
    "nome": "Queima de Estoque - Capacetes",
    "porcentagem": 25.00,
    "data_inicio": "2025-11-01 00:00:00",
    "data_fim": "2025-11-15 23:59:59"
}
```

#### Resposta de Sucesso (Status 201)
Retorna o objeto completo do desconto que acabou de ser criado.
```json
{
    "id_descontos": 2,
    "nome": "Queima de Estoque - Capacetes",
    "porcentagem": "25.00",
    // ...demais dados do desconto...
}
```

---

### 18. Buscar um Desconto Específico

Retorna os detalhes de um único desconto pelo seu ID.

* **Método:** `GET`
* **Endpoint:** `/descontos/{id}`
* **URL Completa:** `http://127.0.0.1:8000/api/descontos/1`

#### Autenticação (Header)
Requer o token de um usuário **administrador**.

#### Resposta de Sucesso (Status 200)
Retorna o JSON completo do desconto solicitado.

---

### 19. Atualizar um Desconto

Atualiza as informações de um desconto existente. Você só precisa enviar os campos que deseja alterar.

* **Método:** `PUT` ou `PATCH`
* **Endpoint:** `/descontos/{id}`
* **URL Completa:** `http://127.0.0.1:8000/api/descontos/1`

#### Autenticação (Header)
Requer o token de um usuário **administrador**.

#### Corpo da Requisição (Body - JSON)
```json
{
    "porcentagem": 30.00
}
```

#### Resposta de Sucesso (Status 200)
Retorna o JSON completo do desconto com as informações atualizadas.

---

### 20. Deletar um Desconto

Remove um desconto do sistema.

* **Método:** `DELETE`
* **Endpoint:** `/descontos/{id}`
* **URL Completa:** `http://127.0.0.1:8000/api/descontos/1`

#### Autenticação (Header)
Requer o token de um usuário **administrador**.

#### Resposta de Sucesso (Status 204)
A resposta não contém corpo (`No Content`), indicando que a deleção foi bem-sucedida.
---

## Endpoints de Categorias Especiais (Admin)

Endpoints para gerenciar categorias promocionais, como "Ofertas do Dia". **Todas as rotas deste grupo exigem autenticação de um usuário com permissão de `admin`.**

### 21. Listar Todas as Categorias Especiais

Retorna uma lista paginada de todas as categorias especiais cadastradas.

* **Método:** `GET`
* **Endpoint:** `/categorias-especiais`
* **URL Completa:** `http://127.0.0.1:8000/api/categorias-especiais`

#### Autenticação (Header)
É necessário enviar um Header de `Authorization` com o **token de um usuário administrador**.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_DE_ADMIN_AQUI`

#### Resposta de Sucesso (Status 200)
```json
{
    "current_page": 1,
    "data": [
        {
            "id_categorias_especiais": 1,
            "nome": "Ofertas de Outubro",
            "data_inicio": "2025-10-15T00:00:00.000000Z",
            "data_fim": "2025-10-31T23:59:59.000000Z",
            "porcentagem": "12.00",
            "created_at": "...",
            "updated_at": "..."
        }
    ],
    // ...outras informações de paginação...
}
```

---

### 22. Criar uma Nova Categoria Especial

Cria uma nova categoria especial no sistema.

* **Método:** `POST`
* **Endpoint:** `/categorias-especiais`
* **URL Completa:** `http://127.0.0.1:8000/api/categorias-especiais`

#### Autenticação (Header)
---

## Endpoints de Associação de Produtos (Admin)

Endpoints para vincular/desvincular descontos e categorias especiais a produtos específicos. **Todas as rotas deste grupo exigem autenticação de um usuário com permissão de `admin`.**

### 23. Associar Desconto a um Produto

Associa um desconto existente a um produto específico, fazendo com que o desconto seja aplicável a ele.

* **Método:** `POST`
* **Endpoint:** `/produtos/{uuid}/descontos`
* **URL Completa:** `http://127.0.0.1:8000/api/produtos/c270a439-19df-3066-a9a0-978a6a54b630/descontos`

#### Autenticação (Header)
É necessário enviar um Header de `Authorization` com o **token de um usuário administrador**.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_DE_ADMIN_AQUI`

#### Corpo da Requisição (Body - JSON)
```json
{
    "id_desconto": 1
}
```

#### Resposta de Sucesso (Status 200)
Retorna o objeto completo do produto, agora com o desconto incluído na sua lista de `descontos`.
```json
{
    "id_produto": 1,
    "id_publico": "c270a439-19df-3066-a9a0-978a6a54b630",
    "nome": "Capacete Pro Tork R8",
    // ...demais dados do produto...
    "descontos": [
        {
            "id_descontos": 1,
            "nome": "Promoção de Fim de Semana",
            "porcentagem": "15.50",
            // ...demais dados do desconto...
        }
    ]
}
```

---

### 24. Desassociar Desconto de um Produto

Remove a associação de um desconto de um produto específico.

* **Método:** `DELETE`
* **Endpoint:** `/produtos/{uuid}/descontos/{id_desconto}`
* **URL Completa:** `http://127.0.0.1:8000/api/produtos/c270a439-19df-3066-a9a0-978a6a54b630/descontos/1`

#### Autenticação (Header)
Requer o token de um usuário **administrador**.
* **Key:** `Authorization`
* **Value:** `Bearer SEU_TOKEN_DE_ADMIN_AQUI`

#### Resposta de Sucesso (Status 200)
Retorna o JSON completo do produto, agora com a lista de `descontos` atualizada (sem o desconto que foi removido).
