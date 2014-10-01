selectel-storage-api
====================

Selectel cloud storage API


An example of use
====================

Uploading a file
-------------------
```php
    $config = include(__DIR__ . '/../data/config.php');
    $container = new Container($config['auth_container']);

    $auth = new CredentialsAuthentication($config['auth_user'], $config['auth_key'], $config['auth_url']);
    $auth->authenticate();

    $file = new File('test.txt');
    $file->setLocalName(__DIR__ . '/../data/config.php');
    $file->setSize();

    $service = new StorageService($auth);
    $service->uploadFile($container, $file);
```

Deleting a file
-------------------

```php
    $config = include(__DIR__ . '/../data/config.php');
    $container = new Container($config['auth_container']);

    $file = new File('test.txt');

    $auth = new CredentialsAuthentication($config['auth_user'], $config['auth_key'], $config['auth_url']);
    $auth->authenticate();

    $service = new StorageService($auth);
    $service->deleteFile($container, $file);
```