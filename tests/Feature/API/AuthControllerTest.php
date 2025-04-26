<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
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
        parent ::setUp();

        // 运行数据库填充
        $this -> artisan('db:seed');
        // 禁用控制台交互
        $this -> withoutMockingConsoleOutput();

        // 设置Passport配置为内存模式
        config(['passport.storage.database.connection' => null]);

        // 创建OAuth客户端
        $this -> artisan('passport:install', ['--no-interaction' => true, '--force' => true]);
    }

    /**
     * 测试用户注册
     */
    public function test_user_can_register()
    {
        $userData = [
            'name'                  => $this -> faker -> name,
            'email'                 => $this -> faker -> unique() -> safeEmail,
            'password'              => 'admin123',
            'password_confirmation' => 'admin123',
        ];

        $response = $this -> postJson('/api/register', $userData);

        $response -> assertStatus(200)
            -> assertJsonStructure([
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
        // 使用唯一邮箱，避免与之前测试的邮箱冲突
        $email = 'login_test_' . time() . '@163.com';

        $user = User ::factory() -> create([
            'email'    => $email,
            'password' => bcrypt('admin123'),
        ]);

        $loginData = [
            'email'    => $email,
            'password' => 'admin123',
        ];

        $response = $this -> postJson('/api/login', $loginData);

        $response -> assertStatus(200)
            -> assertJsonStructure([
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
        $user = User ::factory() -> create([
            'password' => Hash ::make('admin123')
        ]);
        // 使用API令牌
        $token = $user -> createToken('test-token') -> accessToken;
        Passport ::actingAs($user);

        $response = $this -> withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]) -> postJson('/api/logout');

        $response -> assertStatus(200)
            -> assertJson([
                'status'  => true,
                'message' => '成功登出'
            ]);
    }

    /**
     * 测试获取用户信息
     */
    public function test_can_get_user_info()
    {
        $user = User ::factory() -> create([
            'password' => Hash ::make('password123')
        ]);
        Passport ::actingAs($user);
        // 使用API令牌
        $token    = $user -> createToken('test-token') -> accessToken;
        $response = $this -> withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]) -> getJson('/api/user');

        $response -> assertStatus(200)
            -> assertJsonPath('user.id', $user -> id)
            -> assertJsonPath('user.email', $user -> email);
    }
}
