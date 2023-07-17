<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;

class Video extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getDiffTimeAccess()
    {
        if ($this->time_access == null) {
            $start_date = new DateTime($this->created_at->format('Y-m-d'));

            $since_start = $start_date->diff(new DateTime());
            return $since_start->days;
        } else {

            $start_date = new DateTime($this->time_access);

            $since_start = $start_date->diff(new DateTime());
            return $since_start->days;

        }
    }
}
