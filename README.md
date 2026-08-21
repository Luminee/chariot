# Chariot

Laravel 脚本管理工具 —— 脚本文件按目录自动组织签名，支持在签名中直接指定数据库连接。

## 安装

```bash
composer require luminee/chariot
```

如需多连接支持（`extra_connections`），建议同时安装 Switcher：

```bash
composer require luminee/switcher
```

## 快速开始

### 1. 替换 Kernel

在 `bootstrap/app.php` 中绑定 Chariot Kernel：

```php
$app->singleton(
    Luminee\Chariot\Contracts\Console\Kernel::class,
    class_exists('App\\Console\\Kernel') ?
        Luminee\Chariot\Console\KernelArtisan::class :
        Luminee\Chariot\Console\KernelChariot::class
);
```

或者直接使用 Chariot 入口脚本（发布后在项目根目录）：

```bash
php chariot trade.order#sync@dev
```

### 2. 发布配置

```bash
php artisan vendor:publish --provider="Luminee\\Chariot\\ChariotServiceProvider"
```

### 3. 配置

```php
// config/chariot.php
return [
    'scripts_dir' => base_path('database/scripts'),

    'signature' => [
        'directory_separator' => '#',   // 目录前缀与签名之间的分隔符
        'connection_separator' => '@',  // 签名与连接名之间的分隔符
    ],
];
```

## 使用

### 创建脚本

```bash
php artisan chariot:make:script sync:orders --project=trade --module=order
```

生成 `database/scripts/trade/order/2024_01_01_000000_sync_orders.php`：

```php
use Luminee\Chariot\Console\Command;

return new class extends Command {
    protected $signature = 'sync:orders';

    public function handle()
    {
        // 脚本逻辑
    }
};
```

### 签名规则

脚本文件所在的目录路径自动映射为签名前缀：

```
database/scripts/trade/order/  →  trade.order#
```

最终签名 = `trade.order#` + 原始签名 `sync:orders` = `trade.order#sync:orders`。

### 指定连接

```bash
php artisan trade.order#sync:orders@dev
```

- `#` 分隔目录前缀与命令签名
- `@` 分隔签名与数据库连接名
- 安装了 `luminee/switcher` 时，使用其切换连接（支持 `extra_connections`）
- 未安装时，直接使用 Laravel 原生的 `DB::setDefaultConnection()`

### `--run` 选项

所有 Chariot 脚本自动获得 `--run` / `-r` 选项，用于区分预览和执行模式。

## 依赖

| 包 | 关系 | 说明 |
|---|---|---|
| `luminee/switcher` | 可选 | 安装后支持 `extra_connections` |

## License

MIT