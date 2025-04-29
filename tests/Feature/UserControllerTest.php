<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $socio;
    protected $empleado;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles
        $this->adminRole = Role::create(['name' => 'Administrador']);
        $this->socioRole = Role::create(['name' => 'Socio']);
        $this->empleadoRole = Role::create(['name' => 'Empleado']);
    }

    public function test_admin_can_list_users()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach($this->adminRole);
        Sanctum::actingAs($admin, ['*']);

        User::factory()->count(3)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'username',
                        'first_name',
                        'last_name',
                        'dni',
                        'email',
                        'phone',
                        'status',
                        'roles',
                    ],
                ],
            ]);
    }

    public function test_admin_can_create_user()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach($this->adminRole);
        Sanctum::actingAs($admin, ['*']);

        $response = $this->postJson('/api/users', [
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'dni' => '12345678Z',
            'email' => 'juan@example.com',
            'phone' => '600123456',
            'user_type' => 'Socio',
            'status' => true
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'username',
                    'first_name',
                    'last_name',
                    'dni',
                    'email',
                    'phone',
                    'roles',
                ],
            ]);
    }

    public function test_admin_can_view_user_details()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach($this->adminRole);
        Sanctum::actingAs($admin, ['*']);

        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'username',
                    'first_name',
                    'last_name',
                    'dni',
                    'email',
                    'phone',
                    'status',
                    'roles',
                ],
            ]);
    }

    public function test_admin_can_update_user()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach($this->adminRole);
        Sanctum::actingAs($admin, ['*']);

        $user = User::factory()->create();

        $response = $this->putJson("/api/users/{$user->id}", [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'phone' => '700123456',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'Updated',
                    'last_name' => 'Name',
                    'phone' => '700123456',
                ],
            ]);
    }

    public function test_admin_can_delete_user()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach($this->adminRole);
        Sanctum::actingAs($admin, ['*']);

        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_user_can_view_own_profile()
    {
        $socio = User::factory()->create();
        $socio->roles()->attach($this->socioRole);
        Sanctum::actingAs($socio, ['*']);

        $response = $this->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'username',
                    'first_name',
                    'last_name',
                    'dni',
                    'email',
                    'phone',
                    'status',
                    'roles',
                ],
            ]);
    }

    public function test_user_can_update_own_profile()
    {
        $socio = User::factory()->create();
        $socio->roles()->attach($this->socioRole);
        Sanctum::actingAs($socio, ['*']);

        $response = $this->putJson('/api/profile', [
            'first_name' => 'Updated',
            'last_name' => 'Profile',
            'phone' => '700123456',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'Updated',
                    'last_name' => 'Profile',
                    'phone' => '700123456',
                ],
            ]);
    }

    public function test_socio_cannot_list_users()
    {
        $socio = User::factory()->create();
        $socio->roles()->attach($this->socioRole);
        Sanctum::actingAs($socio, ['*']);

        $response = $this->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_socio_cannot_create_user()
    {
        $socio = User::factory()->create();
        $socio->roles()->attach($this->socioRole);
        Sanctum::actingAs($socio, ['*']);

        $response = $this->postJson('/api/users', [
            'first_name' => 'Carlos',
            'last_name' => 'López',
            'dni' => '98765432X',
            'email' => 'carlos@example.com',
            'phone' => '600987654',
            'user_type' => 'Socio',
        ]);

        $response->assertStatus(403);
    }

    public function test_empleado_cannot_list_users()
    {
        $empleado = User::factory()->create();
        $empleado->roles()->attach($this->empleadoRole);
        Sanctum::actingAs($empleado, ['*']);

        $response = $this->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_empleado_cannot_create_user()
    {
        $empleado = User::factory()->create();
        $empleado->roles()->attach($this->empleadoRole);
        Sanctum::actingAs($empleado, ['*']);

        $response = $this->postJson('/api/users', [
            'first_name' => 'Luis',
            'last_name' => 'Martínez',
            'dni' => '87654321Y',
            'email' => 'luis@example.com',
            'phone' => '700123456',
            'user_type' => 'Empleado',
            'department_id' => 1,
        ]);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_list_users()
    {
        $randomUser = User::factory()->create();

        // Realizar una solicitud GET sin autenticar
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }
}
