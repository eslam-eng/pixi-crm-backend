<?php


namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentGatewayInterface $paymentGateway
    ) {}

    public function paymentProcess(Request $request)
    {
        return $this->paymentGateway->sendPayment($request);
    }

    public function callback(Request $request)
    {
        $response = $this->paymentGateway->callback($request);
        if ($response['status'] == 'success') {
            return apiResponse($response, 'Payment successful.');
        } else {
            return apiResponse($response, 'Payment failed.');
        }
    }
}
