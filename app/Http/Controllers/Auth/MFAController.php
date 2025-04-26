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
        $this -> middleware('auth');
    }

    /**
     * 显示MFA验证表单
     */
    public function showVerificationForm()
    {
        if (Session ::has('mfa_verified')) {
            return redirect() -> intended('/customers');
        }

        $user = auth() -> user();

        // 确保用户已登录
        if (!$user) {
            return redirect() -> route('login') -> with('error', '请先登录');
        }

        // 生成并发送MFA令牌
        $this -> generateAndSendToken($user);

        return view('auth.mfa-verify');
    }

    /**
     * 验证MFA令牌
     */
    public function verify(Request $request)
    {
        $request -> validate([
            'token' => 'required|string|size:6',
        ]);

        $user  = auth() -> user();
        $token = MFAToken ::where('user_id', $user -> id)
            -> latest()
            -> first();

        if (!$token || $token -> isExpired() || $token -> token !== $request -> token) {
            return back() -> withErrors([
                'token' => '令牌无效或已过期。',
            ]);
        }

        // 标记为已通过MFA验证
        Session ::put('mfa_verified', true);

        // 删除使用过的令牌
        $token -> delete();

        return redirect() -> intended('/home');
    }

    /**
     * 重新发送MFA令牌
     */
    public function resend()
    {
        $user = auth() -> user();

        // 删除旧令牌
        MFAToken ::where('user_id', $user -> id) -> delete();

        // 生成并发送新令牌
        $this -> generateAndSendToken($user);

        return back() -> with('status', '新令牌已发送到您的电子邮件。');
    }

    /**
     * 生成并发送MFA令牌
     */
    private function generateAndSendToken($user)
    {
        // 添加检查确保用户对象存在且有email属性
        if (!$user || !isset($user -> email)) {
            // 处理错误情况，例如记录日志或重定向
            \Log ::error('尝试为空用户生成MFA令牌');
            return false;
        }
        // 生成6位随机数字令牌
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // 保存令牌到数据库
        MFAToken ::create([
            'user_id'    => $user -> id,
            'token'      => $token,
            'expires_at' => now() -> addMinutes(10), // 10分钟有效期
        ]);

        // 发送令牌到用户邮箱
        Mail ::to($user -> email) -> send(new MFATokenMail($token));
    }
}
