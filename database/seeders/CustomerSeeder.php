<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'first_name' => '张',
                'last_name' => '三',
                'age' => 30,
                'dob' => '1993-01-15',
                'email' => 'zhangsan@163.com',
            ],
            [
                'first_name' => '李',
                'last_name' => '四',
                'age' => 25,
                'dob' => '1998-05-20',
                'email' => 'lisi@163.com',
            ],
            [
                'first_name' => '王',
                'last_name' => '五',
                'age' => 40,
                'dob' => '1983-11-10',
                'email' => 'wangwu@163.com',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }
    }
}
