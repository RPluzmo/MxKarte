<?php

use App\Models\Track;
use App\Models\TrackOwnerAccessRequest;
use App\Models\User;

it('rejects a password that does not meet the shared password policy', function () {
    $this->post(route('register.store'), [
        'name' => 'Anna',
        'surname' => 'Bērziņa',
        'email' => 'anna@example.test',
        'password' => 'abcdefgh1',
        'password_confirmation' => 'abcdefgh1',
    ])->assertSessionHasErrors('password');

    $this->assertDatabaseMissing('users', ['email' => 'anna@example.test']);
});

it('allows only an administrator to approve a track-owner request', function () {
    $applicant = User::factory()->create(['role' => 'user']);
    $track = Track::create([
        'name' => 'Brīvā trase',
        'slug' => 'briva-trase',
        'lat' => 56.94600,
        'lng' => 24.10500,
    ]);
    $accessRequest = TrackOwnerAccessRequest::create([
        'user_id' => $applicant->id,
        'track_id' => $track->id,
        'message' => 'Esmu šīs trases pilnvarotais pārstāvis.',
    ]);

    $this->actingAs($applicant)
        ->put(route('admin.owner-requests.approve', $accessRequest))
        ->assertForbidden();

    $this->assertDatabaseHas('track_owner_access_requests', [
        'id' => $accessRequest->id,
        'status' => 'pending',
    ]);
    $this->assertDatabaseHas('tracks', ['id' => $track->id, 'user_id' => null]);
});

it('grants only the requested track when an administrator approves access', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $applicant = User::factory()->create(['role' => 'user']);
    $track = Track::create([
        'name' => 'Apstiprināmā trase',
        'slug' => 'apstiprinama-trase',
        'lat' => 56.94600,
        'lng' => 24.10500,
    ]);
    $otherTrack = Track::create([
        'name' => 'Cita trase',
        'slug' => 'cita-trase',
        'lat' => 56.94601,
        'lng' => 24.10501,
    ]);
    $accessRequest = TrackOwnerAccessRequest::create([
        'user_id' => $applicant->id,
        'track_id' => $track->id,
        'message' => 'Esmu šīs trases pilnvarotais pārstāvis.',
    ]);

    $this->actingAs($admin)
        ->put(route('admin.owner-requests.approve', $accessRequest))
        ->assertRedirect();

    $this->assertDatabaseHas('users', ['id' => $applicant->id, 'role' => 'owner']);
    $this->assertDatabaseHas('tracks', ['id' => $track->id, 'user_id' => $applicant->id]);
    $this->assertDatabaseHas('tracks', ['id' => $otherTrack->id, 'user_id' => null]);
    $this->assertDatabaseHas('track_owner_access_requests', [
        'id' => $accessRequest->id,
        'status' => 'approved',
        'reviewed_by' => $admin->id,
    ]);
});

it('does not allow a track owner to edit another owner’s track', function () {
    $owner = User::factory()->create(['role' => 'owner']);
    $otherOwner = User::factory()->create(['role' => 'owner']);
    $track = Track::create([
        'user_id' => $otherOwner->id,
        'name' => 'Cita saimnieka trase',
        'slug' => 'cita-saimnieka-trase',
        'lat' => 56.94600,
        'lng' => 24.10500,
    ]);

    $this->actingAs($owner)
        ->get(route('tracks.edit', $track))
        ->assertForbidden();

    $this->assertDatabaseHas('tracks', [
        'id' => $track->id,
        'name' => 'Cita saimnieka trase',
        'user_id' => $otherOwner->id,
    ]);
});

