<?php

declare(strict_types=1);

csrf_verify();
auth_logout();
flash('success', 'You have been signed out.');
redirect('/');
