<?php

require_once 'model.php';

class Showtime extends Model
{
    protected $table = 'showtimes';
    protected $fillable = [
        'movie_id',
        'auditorium_id',
        'start_time',
    ];
}
