<?php
return [
    'user-1' => [
        'id' => 1,
        'username' => 'user',
        'auth_key' => 'testkeyuser',
        'password_hash' => Yii::$app->security->generatePasswordHash('user123'),
        'email' => 'user@example.com',
        'phone' => '+70000000000',
        'role' => 'user',
        'status' => 10,
        'created_at' => time(),
        'updated_at' => time(),
    ],
];

