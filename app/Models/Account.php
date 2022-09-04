<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Card;

class Account extends Model
{
    use HasFactory;

    /**
     * Get the cards of the account.
     */
    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    /**
     * Get the user that owns the accounts.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

