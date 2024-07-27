<?php

namespace App\Http\Controllers\Traits;

use App\Models\InventoriTrans;
use DB;

trait TransaksiTrait
{
    public function saveTrans($trans)
    {
        try {
            DB::beginTransaction();

            InventoriTrans::create([
                'tanggal'               => $trans->tanggal . ' ' . date('H:i:s'),
                'tipe'                  => 'TR',
                'transaction_id'        => $trans->id,
                'trans_no'              => $trans->no,
                'customer_name'         => $trans->customer_name,
                'total'                 => $trans->total,
                'keterangan'            => $trans->text,
                'created_by'            => auth()->user()->id,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ]);

            DB::commit();

            $response           = [
                'status'            => true,
                'msg'               => 'Data Saved.',
            ];
            return $response;
        } catch (Exception $e) {

            DB::rollback();

            $response           = [
                'status'            => false,
                'msg'               => $e->getMessage(),
            ];
            return $response;
        }
    }

    public function deleteTrans($trans)
    {
        try {
            DB::beginTransaction();

            $invTrans                   = InventoriTrans::where('transaction_id', $trans->id)->get();


            if ($invTrans->isNotEmpty()) {

                ///DELETE INV TRANS
                $deleteInvTrans = InventoriTrans::where('transaction_id', $trans->id)->delete();
                ///DELETE INV TRANS
            }

            DB::commit();

            $response           = [
                'status'            => true,
                'msg'               => 'Data Saved.',
            ];
            return $response;
        } catch (Exception $e) {

            DB::rollback();

            $response           = [
                'status'            => false,
                'msg'               => $e->getMessage(),
            ];
            return $response;
        }
    }
}
