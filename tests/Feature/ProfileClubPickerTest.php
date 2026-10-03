<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileClubPickerTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_renders_club_picker_with_available_club_and_current_selection(): void
    {
        Club::create([
            'name' => 'Testa motoklubs',
            'logo_path' => 'clubs/testa-motoklubs.png',
        ]);

        $user = User::factory()->create([
            'surname' => 'Testētājs',
            'club' => 'Testa motoklubs',
            'category' => 'MX 125',
            'experience_level' => 'Amatieris',
        ]);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('id="club-picker-dialog"', false)
            ->assertSee('data-club-value="Testa motoklubs"', false)
            ->assertSee('aria-pressed="true"', false);
    }
}
