<?php

require_once 'model.php';

class Auditorium extends Model
{
    protected $table = 'auditoriums';
    protected $fillable = [
        'name',
        'seats_rows',
        'seats_per_row',
    ];
}
