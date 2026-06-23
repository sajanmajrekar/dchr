<?php
$servername = "localhost"; // Replace with your server name
$username = "digiplmk_hrcrm"; // Replace with your username
$password = "s5U@[c;?7B=k"; // Replace with your password
$dbname = "digiplmk_hrcrm"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = 'SELECT `id`,`name`,`email`,`phonenumber`,`skillset`,`resume`,`experiance`,`csalary`,`esalary`,`nperiod`,`city` FROM `tblleads` ORDER BY `tblleads`.`id` DESC;';
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Set headers to download file rather than displayed
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=leads.csv');

    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // Output the column headings
    fputcsv($output, array('ID', 'Name', 'Email', 'Phone Number', 'Skill Set', 'Resume Link', 'Experience', 'Current Salary', 'Expected Salary', 'Notice Period', 'City'));

    // Base URL for resumes
    $baseUrl = 'https://digichefs.in/hr/resume/';

    // Output each row of the data, format line as CSV and write to file pointer
    while ($row = $result->fetch_assoc()) {
        // Append the base URL to the resume filename and wrap in HTML anchor tag
        $row['resume'] = '=HYPERLINK("' . $baseUrl . $row['resume'] . '","View Resume")';
        fputcsv($output, $row);
    }
    fclose($output);
} else {
    echo "No results found.";
}

$conn->close();
?>
