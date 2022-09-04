<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use App\Models\َAccount;

class Card extends Model
{
    use HasFactory;

    /**
     * Get the transactions of the card.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the account that owns the cards.
     */
    public function account()
    {
        return $this->belongsTo(َAccount::class, 'account_id');
    }
}