# ContaoDatabaseCommandsBundle

No need for the install tool anymore.
This Bundle comes with a command to update the database to reflect all changed made in the dca files.
Furthermore you can now add admin user per command!

## Installation

### Step 1: Install the bundle

```bash
composer require fuzzyma/contao-database-commands-bundle
```

### Step 2: Register the bundle

Open your AppBundle.php and add the following line in the dev/test section:

```php
$bundles[] = new Fuzzyma\Contao\DatabaseCommandsBundle\ContaoDatabaseCommandsBundle();
```

## Usage

```bash
app/console contao:database:update --help // prints help messages
app/console contao:database:update -d     // updates the database INCLUDING [d]rops
app/console contao:database:addAdmin      // interactive
app/console contao:database:addAdmin -u username -a name -m mail -p password // for the pros
```

## Register database updates to the composer post-update-cmd

Just add the following to the post-update-cmd array in your composer.json:

```bash
php app/console contao:database:update
```