<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login E-Rapor Futuristik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/x-icon" href="images/lgo.png">

    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Canvas container */
        #canvas-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #050505;
            z-index: -1;
        }

        .login-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        /* Login Card Custom Styling */
        .login-card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            color: white;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }

        /* Hover Effect on Card */
        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(100, 181, 246, 0.4);
            /* Warna biru custom Anda */
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 10px;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-color: #64b5f6;
            /* Warna biru custom */
            box-shadow: 0 0 10px rgba(100, 181, 246, 0.3);
        }

        .btn-login {
            background: #64b5f6;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #42a5f5;
            transform: scale(1.02);
        }

        .text-muted-custom {
            color: rgba(255, 255, 255, 0.6) !important;
        }
    </style>
</head>

<body>

    <div id="canvas-container"></div>

    <div class="login-wrapper">
        <div class="card login-card shadow-lg">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <div class="display-5 mb-2"><i class="bi bi-mortarboard-fill"></i></div>
                    <h3 class="fw-bold">E-Rapor Digital</h3>
                    <p class="text-muted-custom small">Masuk untuk melihat laporan belajar</p>
                </div>

                <form action="validasi.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small">Email atau NISN</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-white"><i class="bi bi-person"></i></span>
                            <input type="text" name="user_input" class="form-control border-start-0" placeholder="Masukkan ID" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-white"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="login" class="btn btn-login text-white">MASUK</button>
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <small class="text-muted-custom">Belum punya akun? <a href="register.php" class="text-white text-decoration-none fw-bold">Daftar</a></small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    <script>
        // --- THREE.JS ANIMATION CODE ---
        let scene, camera, renderer, stars, starGeo;

        function init() {
            scene = new THREE.Scene();
            camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 1, 1000);
            camera.position.z = 1;
            camera.rotation.x = Math.PI / 2;

            renderer = new THREE.WebGLRenderer();
            renderer.setSize(window.innerWidth, window.innerHeight);
            document.getElementById('canvas-container').appendChild(renderer.domElement);

            starGeo = new THREE.BufferGeometry();
            let starCoords = [];
            for (let i = 0; i < 6000; i++) {
                starCoords.push(Math.random() * 600 - 300);
                starCoords.push(Math.random() * 600 - 300);
                starCoords.push(Math.random() * 600 - 300);
            }
            starGeo.setAttribute('position', new THREE.Float32BufferAttribute(starCoords, 3));

            let sprite = new THREE.TextureLoader().load('https://threejs.org/examples/textures/sprites/disc.png');
            let starMaterial = new THREE.PointsMaterial({
                color: 0x64b5f6, // Warna biru custom Anda
                size: 0.7,
                map: sprite,
                transparent: true
            });

            stars = new THREE.Points(starGeo, starMaterial);
            scene.add(stars);

            animate();
        }

        function animate() {
            stars.rotation.y += 0.002;
            renderer.render(scene, camera);
            requestAnimationFrame(animate);
        }

        // Interactive Hover - Mouse Move Effect
        document.addEventListener('mousemove', (e) => {
            let mouseX = e.clientX / window.innerWidth - 0.5;
            let mouseY = e.clientY / window.innerHeight - 0.5;
            stars.rotation.x = mouseY * 0.5;
            stars.rotation.z = mouseX * 0.5;
        });

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        init();
    </script>

</body>

</html>