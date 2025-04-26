<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\CustomerController as APICustomerController;

/**
 * @OA\Tag(
 *     name="客户管理-Web",
 *     description="客户管理Web界面相关操作"
 * )
 */
class CustomerController extends Controller
{
    /**
     * 显示所有客户列表
     * 
     * @OA\Get(
     *     path="/customers",
     *     tags={"客户管理-Web"},
     *     summary="显示所有客户列表",
     *     @OA\Response(response=200, description="显示客户列表页面")
     * )
     */
    public function index()
    {
        $customers = Customer ::all();
        return view('customers.index', compact('customers'));
    }

    /**
     * 显示创建客户表单
     * 
     * @OA\Get(
     *     path="/customers/create",
     *     tags={"客户管理-Web"},
     *     summary="显示创建客户表单",
     *     @OA\Response(response=200, description="显示创建客户表单")
     * )
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * 创建客户
     * 
     * @OA\Post(
     *     path="/customers",
     *     tags={"客户管理-Web"},
     *     summary="创建新客户",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "email"},
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="age", type="integer"),
     *             @OA\Property(property="dob", type="string", format="date"),
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(response=302, description="创建成功重定向")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request -> validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'age'        => 'nullable|integer|min:0|max:150',
            'dob'        => 'nullable|date',
            'email'      => 'required|email|max:100|unique:customers',
        ]);

        try {
            // 检查是否已存在相同数据记录的会话令牌
            $formToken = $request -> input('_token');
            $tokenKey  = 'customer_form_' . $formToken;

            if (session() -> has($tokenKey)) {
                // 如果存在令牌，说明是重复提交
                return redirect() -> route('customers.index')
                    -> with('warning', '表单已经提交过，请勿重复提交');
            }

            // 标记此表单已提交，存储在会话中（有效期5分钟）
            session() -> put($tokenKey, true);
            session() -> save();

            // 使用API发送请求或直接创建
            if (config('app.use_api', false)) {
                // API方式
                $apiController = new APICustomerController();
                $apiRequest    = new Request();
                $apiRequest -> replace($validated);
                $apiResponse = $apiController -> store($apiRequest);

                if ($apiResponse -> getStatusCode() >= 200 && $apiResponse -> getStatusCode() < 300) {
                    return redirect() -> route('customers.index')
                        -> with('success', '客户创建成功！');
                }

                $errorData    = json_decode($apiResponse -> getContent(), true);
                $errorMessage = $errorData['message'] ?? '创建客户失败';

                return back() -> withErrors(['api_error' => $errorMessage]) -> withInput();
            } else {
                // 直接数据库操作
                $customer = Customer ::create($validated);

                Log ::info('客户创建成功', [
                    '客户ID' => $customer -> id,
                    '客户名' => $customer -> first_name . ' ' . $customer -> last_name
                ]);

                return redirect() -> route('customers.index')
                    -> with('success', '客户创建成功！');
            }
        } catch (\Exception $e) {
            Log ::error('客户创建失败', ['错误' => $e -> getMessage()]);
            return back() -> withErrors(['api_error' => '创建客户失败: ' . $e -> getMessage()]) -> withInput();
        }
    }

    /**
     * 显示指定客户
     * 
     * @OA\Get(
     *     path="/customers/{id}",
     *     tags={"客户管理-Web"},
     *     summary="显示指定客户详情",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="显示客户详情页面"),
     *     @OA\Response(response=404, description="客户不存在")
     * )
     */
    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    /**
     * 显示编辑客户表单
     * 
     * @OA\Get(
     *     path="/customers/{id}/edit",
     *     tags={"客户管理-Web"},
     *     summary="显示编辑客户表单",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="显示编辑客户表单"),
     *     @OA\Response(response=404, description="客户不存在")
     * )
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * 更新指定客户
     * 
     * @OA\Put(
     *     path="/customers/{id}",
     *     tags={"客户管理-Web"},
     *     summary="更新客户信息",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="age", type="integer"),
     *             @OA\Property(property="dob", type="string", format="date"),
     *             @OA\Property(property="email", type="string", format="email")
     *         )
     *     ),
     *     @OA\Response(response=302, description="更新成功重定向"),
     *     @OA\Response(response=404, description="客户不存在")
     * )
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request -> validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'age'        => 'nullable|integer|min:0|max:150',
            'dob'        => 'nullable|date',
            'email'      => 'required|email|max:100|unique:customers,email,' . $customer -> id,
        ]);

        try {
            // 直接使用API控制器
            $apiController = new APICustomerController(); // 使用正确的类名

            // 创建请求实例
            $apiRequest = new Request();
            $apiRequest -> replace($validated);

            $apiResponse = $apiController -> update($apiRequest, $customer);

            if ($apiResponse -> getStatusCode() >= 200 && $apiResponse -> getStatusCode() < 300) {
                return redirect() -> route('customers.index')
                    -> with('success', '客户更新成功！');
            }

            $errorData    = json_decode($apiResponse -> getContent(), true);
            $errorMessage = $errorData['message'] ?? '更新客户失败';

            return back() -> withErrors(['api_error' => $errorMessage]) -> withInput();

        } catch (\Exception $e) {
            Log ::error('调用API控制器失败', ['错误' => $e -> getMessage()]);

            // 异常时直接更新客户
            try {
                $customer -> update($validated);
                return redirect() -> route('customers.index')
                    -> with('success', '客户更新成功！');
            } catch (\Exception $dbEx) {
                return back() -> withErrors(['api_error' => '更新客户失败: ' . $dbEx -> getMessage()]) -> withInput();
            }
        }
    }

    /**
     * 删除指定客户
     * 
     * @OA\Delete(
     *     path="/customers/{id}",
     *     tags={"客户管理-Web"},
     *     summary="删除客户",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=302, description="删除成功重定向"),
     *     @OA\Response(response=404, description="客户不存在")
     * )
     */
    public function destroy(Customer $customer)
    {
        try {
            // 直接使用API控制器
            $apiController = new APICustomerController(); // 使用正确的类名
            $apiResponse   = $apiController -> destroy($customer);

            if ($apiResponse -> getStatusCode() >= 200 && $apiResponse -> getStatusCode() < 300) {
                return redirect() -> route('customers.index')
                    -> with('success', '客户删除成功！');
            }

            $errorData    = json_decode($apiResponse -> getContent(), true);
            $errorMessage = $errorData['message'] ?? '删除客户失败';

            return back() -> withErrors(['api_error' => $errorMessage]);

        } catch (\Exception $e) {
            Log ::error('调用API控制器失败', ['错误' => $e -> getMessage()]);

            // 异常时直接删除客户
            try {
                $customer -> delete();
                return redirect() -> route('customers.index')
                    -> with('success', '客户删除成功！');
            } catch (\Exception $dbEx) {
                return back() -> withErrors(['api_error' => '删除客户失败: ' . $dbEx -> getMessage()]);
            }
        }
    }

