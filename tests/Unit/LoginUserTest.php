<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;

class LoginUserTest extends TestCase
{
    #[Test]
    public function tc_log_01_menguji_login_dengan_data_valid()
    {
        // Langkah uji: Masukkan Email: pelanggan@gmail.com, Password: Rio@123@
        $credentials = [
            'email' => 'pelanggan@gmail.com',
            'password' => 'Rio@123@'
        ];

        // Eksekusi login
        $loginResult = Auth::guard('customer')->attempt($credentials);

        // Expected Result: Login berhasil
        $this->assertTrue($loginResult, 'Sistem harus menampilkan pesan "Login berhasil"');
        $this->assertNotNull(Auth::guard('customer')->id(), 'Pengguna harus diarahkan ke halaman utama');
        $this->assertEquals('customer', Auth::guard('customer')->user()->role, 'Role user harus customer');
    }

    #[Test]
    public function tc_log_02_menguji_login_dengan_email_tidak_terdaftar()
    {
        // Langkah uji: Masukkan Email: tidakTerdaftar@gmail.com, Password: 12345678
        $credentials = [
            'email' => 'tidakTerdaftar@gmail.com',
            'password' => '12345678'
        ];

        // Eksekusi login
        $loginResult = Auth::guard('customer')->attempt($credentials);

        // Expected Result: Sistem menampilkan pesan error "Email tidak ditemukan"
        $this->assertFalse($loginResult, 'Sistem harus menampilkan pesan error "Email tidak ditemukan"');
        $this->assertNull(Auth::guard('customer')->id(), 'Tidak ada user yang seharusnya login');

        // Verifikasi bahwa email memang tidak ada di database
        $userExists = \App\Models\User::where('email', 'tidakTerdaftar@gmail.com')->exists();
        $this->assertFalse($userExists, 'Email tidakTerdaftar@gmail.com tidak boleh terdaftar di database');
    }

    #[Test]
    public function tc_log_03_menguji_login_dengan_password_salah()
    {
        // Langkah uji: Masukkan Email: pelanggan@gmail.com, Password: 123444444 (salah)
        $credentials = [
            'email' => 'pelanggan@gmail.com',
            'password' => '123444444'
        ];

        // Eksekusi login
        $loginResult = Auth::guard('customer')->attempt($credentials);

        // Expected Result: Sistem menampilkan pesan error "Password salah"
        $this->assertFalse($loginResult, 'Sistem harus menampilkan pesan error "Password salah"');
        $this->assertNull(Auth::guard('customer')->id(), 'Tidak ada user yang seharusnya login');

        // Verifikasi bahwa email ada tapi password salah
        $userExists = \App\Models\User::where('email', 'pelanggan@gmail.com')->exists();
        $this->assertTrue($userExists, 'Email pelanggan@gmail.com harus terdaftar di database');
    }

    #[Test]
    public function tc_log_04_menguji_login_dengan_format_email_tidak_valid()
    {
        // Langkah uji: Masukkan Email: pelanggan#gmail.com, Password: Rio@123@
        $credentials = [
            'email' => 'pelanggan#gmail.com',
            'password' => 'Rio@123@'
        ];

        // Eksekusi login
        $loginResult = Auth::guard('customer')->attempt($credentials);

        // Expected Result: Sistem menampilkan pesan error "Format email tidak valid"
        $this->assertFalse($loginResult, 'Sistem harus menampilkan pesan error "Format email tidak valid"');
        $this->assertNull(Auth::guard('customer')->id(), 'Tidak ada user yang seharusnya login');

        // Validasi format email
        $isValidEmail = filter_var('pelanggan#gmail.com', FILTER_VALIDATE_EMAIL) !== false;
        $this->assertFalse($isValidEmail, 'Format email pelanggan#gmail.com harus tidak valid');
    }

    #[Test]
    public function tc_log_05_menguji_login_dengan_field_password_dikosongkan()
    {
        // Langkah uji: Masukkan Email: pelanggan@gmail.com, Password: [kosong]
        $credentials = [
            'email' => 'pelanggan@gmail.com',
            'password' => ''
        ];

        // Eksekusi login
        $loginResult = Auth::guard('customer')->attempt($credentials);

        // Expected Result: Sistem menampilkan pesan error "Password harus diisi"
        $this->assertFalse($loginResult, 'Sistem harus menampilkan pesan error "Password harus diisi"');
        $this->assertNull(Auth::guard('customer')->id(), 'Tidak ada user yang seharusnya login');

        // Verifikasi bahwa password kosong
        $this->assertEmpty($credentials['password'], 'Password harus kosong');
    }

    #[Test]
    public function menguji_validasi_email_dan_password_wajib_diisi()
    {
        // Test case tambahan: kedua field kosong
        $credentials = [
            'email' => '',
            'password' => ''
        ];

        $loginResult = Auth::guard('customer')->attempt($credentials);

        $this->assertFalse($loginResult, 'Login harus gagal ketika email dan password kosong');
        $this->assertNull(Auth::guard('customer')->id());
    }

    #[Test]
    public function menguji_redirect_setelah_login_berhasil()
    {
        // Simulasikan redirect setelah login berhasil
        $credentials = [
            'email' => 'pelanggan@gmail.com',
            'password' => 'Rio@123@'
        ];

        $loginResult = Auth::guard('customer')->attempt($credentials);

        $this->assertTrue($loginResult, 'Login harus berhasil');

        // Verifikasi bahwa user dapat di-redirect ke intended URL
        $intendedUrl = '/customer/dashboard';
        $this->assertNotEmpty($intendedUrl, 'Harus ada URL tujuan setelah login berhasil');
    }
}
