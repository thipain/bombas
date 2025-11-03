<?php
/**
 * GERADOR DE HASH PARA A SENHA: Rosalia@21
 * Execute este arquivo e copie o hash gerado
 */

// Senha que você quer usar
$senha = 'Rosalia@21';

// Gerar hash seguro
$hash = password_hash($senha, PASSWORD_DEFAULT);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hash Gerado - Rosalia@21</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 2rem;
        }

        .info-box {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin: 20px 0;
        }

        .info-box h3 {
            color: #495057;
            margin-bottom: 15px;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .code-box {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
            font-size: 0.9rem;
            line-height: 1.8;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }

        .copy-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }

        .copy-btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            display: none;
            font-weight: 600;
        }

        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            color: #856404;
            font-weight: 500;
        }

        .steps {
            background: #e7f3ff;
            padding: 25px;
            border-radius: 12px;
            margin: 20px 0;
        }

        .steps h3 {
            color: #004e89;
            margin-bottom: 15px;
        }

        .steps ol {
            margin-left: 20px;
            line-height: 2.2;
            color: #333;
        }

        .steps ol li {
            margin-bottom: 10px;
        }

        .credential-box {
            background: #fff;
            border: 2px solid #667eea;
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
        }

        .credential-box strong {
            color: #667eea;
            font-size: 1.1rem;
        }

        .credential-box code {
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            color: #c7254e;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Hash Gerado com Sucesso!</h1>

        <div class="credential-box">
            <p><strong>Usuário (CNPJ):</strong> <code>51815514000136</code></p>
            <p style="margin-top: 10px;"><strong>Senha:</strong> <code>Rosalia@21</code></p>
        </div>

        <div class="info-box">
            <h3>🔑 Hash Gerado</h3>
            <div class="code-box" id="hashBox"><?= $hash ?></div>
            <button class="copy-btn" onclick="copyHash()">
                📋 Copiar Hash
            </button>
            <div class="success" id="successMsg">✅ Hash copiado com sucesso!</div>
        </div>

        <div class="info-box">
            <h3>💾 SQL para Atualizar no Banco</h3>
            <p style="color: #666; margin-bottom: 15px;">Execute este comando SQL no seu banco de dados:</p>
            <div class="code-box" id="sqlBox">UPDATE administradores 
SET senha = '<?= $hash ?>' 
WHERE usuario = '51815514000136';</div>
            <button class="copy-btn" onclick="copySQL()">
                📋 Copiar SQL
            </button>
            <div class="success" id="successMsg2">✅ SQL copiado com sucesso!</div>
        </div>

        <div class="steps">
            <h3>📝 Passo a Passo para Corrigir</h3>
            <ol>
                <li>Copie o <strong>SQL acima</strong> (clique no botão "Copiar SQL")</li>
                <li>Acesse o <strong>phpMyAdmin</strong> ou seu gerenciador de banco de dados</li>
                <li>Selecione o banco de dados <code>loja</code></li>
                <li>Vá na aba <strong>"SQL"</strong></li>
                <li>Cole e execute o comando SQL</li>
                <li>Tente fazer login novamente com:
                    <ul style="margin-top: 10px;">
                        <li><strong>Usuário:</strong> 51815514000136</li>
                        <li><strong>Senha:</strong> Rosalia@21</li>
                    </ul>
                </li>
            </ol>
        </div>

        <div class="warning">
            ⚠️ <strong>IMPORTANTE:</strong><br>
            • Delete este arquivo após usar<br>
            • O hash é diferente a cada execução (isso é normal e seguro)<br>
            • Certifique-se de digitar a senha EXATAMENTE como: <code>Rosalia@21</code> (com R maiúsculo e @)<br>
            • O CNPJ deve ser digitado SEM pontos, traços ou barras: <code>51815514000136</code>
        </div>

        <div class="info-box">
            <h3>🔍 Verificar Hash Atual no Banco</h3>
            <p style="color: #666; margin-bottom: 15px;">Para verificar qual senha está cadastrada atualmente:</p>
            <div class="code-box">SELECT usuario, senha FROM administradores WHERE usuario = '51815514000136';</div>
        </div>
    </div>

    <script>
        function copyHash() {
            const hashText = document.getElementById('hashBox').textContent.trim();
            navigator.clipboard.writeText(hashText).then(() => {
                showSuccess('successMsg');
            });
        }

        function copySQL() {
            const sqlText = document.getElementById('sqlBox').textContent.trim();
            navigator.clipboard.writeText(sqlText).then(() => {
                showSuccess('successMsg2');
            });
        }

        function showSuccess(id) {
            const msg = document.getElementById(id);
            msg.style.display = 'block';
            setTimeout(() => {
                msg.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>