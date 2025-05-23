# Laravel 客户管理系统

这是一个使用Laravel框架开发的客户管理系统，提供RESTful API和Web界面。系统支持客户信息的增删改查操作，并提供完善的API文档和认证机制。

## 功能特点

- 客户信息的CRUD操作
- RESTful API接口
- 用户认证与授权
- 多因素认证(MFA)
- 自动生成API文档

## 环境要求

- PHP >= 8.1
- Composer
- MySQL5.7
- Node.js和NPM

## 环境搭建

### 1. 克隆代码库

```bash
git clone https://github.com/dinuo966/client-management.git
cd client-management
```

### 2. 安装依赖

```bash
composer install
npm install
npm run build
```

### 3. 配置环境变量（项目根目录执行）

```bash
cp .env.example .env
php artisan key:generate
```

## 邮箱配置说明

### 配置位于根目录的.env文件（目前我已配置可直接使用）

``` bash
MAIL_MAILER=smtp                    # 邮件发送驱动
MAIL_HOST=smtp.qiye.aliyun.com      # SMTP服务器地址
MAIL_PORT=465                       # SMTP端口
MAIL_USERNAME=email@weilai.red      # SMTP用户名
MAIL_PASSWORD=zq533124!             # SMTP密码
MAIL_ENCRYPTION=ssl                 # 加密方式，可选: tls, ssl
MAIL_FROM_ADDRESS=email@weilai.red  # 默认发件人地址
MAIL_FROM_NAME="${APP_NAME}"        # 默认发件人名称，使用应用名称
```

## 数据库说明

### 配置位于根目录的.env文件（当前使用Mysql5.7版本）
#### 已在配置中使用了云数据库，拉取代码可直接使用，当然我也会将数据库文件保存到根目录中:laravel.sql
``` bash
DB_CONNECTION=mysql           # 数据库连接类型
DB_HOST=127.0.0.1             # 数据库主机地址
DB_PORT=3306                  # 数据库端口
DB_DATABASE=laravel           # 数据库名称
DB_USERNAME=root              # 数据库用户名
DB_PASSWORD=root              # 数据库密码
```

### 4. 运行数据库迁移

```bash
php artisan migrate
php artisan db:seed
```

### 5. 配置API认证

```bash
php artisan passport:install
```

## 运行应用

### 启动服务

#### 前端UI运行

```bash
php npm run dev
```

#### 前端UI打包

```bash
php npm run build
```

#### web服务器

```bash
php artisan serve
```

假设已打包，那么运行web服务器时可直接访问（目前已运行：php npm run build）

应用将在 http://localhost:8000 运行。

### 访问API文档

API文档可以通过以下URL访问：

```
http://localhost:8000/api/documentation
```

### API认证

API使用Bearer Token认证机制。要获取访问令牌，请发送POST请求到：

```
POST /api/login
```

请求参数：

```json
{
    "email": "user@example.com",
    "password": "password"
}
```

## 主要API接口

### 客户管理

- `GET /api/customers` - 获取所有客户
- `POST /api/customers` - 创建新客户
- `GET /api/customers/{id}` - 获取特定客户
- `PUT /api/customers/{id}` - 更新客户信息
- `DELETE /api/customers/{id}` - 删除客户

## 运行测试

### 功能测试
#### 特别注意：执行测试时假设需要更改配置，那么需要将根目录的“phpunit.xml”中的APP_KEY配置与“.env.testing”中的APP_KEY一致。
```bash
php artisan test
```

### API测试

```bash
php artisan test --testsuite=Feature
```

## 自动生成API文档

本项目使用L5-Swagger自动生成API文档。要重新生成文档，请运行：

```bash
php artisan l5-swagger:generate
```

文档将生成在`/api/documentation`路径下。

## 常见问题排查

### API文档无法访问

确保已经执行了以下操作：

1. 安装了`darkaonline/l5-swagger`包
2. 已运行`php artisan l5-swagger:generate`命令
3. 检查存储目录权限是否正确

### 认证失败

如果遇到API认证问题，请检查：

1. Passport是否正确安装
2. 验证请求是否包含有效的Authorization头

## 作者

面试者：李立科
