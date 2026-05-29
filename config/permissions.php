<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Role Permission Matrix
    |--------------------------------------------------------------------------
    |
    | Definisikan izin akses per role per modul.
    | Format: 'module' => ['role' => ['create', 'read', 'update', 'delete']]
    |
    */

    'roles' => [
        'admin' => [
            'patients' => ['create', 'read', 'update', 'delete'],
            'consultations' => ['read', 'delete'],
            'services' => ['create', 'read', 'update', 'delete'],
            'orders' => ['create', 'read', 'update', 'delete'],
            'payments' => ['create', 'read', 'update', 'delete', 'verify'],
            'production' => ['read'],
            'inventory' => ['create', 'read', 'update', 'delete'],
            'reports' => ['read'],
            'users' => ['create', 'read', 'update', 'delete'],
            'audit' => ['read'],
        ],

        'dokter' => [
            'patients' => ['read'],
            'consultations' => ['create', 'read', 'update', 'delete'],
            'services' => ['read'],
            'orders' => ['create', 'read'],
            'payments' => [],
            'production' => ['read'],
            'inventory' => ['read'],
            'reports' => ['read'],
            'users' => [],
            'audit' => [],
        ],

        'staf_klinik' => [
            'patients' => ['create', 'read', 'update', 'delete'],
            'consultations' => ['read'],
            'services' => ['read'],
            'orders' => ['create', 'read', 'update'],
            'payments' => ['create', 'read', 'update', 'delete'],
            'production' => ['read'],
            'inventory' => ['create', 'read'],
            'reports' => ['read'],
            'users' => [],
            'audit' => [],
        ],

        'teknisi' => [
            'patients' => [],
            'consultations' => [],
            'services' => [],
            'orders' => ['read'],
            'payments' => [],
            'production' => ['read', 'update'],
            'inventory' => ['read'],
            'reports' => [],
            'users' => [],
            'audit' => [],
        ],
    ],

];
