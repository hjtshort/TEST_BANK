<?php

namespace Database\Factories;

use App\Models\FileImport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FileImportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FileImport::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'path' => $this->faker->filePath(),
            'name' => Str::random(10) . '.xls',
            'state' => 2,
        ];
    }
}
