<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\MultiAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginAdminUnitTest extends TestCase
{
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new MultiAuthController();
    }

    /** TC-Log-01: Login admin dengan data valid */
    public function test_login_dengan_data_admin_valid()
    {
        $request = new Request([
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        // ✅ Tambahkan session palsu ke request
        $request->setLaravelSession(app('session')->driver('array'));

        $response = $this->controller->login($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('admin', $response->headers->get('Location'));
    }

    /** TC-Log-02: Login dengan email tidak terdaftar */
    public function test_login_dengan_email_tidak_terdaftar()
    {
        $request = new Request([
            'email' => 'tidakada@example.com',
            'password' => 'password123',
        ]);

        $response = $this->controller->login($request);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /** TC-Log-03: Login dengan password salah */
    public function test_login_dengan_password_salah()
    {
        $request = new Request([
            'email' => 'admin@example.com',
            'password' => 'salahpassword',
        ]);

        $response = $this->controller->login($request);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /** TC-Log-04: Login dengan format email tidak valid */
    public function test_login_dengan_format_email_tidak_valid()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new Request([
            'email' => 'adminexample.com',
            'password' => 'password123',
        ]);

        $request->setLaravelSession(app('session')->driver('array'));

        $this->controller->login($request);
    }

    /** TC-Log-05: Login dengan password kosong */
    public function test_login_dengan_password_kosong()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = new Request([
            'email' => 'admin@example.com',
            'password' => '',
        ]);

        $request->setLaravelSession(app('session')->driver('array'));

        $this->controller->login($request);
    }
}
