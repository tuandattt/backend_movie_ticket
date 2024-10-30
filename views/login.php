<!-- views/login.php -->
<form action="controllers/auth_controller.php" method="POST">
    <h2>Admin Login</h2>
    <label for="username">Username:</label>
    <input type="text" name="username" required>
    <label for="password">Password:</label>
    <input type="password" name="password" required>
    <button type="submit" name="login">Login</button>
</form>
