<!DOCTYPE html>
<?php
session_start();
?>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet" />
    <title>Übermensch – Education Aid Disbursement System</title>
    <style>
        body {
            font-family: 'Fredoka', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
        }

        /* Header and Navigation */
        header {
            background-color: #0d3b66;
        }

        nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav_logo h1 a {
            color: #fff;
            text-decoration: none;
            font-size: 26px;
        }

        .nav_link {
            list-style: none;
            display: flex;
            gap: 25px;
            margin: 0;
            padding: 0;
        }

        .nav_link li a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav_link li a:hover {
            color: #ffdd00;
        }

        /* Footer */
        footer {
            background-color: #0d3b66;
            color: #fff;
            padding: 20px 0;
            margin-top: 40px;
        }

        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
        }

        .footer-inner a {
            color: #ffdd00;
            text-decoration: none;
            margin-left: 8px;
        }

        /* Main Section */
        main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
            text-align: center;
            flex-direction: column;
        }

        main h1 {
            color: #0d3b66;
            font-weight: 600;
            margin-bottom: 10px;
        }

        main p {
            color: #333;
            font-size: 18px;
            max-width: 600px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav_link {
                flex-direction: column;
                gap: 10px;
                margin-top: 10px;
            }

            .footer-inner {
                flex-direction: column;
                gap: 8px;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <?php require_once(__DIR__ . '/inc/header.php'); ?>
    <main>
        <section class="home">
            <h1>Welcome to Übermensch</h1>
            <p>
                Übermensch is an Education Aid Disbursement System that helps talented students in rural Bangladesh
                access scholarships and aid.
                Track applications, verify students, monitor disbursements, and analyze regional impact transparently
                and efficiently.
            </p>
        </section>
    </main>

    <?php require_once(__DIR__ . '/inc/footer.php'); ?>
</body>

</html>