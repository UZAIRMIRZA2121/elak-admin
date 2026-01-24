<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\File;

class VoucherController extends Controller
{

    public function shareVoucher(Request $request, $qr_code)
    {
        $order = Order::where('qr_code', $qr_code)->first();

        if (!$order) {
            return response()->json(['message' => 'Voucher not found'], 404);
        }

        // Assuming you have a view named 'voucher.share' to display the voucher details
        return view('voucher.share', compact('order'));
    }




    public function downloadVoucher($qr_code)
    {
        $order = Order::where('qr_code', $qr_code)->firstOrFail();
        $qrPath = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={$order->qr_code}";
  
     
        
     

        $pdf = Pdf::loadView('voucher.pdf', compact('order', 'qrPath'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => false,
                'defaultFont' => 'dejavu sans'
            ]);

        return $pdf->download("voucher-$qr_code.pdf");
    }
}
