<?php
include 'core/dbConfig.php';

function createApplicant($first_name, $last_name, $email, $phone, $job_title, $experience_years, $resume_submitted, $application_date, $user_id, $timestamp) {
    global $pdo;

    try {
        // SQL query to insert the new applicant
        $query = "INSERT INTO applicants (first_name, last_name, email, phone, job_title, experience_years, resume_submitted, application_date, created_by, created_at) 
                  VALUES (:first_name, :last_name, :email, :phone, :job_title, :experience_years, :resume_submitted, :application_date, :user_id, :timestamp)";
        
        $stmt = $pdo->prepare($query);

        // Execute the query with the passed values
        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':phone' => $phone,
            ':job_title' => $job_title,
            ':experience_years' => $experience_years,
            ':resume_submitted' => $resume_submitted,
            ':application_date' => $application_date,
            ':user_id' => $user_id,
            ':timestamp' => $timestamp
        ]);

        // Check if the insert was successful
        if ($stmt->rowCount() > 0) {
            return ['status' => 'success', 'message' => 'Applicant added successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Error adding applicant'];
        }

    } catch (PDOException $e) {
        // Catch any PDO exceptions and return a detailed error message
        return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
}



// READ function - Get all applicants or search applicants based on a search query
function searchApplicants($user_id, $username, $searchQuery = '') {
    global $pdo;

    try {
        // Modified query to search by job title and other attributes
        $query = "SELECT * FROM applicants WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ? OR job_title LIKE ?";

        $stmt = $pdo->prepare($query);
        $stmt->execute(['%' . $searchQuery . '%', '%' . $searchQuery . '%', '%' . $searchQuery . '%', '%' . $searchQuery . '%', '%' . $searchQuery . '%']);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Log the search activity
        logActivity($user_id, $username, 'search', 'Search performed with query: ' . $searchQuery);

        return [
            'message' => 'Search successful.',
            'statusCode' => 200,
            'querySet' => $result
        ];
    } catch (PDOException $e) {
        return [
            'message' => 'Search failed: ' . $e->getMessage(),
            'statusCode' => 400
        ];
    }
}

// UPDATE function - Update an existing applicant
function updateApplicant($id, $first_name, $last_name, $email, $phone, $job_title, $experience_years, $resume_submitted, $application_date, $user_id, $update_timestamp) {
    global $pdo;

    // Assuming you store the logged-in user's username in the session
    $username = $_SESSION['username']; // Get the username from the session
    
    try {
        $query = "UPDATE applicants SET first_name = ?, last_name = ?, email = ?, phone = ?, job_title = ?, experience_years = ?, resume_submitted = ?, application_date = ? WHERE id = ?";
        $stmt = $pdo->prepare($query);

        // Debugging: Log the values before executing
        error_log("Executing query with values: $first_name, $last_name, $email, $phone, $job_title, $experience_years, $resume_submitted, $application_date, $id");

        $stmt->execute([$first_name, $last_name, $email, $phone, $job_title, $experience_years, $resume_submitted, $application_date, $id]);

        // Log the activity (Applicant updated)
        logActivity($user_id, $username, 'update', 'Applicant updated: ' . $first_name . ' ' . $last_name);

        return [
            'message' => 'Applicant updated successfully.',
            'statusCode' => 200
        ];
    } catch (PDOException $e) {
        return [
            'message' => 'Failed to update applicant: ' . $e->getMessage(),
            'statusCode' => 400
        ];
    }
}




// DELETE function - Delete an applicant
function deleteApplicant($user_id, $username, $id) {
    global $pdo;

    try {
        $query = "DELETE FROM applicants WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);

        // Log the activity (Applicant deleted)
        logActivity($user_id, $username, 'delete', 'Applicant deleted with ID: ' . $id);

        return [
            'message' => 'Applicant deleted successfully.',
            'statusCode' => 200
        ];
    } catch (PDOException $e) {
        return [
            'message' => 'Failed to delete applicant: ' . $e->getMessage(),
            'statusCode' => 400
        ];
    }
}

// Function to log user activity
function logActivity($user_id, $username, $action, $description) {
    global $pdo;  // Assuming you are using PDO for database connection

    // Validate if the user_id exists in the user_accounts table
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_accounts WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user_exists = $stmt->fetchColumn();

    if ($user_exists) {
        // If the user exists, proceed with inserting into activity_logs
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, username, action, description, timestamp) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $username, $action, $description]);
    } else {
        // Log the error if the user_id does not exist
        error_log("Invalid user_id: $user_id. Activity log could not be created.");
    }
}



// USER AUTHENTICATION (Login and Registration)

function registerUser($username, $first_name, $last_name, $password) {
    global $pdo;

    // Check if the username already exists
    $query = "SELECT * FROM user_accounts WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        return "Username already exists.";
    }

    // Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $query = "INSERT INTO user_accounts (username, first_name, last_name, password) VALUES (:username, :first_name, :last_name, :password)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->execute();

    return "User registered successfully.";
}

function loginUser($username, $password) {
    global $pdo;

    // Check if the user exists
    $query = "SELECT * FROM user_accounts WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // User is authenticated, return user data
        return $user;
    } else {
        return "Invalid username or password.";
    }
}


function getActivityLogs($user_id) {
    global $pdo; // Assuming you're using PDO for database connection

    // Prepare SQL to get activity logs for the specific user
    $stmt = $pdo->prepare("SELECT * FROM activity_logs WHERE user_id = :user_id ORDER BY timestamp DESC");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch all the logs as an associative array
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


?>
