<?php

namespace App\Console\Commands;

use App\Enums\Activity;
use App\Enums\GatewayMode;
use App\Enums\InputType;
use App\Models\PaymentGateway;
use Illuminate\Console\Command;

class AddMpesaPaymentGateway extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-mpesa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $paymentGateway = PaymentGateway::create([
            'name' => 'Mpesa',
            'slug' => 'mpesa',
        ]);

        $paymentGateway->gatewayOptions()->createMany([
            [
                'option' => 'safaricom_passkey',
                'value' => null,
                'type' => InputType::TEXT,
            ],
            [
                'option' => 'mpesa_consumer_key',
                'value' => null,
                'type' => InputType::TEXT,
            ],
            [
                'option' => 'mpesa_consumer_secret',
                'value' => null,
                'type' => InputType::TEXT,
            ],
            [
                'option' => 'mpesa_business_shortcode',
                'value' => null,
                'type' => InputType::TEXT,
            ],
            [
                'option' => 'mpesa_status',
                'value' => Activity::ENABLE,
                'type' => InputType::SELECT,
                'activities' => json_encode([
                    Activity::ENABLE => 'enable',
                    Activity::DISABLE => 'disable',
                ]),
            ],
            [
                'option' => 'mpesa_environment',
                'value' => GatewayMode::SANDBOX,
                'type' => InputType::SELECT,
                'activities' => json_encode([
                    GatewayMode::SANDBOX => 'sandbox',
                    GatewayMode::LIVE => 'live',
                ]),
            ],
        ]);

        return Command::SUCCESS;
    }
}
