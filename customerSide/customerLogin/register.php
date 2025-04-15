<?php
// Include your database connection code here
require_once '../config.php';
session_start();

// Define variables and initialize them to empty values
$email = $member_name = $password = $phone_number = "";
$email_err = $member_name_err = $password_err = $phone_number_err = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email. Ex: johndoe@email.com";
    } else {
        $email = trim($_POST["email"]);
    }

    // Check if email already exists
    $selectCreatedEmail = "SELECT email FROM Accounts WHERE email = ?";
    if ($stmt = $link->prepare($selectCreatedEmail)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $email_err = "This email is already registered.";
        }
        $stmt->close();
    } else {
        die("Error preparing statement: " . $link->error);
    }

    // Validate member name
    if (empty(trim($_POST["member_name"]))) {
        $member_name_err = "Please enter your member name.";
    } else {
        $member_name = trim($_POST["member_name"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT); // Hash the password
    }

    // Validate phone number
    if (empty(trim($_POST["phone_number"]))) {
        $phone_number_err = "Please enter your phone number.";
    } elseif (!is_numeric(trim($_POST["phone_number"]))) {
        $phone_number_err = "Only enter numeric values!";
    } else {
        $phone_number = trim($_POST["phone_number"]);
    }

    // Check input errors before inserting into the database
    if (empty($email_err) && empty($member_name_err) && empty($password_err) && empty($phone_number_err)) {
        // Start a transaction
        mysqli_begin_transaction($link);

        try {
            // Insert into Accounts table
            $sql_accounts = "INSERT INTO Accounts (email, password, phone_number, register_date) VALUES (?, ?, ?, NOW())";
            if ($stmt_accounts = mysqli_prepare($link, $sql_accounts)) {
                mysqli_stmt_bind_param($stmt_accounts, "sss", $email, $password, $phone_number);

                if (mysqli_stmt_execute($stmt_accounts)) {
                    $last_account_id = mysqli_insert_id($link);

                    // Insert into Memberships table
                    $sql_memberships = "INSERT INTO Memberships (member_name, points, account_id) VALUES (?, ?, ?)";
                    if ($stmt_memberships = mysqli_prepare($link, $sql_memberships)) {
                        $points = 0; // Initial points
                        mysqli_stmt_bind_param($stmt_memberships, "sii", $member_name, $points, $last_account_id);

                        if (mysqli_stmt_execute($stmt_memberships)) {
                            // Commit the transaction
                            mysqli_commit($link);

                            // Redirect to success page
                            header("location: register_process.php");
                            exit;
                        } else {
                            throw new Exception("Error inserting into Memberships: " . mysqli_stmt_error($stmt_memberships));
                        }
                    } else {
                        throw new Exception("Error preparing Memberships statement: " . mysqli_error($link));
                    }
                } else {
                    throw new Exception("Error inserting into Accounts: " . mysqli_stmt_error($stmt_accounts));
                }
            } else {
                throw new Exception("Error preparing Accounts statement: " . mysqli_error($link));
            }
        } catch (Exception $e) {
            // Rollback the transaction on error
            mysqli_rollback($link);
            echo "Transaction failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: black;
            background-image: url('../image/loginBackground.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: white;
        }
        .register-container {
            padding: 50px;
            border-radius: 10px;
            margin: 100px auto;
            max-width: 500px;
        }
        .register_wrapper {
            width: 400px;
            padding: 20px;
        }
        h2 {
            text-align: center;
            font-family: 'Montserrat', serif;
        }
        p {
            font-family: 'Montserrat', serif;
        }
        .form-group {
            margin-bottom: 15px;
        }
        ::placeholder {
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register_wrapper">
            <a class="nav-link" href="../home/home.php#hero">
                <h1 class="text-center" style="font-family:Copperplate; color:white;">JOHNNY'S</h1>
            </a><br>
            <form action="register.php" method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" placeholder="Enter Email">
                    <span class="text-danger"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Member Name</label>
                    <input type="text" name="member_name" class="form-control" placeholder="Enter Member Name">
                    <span class="text-danger"><?php echo $member_name_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter Password">
                    <span class="text-danger"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" placeholder="Enter Phone Number">
                    <span class="text-danger"><?php echo $phone_number_err; ?></span>
                </div>
                <button style="background-color:black;" class="btn btn-dark" type="submit" name="register" value="Register">Register</button>
            </form>
            <p style="margin-top:1em; color:white;">Already have an account? <a href="../customerLogin/login.php">Proceed to Login</a></p>
        </div>
    </div>
</body>
</html>