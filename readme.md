# PHP Challenge

This documentation will guide you through the Jobsity PHP Challenge which uses PHP 7.4 and Slim Framework. This application provides a simple API to get latest stock market values. So come along for the ride and let's setup the project.

## Features

| Feature | 3rd Party lib |
| ------ | ------ |
| Authentication | JWT |
| Tests | PHPUnit |
| ORM | Eloquent |
| Mailer | Swiftmailer |
| Database (prod/dev) | MySQL |
| Testing Database | Sqlite |

## Install

After clone and change to the project folder, change to the branch feature/challenge and install all dependencies through the command

```
composer install
```

## Configuration

Let's copy the .env.sample

```
cp .env.sample .env
```

Fill the ENV key with `dev or prod`

Set the APP_KEY with the command

```
composer key:generate
```

Fill the **Database and SwiftMailer settings** in the .env file.

**Note**: I suggest you to configure a MySQL database. Don't forget to create the database in your DBMS

#### Migrations

This API provides a simplistic way to run migrations, with the Database settings fulfilled, we can run our migrations with the follow command

```
composer migrate up
or
composer migrate
```

It's also possible to run `composer migrate down` to revert the migrations

Now run the below command to start the application

```
composer start
```

## Usage

#### POST - Register

```/register``` - Registers a new user

##### Body
```js
{
  "email": "humberto.souza@jobsity.io",
  "password": "Jobsity@2022",
  "password_confirmation": "Jobsity@2022"
}
```

##### Response

```
{
    "message": "User created"
}
```
##### Validation Erros

Status Code: 400 (Bad Request)

The following messages can be thrown

```
[
    {"message": "The email field is required."},
    {"message": "The email must be a valid email address."}
    {"message": "The email has already been taken."},
    {"message": "The password field is required."},
    {"message": "Password must be at least 8 characters."}
    {"message": "The password_confirmation field is required."},
    {"message": "Password must match the confirmation."}
]
```

#### POST - Login

```/login``` - Authenticates the user. If credentials are correct, the api_token will be sent back to use it on authorization header (bearer token).

##### Body
```js
{
  "email": "humberto.souza@jobsity.io",
  "password": "Jobsity@2022"
}
```

##### Response

```
{
    "message": "User already signed in",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.Imh1bWJlcnRvLnNvdXphQGpvYnNpdHkuaW8i.A0f3Oq8ma5yx2PB0lcjX2DrpFGu5JFCwXUBuKia_bBM"
}
```

##### Validation Erros

Status Code: 400 (Bad Request)

The following messages can be thrown

```
[
    {"message": "The email field is required."},
    {"message": "The password field is required."},
    {"message": "Incorrect username or password."},
]
```

The next endpoints need the token retrieved from the /login in the Authorization Header. If the token be not set, then this response will appears

```
{
    "message": "401 Unauthorized"
}
```

#### GET - stock

```/stock``` - Get the latest stock quote for a given stock code, an email with the same data will be sent to the user 

##### AUTHORIZATION - Bearer Token

##### QueryString
```js
?q=aaqc-ws.us
```

##### Response

```
{
    "name":"ACCELERATE ACQUISITION"
    "symbol":"AAQC-WS.US",
    "open":"0.4998",
    "high":"0.4999",
    "low":"0.4916",
    "close":"0.4981"
}
```

##### Validation Erros

Status Code: 400 (Bad Request)

```
{
    "message": "<stock_code> is not a valid stock code"
}
```

#### GET - history

```/history``` - Get the user's stock quote query history

##### AUTHORIZATION - Bearer Token

##### Response


```
[
    {
        "date": "2022-02-09 22:24:28",
        "name": "APPLE",
        "symbol": "AAPL.US",
        "open": "176.05",
        "high": "176.65",
        "low": "174.90",
        "close": "176.28"
    },
    {
        "date": "2022-02-09 22:24:11",
        "name": "APPL.US",
        "symbol": "APPL.US",
        "open": "0.00",
        "high": "0.00",
        "low": "0.00",
        "close": "0.00"
    },
    {
        "date": "2022-02-09 22:24:03",
        "name": "ACCELERATE ACQUISITION",
        "symbol": "AAQC-WS.US",
        "open": "0.50",
        "high": "0.50",
        "low": "0.49",
        "close": "0.50"
    }
]
```
## Testing Database

This API expects that you are using the MySQL as prod/dev database and sqlite for testing database. We'll configure the test database to run the tests.

Create a database.sqlite file in the database folder (database/database.sqlite)

Now, run the migration, but this time we'll use one more option to indicates that we will use another driver connection

```
composer migrate up driver:sqlite
```

With this **driver:sqlite** option, the migrations will be run on a sqlite database, that one you created one step above

With the database setup lets start to testing

## Unit Tests

First, lets see if the phpunit was correctly installed, run:

```
vendor/bin/phpunit --version
```

If the command does not exists, you'll need to install the dev dependencies

```
composer install --dev
```

Run the phpunit command to check again.

The API sets two test suites, feature and unit.

To run one at time use

```
composer feature:test
or
composer unit:test
```

In other hands you can use **composer test**, that will trigger both test suites

And that is it, I hope you enjoy the project. Thanks