    /**
     * 发送API请求
     */
    /**
     * 发送API请求
     * 完全重写的API请求方法，模拟测试中的API调用方式
     */
    private function apiRequest($method, $endpoint, $data = [])
    {
        try {
            $user  = auth() -> user();
            $token = $user -> createToken('api-token') -> accessToken;

            // 构建API请求
            $request = Request ::create(
                '/api/' . ltrim($endpoint, '/'),
                $method,
                $method === 'GET' ? $data : [],
                [],
                [],
                [
                    'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                    'HTTP_ACCEPT'        => 'application/json',
                    'CONTENT_TYPE'       => 'application/json',
                ],
                $method !== 'GET' ? json_encode($data) : null
            );

            Log ::info('内部API请求开始', [
                '方法' => $method,
                '端点' => '/api/' . ltrim($endpoint, '/'),
                '数据' => $data
            ]);

            // 使用应用内的请求处理，同测试环境一样
            $response = app() -> handle($request);

            // 转换为HTTP响应对象
            $content    = $response -> getContent();
            $statusCode = $response -> getStatusCode();

            // 记录响应
            Log ::info('内部API响应', [
                '状态码' => $statusCode,
                '内容'   => json_decode($content, true) ?? $content
            ]);

            // 创建HTTP客户端响应对象以保持一致性
            return new \Illuminate\Http\Client\Response(
                new \GuzzleHttp\Psr7\Response(
                    $statusCode,
                    ['Content-Type' => 'application/json'],
                    $content
                )
            );
        } catch (\Exception $e) {
            Log ::error('API请求失败', ['错误' => $e -> getMessage()]);

            // 紧急备用方案：直接数据库操作
            if ($method === 'POST' && str_contains($endpoint, 'customers')) {
                try {
                    Log ::warning('API失败，切换到直接数据库操作');
                    $customer = Customer ::create($data);

                    return new \Illuminate\Http\Client\Response(
                        new \GuzzleHttp\Psr7\Response(
                            201,
                            ['Content-Type' => 'application/json'],
                            json_encode(['data' => $customer])
                        )
                    );
                } catch (\Exception $dbEx) {
                    Log ::error('降级到数据库操作也失败', ['错误' => $dbEx -> getMessage()]);
                }
            }

            // 返回错误响应
            return new \Illuminate\Http\Client\Response(
                new \GuzzleHttp\Psr7\Response(
                    500,
                    ['Content-Type' => 'application/json'],
                    json_encode(['error' => $e -> getMessage()])
                )
            );
        }
    }
}
