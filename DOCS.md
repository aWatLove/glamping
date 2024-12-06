


# Таблицы

### Диаграмма
![glamp.drawio.png](glamp.drawio.png)

## Пользователи
User
- id
- firstname
- lastname
- email
- phone
- password
- role

## Места
Places
- id
- title
- description
- coordinatex
- coordinatey
- photo (URL фотографий)
- base_id
- tariffs_limit
- is_del

## Тарифы 
Под тарифами подразумевается: дома на колесах, бани на колесах, крутые палатки
Tariffs
- id
- title
- description
- price_per_day
- photo (URL фотографий)
- base_id
- is_del

## Заказы
Orders
- id
- user_Id
- place_Id
- date_start
- date_end
- days_count
- status
- payment_status
- total_price

## Опции
Additonal Options
- id
- title
- price
- count
- is_del

## Order_options
- id
- order_id
- option_id
- count


## Bases
- id
- title
- coordinate_x
- coordinate_y

## Order_tariffs
- id
- order_id
- tariff_id
- place_id
- date_start
- date_end
- status


**Enum Status**:
- Обрабатывается
- В пути
- Доставлено
- Отменено
- Ошибка

**Enum payment status**:
- Оплачено
- Не оплачено

# Эндпоинты 

### Авторизация через:

**Authorization: Bearer {token}**

## Авторизация
### POST sign up
`POST /api/register`

input:
```json
{
	"name":"firstname",
	"surname":"lastname",
	"email":"useremail@mail.ru",
	"phone":"89528125252",
	"password":"password12345",
	"password_confirmation":"password12345"
}
```
output:
```json
{
    "name": "test1",
    "surname": "sdgsgdsd",
    "phone": "12313213213",
    "email": "test1@mail.ru",
    "updated_at": "2024-11-13T20:57:58.000000Z",
    "created_at": "2024-11-13T20:57:58.000000Z",
    "id": 4
}
```



### POST sign in
`POST /api/login`

input:
```json
{
	"email":"username",
	"password":"password12345"
}
```
output:
```json
{
    "token": "9|GXv2xYGxvBMeGBMKv7WBLTrlceObZ73tI3MsXWJN93128b50",
    "name": "Admin",
    "surname": "",
    "phone": "",
    "email": "admin@mail.ru",
    "role": "admin"
}
```

## Пользователь
### GET users info
`GET /api/user`
**auth**

output:
```json
{
    "id": 1,
    "name": "Vladislav",
    "email": "vlad@mail.ru",
    "email_verified_at": null,
    "created_at": "2024-11-11T20:13:31.000000Z",
    "updated_at": "2024-11-11T20:13:31.000000Z",
    "role": "user",
    "surname": "",
    "phone": ""
}
```


### PUT update user details // todo не хочу делать!
`PUT /api/user`
**auth**

input:
```json
{
	"username":"username",
	"firstname":"firstname",
	"lastname":"lastname",
	"email":"useremail@mail.ru",
	"phone":"89528125252"
}
```
output:
```json
{
	"username":"username",
	"firstname":"firstname",
	"lastname":"lastname",
	"email":"useremail@mail.ru",
	"phone":"89528125252"
}
```

## Place
### GET all place previews around
`GET /api/places`
**auth**

Input: 
Query:
- XСoordinate - Double
- YСoordinate - Double
- radius - Double. в километрах (default 50)

Output:
```json
{
	"data":[
		{
			"id":0, 
			"title":"some place 0",
			"description":"some place 0",
			"photo":"photourl",
			"XCoordinate":56.2853,
			"YCoordinate":42.0971
		  },
	  { 
			"id":1, 
			"title":"some place 1",
            "description":"some place 0",
            "photo":"photourl",
			"XCoordinate":36.2853, 
			"YCoordinate":52.0971
		}
	]
}
```

### GET place by id
`GET /api/places/:id`

**auth**

Input:

Path: `id - Long`

