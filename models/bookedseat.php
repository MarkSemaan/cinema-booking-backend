<?php

require_once 'model.php';

class BookedSeat extends Model
{
    protected $table = 'booked_seats';
    protected $fillable = [
        'booking_id',
        'seat_row',
        'seat_number',
    ];
}
