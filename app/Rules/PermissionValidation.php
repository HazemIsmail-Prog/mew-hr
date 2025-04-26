<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Permission;
use Carbon\Carbon;
class PermissionValidation implements ValidationRule
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

        if (auth()->user()->dailyPermissionsCount(date:$date, id:$this->id) >= 1) {
            $fail(__('You have a previous permission for this day'));
            return;
        }
        if (auth()->user()->monthlyPermissionsCount(date:$date, id:$this->id) >= 4) {
            $fail(__('You cannot have more than 4 permissions per month'));
            return;
        }

    }
}