Output:
```json
{
    "data": {
        "id": 1,
        "title": "Place A",
        "description": "Updated description",
        "XCoordinate": 35.2,
        "YCoordinate": 22.2,
        "tariffs": [
            {
                "id": 1,
                "title": "Крутая палатка",
                "description": "Комфортный вариант для отдыха",
                "price_per_day": "1500",
                "photo": "http://example.com/photo.jpg"
            }
        ],
        "tariffs_limit": 10,
        "photo": "http://example.com/updated_photo.jpg"
    }
}
```

### POST create place
`POST /api/places`

**auth admin**

Input:
```json
{
    "title":"new plave title",
    "description":"new description",
    "XCoordinate":52.0808,
    "YCoordinate":12.1489,
    "base_id":1,
    "tariff_limit":5,
    "photo":"url1"
}
```

Output:
```json
{
    "data": {
        "id": 1,
        "title": "Place A",
        "description": "Updated description",
        "XCoordinate": 35.2,
        "YCoordinate": 22.2,
        "tariffs": [
            {
                "id": 1,
                "title": "Крутая палатка",
                "description": "Комфортный вариант для отдыха",
                "price_per_day": "1500",
                "photo": "http://example.com/photo.jpg"
            }
        ],
        "tariffs_limit": 10,
        "photo": "http://example.com/updated_photo.jpg"
    }
}
```




### PATCH update place info
`PATCH /api/places/:id`

**auth admin**

Input:

Path: `id - int`

```json
{
	"title":"new plave title",
	"description":"new description", 
	"XCoordinate":52.0808,
	"YCoordinate":12.1489,
	"photos":"url1",
	"base_id":3,
	"tariff_limit":6
}
```

Output:
```json
{
	"id":0,
	"title":"some place", 
	"description":"some descriptin", 
	"XCoordinate":52.5252,
	"YCoordinate":34.4333,
	"tariffs":[
		{
			"id":1,
			"title":"title1",
			"description":"desc",
			"price_per_day":5000.0, 
			"photo":"url" 
		}
	], 
	"tariff_limit":5,
	"photo":"url1"
}
```

### DELETE place
`DELETE /api/places/:id`

**auth admin**

Input: `id - int`

Soft delete

Output: 200 ok

## Orders

### POST create order //done
`POST /api/orders`
**auth**

input:
```json
{
	"place_id":1,
	"days_count":5,
	"tarrif_ids":[0],
	"optional_ids":[
		{
            "id":0,
            "count":1
		},
		{
			"id":2,
			"count":4
		}
	],
	"date_start":"2017-03-12T13:37:27+00:00",
	"date_end":"2017-03-12T13:37:27+00:00"
}
```

output:
```json
{
    "data": {
        "id": 0,
        "user": {
            "id": 0,
            "name": "vlad",
            "surname": "suvorov",
            "email": "mail@mail.ru",
            "phone": "89528120252"
        },
        "place": {
            "id": 0,
            "title": "sometitle",
            "XCoordinate": 52.2552,
            "YCoordinate": 52.5252
        },
        "tariffs": [
            {
                "id": 0,
                "title": "title",
                "description": "description",
                "price_per_day": 5000.0,
                "photo": "url",
                "status": "Обрабатывается"
            }
        ],
        "days_count": 5,
        "status": "Обрабатывается",
        "date_start": "2017-03-12",
        "date_end": "2017-03-17",
        "additional_options": [
            {
                "id": 0,
                "title": "opt0",
                "price": 200.0,
                "count": 1
            },
            {
                "id": 2,
                "title": "opt2",
                "price": 100.0,
                "count": 4
            }
        ],
        "total_price": 25600.0,
        "payment_status": "Не оплачено",
        "created_at": "2017-03-12T13:37:27+00:00",
        "updated_at": "2017-03-12T13:37:27+00:00"
    }
}
```

### GET order by id  //done
`GET /api/orders/:id`

input: id - int,

