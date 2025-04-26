<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="客户管理",
 *     description="客户相关接口"
 * )
 */
class CustomerController extends Controller
{
    /**
     * 显示客户列表
     * 
     * @OA\Get(
     *     path="/api/customers",
     *     tags={"客户管理"},
     *     summary="获取所有客户",
     *     @OA\Response(response=200, description="成功获取客户列表")
     * )
     */
    public function index()
    {
        $customers = Customer ::all();
        return CustomerResource ::collection($customers);
    }

    /**
     * 存储新客户
     * 
     * @OA\Post(
     *     path="/api/customers",
     *     tags={"客户管理"},
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
     *     @OA\Response(response=200, description="客户创建成功"),
     *     @OA\Response(response=422, description="验证错误")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator ::make($request -> all(), [
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'age'        => 'nullable|integer|min:0|max:150',
            'dob'        => 'nullable|date',
            'email'      => 'required|email|max:100|unique:customers',
        ]);

        if ($validator -> fails()) {
            return response() -> json([
                'status'  => false,
                'message' => '验证错误',
                'errors'  => $validator -> errors()
            ], 422);
        }

        $customer = Customer ::create($request -> all());

        return new CustomerResource($customer);
    }

    /**
     * 显示指定客户
     * 
     * @OA\Get(
     *     path="/api/customers/{id}",
     *     tags={"客户管理"},
     *     summary="获取指定客户",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="成功获取客户信息"),
     *     @OA\Response(response=404, description="客户不存在")
     * )
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * 更新指定客户
     * 
     * @OA\Put(
     *     path="/api/customers/{id}",
     *     tags={"客户管理"},
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
     *     @OA\Response(response=200, description="客户更新成功"),
     *     @OA\Response(response=422, description="验证错误"),
     *     @OA\Response(response=404, description="客户不存在")
     * )
     */
    public function update(Request $request, Customer $customer)
    {
        $validator = Validator ::make($request -> all(), [
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'age'        => 'nullable|integer|min:0|max:150',
            'dob'        => 'nullable|date',
            'email'      => 'required|email|max:100|unique:customers,email,' . $customer -> id,
        ]);

        if ($validator -> fails()) {
            return response() -> json([
                'status'  => false,
                'message' => '验证错误',
                'errors'  => $validator -> errors()
            ], 422);
        }

        $customer -> update($request -> all());

        return new CustomerResource($customer);
    }

    /**
     * 删除指定客户
     * 
     * @OA\Delete(
     *     path="/api/customers/{id}",
     *     tags={"客户管理"},
     *     summary="删除客户",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="客户删除成功"),
     *     @OA\Response(response=404, description="客户不存在")
     * )
     */
    public function destroy(Customer $customer)
    {
        $customer -> delete();

        return response() -> json([
            'status'  => true,
            'message' => '客户删除成功'
        ]);
    }
}
