<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScannerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the scanner page returns a successful response and DCC TED Library is selected by default in the dropdown.
     */
    public function test_scanner_page_defaults_to_dcc_ted_library(): void
    {
        $response = $this->get('/scanner');

        $response->assertStatus(200);

        // Assert that DCC TED is selected by default
        $response->assertSee('<option value="DCC TED" selected>', false);
        $response->assertDontSee('<option value="DCC BED" selected>', false);
    }
}