output:
```json
{
    "data": {
        "id": 0,
        "user": {
            "id": 0,
            "name": "vlad",
            "surname": "suvorov",
            "email": "mail@mail.ru",
            "phone": "89528120252"
        },
        "place": {
            "id": 0,
            "title": "sometitle",
            "XCoordinate": 52.2552,
            "YCoordinate": 52.5252
        },
        "tariffs": [
            {
                "id": 0,
                "title": "title",
                "description": "description",
                "price_per_day": 5000.0,
                "photo": "url",
                "status": "Обрабатывается"
            }
        ],
        "days_count": 5,
        "status": "Обрабатывается",
        "date_start": "2017-03-12",
        "date_end": "2017-03-17",
        "additional_options": [
            {
                "id": 0,
                "title": "opt0",
                "price": 200.0,
                "count": 1
            },
            {
                "id": 2,
                "title": "opt2",
                "price": 100.0,
                "count": 4
            }
        ],
        "total_price": 25600.0,
        "payment_status": "Не оплачено",
        "created_at": "2017-03-12T13:37:27+00:00",
        "updated_at": "2017-03-12T13:37:27+00:00"
    }
}
```

### GET users by user_id order. for Admins //done
`GET /api/orders/user/:id`

**auth admin**

возвращаются все заказы пользователя по user_id, отсортированно по updated_at


output:
```json
{
	"data":[
		{
            "id": 0,
            "user": {
                "id": 0,
                "name": "vlad",
                "surname": "suvorov",
                "email": "mail@mail.ru",
                "phone": "89528120252"
            },
            "place": {
                "id": 0,
                "title": "sometitle",
                "XCoordinate": 52.2552,
                "YCoordinate": 52.5252
            },
            "tariffs": [
                {
                    "id": 0,
                    "title": "title",
                    "description": "description",
                    "price_per_day": 5000.0,
                    "photo": "url",
                    "status": "Обрабатывается"
                }
            ],
            "days_count": 5,
            "status": "Обрабатывается",
            "date_start": "2017-03-12",
            "date_end": "2017-03-17",
            "additional_options": [
                {
                    "id": 0,
                    "title": "opt0",
                    "price": 200.0,
                    "count": 1
                },
                {
                    "id": 2,
                    "title": "opt2",
                    "price": 100.0,
                    "count": 4
                }
            ],
            "total_price": 25600.0,
            "payment_status": "Не оплачено",
            "created_at": "2017-03-12T13:37:27+00:00",
            "updated_at": "2017-03-12T13:37:27+00:00"
		},
        {
            "id": 2,
            "user": {
                "id": 0,
                "name": "vlad",
                "surname": "suvorov",
                "email": "mail@mail.ru",
                "phone": "89528120252"
            },
            "place": {
                "id": 0,
                "title": "sometitle",
                "XCoordinate": 52.2552,
                "YCoordinate": 52.5252
            },
            "tariffs": [
                {
                    "id": 0,
                    "title": "title",
                    "description": "description",
                    "price_per_day": 5000.0,
                    "photo": "url",
                    "status": "Обрабатывается"
                }
            ],
            "days_count": 5,
            "status": "Обрабатывается",
            "date_start": "2017-03-12",
            "date_end": "2017-03-17",
            "additional_options": [
                {
                    "id": 0,
                    "title": "opt0",
                    "price": 200.0,
                    "count": 1
                },
                {
                    "id": 2,
                    "title": "opt2",
                    "price": 100.0,
                    "count": 4
                }
            ],
            "total_price": 25600.0,
            "payment_status": "Не оплачено",
            "created_at": "2017-03-12T13:37:27+00:00",
            "updated_at": "2017-03-12T13:37:27+00:00"
        }
	]
}
```

### GET all users orders previews. for Managers //done
`GET /api/orders`

**auth admin**

возвращаются все заказы всех пользователей, отсортированно по updated_at


