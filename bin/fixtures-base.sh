#!/bin/bash
php bin/console doctrine:fixtures:load
if [ $# -lt 1 ]; then
    php bin/console fos:user:create adminuser --super-admin
    php bin/console fos:user:promote adminuser ROLE_API
fi;