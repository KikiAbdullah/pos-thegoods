<?php

use App\Models\Master\Addon;
use App\Models\Master\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'Basic Self Studio',
                'description' => 'Dimohon untuk datang 15 menit sebelum pemilihan jam sesi booking | Kami tidak mentoleransi keterlambatan 🙏🏻 Jika telat melebihi 20 menit maka booking hangus dan pembayaran tidak bisa di refund',
                'photo_session' => '30 min',
                'jumlah_orang' => '2',
                'harga' => '70000',
                'created_by' => 1,
            ],
            [
                'name' => 'Group Self Studio',
                'description' => 'Dimohon untuk datang 15 menit sebelum pemilihan jam sesi booking | Kami tidak mentoleransi keterlambatan 🙏🏻 Jika telat melebihi 20 menit maka booking hangus dan pembayaran tidak bisa di refund.',
                'photo_session' => '45 min',
                'jumlah_orang' => '6',
                'harga' => '120000',
                'created_by' => 1,
            ],
        ];

        Package::insert($data);


        $addons = [
            [
                'name' => 'Per orang',
                'description' => '',
                'harga' => '15000',
                'created_by' => 1,
            ],
            [
                'name' => 'Foto Berwarna',
                'description' => '',
                'harga' => '20000',
                'created_by' => 1,
            ],
            [
                'name' => 'Projected Background',
                'description' => '',
                'harga' => '25000',
                'created_by' => 1,
            ],
        ];

        Addon::insert($addons);
    }
}
