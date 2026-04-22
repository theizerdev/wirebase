<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_numeric_6_digit_code_and_validate_within_30_minutes()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'status' => true,
        ]);

        $code = $user->generateVerificationCode();
        $this->assertMatchesRegularExpression('/^[0-9]{6}$/', $code);
        $this->assertNotNull($user->verification_code_sent_at);
        $this->assertTrue($user->isVerificationCodeValid($code));
    }

    public function test_code_expires_after_30_minutes()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'status' => true,
        ]);

        $code = $user->generateVerificationCode();
        $this->assertMatchesRegularExpression('/^[0-9]{6}$/', $code);

        // Forzar expiración
        $user->verification_code_sent_at = Carbon::now()->subMinutes(31)->subSeconds(1);
        $user->save();

        $user = $user->fresh();
        $this->assertFalse($user->isVerificationCodeValid($code));
    }
}
