<?php
session_start(); // Ensure session is started
?>
<?php include '../inc/dashHeader.php'; ?>
<?php
// Include config file
require_once "../config.php";

// Initialize variables
$staff_id_err = $staff_name_err = $role_err = $account_id_err = $email_err = $register_date_err = $phone_number_err = $password_err = "";
$staff_id = $staff_name = $role = $account_id = $email = $register_date = $phone_number = $password = "";

// Function to get the next available account ID
function getNextAvailableAccountID($conn) {
    $sql = "SELECT MAX(account_id) as max_account_id FROM Accounts";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $next_account_id = isset($row['max_account_id']) ? $row['max_account_id'] + 1 : 1; // Default to 1 if no accounts exist
    return $next_account_id;
}

// Function to get the next available Staff ID
function getNextAvailableStaffID($conn) {
    $sql = "SELECT MAX(staff_id) as max_staff_id FROM Staffs";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $next_staff_id = isset($row['max_staff_id']) ? $row['max_staff_id'] + 1 : 1; // Default to 1 if no staff exist
    return $next_staff_id;
}

// Get the next available Staff ID
$next_staff_id = getNextAvailableStaffID($link);

// Get the next available account ID
$next_account_id = getNextAvailableAccountID($link);

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $staff_id = filter_input(INPUT_POST, 'staff_id', FILTER_SANITIZE_NUMBER_INT);
    $staff_name = filter_input(INPUT_POST, 'staff_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $account_id = filter_input(INPUT_POST, 'account_id', FILTER_SANITIZE_NUMBER_INT);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $register_date = filter_input(INPUT_POST, 'register_date', FILTER_SANITIZE_STRING);
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Insert into Accounts table
    $insertAccountQuery = "INSERT INTO Accounts (account_id, email, register_date, phone_number, password) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = $link->prepare($insertAccountQuery)) {
        $stmt->bind_param("issss", $account_id, $email, $register_date, $phone_number, $password);
        if ($stmt->execute()) {
            echo "Account created successfully!<br>";
        } else {
            echo "Error creating account: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }

    // Insert into Staffs table
    $insertStaffQuery = "INSERT INTO Staffs (staff_id, staff_name, role, account_id) VALUES (?, ?, ?, ?)";
    if ($stmt = $link->prepare($insertStaffQuery)) {
        $stmt->bind_param("issi", $staff_id, $staff_name, $role, $account_id);
        if ($stmt->execute()) {
            echo "Staff created successfully!";
        } else {
            echo "Error creating staff: " . $stmt->error;
        }
        $stmt->close();
    }

    // Close the database connection
    $link->close();
}
?>
<head>
    <meta charset="UTF-8">
    <title>Create New Staff</title>
    <style>
       .wrapper { width: 1300px; padding-left: 200px; padding-top: 80px; }
    </style>
</head>

<div class="wrapper">
    <h3>Create New Staff</h3>
    <p>Please fill in the Staff Information</p>

    <form method="POST" action="" class="ht-600 w-50">

        <div class="form-group">
            <label for="staff_id" class="form-label">Staff ID:</label>
            <input min="1" type="number" name="staff_id" placeholder="1" class="form-control <?php echo $staff_id_err ? 'is-invalid' : ''; ?>" id="staff_id" required value="<?php echo $next_staff_id; ?>" readonly><br>
            <div class="invalid-feedback">
                Please provide a valid staff_id.
            </div>
        </div>

        <div class="form-group">
            <label for="staff_name">Staff Name:</label>
            <input type="text" name="staff_name" placeholder="Johnny Hatsoff" id="staff_name" required class="form-control <?php echo $staff_name_err ? 'is-invalid' : ''; ?>"><br>
            <span class="invalid-feedback"></span>
        </div>

        <div class="form-group">
            <label for="role">Role:</label>
            <input type="text" name="role" id="role" placeholder="Waiter" required class="form-control <?php echo $role_err ? 'is-invalid' : ''; ?>"><br>
            <span class="invalid-feedback"></span>
        </div>
        
        <div class="form-group">
            <label for="account_id" class="form-label">Account ID:</label>
            <input min="1" type="number" name="account_id" placeholder="99" class="form-control <?php echo $account_id_err ? 'is-invalid' : ''; ?>" id="account_id" required value="<?php echo $next_account_id; ?>" readonly><br>
            <div class="invalid-feedback">
                Please provide a valid account_id.
            </div>
        </div>
        
        <div class="form-group">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" placeholder="johnny12@dining.bar.com" class="form-control <?php echo $email_err ? 'is-invalid' : ''; ?>" id="email" required><br>
            <div class="invalid-feedback">
                Please provide a valid email.
            </div>
        </div>

        <div class="form-group">
            <label for="register_date">Register Date:</label>
            <input type="date" name="register_date" id="register_date" required class="form-control <?php echo $register_date_err ? 'is-invalid' : ''; ?>"><br>
            <div class="invalid-feedback">
                Please provide a valid register date.
            </div>
        </div>

        <div class="form-group">
            <label for="phone_number" class="form-label">Phone Number:</label>
            <input type="text" name="phone_number" placeholder="+60101231234" class="form-control <?php echo $phone_number_err ? 'is-invalid' : ''; ?>" id="phone_number" required><br>
            <div class="invalid-feedback">
                Please provide a valid phone number.
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="johnny1234@" id="password" required class="form-control <?php echo $password_err ? 'is-invalid' : ''; ?>"><br>
            <div class="invalid-feedback">
                Please provide a valid password.
            </div>
        </div>
        
        <div class="form-group mb-5">
            <input type="submit" class="btn btn-dark" value="Create Staff">
        </div>

    </form>
</div>

<?php include '../inc/dashFooter.php'; ?>