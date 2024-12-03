<?php
session_start();
include 'core/models.php'; // Include models.php to handle database operations

// Initialize message variable for notifications
$message = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $experience_years = (int)$_POST['experience_years'];
    $resume_submitted = htmlspecialchars(trim($_POST['resume_submitted']));
    $application_date = $_POST['application_date'];
    $job_title = htmlspecialchars(trim($_POST['job_title']));

    // Check if user is logged in
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id']; // Get logged-in user's ID
        $username = $_SESSION['username']; // Get logged-in user's username
        $timestamp = date('Y-m-d H:i:s'); // Get the current timestamp

        // Call the createApplicant function from models.php
        $result = createApplicant(
            $first_name,
            $last_name,
            $email,
            $phone,
            $job_title,
            $experience_years,
            $resume_submitted,
            $application_date,
            $user_id,
            $timestamp // Include timestamp as the last parameter
        );

        // Check if the applicant creation was successful
        if ($result['status'] == 'success') {
            // Log the activity for adding a new applicant
            logActivity($user_id, $username, 'create', 'User added a new applicant: ' . $first_name . ' ' . $last_name);

            // Set a success message
            $message = 'Applicant has been successfully added!';
        } else {
            // Set failure message
            $message = 'There was an error adding the applicant. Please try again.';
        }
    } else {
        // Set a message if the user is not logged in
        $message = 'You must be logged in to add an applicant.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Applicant</title>
    <style>
        /* Base Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #0044cc;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        input:focus, select:focus {
            border-color: #0044cc;
            outline: none;
        }

        button {
            background-color: #0044cc;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0033a1;
        }

        .notification {
            padding: 15px;
            margin-bottom: 20px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            border-radius: 5px;
            text-align: center;
        }

        .notification-error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #0044cc;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create New Applicant</h1>

        <!-- Display notification if there's a message -->
        <?php if ($message): ?>
            <div class="notification <?php echo ($result['status'] == 'success') ? '' : 'notification-error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Applicant Creation Form -->
        <form action="createApplicant.php" method="POST">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" name="phone" required>
            </div>

            <div class="form-group">
                <label for="experience_years">Years of Experience:</label>
                <input type="number" name="experience_years" min="0" required>
            </div>

            <div class="form-group">
                <label for="resume_submitted">Resume Submitted:</label>
                <select name="resume_submitted" required>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="application_date">Application Date:</label>
                <input type="datetime-local" name="application_date" required>
            </div>

            <div class="form-group">
                <label for="job_title">Job Title:</label>
                <select name="job_title" required>
                    <option value="Head Manager">Head Manager</option>
                    <option value="Assistant Manager">Assistant Manager</option>
                    <option value="Team Leader">Team Leader</option>
                    <option value="Senior Developer">Senior Developer</option>
                    <option value="Junior Developer">Junior Developer</option>
                    <option value="Intern">Intern</option>
                </select>
            </div>

            <button type="submit">Add Applicant</button>
        </form>

        <!-- Link to go back to the applicants list -->
        <a href="index.php" class="back-link">&laquo; Back to Applicants List</a>
    </div>
</body>
</html>
