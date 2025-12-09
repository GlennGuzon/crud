<?php   
$servername = "localhost";
$username = "root";
$password = "";
$database = "myshop";

// Create connection
$connection = new mysqli($servername, $username, $password, $database);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$name = "";
$email = "";
$phone = "";
$address = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];

    do {
        // Trim inputs
        $name = trim($name);
        $email = trim($email);
        $phone = trim($phone);
        $address = trim($address);
        
        // Check for empty fields
        if (empty($name) || empty($email) || empty($phone) || empty($address)) {
            $errorMessage = "All the fields are required";
            break;
        }
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Please enter a valid email address";
            break;
        }
        
        // Check if email already exists
        $check_sql = "SELECT id FROM clients WHERE email = '$email'";
        $check_result = $connection->query($check_sql);
        
        if ($check_result && $check_result->num_rows > 0) {
            $errorMessage = "This email address is already registered. Please use a different email.";
            break;
        }

        // Escape special characters to prevent SQL injection
        $name = $connection->real_escape_string($name);
        $email = $connection->real_escape_string($email);
        $phone = $connection->real_escape_string($phone);
        $address = $connection->real_escape_string($address);

        // Add new client to database
        $sql = "INSERT INTO clients (name, email, phone, address) 
                VALUES ('$name', '$email', '$phone', '$address')";
        
        $result = $connection->query($sql);

        if (!$result) {
            // Check for duplicate entry error
            if ($connection->errno == 1062) { // MySQL duplicate entry error code
                $errorMessage = "This email address is already registered. Please use a different email.";
            } else {
                $errorMessage = "Error: " . $connection->error;
            }
            break;
        }

        // Reset form fields
        $name = "";
        $email = "";
        $phone = "";
        $address = "";

        $successMessage = "Client added successfully";

        header("location: /myshop/index.php");
        exit; 

    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shop - Add Client</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>  
</head>
<body>
    <div class="container my-5">
        <h2>New Client</h2>

        <?php
        if (!empty($errorMessage)) {
            echo "
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <strong>Error:</strong> $errorMessage
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
            ";
        }
        
        if (!empty($successMessage)) {
            echo "
            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                <strong>Success:</strong> $successMessage
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
            ";
        }
        ?>

        <form method="post">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Name *</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Email *</label>
                <div class="col-sm-6">
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Phone *</label>
                <div class="col-sm-6">
                    <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Address *</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($address); ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="/myshop/index.php" role="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>