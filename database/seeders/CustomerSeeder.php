<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customerUser = User::where('role', 'Customer')
            ->where('email', 'customer@gmail.com')
            ->first();

        if ($customerUser) {
            Customer::create([
                'name' => 'Customer User',
                'address' => 'Kampala, Uganda',
                'gender' => 'Male',
                'job' => 'Guest',
                'birthdate' => '1995-01-01',
                'user_id' => $customerUser->id,
            ]);
        }
    }
}
