<?php
include "koneksi.php";

$pesan_sukses = "";
$pesan_error = "";
$nama_input = "";
$email_input = "";
$subject_input = "";
$message_input = "";

// Menyimpan pesan dari formulir kontak
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["kirim"])) {
    $nama_input = trim($_POST["nama"] ?? "");
    $email_input = trim($_POST["email"] ?? "");
    $subject_input = trim($_POST["subject"] ?? "");
    $message_input = trim($_POST["message"] ?? "");

    if ($nama_input === "" || !filter_var($email_input, FILTER_VALIDATE_EMAIL) || $message_input === "") {
        $pesan_error = "Mohon isi nama, alamat email yang valid, dan pesan.";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO contacts (nama, email, subject, message) VALUES (?, ?, ?, ?)"
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssss", $nama_input, $email_input, $subject_input, $message_input);

            if (mysqli_stmt_execute($stmt)) {
                $pesan_sukses = "Terima kasih! Pesan kamu berhasil disimpan.";
                $nama_input = $email_input = $subject_input = $message_input = "";
            } else {
                $pesan_error = "Pesan belum berhasil disimpan. Silakan coba lagi.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $pesan_error = "Formulir belum dapat diproses. Silakan coba lagi.";
        }
    }
}

// Mengambil layanan dari database
$query_services = mysqli_query($koneksi, "SELECT * FROM services");
if (!$query_services) {
    die("Data layanan gagal diambil.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portofolio Kanisius Nikolas Candra, bidang mekatronika dan otomasi.">
    <title>Portofolio - Kanisius Nikolas Candra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --ink: #17243b;
            --muted: #66748a;
            --primary: #3157d5;
            --primary-dark: #2444b2;
            --soft: #f4f7fc;
            --line: #e6ebf3;
        }
        html { scroll-behavior: smooth; scroll-padding-top: 88px; }
        body { color: var(--ink); background: #fff; font-family: Arial, Helvetica, sans-serif; }
        .navbar { background: rgba(255,255,255,.96); border-bottom: 1px solid var(--line); }
        .navbar-brand { color: var(--ink); font-weight: 800; letter-spacing: -.4px; }
        .brand-dot { color: var(--primary); }
        .nav-link { color: #526078; font-weight: 600; }
        .nav-link:hover, .nav-link.active { color: var(--primary); }
        .hero { padding: 76px 0 72px; background: linear-gradient(135deg, #f4f7ff 0%, #fff 70%); }
        .hero-layout { display: grid; grid-template-columns: 1.05fr .95fr; align-items: center; gap: 42px; text-align: left; }
        .hero-copy h1 { margin: 20px 0 16px; max-width: 650px; font-size: clamp(2.25rem, 4.4vw, 3.8rem); }
        .hero-copy p { margin: 0 0 26px; max-width: 590px; }
        .hero-image-wrap { position: relative; }
        .hero-image { display: block; width: 100%; aspect-ratio: 4 / 3; object-fit: cover; border-radius: 24px; box-shadow: 0 22px 55px rgba(28,48,91,.16); }
        .image-caption { position: absolute; right: 16px; bottom: 16px; padding: 10px 14px; border: 1px solid rgba(255,255,255,.7); border-radius: 12px; background: rgba(255,255,255,.92); color: var(--ink); font-size: .82rem; font-weight: 700; box-shadow: 0 8px 22px rgba(20,35,70,.10); }
        .tech-strip { padding: 26px 0; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); background: #fff; }
        .tech-label { color: var(--muted); font-size: .78rem; font-weight: 800; letter-spacing: 1.2px; text-transform: uppercase; }
        .tech-groups { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; text-align: left; }
        .tech-group { height: 100%; padding: 20px; border: 1px solid var(--line); border-radius: 15px; background: #fff; }
        .tech-group h3 { margin: 0 0 14px; color: var(--ink); font-size: .92rem; font-weight: 800; }
        .tech-logos, .tech-tags { display: flex; flex-wrap: wrap; align-items: center; gap: 9px; }
        .tech-logo, .tech-tag { display: inline-flex; align-items: center; gap: 8px; padding: 8px 11px; border: 1px solid var(--line); border-radius: 10px; background: #fff; color: #33415d; font-size: .82rem; font-weight: 700; }
        .tech-logo img { width: 22px; height: 22px; object-fit: contain; }
        .tech-tag::before { content: ""; width: 7px; height: 7px; flex: 0 0 7px; border-radius: 50%; background: var(--primary); }
        @media (max-width: 767px) { .tech-groups { grid-template-columns: 1fr; } }
        .eyebrow { display: inline-block; padding: 7px 13px; border-radius: 30px; background: #e8eeff; color: var(--primary); font-size: .82rem; font-weight: 700; letter-spacing: .5px; }
        .hero h1 { max-width: 760px; margin: 20px auto 16px; color: var(--ink); font-size: clamp(2.35rem, 5vw, 4.2rem); line-height: 1.1; font-weight: 800; letter-spacing: -2px; }
        .hero p { max-width: 650px; margin: 0 auto 28px; color: var(--muted); font-size: 1.08rem; line-height: 1.8; }
        .btn-primary { background: var(--primary); border-color: var(--primary); border-radius: 9px; padding: 11px 20px; font-weight: 700; }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .btn-outline-primary { border-radius: 9px; padding: 11px 20px; font-weight: 700; }
        .section { padding: 76px 0; }
        .section-soft { background: var(--soft); }
        .section-kicker { color: var(--primary); font-size: .78rem; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; }
        .section-title { margin: 8px 0 10px; font-size: clamp(1.8rem, 3vw, 2.5rem); font-weight: 800; letter-spacing: -.8px; }
        .section-description { max-width: 620px; margin: 0 auto; color: var(--muted); line-height: 1.75; }
        .service-card { height: 100%; padding: 28px; border: 1px solid var(--line); border-radius: 15px; background: #fff; transition: transform .2s ease, box-shadow .2s ease; }
        .service-card:hover { transform: translateY(-5px); box-shadow: 0 16px 35px rgba(29, 48, 87, .09); }
        .service-number { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; margin-bottom: 20px; border-radius: 12px; background: #eaf0ff; color: var(--primary); font-weight: 800; }
        .service-card h3 { margin-bottom: 10px; font-size: 1.15rem; font-weight: 800; }
        .service-card p { margin: 0; color: var(--muted); line-height: 1.7; }
        .chart-panel, .contact-panel { padding: 28px; border: 1px solid var(--line); border-radius: 16px; background: #fff; box-shadow: 0 10px 30px rgba(29,48,87,.04); }
        .chart-wrap { position: relative; min-height: 300px; }
        .form-label { color: var(--ink); font-weight: 700; }
        .form-control { padding: 11px 13px; border-color: #dce3ee; border-radius: 9px; }
        .form-control:focus { border-color: #8ca4f4; box-shadow: 0 0 0 .2rem rgba(49,87,213,.12); }
        .contact-note { color: var(--muted); line-height: 1.8; }
        footer { padding: 25px 0; border-top: 1px solid var(--line); color: var(--muted); font-size: .9rem; }
        @media (max-width: 767px) {
            .hero { padding: 58px 0 52px; }
            .hero-layout { grid-template-columns: 1fr; gap: 30px; text-align: center; }
            .hero-copy h1, .hero-copy p { margin-left: auto; margin-right: auto; }
            .hero h1 { letter-spacing: -1.2px; }
            .hero-image { aspect-ratio: 16 / 10; }
            .section { padding: 58px 0; }
            .chart-panel, .contact-panel { padding: 20px; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#beranda">MEKATECH<span class="brand-dot">.</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto gap-lg-3">
                <li class="nav-item"><a class="nav-link active" href="#beranda">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="#layanan">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#tentang">About</a></li>\n                <li class="nav-item"><a class="nav-link" href="#statistik">Statistics</a></li>
                <li class="nav-item"><a class="nav-link" href="#kontak">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<header class="hero" id="beranda">
    <div class="container hero-layout">
        <div class="hero-copy">
            <span class="eyebrow">MECHATRONICS &amp; AUTOMATION</span>
            <h1>Halo, Saya Kanisius Nikolas Candra.</h1>
            <p>Mechatronics &amp; Automation Engineer. Berfokus pada integrasi sistem mekanik, pemrograman mikrokontroler, dan otomasi industri untuk mengembangkan solusi rekayasa modern.</p>
            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                <a href="#layanan" class="btn btn-primary">Lihat Layanan</a>
                <a href="#kontak" class="btn btn-outline-primary">Hubungi Saya</a>
            </div>
        </div>
        <div class="hero-image-wrap">
            <img class="hero-image" src="assets/img/tech-workspace.png" alt="Meja kerja teknologi dengan laptop, perangkat elektronik, dan komponen robotik">
            <span class="image-caption">Mechatronics · Automation · Robotics</span>
        </div>
    </div>
</header>

<section class="tech-strip" aria-label="Software, hardware, dan keahlian teknis">
    <div class="container">
        <div class="tech-label text-center mb-4">TECHNICAL SKILLS &amp; TECHNOLOGIES</div>
        <div class="tech-groups">
            <div class="tech-group">
                <h3>Software &amp; Development</h3>
                <div class="tech-logos">
                    <span class="tech-logo"><img src="assets/logos/php.svg" alt="">PHP</span>
                    <span class="tech-logo"><img src="assets/logos/mysql.svg" alt="">MySQL</span>
                    <span class="tech-logo"><img src="assets/logos/bootstrap.svg" alt="">Bootstrap</span>
                    <span class="tech-logo"><img src="assets/logos/javascript.svg" alt="">JavaScript</span>
                    <span class="tech-logo"><img src="assets/logos/chartjs.svg" alt="">Chart.js</span>
                    <span class="tech-tag">CAD Software</span>
                    <span class="tech-tag">PLC Programming</span>
                </div>
            </div>
            <div class="tech-group">
                <h3>Hardware &amp; Systems</h3>
                <div class="tech-tags">
                    <span class="tech-tag">PLC</span>
                    <span class="tech-tag">HMI</span>
                    <span class="tech-tag">STM32</span>
                    <span class="tech-tag">ESP32</span>
                    <span class="tech-tag">Arduino</span>
                    <span class="tech-tag">Sensors</span>
                    <span class="tech-tag">Actuators</span>
                </div>
            </div>
            <div class="tech-group">
                <h3>Engineering Skills</h3>
                <div class="tech-tags">
                    <span class="tech-tag">Automation System Design</span>
                    <span class="tech-tag">Mechanical CAD &amp; 3D Modeling</span>
                    <span class="tech-tag">PID Control</span>
                    <span class="tech-tag">Pneumatic &amp; Hydraulic</span>
                    <span class="tech-tag">Sensor Signal Processing</span>
                    <span class="tech-tag">Prototyping</span>
                </div>
            </div>
        </div>
    </div>
</section>

<main>
    <section class="section" id="layanan">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-kicker">WHAT I DO</div>
                <h2 class="section-title">My Services</h2>
                <p class="section-description">Bidang pekerjaan dan keahlian yang menjadi fokus saya dalam pengembangan sistem mekatronika dan otomasi.</p>
            </div>
            <div class="row g-4">
                <?php $nomor = 1; ?>
                <?php while ($data = mysqli_fetch_assoc($query_services)) { ?>
                    <div class="col-md-6 col-lg-4">
                        <article class="service-card">
                            <div class="service-number"><?php echo str_pad($nomor, 2, "0", STR_PAD_LEFT); ?></div>
                            <h3><?php echo htmlspecialchars($data["title"], ENT_QUOTES, "UTF-8"); ?></h3>
                            <p><?php echo htmlspecialchars($data["description"], ENT_QUOTES, "UTF-8"); ?></p>
                        </article>
                    </div>
                    <?php $nomor++; ?>
                <?php } ?>
            </div>
        </div>
    </section>

    <section class="section section-soft" id="tentang">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-kicker">ABOUT ME</div>
                <h2 class="section-title">Tentang Saya</h2>
                <p class="section-description">Saya adalah rekayasawan mekatronika yang berfokus pada efisiensi proses industri dan pengembangan teknologi robotika tepat guna.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="chart-panel">
                        <h3 class="h5 fw-bold mb-3">Fokus &amp; Keahlian Teknis</h3>
                        <p class="text-secondary mb-0" style="line-height:1.8;">Memiliki pemahaman tentang kendali PID, pneumatik dan hidrolik, pengolahan sinyal sensor, serta integrasi sistem perangkat keras dan perangkat lunak.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section section-soft" id="statistik">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-kicker">NUMBERS & DATA</div>
                <h2 class="section-title">My Statistics</h2>
                <p class="section-description">Ringkasan pengalaman dan aktivitas di bidang proyek mesin, kendali, serta riset perangkat keras. Data grafik diambil dari database dan diperbarui otomatis setiap lima detik.</p>
            </div>
            <div class="chart-panel">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h3 class="h5 fw-bold mb-1">Performance</h3>
                        <p class="small text-secondary mb-0">Pembaruan data otomatis</p>
                    </div>
                    <span class="badge rounded-pill text-bg-light border px-3 py-2">Live update · 5 detik</span>
                </div>
                <div class="chart-wrap"><canvas id="grafikStatistik" aria-label="Grafik statistik portofolio" role="img"></canvas></div>
                <p id="statusGrafik" class="small text-secondary mt-3 mb-0" aria-live="polite">Mengambil data statistik...</p>
            </div>
        </div>
    </section>

    <section class="section" id="kontak">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-kicker">GET IN TOUCH</div>
                <h2 class="section-title">Contact Me</h2>
                <p class="section-description">Untuk pertanyaan, diskusi teknis, atau peluang kolaborasi terkait mekatronika dan otomasi, silakan kirim pesan melalui formulir berikut.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="contact-panel">
                        <?php if ($pesan_sukses !== "") { ?>
                            <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($pesan_sukses, ENT_QUOTES, "UTF-8"); ?></div>
                        <?php } ?>
                        <?php if ($pesan_error !== "") { ?>
                            <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($pesan_error, ENT_QUOTES, "UTF-8"); ?></div>
                        <?php } ?>
                        <p class="contact-note mb-4">Kolom bertanda wajib harus diisi. Pesan akan disimpan ke database portofolio.</p>
                        <form method="POST" action="#kontak">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nama" class="form-label">Nama *</label>
                                    <input type="text" id="nama" name="nama" class="form-control" value="<?php echo htmlspecialchars($nama_input, ENT_QUOTES, "UTF-8"); ?>" placeholder="Masukkan nama" maxlength="100" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email_input, ENT_QUOTES, "UTF-8"); ?>" placeholder="nama@email.com" maxlength="100" required>
                                </div>
                                <div class="col-12">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" id="subject" name="subject" class="form-control" value="<?php echo htmlspecialchars($subject_input, ENT_QUOTES, "UTF-8"); ?>" placeholder="Topik pesan" maxlength="150">
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label">Pesan *</label>
                                    <textarea id="message" name="message" class="form-control" rows="5" placeholder="Tuliskan pesan kamu..." required><?php echo htmlspecialchars($message_input, ENT_QUOTES, "UTF-8"); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="kirim" class="btn btn-primary">Kirim Pesan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<footer>
    <div class="container d-flex flex-wrap justify-content-between gap-2">
        <span>© <?php echo date("Y"); ?> Kanisius Nikolas Candra</span>
        <span>Mechatronics &amp; Automation Engineer</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const canvasGrafik = document.getElementById("grafikStatistik");
    const statusGrafik = document.getElementById("statusGrafik");

    const grafik = new Chart(canvasGrafik, {
        type: "bar",
        data: {
            labels: [],
            datasets: [{
                label: "Jumlah",
                data: [],
                backgroundColor: ["#3157d5", "#6d8af0", "#9eb1fa", "#5473df", "#b9c8ff"],
                borderRadius: 7,
                borderSkipped: false,
                maxBarThickness: 64
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: "#17243b", padding: 12, displayColors: false }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: "#66748a" } },
                y: { beginAtZero: true, grid: { color: "#edf0f5" }, ticks: { precision: 0, color: "#66748a" } }
            }
        }
    });

    async function updateGrafik() {
        try {
            const response = await fetch("data_statistik.php", { cache: "no-store" });
            if (!response.ok) throw new Error("Gagal mengambil data statistik.");
            const data = await response.json();
            grafik.data.labels = data.map(item => item.label);
            grafik.data.datasets[0].data = data.map(item => Number(item.value));
            grafik.update();
            statusGrafik.textContent = "Data berhasil diperbarui pada " + new Date().toLocaleTimeString("id-ID") + ".";
        } catch (error) {
            statusGrafik.textContent = "Data statistik belum dapat dimuat. Periksa koneksi database.";
            console.error(error);
        }
    }

    updateGrafik();
    setInterval(updateGrafik, 5000);

    // Menyesuaikan menu aktif dengan bagian yang sedang dibuka
    const sections = document.querySelectorAll("header[id], main section[id]");
    const navLinks = document.querySelectorAll(".navbar .nav-link");
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                navLinks.forEach(link => {
                    link.classList.toggle("active", link.getAttribute("href") === "#" + entry.target.id);
                });
            }
        });
    }, { rootMargin: "-35% 0px -55% 0px" });
    sections.forEach(section => observer.observe(section));
</script>
</body>
</html>
