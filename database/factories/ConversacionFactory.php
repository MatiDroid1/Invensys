<?php

namespace Database\Factories;

use App\Models\Conversacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversacion>
 */
class ConversacionFactory extends Factory
{
    protected $model = Conversacion::class;

    public function definition(): array
    {
        return [
            'usuario_emisor_id' => User::factory(),
            'usuario_receptor_id' => User::factory(),
        ];
    }
}
