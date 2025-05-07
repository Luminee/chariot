# Chariot - Laravel 脚本管理工具

Chariot 是一个专为 Laravel 框架设计的脚本管理扩展包，提供以下核心功能：

## 主要特性

- 🚀 自动化脚本命令生成
- 🔗 多数据库连接管理
- 📂 结构化脚本目录管理
- 🛠️ 命令行工具集成

## 安装指南

1. 通过 Composer 安装包：
```bash
composer require luminee/chariot
```

2. 发布配置文件：
```bash
php artisan vendor:publish --provider="Luminee\Chariot\ChariotServiceProvider"
```

## 配置说明

编辑 `config/chariot.php` 文件：

```php
return [
    'scripts_dir' => base_path('database/scripts'), // 脚本存储目录
    'signature' => [
        'directory_separator' => '#', // 目录分隔符
        'connection_separator' => '@' // 连接分隔符
    ],
    'extra_connections' => [] // 额外数据库连接配置
];
```

## 使用示例

### 创建新脚本

使用命令行工具生成脚本模板：
```bash
php artisan chariot:make:script init:user \
    --project=Project \
    --module=User
```

### 脚本文件结构

生成的脚本文件示例：
```php
<?php

use Luminee\Chariot\Console\Command;

return new class extends Command {
    protected $signature = 'init:user';

    public function handle() {
        // 脚本逻辑实现
    }
};
```

### 运行脚本

执行特定脚本：
```bash
php artisan project.user#init:user@dev
```

## 高级功能

- 支持多层级模块目录结构
- 可配置的签名分隔符
- 动态数据库连接管理

## 贡献指南

欢迎提交 Pull Request 或报告 Issues。