<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\Analysis\StoreAnalysisRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Entry;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StoreAnalysisRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $departamento;
    protected $socioRole;
    protected $empleadoRole;
    protected $user;
    protected $socio;
    protected $empleado;
    protected $entrada1;
    protected $entrada2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        // Luego crea los roles
        $this->socioRole = Role::create(['name' => 'Socio']);
        $this->empleadoRole = Role::create(['name' => 'Empleado']);

        // Crear el usuario primero
        $this->user = User::factory()->create();

        // Crear el departamento
        $this->departamento = Department::create(['name' => 'Laboratorio']);

        // Crear el empleado
        $this->empleado = Employee::create([
            'user_id' => $this->user->id,
            'department_id' => $this->departamento->id
        ]);

        // Crear el socio
        $this->socio = Member::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Crear la entrada
        $this->entrada1 = Entry::factory()->create(['member_id' => $this->socio->id]);
        $this->entrada2 = Entry::factory()->create(['member_id' => $this->socio->id]);
    }


    public function test_fails_when_acidity_is_negative()
    {
        $data = [
            'analysis_date' => $this->entrada1->entry_date,
            'acidity'       => '-10',
            'humidity'      => '10',
            'yield'         => '10',
            'entry_id'      => $this->entrada1->id,
            'member_id' => $this->socio->id,
            'employee_id' => $this->empleado->id,
            'oil_id'        => '',
        ];

        $request = new StoreAnalysisRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('acidity', $validator->errors()->toArray());
    }

    public function test_passes_when_acidity_is_valid()
    {
        $data = [
            'analysis_date' => $this->entrada1->entry_date,
            'acidity'       => '10',
            'humidity'      => '10',
            'yield'         => '10',
            'entry_id'      => $this->entrada2->id,
            'member_id' => $this->socio->id,
            'employee_id' => $this->empleado->id,
            'oil_id'        => '',
        ];

        $request = new StoreAnalysisRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }
}
