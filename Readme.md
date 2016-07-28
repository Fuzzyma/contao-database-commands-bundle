# ContaoDatabaseCommandsBundle

No need for the install tool anymore.
This Bundle comes with a command to update the database to reflect all changed made in the dca files.
Furthermore you can now add admin user per command, accept the license and do the whole first setup process with one command!

## Installation

### Step 1: Install the bundle

```bash
composer require fuzzyma/contao-database-commands-bundle
```

### Step 2: Register the bundle

Open your AppKernel.php and add the following line in the dev/test section:

```php
$bundles[] = new Fuzzyma\Contao\DatabaseCommandsBundle\ContaoDatabaseCommandsBundle();
```

or use the composer plugin to register bundles: [ComposerRegisterBundlePlugin](https://github.com/Fuzzyma/composer-register-bundle-plugin)

## Usage

The following commands are available:

- contao:database:update | Updates the database to the current dca state
- contao:database:addAdmin | Adds a new admin user
- contao:license | Accepts the license
- contao:setup | Creates Database and runs all other commands to perform a full contao setup

### contao:database:update

```
app/console contao:database:update -d        // updates the database INCLUDING [d]rops
app/console contao:database:update --dry-run // only prints queries. database is left untouched
```

### contao:database:addAdmin

```
app/console contao:database:addAdmin // creates a new admin user interactively
app/console contao:database:addAdmin -u username -a name -m mail -p password // for the pros
app/console contao:database:addAdmin --force // will add admin even if admin user already present in tl_user table
```

### contao:license

```
app/console contao:license       // accept the license interactively
app/console contao:license --yes // accept the license directly
```

### contao:setup

```
app/console contao:setup // do all together + creates database if not exists
```

## Register database updates to the composer post-update-cmd

Just add the following to the post-update-cmd array in your composer.json:

```bash
php app/console contao:database:update
```