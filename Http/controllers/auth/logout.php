<?php

auth_session_bootstrap();
auth_logout();
redirect(blog_url('login'));
