<?php

require_once 'model.php';

class Movie extends Model
{
    protected $table = 'movies';
    protected $fillable = [
        'title',
        'director',
        'release_year',
        'genre',
        'synopsis',
        'poster_url'
    ];
}
