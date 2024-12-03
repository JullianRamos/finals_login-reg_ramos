<?php
include 'core/models.php';

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$searchResult = searchApplicants($searchQuery);

if ($searchResult['statusCode'] == 200 && isset($searchResult['querySet'])) {
    echo '<table>';
    echo '<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Phone</th><th>Job Title</th><th>Actions</th></tr>';
    foreach ($searchResult['querySet'] as $applicant) {
        echo "<tr>
                <td>{$applicant['id']}</td>
                <td>{$applicant['first_name']}</td>
                <td>{$applicant['last_name']}</td>
                <td>{$applicant['email']}</td>
                <td>{$applicant['phone']}</td>
                <td>{$applicant['job_title']}</td> <!-- Display Job Title here -->
                <td>
                    <a href='editApplicant.php?id={$applicant['id']}'>Edit</a> |
                    <a href='deleteApplicant.php?id={$applicant['id']}'>Delete</a>
                </td>
              </tr>";
    }
    echo '</table>';
} else {
    echo "<p>No applicants found.</p>";
}
?>
