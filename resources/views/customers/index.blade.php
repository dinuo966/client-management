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
