<?php

declare(strict_types=1);

/**
 * Seed demo users with known passwords.
 * Usage: php database/seed.php
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/Core/helpers.php';
require BASE_PATH . '/app/Core/Autoloader.php';

\App\Core\Autoloader::register(BASE_PATH);
loadEnv(BASE_PATH . '/.env');

use App\Core\Database;

$db = Database::getInstance();

$users = [
    [
        'name' => 'System Admin',
        'email' => 'admin@researchvault.test',
        'password' => password_hash('Admin@12345', PASSWORD_DEFAULT),
        'role' => 'admin',
    ],
    [
        'name' => 'Demo Student',
        'email' => 'student@researchvault.test',
        'password' => password_hash('Student@12345', PASSWORD_DEFAULT),
        'role' => 'student',
    ],
];

foreach ($users as $user) {
    $existing = $db->fetch('SELECT id FROM users WHERE email = :email LIMIT 1', [
        'email' => $user['email'],
    ]);

    if ($existing) {
        $db->update('users', [
            'name' => $user['name'],
            'password' => $user['password'],
            'role' => $user['role'],
            'status' => 'active',
        ], 'id = :id', ['id' => $existing['id']]);
        echo "Updated {$user['email']}\n";
    } else {
        $db->insert('users', [
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => $user['password'],
            'role' => $user['role'],
            'status' => 'active',
        ]);
        echo "Created {$user['email']}\n";
    }
}

// Sample papers (metadata only — upload real PDFs via admin)
$admin = $db->fetch('SELECT id FROM users WHERE role = :role LIMIT 1', ['role' => 'admin']);
$category = $db->fetch('SELECT id FROM categories ORDER BY id ASC LIMIT 1');

if ($admin && $category) {
    $sampleCount = (int) ($db->fetch('SELECT COUNT(*) AS total FROM papers')['total'] ?? 0);
    if ($sampleCount === 0) {
        $samples = [
            [
                'title' => 'Adaptive Learning Systems in West African Universities',
                'abstract' => "This study evaluates adaptive learning platforms across selected universities, examining engagement outcomes, assessment reliability, and implementation barriers for undergraduate STEM programmes.",
                'authors' => 'Ngozi Adeyemi, Kwame Boateng',
                'is_free' => 1,
                'price' => 0,
                'year' => 2024,
                'keywords' => 'adaptive learning, education technology, STEM',
            ],
            [
                'title' => 'Paystack-Enabled Microtransactions for Academic Content Delivery',
                'abstract' => "We propose a secure payment and access-control model for premium research documents, combining tokenized checkout, server-side verification, and download auditing for institutional repositories.",
                'authors' => 'Ibrahim Sule, Amina Diallo',
                'is_free' => 0,
                'price' => 2500.00,
                'year' => 2025,
                'keywords' => 'digital libraries, payments, access control',
            ],
            [
                'title' => 'Climate-Resilient Urban Drainage Design for Coastal Cities',
                'abstract' => "Using hydrodynamic modelling and rainfall projections, this paper presents drainage design guidelines for coastal municipalities facing increased flood frequency and intensity.",
                'authors' => 'Chinedu Okeke, Fatima Yusuf',
                'is_free' => 1,
                'price' => 0,
                'year' => 2023,
                'keywords' => 'climate resilience, drainage, urban planning',
            ],
        ];

        // Create a tiny placeholder PDF for downloads
        $placeholderPdf = "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF\n";

        foreach ($samples as $index => $sample) {
            $stored = 'papers/sample_' . ($index + 1) . '.pdf';
            $absolute = storage_path($stored);
            if (!is_dir(dirname($absolute))) {
                mkdir(dirname($absolute), 0755, true);
            }
            file_put_contents($absolute, $placeholderPdf);

            $slug = slugify($sample['title']);
            $db->insert('papers', [
                'title' => $sample['title'],
                'slug' => $slug,
                'abstract' => $sample['abstract'],
                'authors' => $sample['authors'],
                'category_id' => (int) $category['id'],
                'uploaded_by' => (int) $admin['id'],
                'file_path' => $stored,
                'file_name' => $slug . '.pdf',
                'file_size' => strlen($placeholderPdf),
                'mime_type' => 'application/pdf',
                'is_free' => $sample['is_free'],
                'price' => $sample['price'],
                'publication_year' => $sample['year'],
                'keywords' => $sample['keywords'],
                'status' => 'published',
            ]);
            echo "Seeded paper: {$sample['title']}\n";
        }
    }
}

echo "Seed complete.\n";
