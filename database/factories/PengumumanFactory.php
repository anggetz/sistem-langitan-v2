<?php

namespace Database\Factories;

use App\Models\Pengumuman; // Assuming your Pengumuman model is in App\Models
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon; // For handling dates

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pengumuman>
 */
class PengumumanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pengumuman::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            // 'ID_PENGUMUMAN' is an auto-incrementing primary key, so it's not set here.
            'WAKTU' => $this->faker->dateTimeThisYear(), // Generates a random date/time within the current year
            'TITLE' => $this->faker->sentence(mt_rand(3, 7)), // Generates a sentence for the title
            'DESKRIPSI' => $this->faker->paragraphs(mt_rand(2, 5), true), // Generates multiple paragraphs for description
            // 'created_at' and 'updated_at' are handled automatically by Laravel's timestamps
        ];
    }
}
