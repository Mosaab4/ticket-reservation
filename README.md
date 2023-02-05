# Ticket Reservation System

## Getting Started
1. ``` git clone https://github.com/Mosaab4/ticket-reservation.git```
2. ``cd ticket-reservation``
3. ```sh docker/setup.sh```
4. ```docker-compose up -d```


## Login Credentials
You can use the following credentials to create a token so you can use the APIs:

```
Email:      admin@admin.com
Password:   123456
```
## Reservation flow
You can create an order by creating a session.

During the session you select pickup, destination and seats you want to reserve.

Every session has unique UUID you can use this UUID to create the order .


## Tests
To Run all unit tests:

``` docker-compose exec app vendor/bin/phpunit```

## PHP-CS-Fixer
To Run all unit tests:

``` docker-compose exec app vendor/bin/phpunit```


### Postman Collection
This postman collection contains all the required APIs

[Postman Collection](https://documenter.getpostman.com/view/2179951/2s935oLPFV)
