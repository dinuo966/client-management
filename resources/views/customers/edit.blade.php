@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>编辑客户</span>
                        <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary">返回列表</a>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('customers.update', $customer) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3 row">
                                <label for="first_name" class="col-sm-3 col-form-label">名字 <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                           id="first_name" name="first_name" value="{{ old('first_name', $customer->first_name) }}" required>
                                    @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="last_name" class="col-sm-3 col-form-label">姓氏 <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                           id="last_name" name="last_name" value="{{ old('last_name', $customer->last_name) }}" required>
                                    @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="age" class="col-sm-3 col-form-label">年龄</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control @error('age') is-invalid @enderror"
                                           id="age" name="age" value="{{ old('age', $customer->age) }}" min="0" max="150">
                                    @error('age')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="dob" class="col-sm-3 col-form-label">出生日期</label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control @error('dob') is-invalid @enderror"
                                           id="dob" name="dob" value="{{ old('dob', $customer->dob ? date('Y-m-d', strtotime($customer->dob)) : '') }}">
                                    @error('dob')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="email" class="col-sm-3 col-form-label">邮箱 <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', $customer->email) }}" required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-secondary me-md-2">取消</a>
                                <button type="submit" class="btn btn-primary">保存修改</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
