<?php

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
    redirect(blog_url('contact?sent=1'));
  }
}

view('contact.php', [
  'pageTitle' => 'Contact — Mini Blog',
  'metaDescription' => 'Send a message or question about Mini Blog.',
  'sent' => $sent,
  'errors' => $errors,
  'values' => $values,
]);
