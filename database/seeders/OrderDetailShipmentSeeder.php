<?php

namespace Database\Seeders;

use App\Models\OrderDetail;
use Illuminate\Database\Seeder;

class OrderDetailShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $details = OrderDetail::with('shipmentDetailByMany','shipmentDetailByMany.shipment', 'product')->whereNull('supplier_id')->get();
        foreach ($details as $detail) {
            $shipmentDetail = $detail->shipmentDetailByMany->where('seq', $detail->seq)->first();
            $detail->shipment_no = $shipmentDetail->shipment->shipment_no ?? null;
            $detail->supplier_id = $detail->product->supplier_id ?? null;
            $detail->save();
            \Log::info($detail);
        }
    }
}
