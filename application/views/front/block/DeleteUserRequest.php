
<!DOCTYPE html>
<html>
<head>
      <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <title>Account Deletion Request</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;font-family: "Nunito";
            margin: 0;
            background-color: #f5f5f5;
        }

        .container {
            width: 300px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
p {
    font-size: 14px;
}
h3 {
    
}
        input[type="text"],
        input[type="checkbox"],
        input[type="submit"] {
            width: calc(100% - 10px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background: linear-gradient(86deg, #ff1616, #000);
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        label {
            display: inline;
        }
    </style>
</head>
<body>
    <div class="container">
           {system_message}               
          {system_info}
        <h2>Account Deletion Request</h2>
       <?php echo form_open_multipart('DeleteUserRequest/auth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <label for="mobile_number">Mobile Number:</label>
            <input type="text" id="mobile_number" name="mobile" required><br><br>
            <input type="checkbox" id="confirmation" name="confirmation" value="confirmed" required><label for="confirmation">Please check the box if you confirm to delete your account</label><br><br>
            <input type="submit" name="submit" value="Submit">
        </form>

        <div class="note">
            <h3>Need for Account Deletion</h3>
            <p>Account deletion is a crucial feature for user privacy and control. It allows users to remove their personal data from the system when they no longer wish to use the service. This ensures compliance with data protection regulations and enhances user trust.</p>
            <p>The confirmation process and delay period are important to prevent impulsive decisions and provide users with an opportunity to reconsider. It also allows the organization to communicate with the user and ensure compliance with legal requirements.</p>
        </div>
    </div>

    </body>
</html>
