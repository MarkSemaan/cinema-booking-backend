<?php

require_once __DIR__ . '/seeder.php';
require_once __DIR__ . '/../models/Auditorium.php';

class AuditoriumSeeder extends Seeder
{
    public function run(): void
    {
        echo "Seeding Auditoriums...\n";
        $auditoriumModel = new Auditorium();
        $auditorium1_id = $auditoriumModel->create([
            'name' => 'Grand Hall 1',
            'seats_rows' => 10,
            'seats_per_row' => 15
        ]);
        $auditorium2_id = $auditoriumModel->create([
            'name' => 'Cozy Corner 2',
            'seats_rows' => 6,
            'seats_per_row' => 8
        ]);
        echo "Created Auditoriums with IDs: $auditorium1_id, $auditorium2_id\n\n";
    }
}