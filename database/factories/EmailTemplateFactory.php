<?php

namespace Maksde\FilamentEmailTemplates\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Maksde\FilamentEmailTemplates\Models\EmailTemplate;

/**
 * @extends Factory<EmailTemplate>
 */
class EmailTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = EmailTemplate::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'key' => Str::random(20),
            'language' => config('filament-email-templates.default_locale'),
            'cc' => null,
            'bcc' => null,
            'from' => ['email' => fake()->email, 'name' => fake()->name],
            'name' => fake()->name,
            'subject' => fake()->sentence,
            'content' => '<p>'.fake()->text.'</p>',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
