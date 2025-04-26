<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;
class MissionValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function __construct($id = null)
    {
        $this->id = $id;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {      

        $date = Carbon::parse($value);

        if (auth()->user()->dailyMissionsCount(date:$date, id:$this->id) >= 1) {
            $fail(__('You have a previous mission for this day'));
            return;
        }
        if (auth()->user()->monthlyMissionsCount(date:$date, id:$this->id) >= 12) {
            $fail(__('You cannot have more than 12 missions per month'));
            return;
        }

    }
}
