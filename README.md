# Desafio Backend


## Sobre

__LinkedIn__: [Robson Nascimento](https://www.linkedin.com/in/robsonrrn/)

## Documentação

O presente projecto foi construido utilizando o framework [Laravel](http://www.laravel.com). Abaixo se encontram documentadas as funcionalidades do sistema RESTful com cada tópico apresentando os _endpoints_ e _exemplos_ do sistema:

## Lista todos os usuários

```http
GET /api/users
```

__Exemplo__:
```json
[
    {
        "id": 2,
        "name": "Sr. Fábio Azevedo",
        "email": "anita23@example.com",
        "document": "060.372.981-90",
        "logist": false,
        "balance": 7603
    },
    {
        "id": 4,
        "name": "Sr. Estêvão George Barreto",
        "email": "jonas76@example.org",
        "document": "518.949.137-44",
        "logist": false,
        "balance": 1599.8
    },
    {
        "id": 5,
        "name": "Srta. Norma Chaves Jr.",
        "email": "gael.alcantara@example.com",
        "document": "269.377.702-00",
        "logist": false,
        "balance": 1968.6
    },
]
```

```bash
curl http://localhost:8000/api/users
```

## Exibe informações de determinado usuário

```http
GET /api/users/:id
```

__Exemplo__:
```json
{
    "id": 1,
    "name": "Melissa Cordeiro Sobrinho",
    "email": "carolina85@example.org",
    "document": "123.858.160-91",
    "logist": true,
    "balance": 7002.2
}
```

```bash
curl http://localhost:8000/api/users/1
```

## Cria um novo usuário

```http
POST /api/users
```

__Exemplo__:
```json
{
    "status":"created",
    "data":{
        "id":7,
        "name":"Diogo Siva",
        "email":"dsilva98@hotmail.com",
        "document":"539.064.320-84",
        "logist": false,
        "balance": 0
    }
}
```

```bash
curl -X POST http://localhost:8000/api/users -H "Content-Type: application/json" -d "{\"name\":\"Diogo Siva\",\"email\":\"dsilva98@hotmail.com\",\"document\":\"539.064.320-84\",\"password\":\"secret\"}"
```

## Atualiza os dados de determinado usuário

```http
PUT /api/users/:id
```

__Exemplo__:
```json
{
    "status":"updated",
    "data":{
        "id":7,
        "name":"Diogo Silva",
        "email":"diogo.silva98@hotmail.com",
        "document":"539.064.320-84",
        "logist": true,
        "balance": 7002.2
    }
}
```

```bash
curl -X PUT http://localhost:8000/api/users/7 -H "Content-Type: application/json" -d "{\"name\":\"Diogo Silva\"}"
```

## Deleta determinado usuário (_soft delete_)

```http
DELETE /api/users/:id
```

__Exemplo__:
```json
{
    "status":"deleted",
    "data":{
        "id":7,
        "name":"Diogo Silva",
        "email":"diogo.silva98@hotmail.com",
        "document":"539.064.320-84",
        "logist": true,
        "balance": 7002.2
    }
}
```

```bash
curl -X DELETE http://localhost:8000/api/users/7
```

## Lista as transferências de determinado usuário

```http
GET /api/transactions/:id
```

__Exemplo__:
```json
[
    {
        "id": 2,
        "value": 15,
        "date": "18/04/2021 às 19:07:59",
        "payer": 1,
        "payee": 7
    },
    {
        "id": 1,
        "value": 15,
        "date": "19/04/2021 às 18:07:59",
        "payer": 1,
        "payee": 7
    }
]
```

```bash
curl http://localhost:8000/api/transactions/7
```


## Realiza uma transferência entre usuários (_payer_ e _payee_)

```http
POST /api/transactions
```

__Exemplo__:
```json
{
    "status": "success",
    "data": {
        "value" : 100.00,
        "payer" : 4,
        "payee" : 15
    }
}
```

```bash
curl -X POST http://localhost:8000/api/transactions -H "Content-Type: application/json" -d "{\"value\":100.0,\"payee\":4,\"payer\":15}"
```
