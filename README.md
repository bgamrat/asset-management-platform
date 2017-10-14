# mobile-asset-management-platform
---

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