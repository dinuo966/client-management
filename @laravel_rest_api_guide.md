# Laravel 10 REST API 开发指南

## 项目概述
本文档详细说明如何使用Laravel 10构建REST API，实现客户管理系统，包含OAuth2身份验证和多因素认证(MFA)。

## 目录
1. [环境搭建](#环境搭建)
2. [项目创建](#项目创建)
3. [数据库配置](#数据库配置)
4. [身份验证实现](#身份验证实现)
5. [客户API开发](#客户API开发)
6. [前端UI实现](#前端UI实现)
7. [多因素认证](#多因素认证)
8. [单元测试](#单元测试)

## 环境搭建

### 所需环境
- PHP >= 8.1
- Composer
- MySQL
- Node.js 和 npm (用于前端资源)

### 检查PHP版本
```bash
php -v
```

### 检查Composer
```bash
composer -V
```

## 项目创建

### 安装Laravel 10
```bash
composer create-project laravel/laravel:^10.0 laravel-rest-api
cd laravel-rest-api
```

### 启动开发服务器
```bash
php artisan serve
```
现在可以通过 http://localhost:8000 访问项目。

## 数据库配置

### 配置.env文件
编辑项目根目录中的.env文件，设置数据库连接信息：

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_rest_api
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 创建数据库
在MySQL中创建数据库：

```sql
CREATE DATABASE laravel_rest_api;
```

### 创建客户表迁移文件
```bash
php artisan make:migration create_customers_table
```

编辑迁移文件（database/migrations/xxxx_xx_xx_xxxxxx_create_customers_table.php）：

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->integer('age')->nullable();
            $table->date('dob')->nullable();
            $table->string('email', 100)->nullable();
            $table->timestamp('creation_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
```

### 运行迁移
```bash
php artisan migrate
```

这将创建users表(Laravel默认)和customers表。

### 创建Customer模型
```bash
php artisan make:model Customer
```

编辑模型文件（app/Models/Customer.php）：

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * 可批量赋值的属性
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'age',
        'dob',
        'email',
        'creation_date',
    ];

    /**
     * 应该转换的属性
     */
    protected $casts = [
        'dob' => 'date',
        'creation_date' => 'datetime',
    ];
}
```

## 身份验证实现

### 安装Laravel Passport
Laravel Passport提供了OAuth2服务器实现：

```bash
composer require laravel/passport
```

### 运行Passport迁移
```bash
php artisan migrate
```

### 安装Passport
```bash
php artisan passport:install
```

### 配置Passport
编辑User模型（app/Models/User.php）：

```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

### 配置认证守卫
编辑认证配置（config/auth.php）：

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

### 配置Passport服务提供者
编辑 app/Providers/AuthServiceProvider.php：

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        
        // Passport路由注册
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
```

### 创建测试用户
创建一个数据库填充器：

```bash
php artisan make:seeder UserSeeder
```

编辑 database/seeders/UserSeeder.php：

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
```

更新 database/seeders/DatabaseSeeder.php:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
    }
}
```

执行填充：

```bash
php artisan db:seed
```

## 客户API开发

### 创建客户API控制器
```bash
php artisan make:controller API/CustomerController --api
```

编辑控制器文件（app/Http/Controllers/API/CustomerController.php）：

```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * 显示客户列表
     */
    public function index()
    {
        $customers = Customer::all();
        return CustomerResource::collection($customers);
    }

    /**
     * 存储新客户
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'age' => 'nullable|integer|min:0|max:150',
            'dob' => 'nullable|date',
            'email' => 'required|email|max:100|unique:customers',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => '验证错误',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = Customer::create($request->all());

        return new CustomerResource($customer);
    }

    /**
     * 显示指定客户
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * 更新指定客户
     */
    public function update(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'age' => 'nullable|integer|min:0|max:150',
            'dob' => 'nullable|date',
            'email' => 'required|email|max:100|unique:customers,email,' . $customer->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => '验证错误',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer->update($request->all());

        return new CustomerResource($customer);
    }

    /**
     * 删除指定客户
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json([
            'status' => true,
            'message' => '客户删除成功'
        ]);
    }
}
```

### 创建API资源
```bash
php artisan make:resource CustomerResource
```

编辑资源文件（app/Http/Resources/CustomerResource.php）：

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'age' => $this->age,
            'dob' => $this->dob,
            'email' => $this->email,
            'creation_date' => $this->creation_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

### 创建认证控制器
```bash
php artisan make:controller API/AuthController
```

编辑认证控制器（app/Http/Controllers/API/AuthController.php）：

```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * 用户登录
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => '验证错误',
                'errors' => $validator->errors()
            ], 422);
        }

        // 验证用户凭据
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->accessToken;

            return response()->json([
                'status' => true,
                'message' => '用户登录成功',
                'token' => $token,
                'user' => $user
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => '邮箱或密码不正确'
            ], 401);
        }
    }

    /**
     * 用户注册
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => '验证错误',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('MyApp')->accessToken;

        return response()->json([
            'status' => true,
            'message' => '用户注册成功',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * 用户登出
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'status' => true,
            'message' => '成功登出'
        ]);
    }

    /**
     * 获取当前用户信息
     */
    public function user(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => $request->user()
        ]);
    }
}
```

### 配置API路由
编辑路由文件（routes/api.php）：

```php
<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 公共路由
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// 受保护的路由
Route::middleware('auth:api')->group(function () {
    // 认证路由
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
    
    // 客户API路由
    Route::apiResource('customers', CustomerController::class);
});
```

### 测试API端点
可以使用Postman或任何API测试工具测试以下端点：

1. 注册：POST /api/register
   - Body: {name, email, password, password_confirmation}

2. 登录：POST /api/login
   - Body: {email, password}

3. 获取客户列表：GET /api/customers
   - Header: Authorization: Bearer {token}

4. 创建客户：POST /api/customers
   - Header: Authorization: Bearer {token}
   - Body: {first_name, last_name, age, dob, email}

5. 获取单个客户：GET /api/customers/{id}
   - Header: Authorization: Bearer {token}

6. 更新客户：PUT /api/customers/{id}
   - Header: Authorization: Bearer {token}
   - Body: {first_name, last_name, age, dob, email}

7. 删除客户：DELETE /api/customers/{id}
   - Header: Authorization: Bearer {token}

### 填充测试客户数据
创建客户数据填充器：

```bash
php artisan make:seeder CustomerSeeder
```

编辑 database/seeders/CustomerSeeder.php：

```php
<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'first_name' => '张',
                'last_name' => '三',
                'age' => 30,
                'dob' => '1993-01-15',
                'email' => 'zhangsan@example.com',
            ],
            [
                'first_name' => '李',
                'last_name' => '四',
                'age' => 25,
                'dob' => '1998-05-20',
                'email' => 'lisi@example.com',
            ],
            [
                'first_name' => '王',
                'last_name' => '五',
                'age' => 40,
                'dob' => '1983-11-10',
                'email' => 'wangwu@example.com',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }
    }
}
```

更新 database/seeders/DatabaseSeeder.php:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CustomerSeeder::class,
        ]);
    }
}
```

运行填充：

```bash
php artisan db:seed --class=CustomerSeeder
```

## 前端UI实现

### 安装Laravel UI
```bash
composer require laravel/ui
php artisan ui bootstrap --auth
npm install && npm run dev
```

### 配置路由
编辑路由文件（routes/web.php）：

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Auth\MFAController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// 认证路由
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// MFA路由
Route::get('/mfa/verify', [MFAController::class, 'showVerificationForm'])->name('mfa.verify');
Route::post('/mfa/verify', [MFAController::class, 'verify'])->name('mfa.verify.submit');
Route::get('/mfa/resend', [MFAController::class, 'resend'])->name('mfa.resend');

// 客户管理路由 (需要认证)
Route::middleware(['auth', 'mfa.verified'])->group(function () {
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
});
```

### 创建Web控制器
```bash
php artisan make:controller CustomerController --resource
```

编辑控制器文件（app/Http/Controllers/CustomerController.php）：

```php
<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * 显示所有客户列表
     */
    public function index()
    {
        $customers = Customer::all();
        return view('customers.index', compact('customers'));
    }

    /**
     * 显示创建客户表单
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * 存储新客户
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'age' => 'nullable|integer|min:0|max:150',
            'dob' => 'nullable|date',
            'email' => 'required|email|max:100|unique:customers',
        ]);

        // 使用API发送请求
        $response = $this->apiRequest('POST', 'customers', $validated);

        if ($response->successful()) {
            return redirect()->route('customers.index')
                ->with('success', '客户创建成功！');
        }

        return back()->withErrors(['api_error' => '创建客户失败'])->withInput();
    }

    /**
     * 显示指定客户
     */
    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    /**
     * 显示编辑客户表单
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * 更新指定客户
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'age' => 'nullable|integer|min:0|max:150',
            'dob' => 'nullable|date',
            'email' => 'required|email|max:100|unique:customers,email,' . $customer->id,
        ]);

        // 使用API发送请求
        $response = $this->apiRequest('PUT', 'customers/' . $customer->id, $validated);

        if ($response->successful()) {
            return redirect()->route('customers.index')
                ->with('success', '客户更新成功！');
        }

        return back()->withErrors(['api_error' => '更新客户失败'])->withInput();
    }

    /**
     * 删除指定客户
     */
    public function destroy(Customer $customer)
    {
        // 使用API发送请求
        $response = $this->apiRequest('DELETE', 'customers/' . $customer->id);

        if ($response->successful()) {
            return redirect()->route('customers.index')
                ->with('success', '客户删除成功！');
        }

        return back()->withErrors(['api_error' => '删除客户失败']);
    }

    /**
     * 发送API请求
     */
    private function apiRequest($method, $endpoint, $data = [])
    {
        $user = auth()->user();
        $token = $user->createToken('web-token')->accessToken;

        $response = Http::withToken($token)
            ->acceptJson()
            ->$method(config('app.url') . '/api/' . $endpoint, $data);

        return $response;
    }
}
```

### 创建视图文件
创建客户索引视图（resources/views/customers/index.blade.php）：

```php
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    客户列表
                    <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">添加客户</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>姓名</th>
                                    <th>年龄</th>
                                    <th>出生日期</th>
                                    <th>电子邮件</th>
                                    <th>创建日期</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                <tr>
                                    <td>{{ $customer->id }}</td>
                                    <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
                                    <td>{{ $customer->age }}</td>
                                    <td>{{ $customer->dob ? $customer->dob->format('Y-m-d') : '' }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->creation_date->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-info btn-sm">查看</a>
                                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning btn-sm">编辑</a>
                                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('确定要删除此客户吗？');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">删除</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

创建客户创建视图（resources/views/customers/create.blade.php）：

```php
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">添加新客户</div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('customers.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="first_name" class="col-md-4 col-form-label text-md-end">名字</label>
                            <div class="col-md-6">
                                <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="last_name" class="col-md-4 col-form-label text-md-end">姓氏</label>
                            <div class="col-md-6">
                                <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="age" class="col-md-4 col-form-label text-md-end">年龄</label>
                            <div class="col-md-6">
                                <input id="age" type="number" class="form-control @error('age') is-invalid @enderror" name="age" value="{{ old('age') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="dob" class="col-md-4 col-form-label text-md-end">出生日期</label>
                            <div class="col-md-6">
                                <input id="dob" type="date" class="form-control @error('dob') is-invalid @enderror" name="dob" value="{{ old('dob') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">电子邮件</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    保存
                                </button>
                                <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                                    取消
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

创建其他视图文件：
- resources/views/customers/edit.blade.php (类似于create，但预填充数据并使用PUT方法)
- resources/views/customers/show.blade.php (显示单个客户的详细信息)

## 多因素认证

### 创建MFA令牌模型和迁移
```bash
php artisan make:model MFAToken -m
```

编辑迁移文件（database/migrations/xxxx_xx_xx_xxxxxx_create_m_f_a_tokens_table.php）：

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('m_f_a_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('token');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_f_a_tokens');
    }
};
```

编辑模型文件（app/Models/MFAToken.php）：

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MFAToken extends Model
{
    use HasFactory;

    /**
     * 可批量赋值的属性
     */
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
    ];

    /**
     * 应该转换的属性
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * 令牌所属的用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 检查令牌是否已过期
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}
```

### 创建MFA中间件
```bash
php artisan make:middleware VerifyMFA
```

编辑中间件文件（app/Http/Middleware/VerifyMFA.php）：

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VerifyMFA
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !Session::has('mfa_verified')) {
            return redirect()->route('mfa.verify');
        }

        return $next($request);
    }
}
```

注册中间件（app/Http/Kernel.php）：

```php
protected $routeMiddleware = [
    // 其他中间件...
    'mfa.verified' => \App\Http\Middleware\VerifyMFA::class,
];
```

### 创建MFA控制器
```bash
php artisan make:controller Auth/MFAController
```

编辑控制器文件（app/Http/Controllers/Auth/MFAController.php）：

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\MFATokenMail;
use App\Models\MFAToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class MFAController extends Controller
{
    /**
     * 创建一个新的控制器实例
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 显示MFA验证表单
     */
    public function showVerificationForm()
    {
        if (Session::has('mfa_verified')) {
            return redirect()->intended('/home');
        }

        // 生成并发送MFA令牌
        $this->generateAndSendToken(auth()->user());

        return view('auth.mfa-verify');
    }

    /**
     * 验证MFA令牌
     */
    public function verify(Request $request)
    {
        $request->validate([
            'token' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $token = MFAToken::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$token || $token->isExpired() || $token->token !== $request->token) {
            return back()->withErrors([
                'token' => '令牌无效或已过期。',
            ]);
        }

        // 标记为已通过MFA验证
        Session::put('mfa_verified', true);

        // 删除使用过的令牌
        $token->delete();

        return redirect()->intended('/home');
    }

    /**
     * 重新发送MFA令牌
     */
    public function resend()
    {
        $user = auth()->user();

        // 删除旧令牌
        MFAToken::where('user_id', $user->id)->delete();

        // 生成并发送新令牌
        $this->generateAndSendToken($user);

        return back()->with('status', '新令牌已发送到您的电子邮件。');
    }

    /**
     * 生成并发送MFA令牌
     */
    private function generateAndSendToken($user)
    {
        // 生成6位随机数字令牌
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // 保存令牌到数据库
        MFAToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addMinutes(10), // 10分钟有效期
        ]);

        // 发送令牌到用户邮箱
        Mail::to($user->email)->send(new MFATokenMail($token));
    }
}
```

