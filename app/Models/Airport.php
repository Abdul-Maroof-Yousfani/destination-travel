<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    use HasFactory;

    protected $table = 'airports';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [ 'name', 'code', 'time_zone', 'city_code', 'country', 'city', 'state', 'county', 'order_by' ];
}
