<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function signInAsAnggota() 
    {
    	Role::factory()->create(['name' => Role::ANGGOTA]);
    	$user = User::factory()->create();
    	$user->assignRole(Role::ANGGOTA);

    	return $this->actingAs($user);
    }

    public function signInAsAdministrator() 
    {
    	Role::factory()->create(['name' => Role::ADMINISTRATOR]);
    	$user = User::factory()->create();
    	$user->assignRole(Role::ADMINISTRATOR);
    	
    	return $this->actingAs($user);
    }
}
