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
