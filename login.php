<?php
session_start();
include 'koneksi.php';

// Jika sudah login, langsung lempar ke menu
if (isset($_SESSION['nisn'])) {
    header("location:menu.php");
    exit();
}

$error = "";

if (isset($_POST['login'])) {
    $identifier = mysqli_real_escape_string($conn, $_POST['identifier']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Logika Autentikasi Ganda (Bisa pakai NISN atau Email)
    $query = mysqli_query($conn, "SELECT * FROM data_siswa WHERE (nisn = '$identifier' OR email = '$identifier') AND password = '$password'");

    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);

        // --- PROTEKSI BARU: CEK STATUS ANTREAN WAITING LIST ---
        if ($row['status_akun'] == 'Waiting') {
            $error = "Akun Anda dalam antrean verifikasi Admin. Mohon tunggu!";
        } elseif ($row['status_akun'] == 'Rejected') {
            $error = "Maaf, permohonan pendaftaran akun Anda ditolak oleh sekolah.";
        } else {
            // Jika status_akun == 'Approved', baru sesi login resmi dibuka
            $_SESSION['nisn'] = $row['nisn'];
            $_SESSION['level'] = $row['level']; // 2 = Admin/Guru, 1 = Siswa
            header("location:menu.php");
            exit();
        }
    } else {
        $error = "Identitas atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sekolah Gracia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
            /* Mengubah background menjadi gradasi biru muda cerah */
            background: linear-gradient(135deg, #A8DADC 0%, #457B9D 100%);
        }

        #canvas-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .login-wrapper {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Glassmorphism Cerah */
        .login-card {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        }

        .brand-title {
            color: #1D3557;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            padding: 12px;
            color: #1D3557;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.9);
            border-color: #457B9D;
            box-shadow: 0 0 0 0.25rem rgba(69, 123, 157, 0.25);
            color: #1D3557;
        }

        .form-control::placeholder {
            color: #6c757d;
        }

        .btn-login {
            background: #E63946;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #C12735;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
        }

        /* Styling Tambahan untuk Tombol Register */
        .register-link {
            color: #1D3557;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .register-link:hover {
            color: #E63946;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div id="canvas-container"></div>

    <div class="login-wrapper container">
        <div class="login-card text-center">
            <div class="mb-4">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 70px; height: 70px;">
                    <i class="bi bi-mortarboard-fill fs-2 text-primary"></i>
                </div>
                <h3 class="brand-title mt-3 mb-1">Sekolah Gracia</h3>
                <p class="text-muted small">Sistem Informasi Akademik & E-Rapor</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold text-dark">NISN / Email Sekolah</label>
                    <input type="text" name="identifier" class="form-control" placeholder="Masukkan NISN atau Email" required autocomplete="off">
                </div>
                <div class="mb-4 text-start">
                    <label class="form-label small fw-bold text-dark">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" name="login" class="btn btn-login btn-danger w-100 text-white shadow-sm mb-3">Masuk Ke Sistem</button>

                <div class="mt-2 border-top pt-3 border-secondary border-opacity-25">
                    <span class="text-dark small opacity-75">Pengguna baru sekolah?</span>
                    <a href="register.php" class="register-link ms-1">Daftar Akun Di Sini <i class="bi bi-arrow-right"></i></a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        // --- KONFIGURASI ANIMASI PARTIKEL THREE.JS YANG CERAH ---
        const container = document.getElementById('canvas-container');
        const scene = new THREE.Scene();

        // Kamera
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.z = 5;

        // Renderer dengan alpha true agar background gradasi CSS tetap tembus
        const renderer = new THREE.WebGLRenderer({
            alpha: true,
            antialias: true
        });
        renderer.setSize(window.innerWidth, window.innerHeight);
        container.appendChild(renderer.domElement);

        // Membuat Partikel Bintang (Geometri & Material)
        const particlesGeometry = new THREE.BufferGeometry();
        const particlesCount = 1200; // Jumlah partikel bintang

        const posArray = new Float32Array(particlesCount * 3);

        for (let i = 0; i < particlesCount * 3; i++) {
            posArray[i] = (Math.random() - 0.5) * 10;
        }

        particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));

        // Tekstur lingkaran halus untuk partikel bintang
        const pMaterial = new THREE.PointsMaterial({
            size: 0.03,
            color: 0xffffff, // Partikel bintang berwarna putih berkilau
            transparent: true,
            opacity: 0.8,
            blending: THREE.AdditiveBlending
        });

        const particleSystem = new THREE.Points(particlesGeometry, pMaterial);
        scene.add(particleSystem);

        // Interaktivitas: Mouse Tracking
        let mouseX = 0;
        let mouseY = 0;

        document.addEventListener('mousemove', (event) => {
            mouseX = (event.clientX / window.innerWidth) - 0.5;
            mouseY = (event.clientY / window.innerHeight) - 0.5;
        });

        // Loop Animasi
        const animate = () => {
            requestAnimationFrame(animate);

            // Perputaran konstan partikel
            particleSystem.rotation.y += 0.001;
            particleSystem.rotation.x += 0.0005;

            // Efek interaksi kamera mengikuti pergerakan kursor mouse
            camera.position.x += (mouseX * 2 - camera.position.x) * 0.05;
            camera.position.y += (-mouseY * 2 - camera.position.y) * 0.05;
            camera.lookAt(scene.position);

            renderer.render(scene, camera);
        };

        animate();

        // Handle ukuran browser berubah (Responsif)
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>

</html>