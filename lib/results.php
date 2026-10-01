<?php
/**
 * Online result checking for partner schools.
 *
 * Staff upload a CSV of scores in the admin area. Each student gets a random
 * PIN; only a keyed hash of it is stored, so PINs can't be read back from the
 * database. Parents check a result with the student ID and PIN.
 */

function result_pin_hash(string $pin): string
{
    return hash_hmac('sha256', preg_replace('/\D/', '', $pin) ?? '', (string) config('app_key'));
}

function generate_pin(): string
{
    return (string) random_int(1000000000, 9999999999);
}

function grade_for(float $score): array
{
    foreach (config('grade_scale') as [$min, $grade, $remark]) {
        if ($score >= $min) {
            return [$grade, $remark];
        }
    }
    return ['F', 'Fail'];
}

/**
 * Read an uploaded CSV of results.
 *
 * Columns: student_id, student_name, class, then one column per subject
 * with the total score (0–100). Optional columns: position, remark.
 * Returns [rows, errors].
 */
function parse_results_csv(string $path): array
{
    $handle = fopen($path, 'r');
    if (!$handle) {
        return [[], ['Could not open the file.']];
    }

    $header = fgetcsv($handle);
    if (!$header) {
        return [[], ['The file is empty.']];
    }
    $header = array_map(fn ($h) => trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $h) ?? ''), $header);
    $lower = array_map('strtolower', $header);

    foreach (['student_id', 'student_name', 'class'] as $required) {
        if (!in_array($required, $lower, true)) {
            fclose($handle);
            return [[], ["Missing the \"$required\" column. The first row must contain column names."]];
        }
    }

    $special = ['student_id', 'student_name', 'class', 'position', 'remark'];
    $rows = [];
    $errors = [];
    $line = 1;

    while (($cells = fgetcsv($handle)) !== false) {
        $line++;
        if (count(array_filter($cells, fn ($c) => trim((string) $c) !== '')) === 0) {
            continue;
        }
        $record = [];
        foreach ($header as $i => $name) {
            $record[strtolower($name)] = trim((string) ($cells[$i] ?? ''));
        }

        if ($record['student_id'] === '' || $record['student_name'] === '') {
            $errors[] = "Row $line: student_id and student_name are required.";
            continue;
        }

        $subjects = [];
        foreach ($header as $i => $name) {
            if (in_array(strtolower($name), $special, true)) {
                continue;
            }
            $raw = trim((string) ($cells[$i] ?? ''));
            if ($raw === '') {
                continue;
            }
            if (!is_numeric($raw) || $raw < 0 || $raw > 100) {
                $errors[] = "Row $line: \"$name\" score must be a number from 0 to 100.";
                continue 2;
            }
            [$grade, $remark] = grade_for((float) $raw);
            $subjects[] = ['subject' => $name, 'score' => (float) $raw, 'grade' => $grade, 'remark' => $remark];
        }

        if (!$subjects) {
            $errors[] = "Row $line: no subject scores found.";
            continue;
        }

        $average = array_sum(array_column($subjects, 'score')) / count($subjects);
        $rows[] = [
            'student_ref' => strtoupper($record['student_id']),
            'student_name' => $record['student_name'],
            'class_name' => $record['class'],
            'data' => [
                'subjects' => $subjects,
                'average' => round($average, 1),
                'position' => $record['position'] ?? '',
                'remark' => ($record['remark'] ?? '') !== '' ? $record['remark'] : grade_for($average)[1],
            ],
        ];
    }

    fclose($handle);
    return [$rows, $errors];
}
