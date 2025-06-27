<?php

require_once 'model.php';

class Auditorium extends Model
{
    protected $table = 'auditorium';
    protected $fillable = [
        'name',
        'capacity',
        'seats_rows',
        'seats_per_row',
    ];
}
