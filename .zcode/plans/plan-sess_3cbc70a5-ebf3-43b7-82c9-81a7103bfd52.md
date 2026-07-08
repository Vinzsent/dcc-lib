## Goal
1. Show **all employees** on the Employee Data page regardless of which campus/location the admin logged in from.
2. Let an **employee tap their RFID at any campus's scanner** and be identified on the tapping screen (e.g., a BED employee tapping at Main/TED is recognized, logs a Time In/Out).

## Part 1 — Remove campus auto-filter on Employee Data page
**File:** `app/Http/Controllers/AdminController.php` → `employeeData()` (lines 763–768)

Remove this block:
```php
$location = session('location');
$query = Employee::query();

if ($location && $location !== 'Master') {
    $query->where('campus', $this->getCampus($location));
}
```
Replace with just `$query = Employee::query();`. Every logged-in location (Master, DCC Main, DCC BED) will now see **all** employees. Master already saw all, so this matches your "keep Master behavior, remove for others" choice.

- **Not touched:** `storeEmployee()` (line 843–845) still stamps `campus` on new employees. We keep that because `employee-logs` uses campus for reporting and it's informational — we're only removing the *display* filter.
- **Not touched:** `employee-data.blade.php` itself — it has no campus logic to remove.

## Part 2 — Add employee scanning to the tapping screen
**Approach: extend the existing `/scan` endpoint to auto-detect.** When a tap doesn't match a student, the system checks employees globally (no campus filter). This gives zero-friction "tap and identify" with no mode toggle — the best fit for your cross-campus scenario. (Alternative considered: a separate `/scan-employee` route + UI toggle — rejected as it adds operator friction and an RFID uniquely belongs to one person, so student-first-then-employee can't mis-identify.)

### File: `app/Http/Controllers/ScannerController.php`
- Add imports: `use App\Models\Employee;` and `use App\Models\EmployeeLog;`.
- In `scan()`, change the student `not found` path (currently returns 404 at ~line 132) to instead **fall through to an employee lookup**:
  - `Employee::where('eid', $sid)->orWhere('rfid', $sid)->first();` — **global, no campus/grade filter** (this is the key requirement: a BED employee tapping at Main is found).
  - If matched, run the same toggle logic as students but against `EmployeeLog`:
    - Open `EmployeeLog` (`eid` = employee.eid, `time_out` NULL) → set `time_out` = now → **Time Out**.
    - Else create a new `EmployeeLog` snapshotting `eid, campus, rfid, firstname, middlename, lastname, department, position, employment_type, time_in` → **Time In**.
  - Return JSON: `{ success, type:'employee', status:'in'|'out', message, employee, time, counts }`.
- Only if **neither** student nor employee matches → return 404 "Student/Employee not found."
- **Student scanning is unchanged** — it still applies the existing campus + grade filters (you only asked to make employees global, not students).

### File: `resources/views/scanner.blade.php`
- In the fetch handler (`performScan` / `showStudent`), branch on the payload:
  - If `data.type === 'employee'` → render an **employee display**: name `${e.firstname} ${e.lastname}`, detail string `${e.department} | ${e.position} - ${e.employment_type}`, ID = `eid`, an "Employee" label, and the same green Time In / red Time Out status badge + transaction time. Since employees have no `profile` column, always use the **initials fallback** (no photo).
  - Otherwise keep the existing student display unchanged.
- Counts tiles (`studentsInside / totalTimeIn / totalTimeOut`) remain **student-only** to avoid mixing metrics; the employee scan still returns the current counts so the UI updates normally. (If you later want employees counted in the totals, that's a quick follow-up.)

## Files modified (3)
1. `app/Http/Controllers/AdminController.php` — remove 4 lines of campus filtering.
2. `app/Http/Controllers/ScannerController.php` — add employee lookup + EmployeeLog toggle + JSON payload.
3. `resources/views/scanner.blade.php` — render employee vs student on tap.

## No new routes, migrations, or models needed
`Employee` and `EmployeeLog` models already exist with the correct fields; the `/scan` route is reused.

## Verification
- Log in as `DCC BED` location → Employee Data page now lists DCC Main employees too (previously only DCC BED).
- At a `DCC Main` scanner session, tap a BED employee's RFID → screen identifies them with Time In; tap again → Time Out. A new row appears in `employee_logs`.
- Tap an unknown RFID → "not found" message. Tap a valid student → student flow still works as before.