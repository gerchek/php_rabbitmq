<?php
// Подключите библиотеку RabbitMQ
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Установите учетные данные соединения RabbitMQ
$credentials = [
    'host' => '127.0.0.1',
    'port' => 5672,
    'user' => 'guest',
    'password' => 'guest',
    'vhost' => 'my_host',
];


// Установить соединение
$connection = new AMQPStreamConnection(
    $credentials['host'], 
    $credentials['port'], 
    $credentials['user'], 
    $credentials['password'], 
    $credentials['vhost']
);

// Создать канал . Каналы используются для связи с брокером сообщений
$channel = $connection->channel();

/*
Объявить очередь . Эта строка объявляет на канале очередь с именем «hello» . Параметры ( false, false, false, false) представляют различные 
свойства очереди, такие как долговечность, эксклюзивность, автоматическое удаление и дополнительные аргументы
*/
$channel->queue_declare('hello', false, false, false, false);

// Создать сообщение
$messageBody = 'Hello!';
$message = new AMQPMessage($messageBody);

/* 
Опубликовать сообщение
Сообщение публикуется в очереди «Hello» на канале. Второй параметр ( '') представляет обмен, а третий параметр ( 'hello') — ключ маршрутизации
*/
$channel->basic_publish($message, '', 'hello');

echo " [x] Sent 'Hello!'\n";

// Закройте канал и соединение
$channel->close();
$connection->close();
