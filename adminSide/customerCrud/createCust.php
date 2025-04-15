<?php
session_start(); // Ensure session is started
?>
<?php include '../inc/dashHeader.php'; ?>
<?php
// Include config file
require_once "../config.php";

// Define variables and initialize them
$member_id = $member_name = $points = $account_id = $email = $register_date = $phone_number = $password = "";
$member_id_err = $member_name_err = $points_err = $account_id_err = $email_err = $register_date_err = $phone_number_err = $password_err = "";

// Function to get the next available account ID
function getNextAvailableAccountID($conn) {
    $sql = "SELECT MAX(account_id) as max_account_id FROM Accounts";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return isset($row['max_account_id']) ? $row['max_account_id'] + 1 : 1; // Default to 1 if no accounts exist
}

// Function to get the next available Member ID
function getNextAvailableMemberID($conn) {
    $sql = "SELECT MAX(member_id) as max_member_id FROM Memberships";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return isset($row['max_member_id']) ? $row['max_member_id'] + 1 : 1; // Default to 1 if no members exist
}

// Get the next available Member ID
$next_member_id = getNextAvailableMemberID($link);

// Get the next available account ID
$next_account_id = getNextAvailableAccountID($link);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $member_name = filter_input(INPUT_POST, 'member_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $points = filter_input(INPUT_POST, 'points', FILTER_VALIDATE_INT);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $register_date = $_POST['register_date'];
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Check for errors
    if (empty($member_name)) {
        $member_name_err = "Please enter a valid member name.";
    }
    if ($points === false || $points < 0) {
        $points_err = "Please enter a valid number of points.";
    }
    if ($email === false) {
        $email_err = "Please enter a valid email address.";
    }
    if (empty($register_date)) {
        $register_date_err = "Please select a valid register date.";
    }
    if (empty($phone_number)) {
        $phone_number_err = "Please enter a valid phone number.";
    }

    // If no errors, insert data into the database
    if (empty($member_name_err) && empty($points_err) && empty($email_err) && empty($register_date_err) && empty($phone_number_err)) {
        // Insert into Accounts table
        $insertAccountSQL = "INSERT INTO Accounts (account_id, email, register_date, phone_number, password) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $insertAccountSQL)) {
            mysqli_stmt_bind_param($stmt, "issss", $next_account_id, $email, $register_date, $phone_number, $password);
            if (mysqli_stmt_execute($stmt)) {
                // Insert into Memberships table
                $insertMemberSQL = "INSERT INTO Memberships (member_id, member_name, points, account_id) VALUES (?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($link, $insertMemberSQL)) {
                    mysqli_stmt_bind_param($stmt, "isii", $next_member_id, $member_name, $points, $next_account_id);
                    if (mysqli_stmt_execute($stmt)) {
                        // Redirect to success page
                        header("Location: success_createMembership.php");
                        exit();
                    } else {
                        echo "Error inserting membership: " . mysqli_error($link);
                    }
                }
            } else {
                echo "Error inserting account: " . mysqli_error($link);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<head>
    <meta charset="UTF-8">
    <title>Create New Membership</title>
    <style>
        .wrapper { width: 1300px; padding-left: 200px; padding-top: 80px; }
    </style>
</head>

<div class="wrapper">
    <h3>Create New Membership</h3>
    <p>Please fill in Membership Information</p>

    <form method="POST" action="" class="ht-600 w-50">
        <div class="form-group">
            <label for="member_id" class="form-label">Member ID:</label>
            <input type="number" name="member_id" class="form-control" id="member_id" value="<?php echo $next_member_id; ?>" readonly>
        </div>
        
        <div class="form-group">
            <label for="member_name" class="form-label">Member Name:</label>
            <input type="text" name="member_name" class="form-control <?php echo $member_name_err ? 'is-invalid' : ''; ?>" id="member_name" value="<?php echo $member_name; ?>" required>
            <div class="invalid-feedback"><?php echo $member_name_err; ?></div>
        </div>

        <div class="form-group">
            <label for="points">Points:</label>
            <input type="number" name="points" class="form-control <?php echo $points_err ? 'is-invalid' : ''; ?>" id="points" value="<?php echo $points; ?>" required>
            <div class="invalid-feedback"><?php echo $points_err; ?></div>
        </div>

        <div class="form-group">
            <label for="account_id" class="form-label">Account ID:</label>
            <input type="number" name="account_id" class="form-control" id="account_id" value="<?php echo $next_account_id; ?>" readonly>
        </div>
        
        <div class="form-group">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" class="form-control <?php echo $email_err ? 'is-invalid' : ''; ?>" id="email" value="<?php echo $email; ?>" required>
            <div class="invalid-feedback"><?php echo $email_err; ?></div>
        </div>

        <div class="form-group">
            <label for="register_date">Register Date:</label>
            <input type="date" name="register_date" class="form-control <?php echo $register_date_err ? 'is-invalid' : ''; ?>" id="register_date" value="<?php echo $register_date; ?>" required>
            <div class="invalid-feedback"><?php echo $register_date_err; ?></div>
        </div>

        <div class="form-group">
            <label for="phone_number" class="form-label">Phone Number:</label>
            <input type="text" name="phone_number" class="form-control <?php echo $phone_number_err ? 'is-invalid' : ''; ?>" id="phone_number" value="<?php echo $phone_number; ?>" required>
            <div class="invalid-feedback"><?php echo $phone_number_err; ?></div>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" class="form-control <?php echo $password_err ? 'is-invalid' : ''; ?>" id="password" required>
        </div>
        
        <div class="form-group mb-5">
            <input type="submit" name="submit" class="btn btn-dark" value="Create Membership">
        </div>
    </form>
</div>

<?php include '../inc/dashFooter.php'; ?>