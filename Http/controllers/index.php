<?php

$message = 'Welcome to mini-blog.';

view('index.view.php', ['heading' => 'Home', 'message' => $message]);
