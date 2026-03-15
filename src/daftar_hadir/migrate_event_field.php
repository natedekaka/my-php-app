<?php
/**
 * File: migrate_event_field.php
 * Deskripsi: Menambahkan kolom event_id ke form_fields
 */

include 'koneksi.php';

$result = $conn->query("SHOW COLUMNS FROM form_fields LIKE 'event_id'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE form_fields ADD event_id INT NULL AFTER aktif");
    $conn->query("ALTER TABLE form_fields ADD FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL");
    echo "Kolom event_id berhasil ditambahkan!";
} else {
    echo "Kolom event_id sudah ada.";
}

$conn->close();
