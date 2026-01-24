<!DOCTYPE html>
<html>
<head>
    <title>New Team Registered - {{$teamName}}</title>
</head>
<body>
    <h2>A new team has registered.</h2>
    <p>Details below: </p>
    <table>
        <tr>
            <td><strong>Team Name: </strong></td>
            <td>{{$teamName}}</td>
        </tr>
        <tr>
            <td><strong>Contact Email: </strong></td>
            <td>{{$teamEmail}}</td>
        </tr>
        <tr>
            <td><strong>Contact Number: </strong></td>
            <td>{{$teamPhoneNumber}}</td>
        </tr>
    </table>
    <h2>Thanks!</h2>
</body>
</html>
