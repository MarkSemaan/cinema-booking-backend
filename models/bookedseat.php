<?php

require_once 'model.php';

class Booking extends Model
{
    protected $table = 'bookedseats';
    protected $fillable = [
        'booking_id',
        'seat_row',
        'seat_number',
    ];
}