output:
```json
{
    "data":[
        {
            "id": 0,
            "user": {
                "id": 0,
                "name": "vlad",
                "surname": "suvorov",
                "email": "mail@mail.ru",
                "phone": "89528120252"
            },
            "place": {
                "id": 0,
                "title": "sometitle",
                "XCoordinate": 52.2552,
                "YCoordinate": 52.5252
            },
            "tariffs": [
                {
                    "id": 0,
                    "title": "title",
                    "description": "description",
                    "price_per_day": 5000.0,
                    "photo": "url",
                    "status": "Обрабатывается"
                }
            ],
            "days_count": 5,
            "status": "Обрабатывается",
            "date_start": "2017-03-12",
            "date_end": "2017-03-17",
            "additional_options": [
                {
                    "id": 0,
                    "title": "opt0",
                    "price": 200.0,
                    "count": 1
                },
                {
                    "id": 2,
                    "title": "opt2",
                    "price": 100.0,
                    "count": 4
                }
            ],
            "total_price": 25600.0,
            "payment_status": "Не оплачено",
            "created_at": "2017-03-12T13:37:27+00:00",
            "updated_at": "2017-03-12T13:37:27+00:00"
        },
        {
            "id": 2,
            "user": {
                "id": 0,
                "name": "vlad",
                "surname": "suvorov",
                "email": "mail@mail.ru",
                "phone": "89528120252"
            },
            "place": {
                "id": 0,
                "title": "sometitle",
                "XCoordinate": 52.2552,
                "YCoordinate": 52.5252
            },
            "tariffs": [
                {
                    "id": 0,
                    "title": "title",
                    "description": "description",
                    "price_per_day": 5000.0,
                    "photo": "url",
                    "status": "Обрабатывается"
                }
            ],
            "days_count": 5,
            "status": "Обрабатывается",
            "date_start": "2017-03-12",
            "date_end": "2017-03-17",
            "additional_options": [
                {
                    "id": 0,
                    "title": "opt0",
                    "price": 200.0,
                    "count": 1
                },
                {
                    "id": 2,
                    "title": "opt2",
                    "price": 100.0,
                    "count": 4
                }
            ],
            "total_price": 25600.0,
            "payment_status": "Не оплачено",
            "created_at": "2017-03-12T13:37:27+00:00",
            "updated_at": "2017-03-12T13:37:27+00:00"
        }
    ]
}
```

### GET user order previews. for User
`GET /api/orders`

возвращаются все заказы пользователя

input: 

output:
```json
{
    "data":[
        {
            "id": 0,
            "user": {
                "id": 0,
                "name": "vlad",
                "surname": "suvorov",
                "email": "mail@mail.ru",
                "phone": "89528120252"
            },
            "place": {
                "id": 0,
                "title": "sometitle",
                "XCoordinate": 52.2552,
                "YCoordinate": 52.5252
            },
            "tariffs": [
                {
                    "id": 0,
                    "title": "title",
                    "description": "description",
                    "price_per_day": 5000.0,
                    "photo": "url",
                    "status": "Обрабатывается"
                }
            ],
            "days_count": 5,
            "status": "Обрабатывается",
            "date_start": "2017-03-12",
            "date_end": "2017-03-17",
            "additional_options": [
                {
                    "id": 0,
                    "title": "opt0",
                    "price": 200.0,
                    "count": 1
                },
                {
                    "id": 2,
                    "title": "opt2",
                    "price": 100.0,
                    "count": 4
                }
            ],
            "total_price": 25600.0,
            "payment_status": "Не оплачено",
            "created_at": "2017-03-12T13:37:27+00:00",
            "updated_at": "2017-03-12T13:37:27+00:00"
        },
        {
            "id": 2,
            "user": {
                "id": 0,
                "name": "vlad",
                "surname": "suvorov",
                "email": "mail@mail.ru",
                "phone": "89528120252"
            },
            "place": {
                "id": 0,
                "title": "sometitle",
                "XCoordinate": 52.2552,
                "YCoordinate": 52.5252
            },
            "tariffs": [
                {
                    "id": 0,
                    "title": "title",
                    "description": "description",
                    "price_per_day": 5000.0,
                    "photo": "url",
                    "status": "Обрабатывается"
                }
            ],
            "days_count": 5,
            "status": "Обрабатывается",
            "date_start": "2017-03-12",
            "date_end": "2017-03-17",
            "additional_options": [
                {
                    "id": 0,
                    "title": "opt0",
                    "price": 200.0,
                    "count": 1
                },
                {
                    "id": 2,
                    "title": "opt2",
                    "price": 100.0,
                    "count": 4
                }
            ],
            "total_price": 25600.0,
            "payment_status": "Не оплачено",
            "created_at": "2017-03-12T13:37:27+00:00",
            "updated_at": "2017-03-12T13:37:27+00:00"
        }
    ]
}
```

