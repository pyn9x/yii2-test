<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => sprintf('mysql:host=%s;port=%s;dbname=%s', getenv('DB_HOST') ?: 'db', getenv('DB_PORT') ?: '3306', getenv('DB_DATABASE') ?: 'yii2app'),
    'username' => getenv('DB_USERNAME') ?: 'yii2',
    'password' => getenv('DB_PASSWORD') ?: 'yii2pass',
    'charset' => 'utf8mb4',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
