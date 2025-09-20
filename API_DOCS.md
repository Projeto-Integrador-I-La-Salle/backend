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