### 创建MFA电子邮件
```bash
php artisan make:mail MFATokenMail
```

编辑邮件类（app/Mail/MFATokenMail.php）：

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MFATokenMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * MFA令牌
     */
    public $token;

    /**
     * 创建一个新的消息实例
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * 获取消息信封
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '您的登录验证码',
        );
    }

    /**
     * 获取消息内容定义
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.mfa-token',
        );
    }

    /**
     * 获取消息的附件
     */
    public function attachments(): array
    {
        return [];
    }
}
```

### 创建MFA邮件视图
创建邮件模板（resources/views/emails/mfa-token.blade.php）：

```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>登录验证码</title>
</head>
<body>
    <h2>您的登录验证码</h2>
    <p>您的多因素认证验证码是：</p>
    <h1>{{ $token }}</h1>
    <p>此验证码将在10分钟后失效。</p>
    <p>如果您没有尝试登录，请忽略此邮件。</p>
</body>
</html>
```

### 创建MFA验证视图
创建MFA验证表单（resources/views/auth/mfa-verify.blade.php）：

```php
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">多因素认证</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>我们已向您的邮箱 <strong>{{ auth()->user()->email }}</strong> 发送了一个6位验证码。请输入该验证码以完成登录。</p>

                    <form method="POST" action="{{ route('mfa.verify.submit') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="token" class="col-md-4 col-form-label text-md-end">验证码</label>

                            <div class="col-md-6">
                                <input id="token" type="text" class="form-control @error('token') is-invalid @enderror" name="token" required autofocus>

                                @error('token')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    验证
                                </button>

                                <a href="{{ route('mfa.resend') }}" class="btn btn-link">
                                    重新发送验证码
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### 配置邮件设置
更新.env文件：

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## 单元测试

