<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminRole;
    protected $socioRole;
    protected $empleadoRole;
    protected $userValid;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles
        $this->adminRole = Role::create(['name' => 'Administrador']);
        $this->socioRole = Role::create(['name' => 'Socio']);
        $this->empleadoRole = Role::create(['name' => 'Empleado']);

        // Crear usuario (contraseñ apor defecnto "Password123")
        $this->userValid = User::factory()->create([
            'username' => 'UserTest',
        ]);
    }

    public function test_login_with_valid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'UserTest',
            'password' => 'Password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'username',
                    'first_name',
                    'last_name',
                    'dni',
                    'email',
                    'phone',
                    'status',
                    'roles',
                ]
            ]);
    }

    public function test_login_with_no_valid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'UserTest',
            'password' => '123Password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Credenciales incorrectas.'
            ]);
    }

    public function test_logout_successfully()
    {
        Sanctum::actingAs($this->userValid, ['*']);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout exitoso'
            ]);

        $this->assertCount(0, $this->userValid->tokens);
    }

    public function test_logout_without_authentication()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401)
             ->assertJson([
                 'message' => 'Unauthenticated.'
             ]);
    }
}
