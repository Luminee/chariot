## Chariot

### Installation

- `composer require luminee/chariot`
- write `ChariotServiceProvider::class` on `app.php`
- `php artisan vendor:publish`
- make `.conn.conf.php` according `.conn.conf.php.example`
- change config in `config/chariot.php`

### Make script file

- example : you want to make command `init:user` for `project` in module `user`
- make file `InitUser.php` in `app/Console/Commands/Project/User` (according to config `script_namespace` in chariot.php)
- file looks like:
```php
use Luminee\Chariot\Core\Command;

class Init extends Command
{
    protected $signature = 'init:user';

    public function run(){

    }
}
```

### Run script

- you can run script like `php chariot project.user.init:user.dev --run` to run it on dev connection