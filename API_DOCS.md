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