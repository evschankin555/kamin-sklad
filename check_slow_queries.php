<?php

// Создаем агент, который будет анализировать логи SQL-запросов
function CheckSlowQueriesAgent()
{
    // Путь к файлу лога SQL-запросов
    $logFile = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/sql.log';

    // Минимальное время выполнения запроса для считывания как медленного (в секундах)
    $threshold = 1.0;

    // Открываем файл лога для записи
    $logHandle = fopen($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/slow_queries.log', 'a');

    // Читаем содержимое файла лога
    $logContents = file_get_contents($logFile);

    // Разбиваем содержимое на строки
    $logLines = explode("\n", $logContents);

    // Перебираем строки лога
    foreach ($logLines as $line) {
        // Разбираем строку лога
        if (strpos($line, 'query_time:') !== false) {
            $parts = explode("\n", $line);
            foreach ($parts as $part) {
                if (strpos($part, 'query_time:') !== false) {
                    $time = (float)str_replace('query_time:', '', $part);
                    if ($time > $threshold) {
                        // Записываем медленный запрос в лог
                        fwrite($logHandle, date('Y-m-d H:i:s') . " - Slow Query ({$time}s):" . PHP_EOL);
                        fwrite($logHandle, trim($part) . PHP_EOL);
                    }
                }
            }
        }
    }

    // Закрываем файл лога
    fclose($logHandle);

    return '';
}

// Регистрируем агент для выполнения каждую минуту
$agentName = 'CheckSlowQueriesAgent';
CAgent::AddAgent($agentName, 'my_module', 'N', 60);

return 'CheckSlowQueriesAgent();';
