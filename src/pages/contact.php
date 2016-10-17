---
title: Contact
---
<?php
if (!isset($_POST)) {
    header("location: contact.html");
    return;
}

$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_ENCODED);

if (!isset($post['email'])) {
    header("location: contact.html");
    return;
}

$from = '';
if (isset($post['firstName'])) {
    $from .= $post['firstName'];
}

if (isset($post['lastName'])) {
    $from .= (strlen($from) > 0) ? ' ' . $post['lastName'] : $post['lastName'];
}

$from .= (strlen($from) > 0) ? "<{$post['email']}>" : $post['email'];

$to      = 'sponsorship@north-foundation.org';
$subject = $post['subject'];
$message = $post['message'];
$headers = 'From:' . $from . "\r\n" .
    'Reply-To: ' . $from . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
?>
<div class="registration-page">
    <div class="background">
        <div class="mask1">
            <div class="mask2">
                {{> navigation }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-10 small-centered">
            <h1>Contact<small>.</small></h1>
            <p>Thank you for contacting us, we will reply to your inquiry within the next 24-48 hours.</p>
        </div>
    </div>

</div>