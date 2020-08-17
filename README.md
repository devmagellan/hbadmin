hbadmin
=======

###Развертывание

1. Стянуть проект с репозитория

2. Создать БД в mysql
3. Установить вендоры с указанием параметром подключения к БД и др. 

    ` composer install `

4. Создать пользователя + загрузить дефолтные данные

    ` php bin/console user:create admin@admin.ru 1111 `

5. Запустить локальный серв 
(например встроенный `php bin/console server:start`)

Url логина *server_host*/login

###Deploy
конфиг деплоя deploy.php

Необходимо:
- иметь ssh доступ по ключу на сервер под пользователем ubuntu
- установить [Deployer](https://deployer.org/download/)

Команды:
dep deploy prod
dep deploy dev 

*На данный момент без миграций при деплое*


###RabbitMQ
Для чтения [webhook'ов](https://support.teachable.com/hc/en-us/articles/222808927-Add-a-Webhook) используется rabbitmq (обязательная установка сервера)
текущие параметры указаны в parameters.yml.dist
доступ в менеджер по ip сервера (на данный момент http://18.218.15.255:15672)
логин и пароль из yml файла

для teachable webhook роут *host*/api/hook/teachable/all

запуск обработчика - consumer - consumers:webhook:teachable

также необходимо для гарантии работы процессинга вебхуков настроить supervisor 

Config:
____________________________________
[program:teachable-webhook-listener]
command=php /var/www/merchants.heartbeat.education/htdocs/bin/console consumers:webhook:teachable --env=prod
process_name=%(program_name)s_%(process_num)02d  ; process_name expr (default %(program_name)s)
numprocs=1                    ; number of processes copies to start (def 1)
user=root

autostart=true
startsecs=2
startretries=999999999
autorestart=true
stopwaitsecs=10
redirect_stderr=true
stdout_logfile=AUTO
____________________________________

