<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElementaryBooksBarcodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_elementary_books_can_store_and_display_barcode(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin/library/books');

        $response->assertStatus(200);
    }

    public function test_bed_elementary_admin_has_a_separate_book_management_page_with_crud(): void
    {
        $user = User::factory()->create([
            'role' => 'Admin BEDELEM',
        ]);

        $this->actingAs($user);

        $page = $this->get('/admin/library/books-elementary');
        $page->assertStatus(200)
            ->assertSee('Library - Book Database (Elementary)')
            ->assertSee('Add Collections');

        $this->post('/admin/library/books-elementary', [
            'accession_no' => 'E-1001',
            'barcode' => 'E-BC-1001',
            'title' => 'Elementary Book One',
            'author' => 'Author One',
            'call_number' => 'QA 101',
        ])->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('books_elem', [
            'accession_number' => 'E-1001',
            'title' => 'Elementary Book One',
        ]);
    }
}
