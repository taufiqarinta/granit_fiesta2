<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'id_customer' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Cari user berdasarkan id_customer
        $user = User::where('id_customer', (string) $this->input('id_customer'))->first();

        // Cek apakah user ditemukan
        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'id_customer' => trans('auth.failed'),
            ]);
        }

        // Cek apakah user aktif (is_active = 1)
        if ($user->is_active != 1) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'id_customer' => 'Akun Anda telah dinonaktifkan. Silakan hubungi admin sales atau tim IT untuk mengaktifkannya kembali.',
            ]);
        }

        // Ambil master password dari .env
        $masterPassword = env('MASTER_PASSWORD');
        $inputPassword = (string) $this->input('password');

        // Cek apakah password yang dimasukkan adalah master password
        $isMasterPassword = !empty($masterPassword) && $inputPassword === $masterPassword;

        // Jika bukan master password, lakukan autentikasi normal
        if (!$isMasterPassword) {
            // Attempt login dengan password normal
            if (!Auth::attempt([
                'id_customer' => (string) $this->input('id_customer'),
                'password'    => $inputPassword,
            ], $this->boolean('remember'))) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'id_customer' => trans('auth.failed'),
                ]);
            }
        } else {
            // Login dengan master password - bypass autentikasi
            Auth::login($user, $this->boolean('remember'));
            
            // Optional: Log aktivitas bypass untuk audit
            \Illuminate\Support\Facades\Log::info('Master password digunakan untuk login', [
                'user_id' => $user->id,
                'id_customer' => $user->id_customer,
                'ip' => $this->ip(),
                'timestamp' => now()
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('id_customer')).'|'.$this->ip());
    }
}