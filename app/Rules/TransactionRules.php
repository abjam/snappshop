<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class TransactionRules implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        if(!$this->bankCardCheck($value))
            $fail('شماره کارت معتبر نمی باشد.');
    }

    /**
     * Check validation of bank card
     *
     * @param  mixed $card
     * @param  mixed $irCard
     *
     * @return bool
     */
    private function bankCardCheck($card='', $irCard=true)
    {
        $card = (string) preg_replace('/\D/','',$card);
        $strlen = strlen($card);

        if ($irCard==true and $strlen!=16)
            return false;

        if ($irCard!=true and ($strlen<13 or $strlen>19))
            return false;

        if (!in_array($card[0],[2,4,5,6,9]))
            return false;

        for($i=0; $i<$strlen; $i++)
        {
            $res[$i] = $card[$i];
            if (($strlen%2)==($i%2))
            {
                $res[$i] *= 2;
                if ($res[$i]>9)
                    $res[$i] -= 9;
            }
        }

        return array_sum($res) % 10 == 0 ? true : false;
    }
}
