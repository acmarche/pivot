# hades-library

Librairie php pour interroger l'API de Pivot

https://pivotweb.tourismewallonie.be

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
 'PIVOT_URL'      => 'https://pivotweb.tourismewallonie.be/PivotWeb-3.1',
 'PIVOT_BASE_URI' => 'https://pivotweb.tourismewallonie.be/PivotWeb-3.1',
 'PIVOT_WS_KEY'   => 'xxxxxxx-xxxx-xxxx-xxxx-xxxxxxx',
 'PIVOT_CODE'     => 'xx-xx-xxxx-xxxx',
 'APP_ENV'       => 'prod',
);
```

Utilisation
----

```php
require_once 'vendor/autoload.php';

use AcMarche\Pivot\DependencyInjection\PivotContainer;

$pivotRepository = PivotContainer::getRepository();
$events          = $pivotRepository->getEvents(true);
```
