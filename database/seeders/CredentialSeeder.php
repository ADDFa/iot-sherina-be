<?php

namespace Database\Seeders;

use App\Models\Credential;
use Illuminate\Database\Seeder;

class CredentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = function (string $password) {
            return password_hash($password, PASSWORD_DEFAULT);
        };

        $dataCredentials = [
            [
                "username"  => "admin",
                "password"  => $password("1ZhbbAlUJE"),
                "role"      => "admin"
            ]
        ];

        foreach ($dataCredentials as $dataCredential) {
            $credential = new Credential($dataCredential);
            $credential->save();
        }
    }
}
