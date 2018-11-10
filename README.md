Installation:

in `config/auth.php`

The following should be set

```
    'defaults' => [
        'guard'     => 'api',
        'passwords' => 'users',
    ],

   'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver'   => 'jwt',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\User::class,
        ],
        'jwt'   => [
            'driver' => 'eloquent',
            'model'  => App\User::class,
        ]

```
