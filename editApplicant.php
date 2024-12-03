<?php
session_start();
include 'core/models.php'; // Include the models for database functions

// Initialize message variable
$message = '';

// Check if an ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch applicant data from the database
    $query = "SELECT * FROM applicants WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $applicant = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the applicant exists
    if (!$applicant) {
        echo "<div class='notification error'>Applicant not found!</div>";
        exit;
    }
} else {
    echo "<div class='notification error'>No applicant ID specified!</div>";
    exit;
}

// Update an applicant's data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $job_title = $_POST['job_title'];
    $experience_years = (int)$_POST['experience_years'];
    $resume_submitted = $_POST['resume_submitted'];
    $application_date = $_POST['application_date'];

    // Check if the user is logged in to track who is updating
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id']; // Get the logged-in user's ID
        $username = $_SESSION['username']; // Get the logged-in user's username
        $update_timestamp = date('Y-m-d H:i:s'); // Current timestamp for when the update occurs

        // Call the updateApplicant function
        $response = updateApplicant($id, $first_name, $last_name, $email, $phone, $job_title, $experience_years, $resume_submitted, $application_date, $user_id, $update_timestamp);

        // Check if the response is successful
        if ($response['statusCode'] == 200) {
            // Success message
            $message = '<div class="notification success">' . htmlspecialchars($response['message']) . '</div>';
        } else {
            // Error message
            $message = '<div class="notification error">' . htmlspecialchars($response['message']) . '</div>';
        }
    } else {
        $message = '<div class="notification error">You must be logged in to update the applicant details.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Applicant</title>
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
            border-radius: 5px;
            text-align: center;
        }

        .notification.success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .notification.error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
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
        <h1>Edit Applicant</h1>

        <!-- Display the message (Success or Error) -->
        <?php if (!empty($message)): ?>
            <?= $message; ?>
        <?php endif; ?>

        <!-- Applicant Edit Form -->
        <form method="POST">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($applicant['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($applicant['last_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($applicant['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($applicant['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="job_title">Job Title:</label>
                <select name="job_title" required>
                    <?php
                    $job_titles = [
                        "Head Manager", "Assistant Manager", "Senior Developer", "Junior Developer", "Employee",
                        "HR Specialist", "Marketing Specialist", "Sales Associate", "Customer Service Representative",
                        "Project Manager", "Financial Analyst", "Operations Manager", "Software Engineer",
                        "Product Manager", "UX/UI Designer", "IT Support Specialist", "Data Analyst",
                        "Quality Assurance Tester", "Office Administrator", "Intern", "Business Analyst"
                    ];

                    foreach ($job_titles as $title) {
                        $selected = $applicant['job_title'] == $title ? 'selected' : '';
                        echo "<option value=\"$title\" $selected>$title</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="experience_years">Years of Experience:</label>
                <input type="number" name="experience_years" value="<?php echo htmlspecialchars($applicant['experience_years']); ?>" required>
            </div>

            <div class="form-group">
                <label for="resume_submitted">Resume Submitted:</label>
                <select name="resume_submitted" required>
                    <option value="Yes" <?php if ($applicant['resume_submitted'] == 'Yes') echo 'selected'; ?>>Yes</option>
                    <option value="No" <?php if ($applicant['resume_submitted'] == 'No') echo 'selected'; ?>>No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="application_date">Application Date:</label>
                <input type="datetime-local" name="application_date" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($applicant['application_date']))); ?>" required>
            </div>

            <button type="submit">Update Applicant</button>
        </form>

        <a href="index.php" class="back-link">&laquo; Back to Applicants List</a>
    </div>
</body>
</html>
