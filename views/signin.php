<?php include 'partials/header.php'; ?>
Welcome to signin page<br><br>
<form action="/signin" method="POST">
Email:     <input type="email" name="username" placeholder="Email" required>
Password:  <input type="password" name="password" placeholder="Password" required>
<button class="btn btn-info btn-lg">Signin</button>
</form>
<?php include 'partials/footer.php'; ?>