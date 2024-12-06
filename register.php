<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (email, senha) VALUES (:email, :senha)");
        $stmt->execute(['email' => $email, 'senha' => $senha]);

        $codigo_acesso = rand(100000, 999999);
        $codigo_acesso_create_at = date('Y-m-d H:i:s');

        $stmt = $pdo->prepare("UPDATE users SET codigo_acesso = :codigo_acesso, codigo_acesso_create_at = :codigo_acesso_create_at WHERE email = :email");
        $stmt->execute([
            'codigo_acesso' => $codigo_acesso,
            'codigo_acesso_create_at' => $codigo_acesso_create_at,
            'email' => $email
        ]);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'atv2fa@gmail.com';
            $mail->Password = 'adzd llee aled xnlt';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('atv2fa@gmail.com', 'Atividade 2FA');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Seu Código de Acesso';
            $mail->Body = "Seu codigo de acesso é: <b>$codigo_acesso</b>";

            $mail->send();

            echo "Conta criada com sucesso! Código de acesso enviado para o e-mail!";
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'verify_code.php';
                    }, 3000);
                  </script>";
        } catch (Exception $e) {
            echo "Conta criada, mas erro ao enviar e-mail: {$mail->ErrorInfo}";
        }
    } catch (Exception $e) {
        echo "Erro ao criar conta: " . $e->getMessage();
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
    <form method="POST" action="register.php">
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Senha:</label>
        <input type="password" name="senha" required>
        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>
