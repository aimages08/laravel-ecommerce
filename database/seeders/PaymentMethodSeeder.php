<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        // Cash on Delivery — always enabled, no credentials
        $cod = PaymentMethod::firstOrCreate(
            ['code' => 'cod'],
            [
                'name' => 'Cash on Delivery',
                'is_enabled' => true,
                'is_test_mode' => false,
                'instructions' => 'Pay with cash when your order is delivered.',
                'sort_order' => 1,
            ]
        );

        // Bank Transfer — enabled, has credentials admin can edit
        $bank = PaymentMethod::firstOrCreate(
            ['code' => 'bank'],
            [
                'name' => 'Bank Transfer',
                'is_enabled' => true,
                'is_test_mode' => false,
                'instructions' => 'Transfer to our bank account. Details will be emailed after checkout.',
                'sort_order' => 2,
            ]
        );

        // Add default credential fields (empty — admin fills them)
        if ($bank->settings()->count() === 0) {
            $fields = [
                ['key' => 'bank_name',      'encrypt' => false],
                ['key' => 'account_title',  'encrypt' => false],
                ['key' => 'account_number', 'encrypt' => false],
                ['key' => 'iban',           'encrypt' => false],
            ];
            foreach ($fields as $i => $f) {
                $bank->settings()->create([
                    'key' => $f['key'],
                    'value' => null,
                    'is_encrypted' => $f['encrypt'],
                    'sort_order' => $i,
                ]);
            }
        }
    }
}