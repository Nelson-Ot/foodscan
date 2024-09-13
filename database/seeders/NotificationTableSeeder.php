<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\NotificationSetting;
use Dipokhalder\EnvEditor\EnvEditor;
use Smartisan\Settings\Facades\Settings;


class NotificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $envService = new EnvEditor();
        Settings::group('notification')->set([
            'notification_fcm_public_vapid_key'    => $envService->getValue('DEMO') ? 'BB_1q5RVeHAFd69HDCkeQA62vFohxS_YEA4gVT45JfKeF7-P9UJ3GEypSRb5seCIYb6fI7E2dlXvj0sqbNIXieo' : '',
            'notification_fcm_api_key'             => $envService->getValue('DEMO') ? 'AIzaSyBLVngaS_tDeMogfNmVEfqQ1u_HyqXMqc4' : '',
            'notification_fcm_auth_domain'         => $envService->getValue('DEMO') ? 'foodscan-5102b.firebaseapp.com' : '',
            'notification_fcm_project_id'          => $envService->getValue('DEMO') ? 'foodscan-5102b' : '',
            'notification_fcm_storage_bucket'      => $envService->getValue('DEMO') ? 'foodscan-5102b.appspot.com' : '',
            'notification_fcm_messaging_sender_id' => $envService->getValue('DEMO') ? '1068326850326' : '',
            'notification_fcm_app_id'              => $envService->getValue('DEMO') ? '1:1068326850326:web:fb724f0c9ae7f487ee4a37' : '',
            'notification_fcm_measurement_id'      => $envService->getValue('DEMO') ? 'G-8SFLD2GVEV' : '',
            'notification_fcm_json_file'           => '',
        ]);

        if ($envService->getValue('DEMO') && file_exists(public_path('/file/service-account-file.json'))) {
            $setting = NotificationSetting::where('key', 'notification_fcm_json_file')->first();
            $setting->addMedia(public_path('/file/service-account-file.json'))->preservingOriginal()->usingFileName('service-account-file.json')->toMediaCollection('notification-file');
        }
    }
}