Как пользоваться API
=

> Базовый URL: https://mysite.com/users

### 1. Добавление пользователя

* URL: /users/add
* Метод: ___POST___
* Входные данные:
    * username: Имя пользователя. (_string_)
    * password: Пароль пользователя. (_string_)
* Формат: __JSON__.
* Пример запроса:
```json
{
  "username": "Peter Griffin",
  "password": "secretpassword"
}
```

* ___Коды ответов___:
    * __201 Created__: Пользователь успешно добавлен.
    * __400 Bad Request__: Некорректные входные данные.
    * __500 Internal Server Error__: Ошибка на сервере.

### 2. Обновление пользователя

* URL: /users/update/{id}
* Метод: ___POST___
* Входные данные:
    * id: Идентификатор пользователя. (_int_)
    * username: (опционально) Новое имя пользователя. (_string_)
    * password: (опционально) Новый пароль пользователя. (_string_)
* Формат: __JSON__.
* Пример запроса:
```json
{
  "username": "Stewie Griffin",
  "password": "newsecretpassword"
}
```
* ___Коды ответов___:
    * __200 OK__: Пользователь успешно обновлен.
    * __400 Bad Request__: Некорректные входные данные.
    * __403 Forbidden__: Невозможно обновить имя пользователя, так как оно уже существует.
    * __404 Not Found__: Пользователь с указанным id не найден.
    * __500 Internal Server Error__: Ошибка на сервере.

### 3. Удаление пользователя

* URL: /users/delete/{id}
* Метод: ___POST___
* Входные данные: 
    * id: Идентификатор пользователя. (_int_)
* ___Коды ответов___:
    * __200 OK__: Пользователь успешно удален.
    * __404 Not Found__: Пользователь с указанным id не найден.
    * __500 Internal Server Error__: Ошибка на сервере.

### 4. Аутентификация пользователя

* URL: /users/auth
* Метод: ___POST___
* Входные данные:
    * username: Имя пользователя. (_string_)
    * password: Пароль пользователя. (_string_)
* Формат: JSON.
```json
{
  "username": "Peter Griffin",
  "password": "secretpassword"
}
```
* ___Коды ответов___:
    * __200 OK__: Аутентификация успешна. 
    * __401 Unauthorized__: Неверный логин или пароль.
    * __500 Internal Server Error__: Ошибка на сервере.

### 5. Просмотр пользователя

* URL: /users/get/{id}
* Метод: ___POST___
* Входные данные:
    * id: Идентификатор пользователя. (_int_)
* ___Коды ответов___:
    * __200 OK__: Пользователь с указанным id найден. 
    * __404 Not Found__: Пользователь с указанным id не найден.
    * __500 Internal Server Error__: Ошибка на сервере.

### 6. Просмотр всех пользователей

* URL: /users
* Метод: ___POST___
* ___Коды ответов___:
    * __200 OK__: Пользователи существуют в базе данных. 
    * __404 Not Found__: Пользователи не существуют в базе данных.
    * __500 Internal Server Error__: Ошибка на сервере.


Пример использования с помощью __CURL__:
-

#### Добавление пользователя
```php
curl -X POST https://mysite.com/users/add \
    -H "Content-Type: application/json" \
    -d '{ "username": "john.doe", "password": "secretpassword" }' 
```
#### Обновление пользователя
```php
curl -X POST https://mysite.com/users/update/123 \
    -H "Content-Type: application/json" \
    -d '{ "username": "john.doe1" }' 
```
#### Удаление пользователя
```php
curl -X POST https://mysite.com/users/delete/123
```
#### Авторизация пользователя
```php
curl -X POST https://mysite.com/users/auth \
    -H "Content-Type: application/json" \
    -d '{ "username": "john.doe", "password": "secretpassword" }'
```
#### Получение пользователя
```php
curl -X POST https://mysite.com/users/get/123 \
    -H "Accept: application/json" 
```
#### Получение всех пользователей
```php
curl -X POST https://mysite.com/users \
    -H "Accept: application/json"  
```