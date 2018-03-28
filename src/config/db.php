<?php

return [
    'class' => yii\db\Connection::class,
    'dsn' => getenv('DB_DSN'),
    'username' => getenv('DB_USERNAME'),
    'password' => getenv('DB_PASSWORD'),
    'charset' => 'utf8',
    'enableSchemaCache' => getenv('DB_SCHEMA_CACHE'),
    'schemaCacheDuration' => getenv('DB_SCHEMA_CACHE_DURATION'),
    'schemaCache' => 'cache'
];
