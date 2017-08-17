#!/bin/bash
php bin/console doctrine:fixtures:load
php bin/console fos:user:create adminuser --super-admin
php bin/console fos:user:promote adminuser ROLE_API