### PATCH поменять статус order'а //done
`PATCH /api/orders/:id/process`

**auth admin**
input: id - int

query: 
- status


```json
{
    "message": "",
    "order": {}
}
```

### PATCH поменять статус тарифа заказа //done
`PATCH /api/orders/:id/tariff/:id/process`

**auth admin**
input: id - int

query:
- status

```json
{
    "message": "",
    "order": {}
}
```

### PATCH отменить заказ //done
`PATCH /api/orders/:id/cancel`

**auth**

input: id - int

```json
{
    "message": "",
    "order": {}
}
```

### PATCH мок платёжного шлюза //done 
`POST /api/orders/:id/pay`

input: id - int


мок для оплаты заказа
меняется payment_status

```json
{
    "message": "",
    "order": {}
}
```

## Tarrif
### GET all tariffs
`GET /api/tariffs`

input: -

output:
```json
{
	"tariffs":[
		{
			"id":0,
			"title":"title0",
			"description":"description0",
			"price_per_day":500.0,
			"photo":"url0",
			"base_id":1
		},
		{
			"id":1,
			"title":"title1",
			"description":"description1",
			"price_per_day":400.0,
			"photo":"url1",
			"base_id":1
		},
		{
			"id":2,
			"title":"title2",
			"description":"description2",
			"price_per_day":50000.0,
			"photo":"url2",
			"base_id":2
		}
	]
}
```

### GET all tariffs by base id
`GET /api/tariffs/base/:id`

input: -

output:
```json
{
	"tariffs":[
		{
			"id":0,
			"title":"title0",
			"description":"description0",
			"price_per_day":500.0,
			"photo":"url0",
			"base_id":1
		},
		{
			"id":1,
			"title":"title1",
			"description":"description1",
			"price_per_day":400.0,
			"photo":"url1",
			"base_id":1
		},
		{
			"id":2,
			"title":"title2",
			"description":"description2",
			"price_per_day":50000.0,
			"photo":"url2",
			"base_id":1
		}
	]
}
```
### GET tariff booking info. For Managers
`GET /api/tariffs/booking/:id`

input: -

output:
```json
{
    "data": {
        "id": 1,
        "title": "Крутая палатка",
        "description": "Комфортный вариант для отдыха",
        "price_per_day": "1500",
        "photo": "http://example.com/photo.jpg",
        "booking": [
            {
                "date_start": "2025-04-20",
                "date_end": "2025-04-25",
                "order_id": 1,
                "place_id": 1
            },
            {
                "date_start": "2025-04-22",
                "date_end": "2025-04-27",
                "order_id": 2,
                "place_id": 1
            }
        ]
    }
}
```

### GET tariff booking info. For Users
`GET /api/tariffs/booking/:id`

input: -

output:
```json
{
    "data": {
        "id": 1,
        "title": "Крутая палатка",
        "description": "Комфортный вариант для отдыха",
        "price_per_day": "1500",
        "photo": "http://example.com/photo.jpg",
        "booking": [
            {
                "date_start": "2025-04-20",
                "date_end": "2025-04-25"
            },
            {
                "date_start": "2025-04-22",
                "date_end": "2025-04-27"
            }
        ]
    }
}
```

### POST create tariff
`POST /api/tariffs`

**auth admin**

input:
```json
{
	"title":"title2",
	"description":"description2",
	"price_per_day":50000.0,
	"photo":"url2",
	"base_id":1
}
```

output:
```json
{
	"id":2,
	"title":"title2",
	"description":"description2",
	"price_per_day":50000.0,
	"photo":"url2",
	"base_id":1
}
```

### PATCH update tariff
`PATCH /api/tariffs/:id`

**auth admin**

input: id - int

```json
{
	"title":"title2",
	"description":"description2",
	"price_per_day":50000.0,
	"photo":"url2",
	"base_id":1
}
```

output:
```json
{
	"id":2,
	"title":"title2",
	"description":"description2",
	"price_per_day":50000.0,
	"photo":"url2",
	"base_id":1
}
```

### DELETE delete tariff
`DELETE /api/tariffs/:id`

**auth admin**

input: id - int

output: ok 200

soft-delete

## BASES
### GET all bases
`GET /api/bases`

