<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Account;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAccountDatabase()
    {
        $account = Account::factory()->create();

        $this->assertDatabaseHas('accounts', [
            'account_number' => 6546552315
        ]);
        $this->assertModelExists($account);

        $account->delete();

        $this->assertModelMissing($account);
    }
}
