<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Entry;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreDepartmentRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $departamento;
    protected $userAdmin;
    protected $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => 'Administrador']);

        // Crear usuario admin y autenticar
        $this->userAdmin = User::factory()->create();

        $this->userAdmin->roles()->attach($this->userAdmin);
        Sanctum::actingAs($this->userAdmin, ['*']);

        // Crear el departamento inicial
        $this->departamento = Department::create(['name' => 'Laboratorio']);
    }

    public function test_department_with_duplicate_name_cannot_be_created()
    {
        $response = $this->postJson('/api/departments', [
            'name' => 'Laboratorio',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['name']);

        $this->assertDatabaseCount('departments', 1);
    }

    public function test_department_with_not_duplicate_name_can_be_created()
    {
        $response = $this->postJson('/api/departments', [
            'name' => 'Contabilidad',
        ]);

        $response->assertStatus(201); 

        $response->assertJsonMissingValidationErrors(['name']);

        $this->assertDatabaseCount('departments', 2); 
    }
}
