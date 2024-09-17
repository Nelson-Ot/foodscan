<?php

namespace App\Http\PaymentGateways\Gateways;

use App\Enums\Activity;
use App\Enums\GatewayMode;
use App\Enums\OrderStatus;
use App\Models\PaymentGateway;
use App\Services\PaymentAbstract;
use App\Services\PaymentService;
use Exception;
use Iankumu\Mpesa\Facades\Mpesa as MpesaFacade;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class Mpesa extends PaymentAbstract
{
    public mixed $response;

    public function __construct()
    {
        $paymentService = new PaymentService;

        parent::__construct($paymentService);

        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'mpesa'])->first();

        $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');

        Config::set('mpesa.passkey', $this->paymentGatewayOption['safaricom_passkey']);
        Config::set('mpesa.mpesa_consumer_key', $this->paymentGatewayOption['mpesa_consumer_key']);
        Config::set('mpesa.mpesa_consumer_secret', $this->paymentGatewayOption['mpesa_consumer_secret']);
        Config::set('mpesa.shortcode', $this->paymentGatewayOption['mpesa_business_shortcode']);
        Config::set('mpesa.environment', $this->paymentGatewayOption['mpesa_environment'] == GatewayMode::SANDBOX ? 'sandbox' : 'production');
    }

    public function payment($order, $request): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Config::set('mpesa.callback_url', route('callback.mpesa', $order));

        try {
            $response = MpesaFacade::stkpush($order->user?->phone, floatval($order->total), 'CompanyXLTD');

            if ($response->ok() && Arr::get($response->json(), 'CheckoutRequestID')) {
                $order->update(['status' => OrderStatus::PROCESSING]);

                return redirect('/table-order/'.$order->branch->name.'/'.$order->getKey())
                    ->with('success', 'You will be prompted to pay on Mpesa!');
            } else {
                return redirect()->route('payment.index', [
                    'order' => $order,
                    'paymentGateway' => 'mpesa',
                ])->with('error', 'Payment failed, please try again!');
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());

            return redirect()->route('payment.index', [
                'order' => $order,
                'paymentGateway' => 'mpesa',
            ])->with('error', $e->getMessage());
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'mpesa', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }

        return false;
    }

    public function success($order, $request)
    {
        // Needs implementation check phonepe for inspiration
    }

    public function fail($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['order' => $order])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    }
}
