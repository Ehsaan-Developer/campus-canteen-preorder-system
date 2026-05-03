<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Campus Canteen PreOrder System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .about-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            max-width: 1000px;
            margin: 30px auto;
            color: #333;
            line-height: 1.6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .section-title {
            color: #2c3e50;
            border-left: 5px solid #007bff;
            padding-left: 15px;
            margin-top: 30px;
            text-transform: uppercase;
            font-size: 1.2rem;
            letter-spacing: 1px;
        }
        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }
        .card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        .tech-tag {
            background: #007bff;
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            margin-right: 5px;
            display: inline-block;
            margin-bottom: 5px;
        }
        .dev-info {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 40px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="page-title">Project Overview</h1>

        <div class="about-container">
            <!-- 1. Abstract & Problem Statement -->
            <h2 class="section-title">Abstract & Objective</h2>
            <p>
                The <strong>Campus Canteen PreOrder System</strong> is a sophisticated web-based solution designed to eliminate long queues and wait times during peak campus hours. By allowing students to pre-order meals and select specific pickup times, we optimize the workflow for both students and canteen staff.
            </p>

            <div class="grid-container">
                <!-- 2. System Scope -->
                <div class="card">
                    <h3 style="color: #007bff; margin-top: 0;">User Features</h3>
                    <ul style="padding-left: 20px;">
                        <li>Interactive Digital Menu</li>
                        <li>Dynamic Cart Management</li>
                        <li>Custom Pickup Time Selection</li>
                        <li>Real-time Order Tracking</li>
                    </ul>
                </div>

                <!-- 3. Admin & Analytics -->
                <div class="card">
                    <h3 style="color: #007bff; margin-top: 0;">Admin & Intelligence</h3>
                    <ul style="padding-left: 20px;">
                        <li>Order Status Management (Pending → Ready)</li>
                        <li>Product Inventory Control</li>
                        <li><strong>Python-Powered Analytics:</strong> Sales trends and combo recommendations</li>
                    </ul>
                </div>
            </div>

            <!-- 4. Technology Stack -->
            <h2 class="section-title">Technologies Used</h2>
            <div style="margin-top: 15px;">
                <span class="tech-tag">Frontend: HTML5, CSS3, JS</span>
                <span class="tech-tag">Backend: PHP (Modular Structure)</span>
                <span class="tech-tag">Database: MySQL (XAMPP)</span>
                <span class="tech-tag">Data Science: Python 3 (JSON Analysis)</span>
            </div>

            <!-- 5. Developer Credits -->
            <div class="dev-info">
                <h3 style="margin: 0;">Developed by Ehsaan Ul Haq Tawakly</h3>
                <p style="margin: 5px 0 0;">A passionate developer dedicated to creating seamless user experiences.</p>
                
            </div>
        </div>
    </div>

    <!-- Professional Footer with Flag -->
    <?php include 'footer.php'; ?>
</body>
</html>