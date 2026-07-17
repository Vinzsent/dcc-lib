<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Research;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that unauthenticated users are redirected to login
     */
    public function test_research_index_redirects_when_not_authenticated()
    {
        $response = $this->get('/admin/library/research');

        $response->assertRedirect('/login');
    }

    /**
     * Test that authenticated users can access research index
     */
    public function test_authenticated_user_can_access_research_index()
    {
        // Create and authenticate a user
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)
             ->withSession(['location' => 'DCC TED'])
             ->get('/admin/library/research')
             ->assertStatus(200)
             ->assertViewIs('admin.library.research');
    }

    /**
     * Test that a research can be created
     */
    public function test_research_can_be_created()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)
             ->withSession(['location' => 'DCC TED']);

        $researchData = [
            'accession_no' => 'TEST001',
            'barcode' => 'BC001',
            'title' => 'Test Research Title',
            'author' => 'Test Author',
            'call_number' => 'CALL001',
            'location' => 'Test Location',
            'shelf_number' => 'SHELF001',
            'status' => 'Available'
        ];

        $response = $this->post('/admin/library/research', $researchData);

        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('researches', [
            'accession_no' => 'TEST001',
            'title' => 'Test Research Title',
            'author' => 'Test Author'
        ]);
    }

    /**
     * Test that a research can be updated
     */
    public function test_research_can_be_updated()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)
             ->withSession(['location' => 'DCC TED']);

        // Create a research first
        $research = Research::create([
            'accession_no' => 'TEST002',
            'title' => 'Original Title',
            'author' => 'Original Author',
            'call_number' => 'CALL002',
            'status' => 'Available'
        ]);

        // Update the research
        $updatedData = [
            'accession_no' => 'TEST002',
            'title' => 'Updated Title',
            'author' => 'Updated Author',
            'call_number' => 'CALL002-UPDATED',
            'status' => 'Available'
        ];

        $response = $this->put("/admin/library/research/{$research->accession_no}", $updatedData);

        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('researches', [
            'accession_no' => 'TEST002',
            'title' => 'Updated Title',
            'author' => 'Updated Author'
        ]);
    }

    /**
     * Test that a research can be deleted
     */
    public function test_research_can_be_deleted()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)
             ->withSession(['location' => 'DCC TED']);

        // Create a research first
        $research = Research::create([
            'accession_no' => 'TEST003',
            'title' => 'Test Research to Delete',
            'author' => 'Test Author',
            'call_number' => 'CALL003',
            'status' => 'Available'
        ]);

        // Delete the research
        $response = $this->delete("/admin/library/research/{$research->accession_no}");

        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('researches', [
            'accession_no' => 'TEST003'
        ]);
    }

    /**
     * Test validation for creating research
     */
    public function test_research_creation_validation()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)
             ->withSession(['location' => 'DCC TED']);

        // Test missing required fields
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/admin/library/research', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'accession_no',
            'title',
            'author',
            'call_number'
        ]);

        // Test duplicate accession_no
        Research::create([
            'accession_no' => 'DUPLICATE001',
            'title' => 'First Research',
            'author' => 'Author 1',
            'call_number' => 'CALL001',
            'status' => 'Available'
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/admin/library/research', [
            'accession_no' => 'DUPLICATE001',
            'title' => 'Second Research',
            'author' => 'Author 2',
            'call_number' => 'CALL002',
            'status' => 'Available'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('accession_no');
    }

    /**
     * Test DCC BED elementary school validation (simplified fields required only)
     */
    public function test_research_creation_for_dcc_bed_elementary()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)
             ->withSession(['location' => 'DCC BED Elementary']);

        // For DCC BED Elementary, only accession_no, title, author, call_number are required
        $researchData = [
            'accession_no' => 'ELEM001',
            'title' => 'Elementary Research',
            'author' => 'Elementary Author',
            'call_number' => 'ELEM_CALL001'
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/admin/library/research', $researchData);

        $response->assertJson(['success' => true]);

        // Verify the research was created with default status and no location/shelf/campus
        $this->assertDatabaseHas('researches', [
            'accession_no' => 'ELEM001',
            'title' => 'Elementary Research',
            'author' => 'Elementary Author',
            'call_number' => 'ELEM_CALL001',
            'status' => 'Available',
            'location' => null,
            'shelf_number' => null,
            'campus' => null
        ]);
    }
}
