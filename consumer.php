<?php
// Подключите библиотеку RabbitMQ
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/*
Определите функцию обратного вызова.
Эта функция будет вызываться при получении сообщения. Он просто повторяет тело сообщения.
*/
function callback($msg)
{
    echo " [x] Received ", $msg->body, "\n";
}

// Настройте параметры соединения RabbitMQ:
$credentials = [
    'host' => '127.0.0.1',
    'port' => 5672,
    'user' => 'guest',
    'password' => 'guest',
    'vhost' => 'my_host',
];

// Создайте соединение с RabbitMQ
$connection = new AMQPStreamConnection(
    $credentials['host'], 
    $credentials['port'], 
    $credentials['user'], 
    $credentials['password'], 
    $credentials['vhost']
);

// Создайте канал
$channel = $connection->channel();

// Эта строка объявляет на канале очередь с именем «hello»
$channel->queue_declare('hello', false, false, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

/* 
Определите обратный вызов для потребления сообщений
Эта анонимная функция будет вызываться при использовании сообщения. Он повторяет полученное сообщение.
*/
// $callback = function ($msg) {
//     echo ' [x] Received ', $msg->body, "\n";
// };

$callback = function ($msg) {
    // test
    $logMessage = ' [x] Received ' . $msg->body . "\n";
    file_put_contents('logfile.txt', $logMessage, FILE_APPEND);
};

/* 
Настройте потребителя на прослушивание сообщений в очереди «hello»
Эта строка настраивает потребителя на начало приема сообщений из очереди «hello». 
Функция $callbackбудет вызвана при получении сообщения.
*/
$channel->basic_consume('hello', '', false, true, false, false, $callback);

/* 
Введите цикл для ожидания и обработки сообщений
Этот цикл постоянно ожидает сообщений. Метод $channel->wait()блокируется до тех пор, 
пока не будет получено сообщение, а затем вызывает функцию обратного вызова.
*/
while ($channel->is_consuming()) {
    $channel->wait();
}

// Закройте канал и соединение, когда закончите
$channel->close();
$connection->close();
