<?php

auth_session_bootstrap();

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$sent = isset($_GET['sent']) && $_GET['sent'] === '1';

$values = [
  'name' => '',
  'email' => '',
  'subject' => '',
  'message' => '',
];
/** @var array<string, string> $errors */
$errors = [];

if ($method === 'POST') {
  $values['name'] = trim((string) ($_POST['name'] ?? ''));
  $values['email'] = trim((string) ($_POST['email'] ?? ''));
  $values['subject'] = trim((string) ($_POST['subject'] ?? ''));
  $values['message'] = trim((string) ($_POST['message'] ?? ''));
  $honeypot = trim((string) ($_POST['website'] ?? ''));

  if ($honeypot !== '') {
    redirect(blog_url('contact?sent=1'));
  }

  if (!auth_csrf_validate($_POST['_csrf'] ?? null)) {
    $errors['general'] = 'Your session expired. Refresh the page and try again.';
  }

  if ($values['name'] === '') {
    $errors['name'] = 'Please enter your name.';
  } elseif (mb_strlen($values['name']) > 120) {
    $errors['name'] = 'Name is too long.';
  }

  if ($values['email'] === '') {
    $errors['email'] = 'Please enter your email address.';
  } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Enter a valid email address.';
  } elseif (mb_strlen($values['email']) > 191) {
    $errors['email'] = 'Email is too long.';
  }

  if ($values['subject'] !== '' && mb_strlen($values['subject']) > 200) {
    $errors['subject'] = 'Subject is too long.';
  }

  if ($values['message'] === '') {
    $errors['message'] = 'Please enter a message.';
  } elseif (mb_strlen($values['message']) < 10) {
    $errors['message'] = 'Message should be at least 10 characters.';
  } elseif (mb_strlen($values['message']) > 8000) {
    $errors['message'] = 'Message is too long.';
  }

  if ($errors === []) {
    $last = (int) ($_SESSION['contact_last_sent_at'] ?? 0);
    if ($last > 0 && time() - $last < 45) {
      $errors['general'] = 'Please wait a short moment before sending another message.';
    }
  }

  if ($errors === []) {
    $result = blog_send_contact_mail(
      $values['name'],
      $values['email'],
      $values['subject'],
      $values['message']
    );
    if ($result['ok']) {
      $_SESSION['contact_last_sent_at'] = time();
      redirect(blog_url('contact?sent=1'));
    }
    $errors['general'] = match ($result['error'] ?? '') {
      'send_failed' => 'We could not send the email. If you use Mailtrap, check MAILTRAP_USER and MAILTRAP_PASSWORD in .env, then try again. You can also write directly to '
        . blog_contact_to_email() . '.',
      default => 'Something went wrong while sending. Please try again.',
    };
  }
}

view('contact.php', [
  'pageTitle' => 'Contact — Mini Blog',
  'metaDescription' => 'Send a message or question about Mini Blog.',
  'sent' => $sent,
  'errors' => $errors,
  'values' => $values,
  'csrfToken' => auth_csrf_token(),
]);
