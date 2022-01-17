# hades-library

Librairie php pour interroger l'API de Pivot

https://www.ftlb.be/

Installation
----

`composer require acmarche/pivot:dev-master`

Configuration
-----------------

###  Définir les variables d'environnements:

En créant un fichier .env.local.php à la racine de votre installation ou  
via les variables d'environnment de votre système d'exploitation

```php
<?php
//.env.local.php
return array (
  'PIVOT_URL'      => 'http://ftlb.be',
  'PIVOT_USER'     => 'username',
  'PIVOT_PASSWORD' => 'mdp',
  'APP_ENV'       => 'prod',
);
```

Utilisation
----

```php
require_once 'vendor/autoload.php';

use AcMarche\Pivot\Repository\HadesRepository;

$hadesRepository = new HadesRepository();
$events = $hadesRepository->getEvents();
```
