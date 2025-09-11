<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name     = htmlspecialchars($_POST['fullName']);
  $email    = htmlspecialchars($_POST['email']);
  $phone    = htmlspecialchars($_POST['phone']);
  $program  = htmlspecialchars($_POST['program']);
  $message  = htmlspecialchars($_POST['message']);

  $to       = "md@hltsltd.com";
  $subject  = "New Registration from HLTS Website";
  $body     = "Name: $name\nEmail: $email\nPhone: $phone\nProgram: $program\nMessage:\n$message";
  $headers  = "From: no-reply@hltsltd.com";

  if (mail($to, $subject, $body, $headers)) {
    echo "Thank you for registering!";
  } else {
    echo "Sorry, something went wrong.";
  }
}
?>
