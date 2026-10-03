<?php

namespace Tests\Feature;

use App\Models\Track;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'surname' => 'Lietotājs',
            'role' => 'user',
        ]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create([
            'surname' => 'Administrators',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_admin_can_create_track_with_generated_slug(): void
    {
        $admin = User::factory()->create([
            'surname' => 'Administrators',
            'role' => 'admin',
        ]);

        $owner = User::factory()->create([
            'surname' => 'Saimnieks',
            'role' => 'owner',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.tracks.store'), [
                'name' => 'Jaunā mototrase',
                'slug' => '',
                'lat' => 56.95,
                'lng' => 24.1,
                'description' => 'Testa trase',
                'user_id' => $owner->id,
            ])
            ->assertRedirect(route('admin.tracks.index'));

        $this->assertDatabaseHas('tracks', [
            'name' => 'Jaunā mototrase',
            'slug' => 'jauna-mototrase',
            'user_id' => $owner->id,
        ]);

        $track = Track::where('slug', 'jauna-mototrase')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.tracks.update', $track), [
                'name' => 'Atjaunota mototrase',
                'slug' => 'atjaunota-mototrase',
                'lat' => 56.96,
                'lng' => 24.11,
                'description' => 'Atjaunots apraksts',
                'user_id' => $owner->id,
            ])
            ->assertRedirect(route('admin.tracks.index'));

        $this->assertDatabaseHas('tracks', [
            'id' => $track->id,
            'name' => 'Atjaunota mototrase',
            'slug' => 'atjaunota-mototrase',
        ]);

        $this->delete(route('admin.tracks.destroy', $track))
            ->assertRedirect(route('admin.tracks.index'));

        $this->assertDatabaseMissing('tracks', ['id' => $track->id]);
    }

    public function test_admin_can_create_update_and_delete_user(): void
    {
        $admin = User::factory()->create([
            'surname' => 'Administrators',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Demo',
                'surname' => 'Lietotājs',
                'email' => 'demo-user@example.com',
                'role' => 'user',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ])
            ->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'demo-user@example.com')->firstOrFail();

        $this->put(route('admin.users.update', $user), [
            'name' => 'Demo Updated',
            'surname' => 'Lietotājs',
            'email' => 'demo-user@example.com',
            'role' => 'owner',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Demo Updated',
            'role' => 'owner',
        ]);

        $this->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_can_publish_global_announcement(): void
    {
        $admin = User::factory()->create([
            'surname' => 'Administrators',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.announcements.store'), [
                'title' => 'Sistēmas paziņojums',
                'body' => 'Svarīga informācija visiem lietotājiem.',
                'expires_at' => now()->addDay()->format('Y-m-d H:i:s'),
            ])
            ->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseHas('site_announcements', [
            'user_id' => $admin->id,
            'title' => 'Sistēmas paziņojums',
        ]);

        $announcement = \App\Models\SiteAnnouncement::firstOrFail();

        $this->put(route('admin.announcements.update', $announcement), [
            'title' => 'Atjaunots sistēmas paziņojums',
            'body' => 'Atjaunota svarīga informācija.',
            'expires_at' => '',
        ])->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseHas('site_announcements', [
            'id' => $announcement->id,
            'title' => 'Atjaunots sistēmas paziņojums',
        ]);

        $this->delete(route('admin.announcements.destroy', $announcement))
            ->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseMissing('site_announcements', ['id' => $announcement->id]);
    }
}
