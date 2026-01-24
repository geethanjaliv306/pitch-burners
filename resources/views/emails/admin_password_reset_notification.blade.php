<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Successfully - {{ $user->name }} </title>
</head>
<body>
    <h1>Confirmation: Password Successfully Reset</h1>
    <p>The password for the following user has been successfully reset:</p>
    <p><strong>User Name:</strong> {{ $user->name }}<br>
       <strong>Email:</strong> {{ $user->email }}</p>
      <p>Thanks!</p>
</body>
</html>
