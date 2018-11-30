<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 4)->create()->each(function($user){
        	$user->address_books()->save(factory(App\AddressBook::class)->make());
            $user->cart()->create([]);
        });
    }
}
