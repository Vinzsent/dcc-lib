<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithUpserts;

class StudentsImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithUpserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $courseGrade = $row['coursegrade'] ?? $row['course_grade'] ?? '';

        $course = 'N/A';
        $grade = null;

        if (stripos($courseGrade, 'Grade') !== false) {
            $grade = $courseGrade;
        } else {
            $course = $courseGrade ?: 'N/A';
        }

        return new Student([
            'rfid'       => $row['rfid'] ?? null,
            'sid'        => $row['sid'],
            'firstname'  => $row['firstname'],
            'middlename' => $row['middlename'] ?? null,
            'lastname'   => $row['lastname'],
            'course'     => $course,
            'grade'      => $grade,
            'section'    => $row['section'] ?? null,
            'year'       => $row['year'] ?? 'N/A',
            'campus'     => $row['campus'] ?? 'DCC Main',
        ]);
    }

    public function uniqueBy()
    {
        return 'sid';
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
