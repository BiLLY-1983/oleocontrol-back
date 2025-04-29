<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\Entry\StoreEntryRequest;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreEntryRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $socioRole;
    protected $user;
    protected $socio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->user->roles()->attach($this->socioRole);

        $this->socioRole = Role::create(['name' => 'Socio']);
        
        $this->socio = Member::factory()->create([
            'user_id' => $this->user->id
        ]);
    }

    public function test_fails_when_olive_quantity_is_negative()
    {
        $data = [
            'entry_date' => '2024-01-01',
            'olive_quantity' => -10,
            'oil_quantity' => '',
            'analysis_status' => 'Pendiente',
            'member_id' => $this->socio->id,
        ];

        $request = new StoreEntryRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('olive_quantity', $validator->errors()->toArray());
    }

    public function test_passes_when_olive_quantity_is_valid()
    {
        $data = [
            'entry_date' => '2024-01-01',
            'olive_quantity' => 5000,
            'oil_quantity' => '',
            'analysis_status' => 'Pendiente',
            'member_id' => $this->socio->id,
        ];

        $request = new StoreEntryRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        // Imprimir los errores si la validación falla
        if ($validator->fails()) {
            dd($validator->errors()->toArray());  // Esto imprimirá los errores de validación
        }

        $this->assertFalse($validator->fails());
    }
}
