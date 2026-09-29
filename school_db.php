<?php

$host = "localhost";
$dbname = "school_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Kreiranje baze
    $pdo->exec("CREATE DATABASE IF NOT EXISTS school_db");

    // Povezivanje sa bazom
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Kreiranje tabele students
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS students (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100),
            email VARCHAR(100)
        )
    ");

    // Kreiranje tabele courses
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS courses (
            id INT PRIMARY KEY AUTO_INCREMENT,
            course_name VARCHAR(100),
            student_id INT,
            FOREIGN KEY (student_id) REFERENCES students(id)
        )
    ");

    // 3. Unos učenika
    $students = [
        ["Amra", "amra@gmail.com"],
        ["Sara", "sara@gmail.com"],
        ["Emira", "emira@gmail.com"]
    ];

    $stmt = $pdo->prepare("
        INSERT INTO students (name, email)
        VALUES (?, ?)
    ");

    foreach ($students as $student) {
        $stmt->execute($student);
    }

    // Unos kurseva
    $courses = [
        ["PHP", 1],
        ["MySQL", 1],
        ["HTML & CSS", 2],
        ["JavaScript", 3]
    ];

    $stmt = $pdo->prepare("
        INSERT INTO courses (course_name, student_id)
        VALUES (?, ?)
    ");

    foreach ($courses as $course) {
        $stmt->execute($course);
    }

    // 4. INNER JOIN
    $stmt = $pdo->query("
        SELECT students.name, courses.course_name
        FROM students
        INNER JOIN courses
        ON students.id = courses.student_id
    ");

    echo "<h2>Učenici i kursevi</h2>";

    echo "<table border='1' cellpadding='10'>";
    echo "<tr>";
    echo "<th>Učenik</th>";
    echo "<th>Kurs</th>";
    echo "</tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["course_name"] . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    // 5. Dodavanje kolone age
    $pdo->exec("
        ALTER TABLE students
        ADD age INT
    ");

    echo "<p>Kolona age je dodana.</p>";

    // Brisanje kolone age
    $pdo->exec("
        ALTER TABLE students
        DROP COLUMN age
    ");

    echo "<p>Kolona age je obrisana.</p>";

} catch (PDOException $e) {
    echo "Greška: " . $e->getMessage();
}

// BONUS - Tabela teachers

$pdo->exec("
    CREATE TABLE IF NOT EXISTS teachers (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100),
        subject VARCHAR(100)
    )
");

// Dodavanje nastavnika
$teachers = [
    ["Marko", "PHP"],
    ["Jelena", "HTML & CSS"]
];

$stmt = $pdo->prepare("
    INSERT INTO teachers (name, subject)
    VALUES (?, ?)
");

foreach ($teachers as $teacher) {
    $stmt->execute($teacher);
}

echo "<h2>Nastavnici</h2>";

$stmt = $pdo->query("SELECT * FROM teachers");

echo "<table border='1' cellpadding='10'>";
echo "<tr>";
echo "<th>ID</th>";
echo "<th>Ime</th>";
echo "<th>Predmet</th>";
echo "</tr>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . $row["name"] . "</td>";
    echo "<td>" . $row["subject"] . "</td>";
    echo "</tr>";
}

echo "</table>";

?>