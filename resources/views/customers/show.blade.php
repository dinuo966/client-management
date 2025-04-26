@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>客户详情</span>
                        <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary">返回列表</a>
                    </div>

                    <div class="card-body">
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">姓名</label>
                            <div class="col-sm-9">
                                <p class="form-control-plaintext">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">年龄</label>
                            <div class="col-sm-9">
                                <p class="form-control-plaintext">{{ $customer->age ?? '未填写' }}</p>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">出生日期</label>
                            <div class="col-sm-9">
                                <p class="form-control-plaintext">{{ $customer->dob ? date('Y-m-d', strtotime($customer->dob)) : '未填写' }}</p>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">邮箱</label>
                            <div class="col-sm-9">
                                <p class="form-control-plaintext">{{ $customer->email }}</p>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">创建时间</label>
                            <div class="col-sm-9">
                                <p class="form-control-plaintext">{{ $customer->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">编辑客户</a>

                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('确定要删除该客户吗？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">删除客户</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