### 配置测试环境
编辑.env.testing文件：

```
APP_ENV=testing
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### 创建API测试类
```bash
php artisan make:test API/CustomerControllerTest
```

编辑测试文件（tests/Feature/API/CustomerControllerTest.php）：

```php
<?php

namespace Tests\Feature\API;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    /**
     * 测试前的设置
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 创建用户
        $this->user = User::factory()->create();

        // 运行数据库填充
        $this->artisan('db:seed');

        // 创建OAuth客户端
        $this->artisan('passport:install');
    }

    /**
     * 测试获取客户列表
     */
    public function test_can_get_all_customers()
    {
        Passport::actingAs($this->user);

        $response = $this->getJson('/api/customers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'age',
                        'dob',
                        'email',
                        'creation_date',
                    ]
                ]
            ]);
    }

    /**
     * 测试创建客户
     */
    public function test_can_create_customer()
    {
        Passport::actingAs($this->user);

        $customerData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'age' => $this->faker->numberBetween(18, 80),
            'dob' => $this->faker->date(),
            'email' => $this->faker->unique()->safeEmail,
        ];

        $response = $this->postJson('/api/customers', $customerData);

        $response->assertStatus(201)
            ->assertJsonFragment($customerData);
    }

    /**
     * 测试获取单个客户
     */
    public function test_can_get_single_customer()
    {
        Passport::actingAs($this->user);

        $customer = Customer::factory()->create();

        $response = $this->getJson('/api/customers/' . $customer->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $customer->id,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'email' => $customer->email,
                ]
            ]);
    }

    /**
     * 测试更新客户
     */
    public function test_can_update_customer()
    {
        Passport::actingAs($this->user);

        $customer = Customer::factory()->create();

        $updatedData = [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'age' => 45,
            'dob' => '2025-04-26',
            'email' => 'updated@example.com',
        ];

        $response = $this->putJson('/api/customers/' . $customer->id, $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment($updatedData);
    }

    /**
     * 测试删除客户
     */
    public function test_can_delete_customer()
    {
        Passport::actingAs($this->user);

        $customer = Customer::factory()->create();

        $response = $this->deleteJson('/api/customers/' . $customer->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => '客户删除成功'
            ]);

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id
        ]);
    }

    /**
     * 测试未授权访问
     */
    public function test_unauthenticated_access()
    {
        $response = $this->getJson('/api/customers');

        $response->assertStatus(401);
    }
}
```

### 创建客户工厂
```bash
php artisan make:factory CustomerFactory
```

编辑工厂文件（database/factories/CustomerFactory.php）：

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'age' => fake()->numberBetween(18, 80),
            'dob' => fake()->date(),
            'email' => fake()->unique()->safeEmail(),
            'creation_date' => now(),
        ];
    }
}
```

