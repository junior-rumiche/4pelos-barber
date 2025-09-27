<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderPdfController
{
    public function __invoke(Request $request, Order $order): Response
    {
        $order->loadMissing(['customer', 'items.service', 'createdBy', 'paymentProcessedBy']);

        $pdf = Pdf::loadView('orders.pdf', [
            'order' => $order,
        ])->setPaper('a5', 'portrait');

        $fileName = sprintf('orden-%s.pdf', $order->id);

        return $request->boolean('download', true)
            ? $pdf->download($fileName)
            : $pdf->stream($fileName);
    }
}
