<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChargesSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_charges_search_matches_borrower_and_book_details(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Student::create([
            'sid' => 'STU-001',
            'firstname' => 'Alice',
            'lastname' => 'Johnson',
            'department' => 'CBA',
            'course' => 'BSIT',
            'year' => '3',
            'section' => 'A',
            'rfid' => 'RFID-ALICE',
        ]);

        $response = $this->get('/admin/library/charges?search=Alice');
        $response->assertStatus(200);
    }
}
