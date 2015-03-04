selectel-storage-api
====================

Selectel Cloud Storage API.


An example of use
====================

Composer setup
-------------------

*Please do note, that package name changed to `fhteam/selectel-storage-api`.* Old name should still work, though
it will no longer be maintained.

```
    "require": {
        "fhteam/selectel-storage-api": "dev-master"
    }
```

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