input: -

output:
```json
{
    "data": [
        {
            "id": 3,
            "title": "Mountain Base",
            "coordinate_x": "45.678",
            "coordinate_y": "56.789",
            "created_at": "2024-11-30T17:30:58.000000Z",
            "updated_at": "2024-11-30T17:30:58.000000Z"
        },
        {
            "id": 1,
            "title": "BMX Base",
            "coordinate_x": "55.678",
            "coordinate_y": "52.789",
            "created_at": "2024-11-17T18:33:35.000000Z",
            "updated_at": "2024-11-17T18:34:25.000000Z"
        }
    ]
}
```

### GET base by id
`GET /api/bases/:id`

input: -

output:
```json
{
    "data": {
        "id": 1,
        "title": "BMX Base",
        "coordinate_x": "55.678",
        "coordinate_y": "52.789",
        "created_at": "2024-11-17T18:33:35.000000Z",
        "updated_at": "2024-11-17T18:34:25.000000Z"
    }
}
```

### POST create base
`POST /api/bases`
**auth admin**

input:
```json
{
    "title": "Mountain Base",
    "coordinate_x": 45.678,
    "coordinate_y": 56.789
}
```

output:
```json
{
    "data": {
        "id": 4,
        "title": "Mountain Base",
        "coordinate_x": 45.678,
        "coordinate_y": 56.789,
        "created_at": "2024-11-30T17:38:53.000000Z",
        "updated_at": "2024-11-30T17:38:53.000000Z"
    }
}
```


### PATCH update base
`PATCH /api/bases/:id`
**auth admin**

input: id - int

```json
{
	"title":"title",
	"price":200.0,
	"count":10
}
```

output:
```json
{
    "data": {
        "id": 1,
        "title": "BMX Base",
        "coordinate_x": 55.678,
        "coordinate_y": 52.789,
        "created_at": "2024-11-17T18:33:35.000000Z",
        "updated_at": "2024-11-17T18:34:25.000000Z"
    }
}
```

### DELETE base
`DELETE /api/bases/:id`

input: id - int
output: ok

soft-delete

## Options
### GET all options
`GET /api/option`

input: -

output:
```json
{
    "data": [
        {
            "id": 5,
            "title": "New Option",
            "price": "1000.50",
            "count": 500,
            "created_at": "2024-11-30T17:27:31.000000Z",
            "updated_at": "2024-11-30T17:27:54.000000Z"
        },
        {
            "id": 4,
            "title": "New Option",
            "price": "1000.50",
            "count": 5,
            "created_at": "2024-11-17T18:19:16.000000Z",
            "updated_at": "2024-11-30T17:25:54.000000Z"
        },
        {
            "id": 3,
            "title": "New Option",
            "price": "20.50",
            "count": 34,
            "created_at": "2024-11-11T20:56:07.000000Z",
            "updated_at": "2024-11-30T16:02:58.000000Z"
        },
        {
            "id": 1,
            "title": "New Option",
            "price": "10.50",
            "count": 2,
            "created_at": "2024-11-11T20:55:00.000000Z",
            "updated_at": "2024-11-30T12:21:27.000000Z"
        }
    ]
}
```

### POST create option
`POST /api/option`
**auth admin**

input:

```json
{
    "title": "New Option",
    "price": 20.5,
    "count":200
}
```


output:
```json
{
    "data": {
        "id": 6,
        "title": "New Option",
        "price": 20.5,
        "count": 200,
        "created_at": "2024-11-30T17:44:00.000000Z",
        "updated_at": "2024-11-30T17:44:00.000000Z"
    }
}
```


### PATCH update option
`PATCH /api/option/:id`
**auth admin**

input: id - int

```json
{
	"title":"title",
	"price":200.0,
	"count":10
}
```

output:
```json
{
    "data": {
        "id": 5,
        "title": "New Option",
        "price": 1000.5,
        "count": 500,
        "created_at": "2024-11-30T17:27:31.000000Z",
        "updated_at": "2024-11-30T17:44:17.000000Z"
    }
}
```

### DELETE option
`DELETE /api/option/:id`

input: id - int
output: ok

soft-delete

