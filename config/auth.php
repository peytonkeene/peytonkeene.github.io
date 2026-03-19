<?php
// Simple fallback auth configuration when DB is unavailable.
return [
    'default_user' => [
        'id' => 1,
        'name' => 'Agency Admin',
        'first_name' => 'Agency',
        'last_name' => 'Admin',
        'email' => 'user@mednarrate.net',
        // Password: MedNarrate!2026
        'password_hash' => '$2y$10$W15W5QtGv4a4k4KQtCfnj.7Yi5WwdP12h9fY7J3HfG2JlvhVfOLWm',
        'role' => 'admin',
        'agency_id' => 1,
    ],
];
