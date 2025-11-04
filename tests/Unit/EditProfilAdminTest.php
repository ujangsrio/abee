<?php

namespace Tests\Unit;

use Livewire\Livewire;
use App\Filament\Resources\PengaturanResource\Pages\EditPengaturan;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EditProfilAdminTest extends TestCase
{
    protected $admin;
    protected $originalData = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('email', 'admin@example.com')->first();

        if (!$this->admin) {
            $this->admin = User::create([
                'name' => 'Admin Aretha',
                'email' => 'admin@example.com',
                'password' => Hash::make('00000000'),
                'role' => 'admin',
            ]);
        }

        $this->originalData = [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'password' => $this->admin->password,
            'role' => $this->admin->role,
        ];

        $this->admin->update(['password' => Hash::make('00000000')]);
    }

    protected function tearDown(): void
    {
        if ($this->admin) {
            $this->admin->update($this->originalData);
        }

        parent::tearDown();
    }

    /** @test */
    public function admin_can_update_name_successfully()
    {
        $this->actingAs($this->admin, 'admin');

        Livewire::test(EditPengaturan::class, ['record' => $this->admin->getKey()])
            ->set('data.name', 'Admin Baru')
            ->set('data.email', $this->admin->email)
            ->set('data.current_password', '00000000')
            ->call('save')
            ->assertHasNoErrors();

        $this->admin->refresh();
        $this->assertEquals('Admin Baru', $this->admin->name);
    }

    /** @test */
    public function admin_cannot_update_with_short_name()
    {
        $this->actingAs($this->admin, 'admin');

        Livewire::test(EditPengaturan::class, ['record' => $this->admin->getKey()])
            ->set('data.name', 'Adm')
            ->set('data.email', $this->admin->email)
            ->set('data.current_password', '00000000')
            ->call('save')
            ->assertHasErrors(['data.name' => 'min']);
    }

    /** @test */
    public function admin_cannot_update_with_name_exceeding_max_length()
    {
        $this->actingAs($this->admin, 'admin');

        $tooLongName = str_repeat('A', 55);

        Livewire::test(EditPengaturan::class, ['record' => $this->admin->getKey()])
            ->set('data.name', $tooLongName)
            ->set('data.email', $this->admin->email)
            ->set('data.current_password', '00000000')
            ->call('save')
            ->assertHasErrors(['data.name' => 'max']);
    }

    /** @test */
    public function admin_name_can_be_max_length()
    {
        $this->actingAs($this->admin, 'admin');

        $maxName = str_repeat('A', 50);

        Livewire::test(EditPengaturan::class, ['record' => $this->admin->getKey()])
            ->set('data.name', $maxName)
            ->set('data.email', $this->admin->email)
            ->set('data.current_password', '00000000')
            ->call('save')
            ->assertHasNoErrors();
    }

    /** @test */
    public function admin_email_validation()
    {
        $this->actingAs($this->admin, 'admin');

        $cases = [
            ['email' => 'invalidemail', 'error' => 'email'],
            ['email' => 'admin@.com', 'error' => 'email'],
            ['email' => $this->admin->email, 'error' => null], 
        ];

        foreach ($cases as $case) {
            $test = Livewire::test(EditPengaturan::class, ['record' => $this->admin->getKey()])
                ->set('data.name', 'Admin Aretha')
                ->set('data.email', $case['email'])
                ->set('data.current_password', '00000000')
                ->call('save');

            if ($case['error']) {
                $test->assertHasErrors(['data.email' => $case['error']]);
            } else {
                $test->assertHasNoErrors(['data.email']);
            }
        }
    }


    /** @test */
    public function admin_can_update_password_with_valid_minimum()
    {
        $this->actingAs($this->admin, 'admin');

        $newPassword = 'Abcd1234';

        Livewire::test(EditPengaturan::class, ['record' => $this->admin->getKey()])
            ->set('data.name', $this->admin->name)
            ->set('data.email', $this->admin->email)
            ->set('data.current_password', '00000000')
            ->set('data.new_password', $newPassword)
            ->set('data.new_password_confirmation', $newPassword)
            ->call('save')
            ->assertHasNoErrors();

        $this->admin->refresh();
        $this->assertTrue(Hash::check($newPassword, $this->admin->password));
    }

    /** @test */
    public function admin_cannot_update_password_with_short_password()
    {
        $this->actingAs($this->admin, 'admin');

        $shortPass = 'abc123';

        Livewire::test(EditPengaturan::class, ['record' => $this->admin->getKey()])
            ->set('data.new_password', $shortPass)
            ->set('data.new_password_confirmation', $shortPass)
            ->call('save')
            ->assertHasErrors(['data.new_password' => 'min']);
    }

    /** @test */
    public function admin_cannot_update_password_if_confirmation_mismatch()
    {
        $this->actingAs($this->admin, 'admin');

        $newPassword = 'Abcd1234';
        $wrongConfirm = 'Abcd123';

        $test = Livewire::test(EditPengaturan::class, ['record' => $this->admin->getKey()])
            ->set('data.new_password', $newPassword)
            ->set('data.new_password_confirmation', $wrongConfirm)
            ->set('data.current_password', '00000000')
            ->call('save');

        $errors = array_keys($test->errors()->toArray());
        $this->assertTrue(
            in_array('data.new_password', $errors) || in_array('data.new_password_confirmation', $errors)
        );
    }

    /** @test */
    public function admin_cannot_update_password_if_current_password_wrong()
    {
        $this->actingAs($this->admin, 'admin');

        $newPassword = 'Abcd1234';

        Livewire::test(EditPengaturan::class, ['record' => $this->admin->getKey()])
            ->set('data.name', 'Admin Updated')
            ->set('data.email', 'adminupdated@gmail.com')
            ->set('data.current_password', 'wrongpass')
            ->set('data.new_password', $newPassword)
            ->set('data.new_password_confirmation', $newPassword)
            ->call('save')
            ->assertHasErrors(['data.current_password']);
    }

    /** @test */
    public function admin_cannot_save_with_all_required_fields_empty()
    {
        $this->actingAs($this->admin, 'admin');

        $test = Livewire::test(EditPengaturan::class, ['record' => $this->admin->getKey()])
            ->set('data.name', '')
            ->set('data.email', '')
            ->set('data.current_password', '')
            ->call('save');

        $errors = array_keys($test->errors()->toArray());
        $this->assertContains('data.name', $errors);
        $this->assertContains('data.email', $errors);
    }
}
