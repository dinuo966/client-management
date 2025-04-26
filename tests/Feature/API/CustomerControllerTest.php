<?php

namespace Tests\Feature\API;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
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
        parent ::setUp();
        // 创建用户
        $this -> user = User ::factory() -> create([
            'password' => Hash ::make('admin123') // 确保明确地哈希密码
        ]);

        // 禁用控制台交互
        $this -> withoutMockingConsoleOutput();

        // 设置Passport配置为内存模式
        config(['passport.storage.database.connection' => null]);

        // 运行数据库填充
        $this -> artisan('db:seed');

        // 创建OAuth客户端
        $this -> artisan('passport:install', ['--no-interaction' => true, '--force' => true]);
    }

    /**
     * 测试获取客户列表
     */
    public function test_can_get_all_customers()
    {
        Passport ::actingAs($this -> user);

        $response = $this -> getJson('/api/customers');

        $response -> assertStatus(200)
            -> assertJsonStructure([
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
        Passport ::actingAs($this -> user);

        $customerData = [
            'first_name' => $this -> faker -> firstName,
            'last_name'  => $this -> faker -> lastName,
            'age'        => $this -> faker -> numberBetween(18, 80),
            'dob'        => $this -> faker -> date(),
            'email'      => $this -> faker -> unique() -> safeEmail,
        ];

        $response = $this -> postJson('/api/customers', $customerData);

        $response -> assertStatus(201);
        // 单独断言关键字段，避免日期格式问题
        $jsonData = $response -> json('data');
        $this -> assertEquals($customerData['first_name'], $jsonData['first_name']);
        $this -> assertEquals($customerData['last_name'], $jsonData['last_name']);
        $this -> assertEquals($customerData['email'], $jsonData['email']);
    }

    /**
     * 测试获取单个客户
     */
    public function test_can_get_single_customer()
    {
        Passport ::actingAs($this -> user);

        $customer = Customer ::factory() -> create();

        $response = $this -> getJson('/api/customers/' . $customer -> id);

        $response -> assertStatus(200)
            -> assertJson([
                'data' => [
                    'id'         => $customer -> id,
                    'first_name' => $customer -> first_name,
                    'last_name'  => $customer -> last_name,
                    'email'      => $customer -> email,
                ]
            ]);
    }

    /**
     * 测试更新客户
     */
    public function test_can_update_customer()
    {
        Passport ::actingAs($this -> user);

        $customer = Customer ::factory() -> create();

        $updatedData = [
            'first_name' => 'Updated',
            'last_name'  => 'Name',
            'age'        => 45,
            'dob'        => '2025-04-26',
            'email'      => 'updated@163.com',
        ];

        $response = $this -> putJson('/api/customers/' . $customer -> id, $updatedData);

        $response -> assertStatus(200);
        // 单独断言关键字段，避免日期格式问题
        $jsonData = $response -> json('data');
        $this -> assertEquals($updatedData['first_name'], $jsonData['first_name']);
        $this -> assertEquals($updatedData['last_name'], $jsonData['last_name']);
        $this -> assertEquals($updatedData['email'], $jsonData['email']);
        $this -> assertEquals($updatedData['age'], $jsonData['age']);
        // 日期格式可能不同，但应该包含相同的日期部分
        $this -> assertStringContainsString($updatedData['dob'], $jsonData['dob']);
    }

    /**
     * 测试删除客户
     */
    public function test_can_delete_customer()
    {
        Passport ::actingAs($this -> user);

        $customer = Customer ::factory() -> create();

        $response = $this -> deleteJson('/api/customers/' . $customer -> id);

        $response -> assertStatus(200)
            -> assertJson([
                'status'  => true,
                'message' => '客户删除成功'
            ]);

        $this -> assertDatabaseMissing('customers', [
            'id' => $customer -> id
        ]);
    }

    /**
     * 测试未授权访问
     */
    public function test_unauthenticated_access()
    {
        $response = $this -> getJson('/api/customers');

        $response -> assertStatus(401);
    }
}
