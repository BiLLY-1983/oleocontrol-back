<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\User\StoreUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreUserRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $userAdmin;
    protected $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => 'Administrador']);

        $this->userAdmin = User::factory()->create();

        $this->userAdmin->roles()->attach($this->adminRole);

        Sanctum::actingAs($this->userAdmin, ['*']);
    }

    public function test_valid_dni_passes_validation()
    {
        $data = [
            'username' => 'juan.perez.e',
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'dni' => '77342536E', // DNI en fomato y letra correctos
            'email' => 'juan@example.com',
            'phone' => '600123123',
            'status' => true,
        ];

        $request = new StoreUserRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_invalid_dni_format_fails_validation()
    {
        $data = [
            'dni' => '1234ABCZ',
        ];

        $request = new StoreUserRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey('dni', $validator->errors()->toArray());
        $this->assertEquals('El formato del DNI no es válido.', $validator->errors()->first('dni'));
    }

    public function test_invalid_dni_letter_fails_validation()
    {
        $data = [
            'dni' => '12345678A',
        ];

        $request = new StoreUserRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey('dni', $validator->errors()->toArray());
        $this->assertEquals('La letra del DNI no es válida.', $validator->errors()->first('dni'));
    }

    public function test_valid_unique_dni_passes_validation()
    {
        $existingUser = User::factory()->create([
            'dni' => '87654321Z',
        ]);

        $data = [
            'dni' => $existingUser->dni,
        ];

        $request = new StoreUserRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('dni', $validator->errors()->toArray());
        $this->assertEquals('El campo dni ya ha sido registrado.', $validator->errors()->first('dni'));
    }
}
