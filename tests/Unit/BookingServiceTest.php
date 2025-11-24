<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\BookingService;
use App\Models\Booking;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test - TC-BK-07 */
    public function hitung_dp()
    {
        $service = new BookingService();
        $result = $service->hitungDP(100000);

        $this->assertEquals(50000, $result['dp']);
        $this->assertEquals(50000, $result['sisa']);
    }

    /** @test - TC-BK-08 */
    public function hitung_lunas()
    {
        $service = new BookingService();
        $result = $service->hitungLunas(100000);

        $this->assertEquals(100000, $result['total']);
        $this->assertEquals(0, $result['sisa']);
    }

    /** @test - TC-BK-03 */
    public function jam_required()
    {
        $validator = Validator::make(['jam' => null], [
            'jam' => 'required'
        ]);

        $this->assertTrue($validator->fails());
    }

    /** @test - TC-BK-04 */
    public function format_file_tidak_valid()
    {
        $data = [
            'bukti_bayar' => UploadedFile::fake()->create('bukti.docx', 100)
        ];

        $validator = Validator::make($data, [
            'bukti_bayar' => 'mimes:jpg,png,jpeg|max:2048'
        ]);

        $this->assertTrue($validator->fails());
    }

    /** @test - TC-BK-05 */
    public function file_melebihi_2mb()
    {
        $data = [
            'bukti_bayar' => UploadedFile::fake()->image('bukti.jpg')->size(2500)
        ];

        $validator = Validator::make($data, [
            'bukti_bayar' => 'mimes:jpg,png,jpeg|max:2048'
        ]);

        $this->assertTrue($validator->fails());
    }

    /** @test - TC-BK-06 */
    public function bukti_bayar_required()
    {
        $validator = Validator::make([], [
            'bukti_bayar' => 'required'
        ]);

        $this->assertTrue($validator->fails());
    }


    /** @test - TC-BK-010 */
    public function slot_bentrok()
    {
        // Mock static class Booking
        $mock = \Mockery::mock('alias:App\Models\Booking');

        $mock->shouldReceive('where')
            ->with('tanggal', '2025-01-20')
            ->andReturnSelf();

        $mock->shouldReceive('where')
            ->with('jam', '08:00')
            ->andReturnSelf();

        $mock->shouldReceive('exists')
            ->andReturn(true);

        $service = new BookingService();
        $result  = $service->cekSlotBentrok('2025-01-20', '08:00');

        $this->assertTrue($result);
    }


    /** @test - TC-BK-011 */
    public function tipe_layanan_otomatis()
    {
        $layanan = new \stdClass();
        $layanan->jenis = 'studio';

        $service = new BookingService();
        $jenis = $service->tentukanTipe($layanan);

        $this->assertEquals('studio', $jenis);
    }

    /** @test - TC-BK-01 & TC-BK-02 */
    public function tipe_pembayaran_dp_atau_lunas()
    {
        $service = new BookingService();

        $harga = 100000;

        $dp = $service->hitungDP($harga);
        $lunas = $service->hitungLunas($harga);

        $this->assertEquals(50000, $dp['dp']);
        $this->assertEquals(100000, $lunas['total']);
    }
}
