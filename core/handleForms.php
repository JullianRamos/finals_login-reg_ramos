<?php
session_start();
include 'core/models.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    echo json_encode(['message' => 'User is not logged in.', 'statusCode' => 403]);
    exit();
}

// Get the logged-in user's details from the session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Handle Create
if (isset($_POST['createApplicant'])) {
    $first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
    $last_name = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $job_title = filter_var($_POST['job_title'], FILTER_SANITIZE_STRING);
    $experience_years = filter_var($_POST['experience_years'], FILTER_VALIDATE_INT);
    $resume_submitted = filter_var($_POST['resume_submitted'], FILTER_SANITIZE_STRING);
    $application_date = $_POST['application_date'];

    if ($experience_years === false || !$first_name || !$last_name || !$email || !$phone || !$job_title) {
        echo json_encode(['message' => 'Invalid input data. Please check the fields.', 'statusCode' => 400]);
        exit();
    }

    // Ensure the data is sanitized and valid before inserting
    $response = createApplicant($user_id, $username, $first_name, $last_name, $email, $phone, $job_title, $experience_years, $resume_submitted, $application_date);
    echo json_encode($response);
}

// Handle Update
if (isset($_POST['updateApplicant'])) {
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
    $last_name = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $job_title = filter_var($_POST['job_title'], FILTER_SANITIZE_STRING);
    $experience_years = filter_var($_POST['experience_years'], FILTER_VALIDATE_INT);
    $resume_submitted = filter_var($_POST['resume_submitted'], FILTER_SANITIZE_STRING);
    $application_date = $_POST['application_date'];

    if ($experience_years === false || !$first_name || !$last_name || !$email || !$phone || !$job_title || !$id) {
        echo json_encode(['message' => 'Invalid input data. Please check the fields.', 'statusCode' => 400]);
        exit();
    }

    // Ensure the data is sanitized and valid before updating
    $response = updateApplicant($user_id, $username, $id, $first_name, $last_name, $email, $phone, $job_title, $experience_years, $resume_submitted, $application_date);
    echo json_encode($response);
}

// Handle Delete
if (isset($_POST['deleteApplicant'])) {
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    if ($id === false) {
        echo json_encode(['message' => 'Invalid applicant ID.', 'statusCode' => 400]);
        exit();
    }

    $response = deleteApplicant($user_id, $username, $id);
    echo json_encode($response);
}
?>
