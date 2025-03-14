<?php
// session_destroy();

session_start();

if ($_GET['logout']) {
   session_destroy();
   header("Location: jems.php");
   exit;
}


if ($_POST['user'] ?? false && $_POST['password'] ?? false) {

    $user = $_POST['user'];
    $pwd = $_POST['password'];

    $username1 = 'example_user';
    $password1 = 'example_password';

    $username2 = 'example_user2';
    $password2 = 'example_password2';

    if ($user == $username1 && $pwd == $password1) {
       $_SESSION['user'] = $user;
    }

    else if ($user == $username2 && $pwd == $password2) {
       $_SESSION['user'] = $user;
    }

    else {
       echo "Username or password is unauthorized.";
       exit;
    }
}



// SQLite database file path
$databaseFile = 'jems_database.db';

// Connect to SQLite database
$db = new SQLite3($databaseFile);


/*
try {

    // Create users table
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY,
        username TEXT NOT NULL,
        password TEXT NOT NULL,
        created_time DATETIME,
        last_login DATETIME
    )");

    // Create transactions table
    $db->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INTEGER PRIMARY KEY,
        user_id INTEGER,
        amount INTEGER,
        transaction_time DATETIME,
        description TEXT,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    echo "Tables created successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
*/

if ($_SESSION['user'] ?? false) {
   echo "Hi, you are logged in as ".$_SESSION['user'];
   echo "<p><a href='jems.php?logout=1'>Logout</a></p>";

   echo "<h2>Transactions</h2>";
   $sql = "SELECT amount, transaction_time, description, SUM(amount) OVER (ORDER BY transaction_time) AS balance FROM transactions WHERE user_id = :user ORDER BY transaction_time DESC";
   $stmt = $db->prepare($sql);
   $username = $_SESSION['user'];
   $stmt->bindParam(":user", $username);
   $result = $stmt->execute();
   $transactions = [];
   while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
      $transactions[] = $row;
   }

   echo "<table width=1000><tr><th>Amount</th><th>Time</th><th>Description</th><th>Balance</th>";
   foreach ($transactions as $row) {
       echo "<tr><td>".$row['amount']."</td><td>".$row['transaction_time']."</td><td>".$row['description']."</td><td>".$row['balance']."</td></tr>";
   }
   echo "</table>";
   exit;
}

?>



<html>
  <head>
    <title>Login</title>
  </head>
<body>


<form action="jems.php" method="post">
User:    <input type="text" name="user">
<br>
Password: <input type="password" name="password">
<br><br>
<input type="submit" value="Submit">
</form>

</body>
</html>
