<?php

require_once 'model.php';

class Booking extends Model
{
    protected $table = 'bookings';
    protected $fillable = [
        'user_id',
        'showtime_id',
    ];
}
