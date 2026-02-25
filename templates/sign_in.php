<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Sign in to YourApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f6f8fa;
        }

        .btn-input {
            border: 1px solid #d0d7de;
            border-radius: 6px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .btn-input:focus {
            border-color: #0969da;
            outline: none;
            box-shadow: 0 0 0 3px rgba(9, 105, 218, 0.3);
=======
    <title>เข้าสู่ระบบ - YourApp</title>
    <!-- นำเข้าฟอนต์ Kanit -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #a29bfe; /* พื้นหลังสีม่วงพาสเทล */
            background-image: radial-gradient(#ffffff 2px, transparent 2px); /* ลายจุดไข่ปลา */
            background-size: 30px 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        .login-card {
            background: #ffffff;
            border: 4px solid #2d3436;
            border-radius: 24px;
            padding: 40px 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 8px 8px 0px #2d3436; /* เงาทึบสไตล์ Retro Game */
            text-align: center;
            position: relative;
        }

        h1 {
            color: #2d3436;
            font-size: 32px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 30px;
            text-shadow: 2px 2px 0px #fdcb6e; /* ลูกเล่นเงาตัวอักษรสีเหลือง */
        }

        .input-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 8px;
            font-size: 15px;
        }

        .input-box {
            width: 100%;
            padding: 12px 15px;
            border: 3px solid #2d3436;
            border-radius: 12px;
            font-size: 16px;
            font-family: 'Kanit', sans-serif;
            box-sizing: border-box;
            background-color: #f8f9fa;
            transition: all 0.2s;
            outline: none;
        }

        .input-box:focus {
            background-color: #ffffff;
            border-color: #0984e3;
            box-shadow: 4px 4px 0px #74b9ff; /* เปลี่ยนสีเงาตอนโฟกัส */
        }

        .forgot-pass {
            float: right;
            font-size: 13px;
            color: #d63031;
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-pass:hover { text-decoration: underline; }

        .btn-submit {
            width: 100%;
            background-color: #00b894; /* ปุ่มสีเขียวมิ้นต์ */
            color: white;
            padding: 15px;
            border: 3px solid #2d3436;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 4px 4px 0px #2d3436;
            transition: all 0.1s;
            margin-top: 15px;
            font-family: 'Kanit', sans-serif;
        }

        .btn-submit:active {
            transform: translate(4px, 4px); /* เอฟเฟกต์ปุ่มยุบลงไป */
            box-shadow: 0px 0px 0px #2d3436;
        }

        .link-text {
            margin-top: 25px;
            font-size: 15px;
            color: #636e72;
            font-weight: 600;
        }

        .link-text a {
            color: #0984e3;
            text-decoration: none;
            border-bottom: 2px solid #0984e3;
        }
        
        .link-text a:hover {
            color: #2d3436;
            border-color: #2d3436;
>>>>>>> 3fed582ddb5cdcb80a22209fe920349795b39c1d
        }
    </style>
</head>

<<<<<<< HEAD
<body class="flex flex-col items-center pt-12 px-4">



    <h1 class="text-2xl font-light mb-4">Sign in to YourApp</h1>

    <div class="bg-white border border-[#d8dee4] rounded-lg p-5 w-full max-w-[308px] shadow-sm">
        <form action="/routes/User.php" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="login">

            <div>
                <label class="block text-sm font-normal mb-2 text-slate-900">Email address</label>
                <input type="email" name="email" class="btn-input w-full px-3 py-1.5 text-sm" required>
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-normal text-slate-900">Password</label>
                    <a href="#" class="text-xs text-[#0969da] hover:underline">Forgot password?</a>
                </div>
                <input type="password" name="password" class="btn-input w-full px-3 py-1.5 text-sm" required>
            </div>

            <button type="submit" class="w-full bg-[#2da44e] hover:bg-[#2c974b] text-white font-semibold py-1.5 rounded-md text-sm mt-4 transition duration-200">
                Sign in
            </button>
        </form>
    </div>

    <div class="mt-4 border border-[#d8dee4] rounded-lg p-4 w-full max-w-[308px] text-center bg-transparent">
        <p class="text-sm">New to YourApp? <a href="sign_up.php" class="text-[#0969da] hover:underline">Create an account</a>.</p>
=======
<body>

    <div class="login-card">
        <h1>🎮 เข้าสู่ระบบ</h1>
        
        <form action="/routes/User.php" method="POST">
            <input type="hidden" name="action" value="login">

            <div class="input-group">
                <label>อีเมล (Email)</label>
                <input type="email" name="email" class="input-box" placeholder="player@email.com" required>
            </div>

            <div class="input-group">
                <label>
                    รหัสผ่าน (Password)
                    <a href="#" class="forgot-pass">ลืมรหัสผ่าน?</a>
                </label>
                <input type="password" name="password" class="input-box" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-submit">START GAME 🚀</button>
        </form>

        <div class="link-text">
            ผู้เล่นใหม่? <a href="sign_up.php">สร้างบัญชีที่นี่เลย!</a>
        </div>
>>>>>>> 3fed582ddb5cdcb80a22209fe920349795b39c1d
    </div>

</body>

</html>