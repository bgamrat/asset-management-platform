# mobile-asset-management-platform
---

## Overview

A system intended to help manage assets which are on tractor-trailers.

## Project Status

Proof of concept - not fully operational

*Upgrade to Symfony 4 in progress*

## Demo

Soon

## Requirements

- Database
    - Postgres? I'm not sure, because the bulk of the database interaction is using Doctrine and
I don't have time to test other databases right now.  App was built with Postgres.
- PHP 7.1+
- composer
- node / java to build JavaScript (Dojo)
- web server - this was built on Apache

## Installation

*Not tested. Ever.*

1. Download the code
2. Run **composer install**
3. Create an .env file with the following environment variables
 - APP_ENV
 - APP_SECRET
 - MAILER_URL
 - APP_DATABASE_URL
 - LEGACY_DATABASE_URL
 - MEMCACHED_HOST
 - MEMCACHED_PORT
4. Create the databases or run **php bin/console doctrine:database:create**
5. Run **./bin/fixtures-base.sh** to install the required base data types.  This will also allow you to
create an admin user.
6. Run **./bin/fixtures-demo.sh** to install demo data (optional)
7. To build the *Dojo* assets for production use, **cd assets/vendor; ./build.sh;**.  When in
dev mode, the source JavaScript files are used to allow you to step through the code.
Use **cd public; ln -sf /var/www/html/symfony-dev/assets src** to create a symlink for
development (not on production).
8. Run **./node_modules/.bin/encore dev** to dump the CSS. Building the client side code on my production server didn't go well
   Instead, tar up the files from your local machine and scp them up
     - tar up the client side files with **tar czf ~/public-build.tgz public/build**
     - scp them to the production server with **scp ~/public-build.tgz user@example.com:.
     - Move them into position above the target directory - **sudo mv public-build.tgz /var/www/html/symfony-dev**
     - Change ownership for tidiness **sudo chown user.group /var/www/html/symfony-dev/public-build.tgz**
     - Switch to be that user **sudo su user**
     - Change into the directory **cd /var/www/html/symfony-dev**
     - Extract the files out of the tar with **tar xzf public-build.tgz**

9. There is an *httpd.conf* file in *app/system/etc/httpd/conf.d*, it might work

## Terminology

### Addresses

Addresses are street addresses

### Assets

Assets are organized by a hierarchical category tree.  An asset has a status such as
*operational* or *missing*.

### Barcodes

Barcodes are assigned to **assets**.  An **asset** may have more than one barcode.  Barcodes
may be marked as *default*, indicating the one in use.

### Brands

Brands are collections of **models** associated with **manufacturers**

### Carriers

Carriers move **assets** between **locations**.

### Categories

Categories are organized in a hierarchy

### Clients

Clients are ... clients.

### Contacts

Contacts are **persons**

### Contracts

Contracts are collections of requirements for **trailers** and **assets**.

### Events

An event requires **assets**.  It may include one or more **contracts** which require the **assets**.
An event occurs at a single **venue**.  **Trailers** may be assigned to the event through **contracts**
or directly.  The **assets** on the **trailers** and at the **venue** are considered available for
the event.

### Issues

Issues are specific problems related to **assets**

### Locations

Locations are places where **assets** may be.  A location may be a static place such as a main location,
**manufacturer**, **trailer** **vendor**, or **venue**.  Locations may be a **contact**, meaning a **person**
at a specific **address**.

### Manufacturers

Manufacturers are collections of **brands**

### Models

Models are assigned a primary **category**, and may *satisfy* a category requirement.  They may
also *require* additional **models**, or *extend* other **models**.

Models are associated with **brands**.

### Persons (people)

A person is someone associated with something.  A person may have multiple **contact** instances,
for example an *office* phone and a *personal* email, as well as a *receiving* address.

### Requirements

Requirements are described as **category quantities**, using the categories from the tree.

### Transfers

Transfers describe the transfer of **assets** from one **location** to another

### Trailers

Trailers are ... trailers.  Trailers carry **assets**.  Trailers may be required by **contracts** and
associated with **clients**.  A trailer is a special kind of **asset**.

### Vendor

Vendors sell or fix **assets**.

### Venue

Venues are places where **events** occur.
