<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_be_created_updated_and_deleted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['location' => 'Master']);

        $createResponse = $this->post('/admin/employee-data', [
            'eid' => 'EMP-1001',
            'rfid' => 'RFID-1001',
            'firstname' => 'Jane',
            'middlename' => 'D',
            'lastname' => 'Doe',
            'department' => 'CELA',
            'position' => 'Instructor',
            'employment_type' => 'Full-time',
        ]);

        $createResponse->assertRedirect();
        $createResponse->assertSessionHas('success', 'Employee added successfully.');

        $this->assertDatabaseHas('employees', [
            'eid' => 'EMP-1001',
            'firstname' => 'Jane',
            'lastname' => 'Doe',
        ]);

        $employee = Employee::where('eid', 'EMP-1001')->firstOrFail();

        $updateResponse = $this->put("/admin/employee-data/{$employee->id}", [
            'eid' => 'EMP-1001',
            'rfid' => 'RFID-1001',
            'firstname' => 'Janet',
            'middlename' => 'D',
            'lastname' => 'Doe',
            'department' => 'ITE',
            'position' => 'Professor',
            'employment_type' => 'Part-time',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success', 'Employee updated successfully.');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'firstname' => 'Janet',
            'position' => 'Professor',
            'department' => 'ITE',
        ]);

        $deleteResponse = $this->delete("/admin/employee-data/{$employee->id}");

        $deleteResponse->assertRedirect();
        $deleteResponse->assertSessionHas('success', 'Employee deleted successfully.');

        $this->assertDatabaseMissing('employees', [
            'id' => $employee->id,
        ]);
    }
}
