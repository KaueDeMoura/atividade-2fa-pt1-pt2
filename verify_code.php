<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $codigo_acesso = $_POST['codigo_acesso'];

    $stmt = $pdo->prepare("SELECT codigo_acesso, codigo_acesso_create_at FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $codigo_expirado = strtotime($user['codigo_acesso_create_at']) < strtotime('-10 minutes');

        if ($codigo_expirado) {
            echo "Código expirado.";
        } elseif ($user['codigo_acesso'] === $codigo_acesso) {
            echo "Código correto! Acesso concedido.";
        } else {
            echo "Código incorreto!";
        }
    } else {
        echo "Usuário não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="register.css">

</head>
<body>
<form method="POST" action="verify_code.php">
    <label>Email:</label>
    <input type="email" name="email" required>
    <label>Código de Acesso:</label>
    <input type="text" name="codigo_acesso" required>
    <button type="submit">Verificar Código</button>
</form>
</body>
</html>