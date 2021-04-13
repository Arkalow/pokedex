# Pokedex

Ce projet est un TP de l'esiee d'Amiens

## Script d'installation

Pour installer le projet vous pouvez suivre les étapes classique ou lancer ce script d'installation

```

```

## Installation

```shell
apt install nginx git php-fpm php-zip php-xml
```

> Vous pouvez aussi installer *apache* au lieu de *nginx*

> Ce projet fonctionne sous **symfony 5** Il est donc necessaire d'installer la version ***> 7.3 de PHP***.

```shell
cd /var/www/html
git clone 
chown -R www-data:www-data webapp
```

### Environement

```shell
cp .env.dist .env
```

Editez le fichier `.env` avec vos infos d'environements (database, constantes...)

### Database

> N'oubliez pas de remplir le fichier d'environment avant de procéder à cette étape

Appliquer les migrations

```shell
php bin/console d:m:m
```

### Webserver

#### Nginx
```nginx
server {
    root /var/www/html/webapp/public;

    client_max_body_size 10G;
    fastcgi_buffers 64 4K;

    location / {
        proxy_read_timeout 180000;
         proxy_connect_timeout 180000;
         proxy_send_timeout 180000;
         send_timeout 180000;
        # try to serve file directly, fallback to app.php
        try_files $uri /index.php$is_args$args;
    }
    # DEV
    # This rule should only be placed on your development environment
    # In production, don't include this and don't deploy index_dev.php or config.php
    location ~ ^/(index_dev|config)\.php(/|$) {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        # When you are using symlinks to link the document root to the
        # current version of your application, you should pass the real
        # application path instead of the path to the symlink to PHP
        # FPM.
        # Otherwise, PHP's OPcache may not properly detect changes to
        # your PHP files (see https://github.com/zendtech/ZendOptimizerPlus/issues/126
        # for more information).
        # Caveat: When PHP-FPM is hosted on a different machine from nginx
        #         $realpath_root may not resolve as you expect! In this case try using
        #         $document_root instead.
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }
    # PROD
    location ~ ^/index\.php(/|$) {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
       # When you are using symlinks to link the document root to the
       # current version of your application, you should pass the real
       # application path instead of the path to the symlink to PHP
       # FPM.
       # Otherwise, PHP's OPcache may not properly detect changes to
       # your PHP files (see https://github.com/zendtech/ZendOptimizerPlus/issues/126
       # for more information).
       fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
       fastcgi_param DOCUMENT_ROOT $realpath_root;
       # Prevents URIs that include the webapp controller. This will 404:
       # http://domain.tld/app.php/some-path
       # Remove the internal directive to allow URIs like this
       internal;
   }

   # return 404 for all other php files not matching the webapp controller
   # this prevents access to other php files you don't want to be accessible.
   location ~ \.php$ {
     return 404;
   }

   error_log /var/log/nginx/project_error.log;
   access_log /var/log/nginx/project_access.log;
}
```

Ces deux lignes servent à augmenter la taille upload sur le serveur. J'ai mis 1G pour uploader les images (c'est laaaaaargement bon)

```nginx
client_max_body_size 1G;
fastcgi_buffers 64 4K;
```

#### Apache

Vous pouvez aussi installer ce projet avec apache

```xml
<VirtualHost *:80>
    ServerName domain.tld
    ServerAlias www.domain.tld

    DocumentRoot /var/www/project/public
    DirectoryIndex /index.php

    <Directory /var/www/project/public>
        AllowOverride None
        Order Allow,Deny
        Allow from All

        FallbackResource /index.php
    </Directory>

    # uncomment the following lines if you install assets as symlinks
    # or run into problems when compiling LESS/Sass/CoffeeScript assets
    # <Directory /var/www/project>
    #     Options FollowSymlinks
    # </Directory>

    # optionally disable the fallback resource for the asset directories
    # which will allow Apache to return a 404 error when files are
    # not found instead of passing the request to Symfony
    <Directory /var/www/project/public/bundles>
        DirectoryIndex disabled
        FallbackResource disabled
    </Directory>
    ErrorLog /var/log/apache2/project_error.log
    CustomLog /var/log/apache2/project_access.log combined

    # optionally set the value of the environment variables used in the application
    #SetEnv APP_ENV prod
    #SetEnv APP_SECRET <app-secret-id>
    #SetEnv DATABASE_URL "mysql://db_user:db_pass@host:3306/db_name"
</VirtualHost>
```

## API (jwt)

Sur ce projet j'ai rajouté une API qui permet de récupérer via des requetes **GET**.

```shell
curl http://localhost/pokemon.json
```

```json
[
  {
    "id": 4512,
    "numero": 1,
    "nom": "Bulbasaur",
    "vie": 45,
    "attaque": 49,
    "defense": 49,
    "legendaire": false,
    "type1": "\/api\/types\/1678",
    "type2": "\/api\/types\/1690",
    "generation": {
      "id": 817,
      "name": "1"
    }
  },
  {
    "id": 4513,
    "numero": 2,
    "nom": "Ivysaur",
    "vie": 60,
    "attaque": 62,
    "defense": 63,
    "legendaire": false,
    "type1": "\/api\/types\/1678",
    "type2": "\/api\/types\/1677",
    "generation": {
      "id": 817,
      "name": "1"
    }
  },
]
```

## Import pokemon

Pour importer les pokemons, tapez cette commande à la racine du projet

```shell
php bin/console import:pokemon 
```