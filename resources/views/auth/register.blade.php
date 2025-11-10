<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <form method="POST" action="{{ url('/register') }}">
        @csrf
        <div>
            <label for="user_nik">NIK</label>
            <input type="text" name="user_nik" id="user_nik" required>
        </div>
        <div>
            <label for="user_fullname">Employee Name</label>
            <input type="text" name="user_fullname" id="user_fullname" required>
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label for="user_name">Username</label>
            <input type="text" name="user_name" id="user_name" required>
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div>
            <button type="submit">Register</button>
        </div>
    </form>
</body>
</html>