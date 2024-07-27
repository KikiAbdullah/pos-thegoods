<?php

use App\Models\Kasir;
use App\Models\Transaction;
use App\Models\TransactionAddon;
use App\Models\TransactionPackage;
use Illuminate\Database\Seeder;

class TransSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Kasir::create([
            'tanggal' => date('Y-m-d'),
            'open' => date('Y-m-d H:i:s'),
            'saldo_awal' => 100000,
            'status' => 'open',
            'created_by' => 1,
        ]);

        $transactions = array();
        $transaction_addons = array();
        $transaction_packages = array();

        $tipe_pembayaran_ids = [1, 2];
        $statuses = ['rejected', 'verify',  'payment', 'ordered', 'open'];
        $customer_names = ['kiki abdullah', 'asdasd', 'john doe'];
        $customer_emails = ['kiki@gmail.com', 'asdasd@gmail.com', 'john@gmail.com'];
        $customer_whatsapp_numbers = ['085155300552', '085155300551', '085155300550'];
        $addon_names = ['Per orang', 'Foto Berwarna', 'Projected Background'];
        $package_names = ['Basic Self Studio', 'Group Self Studio'];

        $packages = [
            [
                'name' => 'Basic Self Studio',
                'harga' => '70000'
            ], [
                'name' => 'Group Self Studio',
                'harga' => '120000'

            ]
        ];

        $addon = [
            [
                'name' => 'Per orang',
                'harga' => '15000'
            ], [
                'name' => 'Foto Berwarna',
                'harga' => '20000'
            ],
            [
                'name' => 'Projected Background',
                'harga' => '25000'
            ],
        ];

        for ($i = 3; $i <= 105; $i++) {
            $kasir_id = 1;
            $tipe_pembayaran_id = $tipe_pembayaran_ids[array_rand($tipe_pembayaran_ids)];
            $status = $statuses[array_rand($statuses)];
            $customer_name = $customer_names[array_rand($customer_names)];
            $customer_email = $customer_emails[array_rand($customer_emails)];
            $customer_whatsapp = $customer_whatsapp_numbers[array_rand($customer_whatsapp_numbers)];
            $date = date('Y-m-d');
            $timeCreate = $this->randomDateTime('2024-07-26 00:00:00', '2024-07-26 23:59:59');
            $timeOrder = $this->randomDateTime($timeCreate, '2024-07-26 23:59:59');
            $timePayment = $this->randomDateTime($timeOrder, '2024-07-26 23:59:59');
            $timeVerify = $this->randomDateTime($timePayment, '2024-07-26 23:59:59');
            $timeReject = $this->randomDateTime($timeOrder, '2024-07-26 23:59:59');

            $ordered_at = NULL;
            $payment_at = NULL;
            $verify_at = NULL;
            $rejected_at = NULL;

            if ($status == 'verify') {
                $verify_at = $timeVerify;
                $payment_at = $timePayment;
                $ordered_at = $timeOrder;
            } elseif ($status == 'payment') {
                $payment_at = $timePayment;
                $ordered_at = $timeOrder;
            } elseif ($status == 'ordered') {
                $ordered_at = $timeOrder;
            } elseif ($status == 'rejected') {
                $rejected_at = $timeReject;
                $ordered_at = $timeOrder;
            }

            $no = 'TR' . date('ymd') . str_pad($i, 4, '0', STR_PAD_LEFT);

            $transactions = array(
                'kasir_id' => $kasir_id,
                'tipe_pembayaran_id' => $tipe_pembayaran_id,
                'no' => $no,
                'tanggal' => $date,
                'text' => NULL,
                'text_rejected' => ($status == 'rejected') ? 'pembatalan' : NULL,
                'customer_whatsapp' => $customer_whatsapp,
                'customer_name' => $customer_name,
                'customer_email' => $customer_email,
                'status' => $status,
                'created_by' => '1',
                'rejected_at' => $rejected_at,
                'rejected_by' => ($status == 'rejected') ? '1' : NULL,
                'ordered_at' => $ordered_at,
                'ordered_by' => ($ordered_at) ? '1' : NULL,
                'payment_at' => $payment_at,
                'payment_by' => ($payment_at) ? '1' : NULL,
                'verify_at' => $verify_at,
                'verify_by' => ($verify_at) ? '1' : NULL,
                'created_at' => $timeCreate,
                'updated_at' => max($timeCreate, $verify_at, $payment_at, $ordered_at, $rejected_at),
                'deleted_at' => NULL
            );

            // Add some addons
            for ($j = 1; $j <= rand(1, 3); $j++) {
                $addon_name = $addon[($j - 1)];
                $transaction_addons = array(
                    'transaction_id' => $i,
                    'addon_id' => $j,
                    'addon_name' => $addon_name['name'],
                    'qty' => '1.00',
                    'harga' => $addon_name['harga'],
                    'created_by' => '1',
                    'created_at' => $timeCreate,
                    'updated_at' => max($timeCreate, $verify_at, $payment_at, $ordered_at, $rejected_at),
                    'deleted_at' => NULL
                );
            }

            // Add some packages
            for ($k = 1; $k <= rand(1, 2); $k++) {
                $package_name = $packages[$k - 1];
                $transaction_packages = array(
                    'transaction_id' => $i,
                    'package_id' => $k,
                    'package_name' => $package_name['name'],
                    'harga' => $package_name['harga'],
                    'url' => NULL,
                    'created_by' => '1',
                    'created_at' => $timeCreate,
                    'updated_at' => max($timeCreate, $verify_at, $payment_at, $ordered_at, $rejected_at),
                    'deleted_at' => NULL
                );
            }


            Transaction::create($transactions);
            TransactionAddon::create($transaction_addons);
            TransactionPackage::create($transaction_packages);
        }
    }

    public function randomDateTime($startDate, $endDate)
    {
        $timestamp = mt_rand(strtotime($startDate), strtotime($endDate));
        return date('Y-m-d H:i:s', $timestamp);
    }
}
