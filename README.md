### Jak np używać PDO w zapytaniach

$stmt = $pdo->prepare("SELECT * FROM czytelnik WHERE email = :email");
$stmt->execute([
    'email' => $email,
]);

$user = $stmt->fetch();


### Jak np nie używać

$sql = "SELECT * FROM czytelnik WHERE email = '$email'";
