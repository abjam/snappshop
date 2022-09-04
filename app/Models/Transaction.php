<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Card;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Get the card of the transactions.
     */
    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
