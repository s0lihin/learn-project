<?php include '../inc/dashHeader.php'; ?>
<?php
// Include config file
require_once "../config.php";

// Initialize variables and error messages
$account_id = $email = $register_date = $phone_number = $password = "";
$account_id_err = $email_err = $register_date_err = $phone_number_err = $password_err = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Account ID
    if (empty(trim($_POST["account_id"]))) {
        $account_id_err = "Please enter an Account ID.";
    } elseif (!ctype_digit($_POST["account_id"])) {
        $account_id_err = "Account ID must be a positive integer.";
    } else {
        $account_id = trim($_POST["account_id"]);
    }

    // Validate Email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate Register Date
    if (empty(trim($_POST["register_date"]))) {
        $register_date_err = "Please enter a register date.";
    } else {
        $register_date = trim($_POST["register_date"]);
    }

    // Validate Phone Number
    if (empty(trim($_POST["phone_number"]))) {
        $phone_number_err = "Please enter a phone number.";
    } elseif (!preg_match("/^\+?[0-9]{10,15}$/", trim($_POST["phone_number"]))) {
        $phone_number_err = "Please enter a valid phone number.";
    } else {
        $phone_number = trim($_POST["phone_number"]);
    }

    // Validate Password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT); // Hash the password
    }

    // Check for errors before inserting into database
    if (empty($account_id_err) && empty($email_err) && empty($register_date_err) && empty($phone_number_err) && empty($password_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO Accounts (account_id, email, register_date, phone_number, password) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $link->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("issss", $param_account_id, $param_email, $param_register_date, $param_phone_number, $param_password);

            // Set parameters
            $param_account_id = $account_id;
            $param_email = $email;
            $param_register_date = $register_date;
            $param_phone_number = $phone_number;
            $param_password = $password;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to success page
                header("location: success_create_staff_Account.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $link->close();
}
?>
<head>
    <meta charset="UTF-8">
    <title>Create New Account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper { width: 1300px; padding-left: 200px; padding-top: 80px; }
    </style>
</head>

<div class="wrapper">
    <h1>Johnny's Dining & Bar</h1>
    <h3>Create New Account</h3>
    <p>Please fill in Account Information Properly</p>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="ht-600 w-50">
        <div class="form-group">
            <label for="account_id" class="form-label">Account ID:</label>
            <input min=1 type="number" name="account_id" placeholder="99" class="form-control <?php echo !empty($account_id_err) ? 'is-invalid' : ''; ?>" id="account_id" required value="<?php echo $account_id; ?>">
            <div id="validationServerFeedback" class="invalid-feedback">
                <?php echo $account_id_err; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="email" class="form-label">Email :</label>
            <input type="text" name="email" placeholder="johnny12@dining.bar.com" class="form-control <?php echo !empty($email_err) ? 'is-invalid' : ''; ?>" id="email" required value="<?php echo $email; ?>">
            <div id="validationServerFeedback" class="invalid-feedback">
                <?php echo $email_err; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="register_date">Register Date :</label>
            <input type="date" name="register_date" id="register_date" required class="form-control <?php echo !empty($register_date_err) ? 'is-invalid' : ''; ?>" value="<?php echo $register_date; ?>">
            <div id="validationServerFeedback" class="invalid-feedback">
                <?php echo $register_date_err; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="phone_number" class="form-label">Phone Number:</label>
            <input type="text" name="phone_number" placeholder="+60101231234" class="form-control <?php echo !empty($phone_number_err) ? 'is-invalid' : ''; ?>" id="phone_number" required value="<?php echo $phone_number; ?>">
            <div id="validationServerFeedback" class="invalid-feedback">
                <?php echo $phone_number_err; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password :</label>
            <input type="password" name="password" placeholder="johnny1234@" id="password" required class="form-control <?php echo !empty($password_err) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
            <div id="validationServerFeedback" class="invalid-feedback">
                <?php echo $password_err; ?>
            </div>
        </div>
        
        <div class="form-group">
            <input type="submit" name="submit" class="btn btn-dark" value="Create Account">
        </div>
    </form>
</div>

<?php include '../inc/dashFooter.php'; ?>