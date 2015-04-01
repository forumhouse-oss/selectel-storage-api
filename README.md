selectel-storage-api
====================

Selectel Cloud Storage API.

Composer setup
-------------------

*Please do note, that package name changed to `fhteam/selectel-storage-api`.* Old name should still work, though
it will no longer be maintained.

```
    "require": {
        "fhteam/selectel-storage-api": "dev-master"
    }
```

Authenticating
-------------------
```php
    $config = include(__DIR__ . '/../data/config.php');
    $container = new Container($config['auth_container']);

    $auth = new CredentialsAuthentication($config['auth_user'], $config['auth_key'], $config['auth_url']);
    $auth->authenticate();
```

Uploading a file
-------------------
```php
    $file = new File('test.txt');
    $file->setLocalName(__DIR__ . '/../data/config.php');
    $file->setSize();

    $service = new StorageService($auth);
    $service->uploadFile($container, $file);
```

Deleting a file
-------------------

```php
    $file = new File('test.txt');
    $service = new StorageService($auth);
    $service->deleteFile($container, $file);
```

Parallel operations
-------------------

If you need to do an operation over several files, consider using parallel versions of operations.
When using parallel versions of operations requests are sent to Selectel simultaneously.
Execution blocks until all replies are received from server.
 
For `uploadFile()` there is `uploadFiles()`, accepting an array of files to be uploaded into the container. 
For `deleteFile()` `deleteFiles()` is also available etc.

If a parallel operation fails, `ParallelOperationException` is raised with `$errors` field ready for inspection