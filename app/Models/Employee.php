<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'state_id',
        'city_id',
        'department_id',
        'first_name',
        'last_name',
        'middle_name',
        'address',
        'zip_code',
        'date_of_birth',
        'date_hired',
    ];

    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function state() {
        return $this->belongsTo(State::class);
    }

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function team() {
        return $this->belongsTo(Team::class);
    }

    protected static function booted()
    {
        static::creating(function ($employee) {
            if (Filament::getTenant()) {
                $employee->team_id = Filament::getTenant()->id;
            }
        });
    }
}