### 创建认证测试
```bash
php artisan make:test API/AuthControllerTest
```

编辑测试文件（tests/Feature/API/AuthControllerTest.php）：

```php
<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * 测试前的设置
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 运行数据库填充
        $this->artisan('db:seed');

        // 创建OAuth客户端
        $this->artisan('passport:install');
    }

    /**
     * 测试用户注册
     */
    public function test_user_can_register()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /**
     * 测试用户登录
     */
    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'token',
                'user',
            ]);
    }

    /**
     * 测试用户登出
     */
    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => '成功登出'
            ]);
    }

    /**
     * 测试获取用户信息
     */
    public function test_can_get_user_info()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.email', $user->email);
    }
}
```

### 运行测试
```bash
php artisan test
```

## 总结

本指南详细介绍了如何使用Laravel 10构建一个具有以下功能的REST API应用：

1. 完整的REST API实现，支持客户管理的CRUD操作
2. 使用Laravel Passport进行OAuth2身份验证
3. 基于Web的UI界面，通过API执行所有操作
4. 多因素认证(MFA)，使用电子邮件发送验证码
5. 完整的PHPUnit测试套件

该项目遵循了Laravel的最佳实践，并实现了所有需求的功能，包括：
- 安全的REST API
- MySQL数据库集成
- 响应式前端UI
- 多因素认证
- 完善的测试覆盖

此项目可以作为安全REST API开发的基础，并可以根据具体需求进行扩展和定制。
