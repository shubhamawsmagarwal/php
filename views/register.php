<?php include 'partials/header.php'; ?>
Welcome to register page<br><br>
<form action="/register" method="POST">
Email:     <input type="email" name="username" placeholder="Email" required>
Password:  <input type="password" name="password" placeholder="Password" required>
<button class="btn btn-info btn-lg">Register</button>
</form>
<?php include 'partials/footer.php'; ?>