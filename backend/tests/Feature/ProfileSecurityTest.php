<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_chi_sua_duoc_chinh_minh_du_url_id_la_nguoi_khac(): void
    {
        $me = User::factory()->create(['name' => 'Me']);
        $victim = User::factory()->create(['name' => 'Victim']);
        Sanctum::actingAs($me);

        $this->postJson("/api/v1/user/update/{$victim->id}", [
            'name'  => 'Hacked',
            'email' => $me->email,
        ])->assertStatus(200);

        $this->assertDatabaseHas('users', ['id' => $me->id, 'name' => 'Hacked']);
        $this->assertDatabaseHas('users', ['id' => $victim->id, 'name' => 'Victim']);
    }

    public function test_khong_the_leo_quyen_admin_qua_profile(): void
    {
        $me = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($me);

        $this->postJson("/api/v1/user/update/{$me->id}", [
            'name'   => 'X',
            'email'  => $me->email,
            'role'   => 'admin',
            'status' => 'banned',
        ])->assertStatus(200);

        $this->assertDatabaseHas('users', ['id' => $me->id, 'role' => 'user', 'status' => 'active']);
    }

    public function test_khong_lo_password_trong_response(): void
    {
        $me = User::factory()->create();
        Sanctum::actingAs($me);

        $res = $this->postJson("/api/v1/user/update/{$me->id}", ['name' => 'X', 'email' => $me->email]);
        $auth = $res->json('Auth');
        $this->assertArrayNotHasKey('password', $auth);
    }

    public function test_email_trung_bi_tu_choi(): void
    {
        $other = User::factory()->create();
        $me = User::factory()->create();
        Sanctum::actingAs($me);

        $this->postJson("/api/v1/user/update/{$me->id}", [
            'name'  => 'X',
            'email' => $other->email,
        ])->assertStatus(422);
    }

    public function test_chua_dang_nhap_401(): void
    {
        $u = User::factory()->create();
        $this->postJson("/api/v1/user/update/{$u->id}", ['name' => 'X', 'email' => $u->email])->assertStatus(401);
    }
}
