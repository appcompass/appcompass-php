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
            'model'  => AppCompass\AppCompass\Models\User::class,
        ],
        'jwt'   => [
            'driver' => 'eloquent',
            'model'  => AppCompass\AppCompass\Models\User::class,
        ]

```
