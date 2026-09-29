<?php

namespace Database\Factories;

use App\Models\Conversacion;
use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mensaje>
 */
class MensajeFactory extends Factory
{
    protected $model = Mensaje::class;

    public function definition(): array
    {
        return [
            'conversacion_id' => Conversacion::factory(),
            'usuario_id' => User::factory(),
            'cuerpo' => fake()->sentence(),
        ];
    }

    public function noLeido(): static
    {
        return $this->state(fn () => ['leido_en' => null]);
    }
}
