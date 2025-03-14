<?php return array (
  'jwt' => 
  array (
    'secret' => 'bU7y7uyp3LnbrVjbJbnnEemQO7mETeHc',
    'ttl' => 60000,
    'refresh_ttl' => 20160,
    'algo' => 'HS256',
    'user' => 'App\\Provider',
    'identifier' => 'id',
    'required_claims' => 
    array (
      0 => 'iss',
      1 => 'iat',
      2 => 'exp',
      3 => 'nbf',
      4 => 'sub',
      5 => 'jti',
    ),
    'blacklist_enabled' => true,
    'providers' => 
    array (
      'user' => 'Tymon\\JWTAuth\\Providers\\User\\EloquentUserAdapter',
      'jwt' => 'Tymon\\JWTAuth\\Providers\\JWT\\NamshiAdapter',
      'auth' => 'Tymon\\JWTAuth\\Providers\\Auth\\IlluminateAuthAdapter',
      'storage' => 'Tymon\\JWTAuth\\Providers\\Storage\\IlluminateCacheAdapter',
    ),
  ),
  'cache' => 
  array (
    'default' => 'file',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => '/var/www/clients/client0/web1/web/storage/framework/cache',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
    ),
    'prefix' => 'laravel',
  ),
  'app' => 
  array (
    'name' => 'PickUp',
    'env' => 'production',
    'debug' => false,
    'url' => 'http://',
    'timezone' => 'Africa/Algiers',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'key' => 'base64:KZ+/pajFmLk89qRhmNotIxMUXJfWNAdix2k3E3/0E8s=',
    'cipher' => 'AES-256-CBC',
    'log' => 'single',
    'log_level' => 'debug',
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Cookie\\CookieServiceProvider',
      6 => 'Illuminate\\Database\\DatabaseServiceProvider',
      7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      10 => 'Illuminate\\Hashing\\HashServiceProvider',
      11 => 'Illuminate\\Mail\\MailServiceProvider',
      12 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      13 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      14 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      15 => 'Illuminate\\Queue\\QueueServiceProvider',
      16 => 'Illuminate\\Redis\\RedisServiceProvider',
      17 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      18 => 'Illuminate\\Session\\SessionServiceProvider',
      19 => 'Illuminate\\Translation\\TranslationServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'Laravel\\Passport\\PassportServiceProvider',
      23 => 'Laravel\\Socialite\\SocialiteServiceProvider',
      24 => 'anlutro\\LaravelSettings\\ServiceProvider',
      25 => 'Tymon\\JWTAuth\\Providers\\JWTAuthServiceProvider',
      26 => 'Barryvdh\\TranslationManager\\ManagerServiceProvider',
      27 => 'Davibennun\\LaravelPushNotification\\LaravelPushNotificationServiceProvider',
      28 => 'App\\Providers\\AppServiceProvider',
      29 => 'App\\Providers\\AuthServiceProvider',
      30 => 'App\\Providers\\EventServiceProvider',
      31 => 'App\\Providers\\RouteServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Redis' => 'Illuminate\\Support\\Facades\\Redis',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'Setting' => 'anlutro\\LaravelSettings\\Facade',
      'Socialite' => 'Laravel\\Socialite\\Facades\\Socialite',
      'JWTAuth' => 'Tymon\\JWTAuth\\Facades\\JWTAuth',
      'JWTFactory' => 'Tymon\\JWTAuth\\Facades\\JWTFactory',
      'PushNotification' => 'Davibennun\\LaravelPushNotification\\Facades\\PushNotification',
    ),
  ),
  'mail' => 
  array (
    'driver' => 'smtp',
    'host' => 'smtp.gmail.com',
    'port' => '587',
    'from' => 
    array (
      'address' => 'hello@example.com',
      'name' => 'Example',
    ),
    'encryption' => 'tls',
    'username' => '',
    'password' => '',
    'sendmail' => '/usr/sbin/sendmail -bs',
  ),
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => NULL,
        'secret' => NULL,
        'app_id' => NULL,
        'options' => 
        array (
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'settings' => 
  array (
    'store' => 'database',
    'path' => '/var/www/clients/client0/web1/web/storage/settings.json',
    'table' => 'settings',
    'connection' => NULL,
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'cloud' => 's3',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => '/var/www/clients/client0/web1/web/storage/app/public',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => '/var/www/clients/client0/web1/web/storage/app/public',
        'visibility' => 'public',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => 'your-key',
        'secret' => 'your-secret',
        'region' => 'your-region',
        'bucket' => 'your-bucket',
      ),
    ),
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => '/var/www/clients/client0/web1/web/resources/views',
    ),
    'compiled' => '/var/www/clients/client0/web1/web/storage/framework/views',
  ),
  'debugbar' => 
  array (
    'enabled' => NULL,
    'storage' => 
    array (
      'enabled' => true,
      'driver' => 'file',
      'path' => '/var/www/clients/client0/web1/web/storage/debugbar',
      'connection' => NULL,
      'provider' => '',
    ),
    'include_vendors' => true,
    'capture_ajax' => true,
    'add_ajax_timing' => false,
    'error_handler' => false,
    'clockwork' => false,
    'collectors' => 
    array (
      'phpinfo' => true,
      'messages' => true,
      'time' => true,
      'memory' => true,
      'exceptions' => true,
      'log' => true,
      'db' => true,
      'views' => true,
      'route' => true,
      'auth' => true,
      'gate' => true,
      'session' => true,
      'symfony_request' => true,
      'mail' => true,
      'laravel' => false,
      'events' => false,
      'default_request' => false,
      'logs' => false,
      'files' => false,
      'config' => false,
    ),
    'options' => 
    array (
      'auth' => 
      array (
        'show_name' => true,
      ),
      'db' => 
      array (
        'with_params' => true,
        'backtrace' => true,
        'timeline' => false,
        'explain' => 
        array (
          'enabled' => false,
          'types' => 
          array (
            0 => 'SELECT',
          ),
        ),
        'hints' => true,
      ),
      'mail' => 
      array (
        'full_log' => false,
      ),
      'views' => 
      array (
        'data' => false,
      ),
      'route' => 
      array (
        'label' => true,
      ),
      'logs' => 
      array (
        'file' => NULL,
      ),
    ),
    'inject' => true,
    'route_prefix' => '_debugbar',
    'route_domain' => NULL,
  ),
  'compile' => 
  array (
    'files' => 
    array (
    ),
    'providers' => 
    array (
    ),
  ),
  'image' => 
  array (
    'driver' => 'gd',
    'memory_limit' => '256M',
    'src_dirs' => 
    array (
      0 => '/var/www/clients/client0/web1/web/public',
    ),
    'host' => '',
    'pattern' => '^(.*){parameters}\\.(jpg|jpeg|png|gif|JPG|JPEG|PNG|GIF)$',
    'url_parameter' => '-image({options})',
    'url_parameter_separator' => '-',
    'serve' => true,
    'serve_domain' => NULL,
    'serve_route' => '{image_pattern}',
    'serve_custom_filters_only' => false,
    'serve_expires' => 2678400,
    'write_image' => false,
    'write_path' => NULL,
    'proxy' => false,
    'proxy_expires' => NULL,
    'proxy_route' => '{image_proxy_pattern}',
    'proxy_route_pattern' => NULL,
    'proxy_route_domain' => NULL,
    'proxy_filesystem' => 'cloud',
    'proxy_write_image' => true,
    'proxy_cache' => true,
    'proxy_cache_filesystem' => NULL,
    'proxy_cache_expiration' => 1440,
    'proxy_tmp_path' => '/tmp',
  ),
  'paypal' => 
  array (
    'client_id' => 'AVPSjQbR0VdaRv2h35o6gNAFdUOM0Oe-251pPER1eX_vArda5MPI-BVXCW7uAtuMMi2OuTYsyvryexJj',
    'secret' => 'ECaFz5wYBuR2kHjDXmzQM5dIVkTYT91acDPH2v74h_J0VITI9d3IdS0BwDy6xAmV2cz6k2yiHjzMXNzH',
    'settings' => 
    array (
      'mode' => 'sandbox',
      'http.ConnectionTimeOut' => 1000,
      'log.LogEnabled' => true,
      'log.FileName' => '/var/www/clients/client0/web1/web/storage/logs/paypal.log',
      'log.LogLevel' => 'FINE',
    ),
  ),
  'translation-manager' => 
  array (
    'route' => 
    array (
      'prefix' => 'translations',
      'middleware' => 
      array (
        0 => 'web',
        1 => 'admin',
      ),
    ),
    'delete_enabled' => true,
    'exclude_groups' => 
    array (
    ),
    'sort_keys ' => false,
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'account' => 
      array (
        'driver' => 'session',
        'provider' => 'accounts',
      ),
      'fleet' => 
      array (
        'driver' => 'session',
        'provider' => 'fleets',
      ),
      'provider' => 
      array (
        'driver' => 'session',
        'provider' => 'providers',
      ),
      'admin' => 
      array (
        'driver' => 'session',
        'provider' => 'admins',
      ),
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'api' => 
      array (
        'driver' => 'passport',
        'provider' => 'users',
      ),
      'providerapi' => 
      array (
        'driver' => 'passport',
        'provider' => 'providers',
      ),
    ),
    'providers' => 
    array (
      'accounts' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Account',
      ),
      'fleets' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Fleet',
      ),
      'providers' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Provider',
      ),
      'admins' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Admin',
      ),
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\User',
      ),
    ),
    'passwords' => 
    array (
      'accounts' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Account',
      ),
      'fleets' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Fleet',
      ),
      'providers' => 
      array (
        'provider' => 'providers',
        'table' => 'password_resets',
        'expire' => 600,
      ),
      'admins' => 
      array (
        'provider' => 'admins',
        'table' => 'password_resets',
        'expire' => 600,
      ),
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 600,
      ),
    ),
  ),
  'queue' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => 'your-public-key',
        'secret' => 'your-secret-key',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'your-queue-name',
        'region' => 'us-east-1',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
      ),
    ),
    'failed' => 
    array (
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'database' => 
  array (
    'fetch' => 5,
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'database' => 'c0db',
        'prefix' => '',
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'c0db',
        'username' => 'c0user',
        'password' => 'nbUDjbkqX7mOpRt',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => NULL,
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'c0db',
        'username' => 'c0user',
        'password' => 'nbUDjbkqX7mOpRt',
        'charset' => 'utf8mb4',
        'prefix' => '',
        'schema' => 'public',
        'sslmode' => 'prefer',
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'cluster' => false,
      'default' => 
      array (
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => 0,
      ),
    ),
  ),
  'push-notification' => 
  array (
    'IOSUser' => 
    array (
      'environment' => 'development',
      'certificate' => '/var/www/clients/client0/web1/web/app/apns/user/tranxit_user_live.pem',
      'passPhrase' => 'appoets123$',
      'service' => 'apns',
    ),
    'IOSProvider' => 
    array (
      'environment' => 'development',
      'certificate' => '/var/www/clients/client0/web1/web/app/apns/provider/tranxit_provider_live.pem',
      'passPhrase' => 'appoets123$',
      'service' => 'apns',
    ),
    'AndroidUser' => 
    array (
      'environment' => 'production',
      'apiKey' => '',
      'service' => 'gcm',
    ),
    'AndroidProvider' => 
    array (
      'environment' => 'production',
      'apiKey' => '',
      'service' => 'gcm',
    ),
  ),
  'services' => 
  array (
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
    ),
    'ses' => 
    array (
      'key' => NULL,
      'secret' => NULL,
      'region' => 'us-east-1',
    ),
    'sparkpost' => 
    array (
      'secret' => NULL,
    ),
    'stripe' => 
    array (
      'key' => 'STRIPE_SECRET=',
      'secret' => NULL,
    ),
    'facebook' => 
    array (
      'client_id' => '',
      'client_secret' => '',
      'redirect' => '',
    ),
    'google' => 
    array (
      'client_id' => '',
      'client_secret' => '',
      'redirect' => '',
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => '/var/www/clients/client0/web1/web/storage/framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'laravel_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => false,
    'http_only' => true,
  ),
);
