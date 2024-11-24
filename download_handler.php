<?php
session_start();
require_once 'db_connection.php'; // Create this file with your database connection

header('Content-Type: application/json');

try {
    if (!isset($_GET['type']) || !isset($_GET['id'])) {
        throw new Exception('Missing parameters');
    }

    $type = $_GET['type'];
    $id = intval($_GET['id']);
    $student_id = 3; // Replace with actual session student ID

    switch ($type) {
        case 'subject':
            // Get subject details
            $query = "
                SELECT 
                    s.subject_code,
                    s.subject_name,
                    s.description,
                    s.credits,
                    u.name as teacher_name,
                    u.email as teacher_email,
                    COUNT(a.assignment_id) as total_assignments,
                    ss.status as enrollment_status
                FROM subjects s
                INNER JOIN student_subjects ss ON s.subject_id = ss.subject_id
                INNER JOIN subject_teachers st ON s.subject_id = st.subject_id
                INNER JOIN teachers t ON st.teacher_id = t.teacher_id
                INNER JOIN users u ON t.teacher_id = u.user_id
                LEFT JOIN assignments a ON s.subject_id = a.subject_id
                WHERE s.subject_id = ? AND ss.student_id = ?
                GROUP BY s.subject_id";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $id, $student_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if (!$result) {
                throw new Exception('Subject not found');
            }

            // Generate PDF or CSV
            $filename = "subject_details_{$result['subject_code']}.csv";
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $output = fopen('php://output', 'w');
            fputcsv($output, array_keys($result));
            fputcsv($output, $result);
            fclose($output);
            break;

        case 'assignment':
            // Get assignment details
            $query = "
                SELECT 
                    a.title,
                    a.description,
                    a.due_date,
                    a.max_score,
                    s.subject_name,
                    s.subject_code,
                    COALESCE(sa.status, 'pending') as submission_status,
                    sa.submission_date,
                    sa.score
                FROM assignments a
                INNER JOIN subjects s ON a.subject_id = s.subject_id
                LEFT JOIN student_assignments sa ON a.assignment_id = sa.assignment_id 
                    AND sa.student_id = ?
                WHERE a.assignment_id = ?";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $student_id, $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if (!$result) {
                throw new Exception('Assignment not found');
            }

            $filename = "assignment_details_" . str_replace(' ', '_', $result['title']) . ".csv";
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $output = fopen('php://output', 'w');
            fputcsv($output, array_keys($result));
            fputcsv($output, $result);
            fclose($output);
            break;

        default:
            throw new Exception('Invalid download type');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
