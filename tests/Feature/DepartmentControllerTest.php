<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DepartmentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminRole;
    protected $socioRole;
    protected $empleadoRole;

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

    public function test_admin_can_list_departments()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach($this->adminRole);
        Sanctum::actingAs($admin, ['*']);

        Department::create(['name' => 'Contabilidad']);
        Department::create(['name' => 'Laboratorio']);
        Department::create(['name' => 'RRHH']);

        $response = $this->getJson('/api/departments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ]
                ]
            ]);
    }

    public function test_admin_can_create_department()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach($this->adminRole);
        Sanctum::actingAs($admin, ['*']);

        $data = ['name' => 'Contabilidad'];

        $response = $this->postJson('/api/departments', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'name',
                ],
            ]);
    }

    public function test_admin_can_view_a_department()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach($this->adminRole);
        Sanctum::actingAs($admin, ['*']);

        $department = Department::create(['name' => 'RRHH']);

        $response = $this->getJson("/api/departments/{$department->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'name',
                ],
            ]);
    }

    public function test_admin_can_update_a_department()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach($this->adminRole);
        Sanctum::actingAs($admin, ['*']);

        $department = Department::create(['name' => 'Administración']);

        $response = $this->putJson("/api/departments/{$department->id}", [
            'name' => 'RRHH',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'RRHH'
                ],
            ]);
    }

    public function test_admin_can_delete_a_department()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach($this->adminRole);
        Sanctum::actingAs($admin, ['*']);

        $department = Department::create(['name' => 'Laboratorio']);

        $response = $this->deleteJson("/api/departments/{$department->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_socio_cannot_list_departments()
    {
        $socio = User::factory()->create();
        $socio->roles()->attach($this->socioRole);
        Sanctum::actingAs($socio, ['*']);

        $response = $this->getJson('/api/departments');

        $response->assertStatus(403);
    }

    public function test_socio_cannot_create_department()
    {
        $socio = User::factory()->create();
        $socio->roles()->attach($this->socioRole);
        Sanctum::actingAs($socio, ['*']);

        $data = ['name' => 'Nuevo departamento'];

        $response = $this->postJson('/api/departments', $data);

        $response->assertStatus(403);
    }

    public function test_socio_cannot_view_a_department()
    {
        $socio = User::factory()->create();
        $socio->roles()->attach($this->socioRole);
        Sanctum::actingAs($socio, ['*']);

        $department = Department::create(['name' => 'RRHH']);

        $response = $this->getJson("/api/departments/{$department->id}");

        $response->assertStatus(403);
    }

    public function test_socio_cannot_update_a_department()
    {
        $socio = User::factory()->create();
        $socio->roles()->attach($this->socioRole);
        Sanctum::actingAs($socio, ['*']);

        $department = Department::create(['name' => 'RRHH']);

        $response = $this->putJson("/api/departments/{$department->id}", [
            'name' => 'Actualizado',
        ]);

        $response->assertStatus(403);
    }

    public function test_socio_cannot_delete_a_department()
    {
        $socio = User::factory()->create();
        $socio->roles()->attach($this->socioRole);
        Sanctum::actingAs($socio, ['*']);

        $department = Department::create(['name' => 'RRHH']);

        $response = $this->deleteJson("/api/departments/{$department->id}");

        $response->assertStatus(403);
    }

    public function test_rrhh_employee_can_list_departments()
    {
        $rrhh = Department::create(['name' => 'RRHH']);

        $empleado = User::factory()->create();

        $empleado->roles()->attach($this->empleadoRole);

        $emp = Employee::create([
            'user_id' => $empleado->id,
            'department_id' => $rrhh->id
        ]);

        Sanctum::actingAs($empleado, ['*']);

        Department::create(['name' => 'Contabilidad']);

        $response = $this->getJson('/api/departments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'name']]
            ]);
    }

    public function test_rrhh_employee_can_view_a_department()
    {
        $rrhh = Department::create(['name' => 'RRHH']);

        $empleado = User::factory()->create();

        $empleado->roles()->attach($this->empleadoRole);

        $emp = Employee::create([
            'user_id' => $empleado->id,
            'department_id' => $rrhh->id
        ]);

        Sanctum::actingAs($empleado, ['*']);

        $department = Department::create(['name' => 'Contabilidad']);

        $response = $this->getJson("/api/departments/{$department->id}");

        $response->assertStatus(200)
            ->assertJson(['data' => ['name' => 'Contabilidad']]);
    }

    public function test_rrhh_employee_can_create_department()
    {
        $rrhh = Department::create(['name' => 'RRHH']);

        $empleado = User::factory()->create();

        $empleado->roles()->attach($this->empleadoRole);

        $emp = Employee::create([
            'user_id' => $empleado->id,
            'department_id' => $rrhh->id
        ]);

        Sanctum::actingAs($empleado, ['*']);

        $response = $this->postJson('/api/departments', ['name' => 'Almacén']);

        $response->assertStatus(201)
            ->assertJson(['data' => ['name' => 'Almacén']]);
    }

    public function test_rrhh_employee_can_update_department()
    {

        $rrhh = Department::create(['name' => 'RRHH']);

        $empleado = User::factory()->create();

        $empleado->roles()->attach($this->empleadoRole);

        $emp = Employee::create([
            'user_id' => $empleado->id,
            'department_id' => $rrhh->id
        ]);
        Sanctum::actingAs($empleado, ['*']);

        $department = Department::create(['name' => 'Compras']);

        $response = $this->putJson("/api/departments/{$department->id}", [
            'name' => 'Compras Actualizado',
        ]);

        $response->assertStatus(200)
            ->assertJson(['data' => ['name' => 'Compras Actualizado']]);
    }

    public function test_rrhh_employee_can_delete_department()
    {
        $rrhh = Department::create(['name' => 'RRHH']);

        $empleado = User::factory()->create();

        $empleado->roles()->attach($this->empleadoRole);

        $emp = Employee::create([
            'user_id' => $empleado->id,
            'department_id' => $rrhh->id
        ]);

        Sanctum::actingAs($empleado, ['*']);

        $department = Department::create(['name' => 'Obsoleto']);

        $response = $this->deleteJson("/api/departments/{$department->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_no_rrhh_employee_can_list_departments()
    {
        $rrhh = Department::create(['name' => 'Otro departamento']);

        $empleado = User::factory()->create();

        $empleado->roles()->attach($this->empleadoRole);

        $emp = Employee::create([
            'user_id' => $empleado->id,
            'department_id' => $rrhh->id
        ]);

        Sanctum::actingAs($empleado, ['*']);

        Department::create(['name' => 'Contabilidad']);

        $response = $this->getJson('/api/departments');

        $response->assertStatus(403);
    }

    public function test_no_rrhh_employee_can_view_a_department()
    {
        $rrhh = Department::create(['name' => 'Otro departamento']);

        $empleado = User::factory()->create();

        $empleado->roles()->attach($this->empleadoRole);

        $emp = Employee::create([
            'user_id' => $empleado->id,
            'department_id' => $rrhh->id
        ]);

        Sanctum::actingAs($empleado, ['*']);

        $department = Department::create(['name' => 'Contabilidad']);

        $response = $this->getJson("/api/departments/{$department->id}");

        $response->assertStatus(403);
    }

    public function test_no_rrhh_employee_can_create_department()
    {
        $rrhh = Department::create(['name' => 'Otro departamento']);

        $empleado = User::factory()->create();

        $empleado->roles()->attach($this->empleadoRole);

        $emp = Employee::create([
            'user_id' => $empleado->id,
            'department_id' => $rrhh->id
        ]);

        Sanctum::actingAs($empleado, ['*']);

        $response = $this->postJson('/api/departments', ['name' => 'Almacén']);

        $response->assertStatus(403);
    }

    public function test_no_rrhh_employee_can_update_department()
    {

        $rrhh = Department::create(['name' => 'Otro departamento']);

        $empleado = User::factory()->create();

        $empleado->roles()->attach($this->empleadoRole);

        $emp = Employee::create([
            'user_id' => $empleado->id,
            'department_id' => $rrhh->id
        ]);
        Sanctum::actingAs($empleado, ['*']);

        $department = Department::create(['name' => 'Compras']);

        $response = $this->putJson("/api/departments/{$department->id}", [
            'name' => 'Compras Actualizado',
        ]);

        $response->assertStatus(403);
    }

    public function test_no_rrhh_employee_can_delete_department()
    {
        $rrhh = Department::create(['name' => 'Otro departamento']);

        $empleado = User::factory()->create();

        $empleado->roles()->attach($this->empleadoRole);

        $emp = Employee::create([
            'user_id' => $empleado->id,
            'department_id' => $rrhh->id
        ]);

        Sanctum::actingAs($empleado, ['*']);

        $department = Department::create(['name' => 'Obsoleto']);

        $response = $this->deleteJson("/api/departments/{$department->id}");

        $response->assertStatus(403);
    }
}
