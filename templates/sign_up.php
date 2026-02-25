<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - YourApp</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #74b9ff; /* พื้นหลังสีฟ้าสดใส */
            background-image: radial-gradient(#ffffff 2px, transparent 2px);
            background-size: 30px 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 30px 20px;
            box-sizing: border-box;
        }

        .register-card {
            background: #ffffff;
            border: 4px solid #2d3436;
            border-radius: 24px;
            padding: 35px 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: 8px 8px 0px #2d3436;
        }

        h1 {
            color: #2d3436;
            font-size: 28px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 25px;
            text-align: center;
            text-shadow: 2px 2px 0px #ff7675; /* ลูกเล่นเงาสีแดงส้ม */
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .input-box {
            width: 100%;
            padding: 10px 15px;
            border: 3px solid #2d3436;
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Kanit', sans-serif;
            box-sizing: border-box;
            background-color: #f8f9fa;
            transition: all 0.2s;
            outline: none;
        }

        .input-box:focus {
            background-color: #ffffff;
            border-color: #0984e3;
            box-shadow: 3px 3px 0px #74b9ff;
        }

        /* แบ่งครึ่งกล่องสำหรับเพศและวันเกิด */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        select.input-box {
            cursor: pointer;
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="%232d3436" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
        }

        .btn-submit {
            width: 100%;
            background-color: #fdcb6e; /* ปุ่มสีเหลืองสดใส */
            color: #2d3436;
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
            transform: translate(4px, 4px);
            box-shadow: 0px 0px 0px #2d3436;
        }

        .link-text {
            margin-top: 25px;
            font-size: 15px;
            color: #636e72;
            font-weight: 600;
            text-align: center;
        }

        .link-text a {
            color: #e84393;
            text-decoration: none;
            border-bottom: 2px solid #e84393;
        }
        
        .link-text a:hover {
            color: #2d3436;
            border-color: #2d3436;
        }
    </style>
</head>

<body>

    <div class="register-card">
        <h1>✨ สร้างตัวละครใหม่</h1>
        
        <form action="/routes/User.php?url=User" method="POST">
            <input type="hidden" name="action" value="register">

            <div class="input-group">
                <label>ชื่อ-นามสกุล</label>
                <input type="text" name="name" class="input-box" placeholder="ระบุชื่อของคุณ" required>
            </div>

            <div class="grid-2">
                <div class="input-group">
                    <label>เพศ</label>
                    <select name="gender" class="input-box">
                        <option value="Male">ชาย</option>
                        <option value="Female">หญิง</option>
                        <option value="Other">อื่นๆ</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>วันเกิด</label>
                    <input type="date" name="birthdate" class="input-box">
                </div>
            </div>

            <div class="input-group">
                <label>จังหวัด</label>
                <input type="text" name="province" class="input-box" placeholder="ระบุจังหวัด">
            </div>

            <div class="input-group">
                <label>อีเมล</label>
                <input type="email" name="email" class="input-box" placeholder="example@email.com" required>
            </div>

            <div class="input-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" class="input-box" placeholder="ตั้งรหัสผ่านที่จำง่ายแต่เดายาก!" required>
            </div>

            <button type="submit" class="btn-submit">สมัครสมาชิก 📝</button>
        </form>

        <div class="link-text">
            มีตัวละครอยู่แล้ว? <a href="sign_in.php">ล็อกอินเข้าเกมเลย!</a>
        </div>
    </div>

</body>

</html>