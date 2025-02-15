<?php
if (isset($_POST['contact_submit'])) {
    // Your other form fields
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $messag = $_POST['message'];

    // File Upload Handling
    $attachments = $_FILES['attachment'];

    // Check if files were uploaded
    if (empty($attachments['name'][20])) {
        $boundary = md5(time());
        $headers = "From: Travelviacoach\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

        // Compose email body
        $message = "--$boundary\r\n";
        $message .= "Content-Type: text/html; charset=iso-8859-1\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= file_get_contents("email.html");

        $variables = array(
            "{{name}}" => $name,
            "{{email}}" => $email,
            "{{phone}}" => $phone,
            "{{messag}}" => $messag,
        );

        foreach ($variables as $key => $value) {
            $message = str_replace($key, $value, $message);
        }

        // Attach each file to the email
        foreach ($attachments['name'] as $index => $attachment_name) {
            $attachment_tmp_name = $attachments['tmp_name'][$index];
            $attachment_size = $attachments['size'][$index];
            $attachment_content = file_get_contents($attachment_tmp_name);
            $attachment_content = chunk_split(base64_encode($attachment_content));
            $attachment_type = mime_content_type($attachment_tmp_name);

            $message .= "\r\n--$boundary\r\n";
            $message .= "Content-Type: $attachment_type; name=\"$attachment_name\"\r\n";
            $message .= "Content-Disposition: attachment; filename=\"$attachment_name\"\r\n";
            $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $message .= $attachment_content;
        }

        $message .= "\r\n--$boundary--";
    } else {
        // If no file was uploaded, use your original message content
        $message = file_get_contents("email.html");

        $variables = array(
            "{{name}}" => $name,
            "{{email}}" => $email,
            "{{phone}}" => $phone,
            "{{messag}}" => $messag,
        );

        foreach ($variables as $key => $value) {
            $message = str_replace($key, $value, $message);
        }
    }

    // Send email
    $to = "info@travelviacoach.com";
    $subject = "New Query";

    $email_sent = mail($to, $subject, $message, $headers);

    if ($email_sent) {
        // echo "<script>alert('Message Sent Successfully!');</script>";
        echo "<script>location='/thankyou.html'</script>";
    } else {
        echo "<script>alert('Something Went Wrong!');</script>";
        echo "<script>location='/'</script>";
    }
}
?>
