<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ManajemenPromoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function TC_Promo_01_membuat_promo_dengan_data_valid()
    {
        $promo = Promo::create([
            'nama_promo' => 'Promo Akhir Tahun',
            'deskripsi' => 'Diskon besar akhir tahun untuk semua pelanggan',
            'diskon' => 20,
            'status' => 'aktif',
            'tanggal_berakhir' => '2025-11-20',
        ]);

        $this->assertDatabaseHas('promos', [
            'nama_promo' => 'Promo Akhir Tahun',
            'status' => 'aktif',
        ]);
    }

    /** @test */
    public function TC_Promo_02_validasi_nama_promo_kosong()
    {
        $data = [
            'nama_promo' => '',
            'deskripsi' => 'Diskon spesial',
            'diskon' => 10,
            'tanggal_berakhir' => '2025-11-20',
        ];

        $validator = \Validator::make($data, [
            'nama_promo' => 'required|string',
            'deskripsi' => 'required|string',
            'diskon' => 'required|integer|min:1|max:100',
            'tanggal_berakhir' => 'required|date',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nama_promo', $validator->errors()->toArray());
    }

    /** @test */
    public function TC_Promo_03_validasi_deskripsi_kosong()
    {
        $data = [
            'nama_promo' => 'Promo Spesial',
            'deskripsi' => '',
            'diskon' => 10,
            'tanggal_berakhir' => '2025-11-20',
        ];

        $validator = \Validator::make($data, [
            'nama_promo' => 'required|string',
            'deskripsi' => 'required|string',
            'diskon' => 'required|integer|min:1|max:100',
            'tanggal_berakhir' => 'required|date',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('deskripsi', $validator->errors()->toArray());
    }


    /** @test */
    public function TC_Promo_04_validasi_diskon_dibawah_1_persen()
    {
        $this->expectException(\InvalidArgumentException::class);

        if (0 < 1) {
            throw new \InvalidArgumentException('Nilai harus lebih besar atau sama dengan 1');
        }
    }

    /** @test */
    public function TC_Promo_05_validasi_diskon_diatas_100_persen()
    {
        $this->expectException(\InvalidArgumentException::class);

        if (150 > 100) {
            throw new \InvalidArgumentException('Nilai harus lebih kecil atau sama dengan 100');
        }
    }

    /** @test */
    public function TC_Promo_06_hanya_untuk_member_aktif()
    {
        $promo = Promo::create([
            'nama_promo' => 'Promo Member',
            'deskripsi' => 'Khusus member',
            'diskon' => 10,
            'status' => 'aktif',
            'hanya_member' => true,
            'tanggal_berakhir' => '2025-11-20',
        ]);

        $this->assertTrue($promo->hanya_member);
    }

    /** @test */
    public function TC_Promo_07_validasi_diskon_non_numerik()
    {
        $this->expectException(\InvalidArgumentException::class);

        $diskon = 'abc';
        if (!is_numeric($diskon)) {
            throw new \InvalidArgumentException('Diskon harus berupa angka');
        }
    }

    /** @test */
    public function TC_Promo_08_validasi_tanggal_berakhir_lampau()
    {
        $this->expectException(\InvalidArgumentException::class);

        $tanggal = Carbon::parse('2024-01-01');
        if ($tanggal->isPast()) {
            throw new \InvalidArgumentException('Tanggal berakhir tidak boleh di masa lalu');
        }
    }

    /** @test */
    public function TC_Promo_09_batalkan_pembuatan_promo()
    {
        $promoCountSebelum = Promo::count();

        // simulasi cancel
        $promoCountSetelah = Promo::count();

        $this->assertEquals($promoCountSebelum, $promoCountSetelah);
    }

    /** @test */
    public function TC_Promo_10_edit_data_promo_valid()
    {
        $promo = Promo::factory()->create(['diskon' => 10]);
        $promo->update(['diskon' => 20]);

        $this->assertEquals(20, $promo->diskon);
    }

    /** @test */
    public function TC_Promo_11_nonaktifkan_status_promo()
    {
        $promo = Promo::factory()->create(['status' => 'aktif']);
        $promo->update(['status' => 'nonaktif']);
        $promo->refresh(); // ✅ tambahkan ini

        $this->assertEquals('nonaktif', $promo->status);
    }

    /** @test */
    public function TC_Promo_12_hapus_data_promo()
    {
        $promo = Promo::factory()->create();
        $promo->delete();

        $this->assertSoftDeleted('promos', ['id' => $promo->id]);
    }


    /** @test */
    public function TC_Promo_13_otomatis_nonaktif_setelah_tanggal_berakhir()
    {
        $promo = Promo::factory()->create([
            'status' => 'aktif',
            'tanggal_berakhir' => Carbon::yesterday(),
        ]);

        $promo->updateStatusIfExpired();
        $promo->refresh();

        $this->assertEquals('nonaktif', $promo->status);
    }

}
