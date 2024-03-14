<?php

namespace Tests\Feature;

use App\Models\HolidayPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HolidayPlanTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_create_holiday_plans()
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $data = [
            "title" => "Ferias de Verão",
            "description" => "Vamos no resort mais famoso do Brasil",
            "date" => "2024-12-12",
            "location" => "Fernando de Noronha",
            "participants" => ["Pedro", "Maria"]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/holiday-plan', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Holiday plan created successfully!'
            ]);
    }

    public function test_list_holiday_plans()
    {
        self::test_create_holiday_plans();

        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/holiday-plans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'title',
                    'description',
                    'date',
                    'location',
                    'participants'
                ]
            ]);
    }

    public function test_get_holiday_plans()
    {
        $plan = HolidayPlan::factory()->create();

        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/holiday-plan/' . $plan->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'date',
                'location',
                'participants'
            ]);
    }

    public function test_update_holiday_plans()
    {

        $plan = HolidayPlan::factory()->create();

        $token = $this->user->createToken('test-token')->plainTextToken;

        $data = [
            "title" => "Ferias de Verão",
            "description" => "Vamos no resort mais famoso do Brasil",
            "date" => "2024-12-12",
            "location" => "Fernando de Noronha",
            "participants" => ["Pedro", "Bianca"]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/holiday-plan/' . $plan->id, $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Holiday plan updated successfully!'
            ]);
    }

    public function test_delete_holiday_plans()
    {
        $plan = HolidayPlan::factory()->create();

        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/holiday-plan/' . $plan->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => "Holiday plan removed successfully!"
            ]);
    }

    public function test_pdf_generate()
    {

        $plan = HolidayPlan::factory()->create();

        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/holiday-plan/' . $plan->id . '/pdf');


        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        $this->assertNotEmpty($response->getContent());

    }
}
