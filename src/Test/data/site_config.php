<?php
define('APPLICATION_NAME','wave');

define('BASE_URI' , '/wave');
define('BASE_HOST', 'localhost');
define('BASE_URL' , 'https://'.BASE_HOST.BASE_URI);
define('USWDS_URL', '/static/uswds/dist');

define('WAVE_API_KEY', 'something secret');
define('DRUPAL_SITE',  'https://localhost/drupal');
define('DRUPAL_HOME',  '/srv/data/drupal');


/**
 * Database Setup
 * Refer to the PDO documentation for DSN sytnax for your database type
 * http://www.php.net/manual/en/pdo.drivers.php
 */
$DATABASES = [
    'default' => [
        'dsn'  => 'mysql:dbname=wave;host=localhost',
        'user' => 'test',
        'pass' => 'h++pd',
    ],
    'drupal' => [
        'dsn'  => 'mysql:dbname=drupal;host=localhost',
        'user' => 'test',
        'pass' => 'h++pd',
    ]
];

$LDAP = [
    'COB' => [
        'classname'          => 'Site\Employee',
        'server'             => 'ldaps://ldap.example.org:636',
        'base_dn'            => 'DC=ldap,DC=example,DC=org',
        'username_attribute' => 'sAMAccountName',
        'user_binding'       => '{username}@ldap.example.org',
        'admin_binding'      => 'admin@ldap.example.org',
        'admin_pass'         => 'secret password'
    ]
];

$AUTHENTICATION = [
    'oidc' => [
        'server'         => 'https://ad.example.org/adfs',
        'client_id'      => '',
        'client_secret'  => '',
        'claims' => [
            // OnBoard field => OIDC Claim
            'username'    => 'preferred_username',
            'displayname' => 'commonname',
            'firstname'   => 'given_name',
            'lastname'    => 'family_name',
            'email'       => 'email',
            'groups'      => 'group',
            'groupmap'    => [ ]
        ],
    ]
];

$GRACKLE = [
    'server' => 'https://api.grackledocs.com/prod/v1.2',
    'user'   => '',
    'pass'   => ''
];

define('SMTP_HOST', "smtp.example.org");
define('SMTP_PORT', 25);

define('DATE_FORMAT', 'n/j/Y');
define('TIME_FORMAT', 'g:i a');
define('DATETIME_FORMAT', 'n/j/Y g:i a');

define('LOCALE', 'en_US');
