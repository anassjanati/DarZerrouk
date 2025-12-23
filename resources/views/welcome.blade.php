<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bienvenue</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }
        #vanta-bg {
            width: 100vw;
            height: 100vh;
            position: relative;
            overflow: hidden;
        }
        .welcome-content {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-align: center;
            z-index: 10;
        }
        .btn {
            padding: 12px 28px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            margin: 8px;
        }
        .btn-primary {
            background: #16a34a;
            color: #fff;
        }
        .btn-outline {
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.5);
        }
    </style>
</head>
<body>
<div id="vanta-bg">
    <div class="welcome-content">
        <img src="{{ asset('images/dz.png') }}" alt="Logo" style="height:80px;margin-bottom:24px;">
        <h1>Bienvenue dans DarZerrouk</h1>
        <div>
            <a href="{{ route('login') }}">
                <button class="btn btn-primary">Connexion</button>
            </a>
        </div>
    </div>
</div>

<!-- Vanta + three.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vanta@0.5.21/dist/vanta.halo.min.js"></script>

<script>
    VANTA.HALO({
        el: "#vanta-bg",
        mouseControls: true,
        touchControls: true,
        gyroControls: false,
        minHeight: 200.00,
        minWidth: 200.00,
        baseColor: 0x16a34a,      // couleur principale
        backgroundColor: 0x808de, // fond derrière le halo
        amplitudeFactor: 1.2,
        size: 1.0
    });
</script>
</body>
</html>
