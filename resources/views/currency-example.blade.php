<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Currency Exchange Rates | EDESTINATIONS</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #00788a;
            --primary-light: #00a0b8;
            --secondary: #f4b400;
            --dark: #1a1a1a;
            --gray: #64748b;
            --light-gray: #f1f5f9;
            --white: #ffffff;
            --bg: #f8fafc;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 800px;
        }

        .card {
            background: var(--white);
            border-radius: 24px;
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            padding: 40px 30px;
            text-align: center;
            color: var(--white);
        }

        .card-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .card-header p {
            opacity: 0.9;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .card-body {
            padding: 40px;
        }

        .rates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .rate-box {
            background: var(--light-gray);
            padding: 24px;
            border-radius: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
        }

        .rate-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -10px rgba(0, 120, 138, 0.15);
            border-color: rgba(0, 120, 138, 0.2);
            background: var(--white);
        }

        .currency-pair {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .flag-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--white);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .pair-text {
            font-weight: 700;
            font-size: 14px;
            color: var(--gray);
            text-transform: uppercase;
        }

        .rate-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .rate-label {
            font-size: 13px;
            color: var(--gray);
            font-weight: 500;
        }

        .divider {
            height: 1px;
            background: var(--light-gray);
            margin: 30px 0;
        }

        .reverse-rates {
            background: #fffbeb;
            border: 1px solid #fde68a;
            padding: 20px;
            border-radius: 16px;
        }

        .reverse-rates h3 {
            font-size: 16px;
            color: #92400e;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .reverse-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .reverse-item {
            text-align: center;
        }

        .reverse-label {
            font-size: 12px;
            color: #92400e;
            margin-bottom: 4px;
            display: block;
        }

        .reverse-val {
            font-weight: 700;
            font-size: 15px;
            color: #b45309;
        }

        .footer-note {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            color: var(--gray);
        }

        @media (max-width: 600px) {
            .reverse-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1>Global Exchange Rates</h1>
            <p>Live values relative to Pakistani Rupee (PKR)</p>
        </div>
        <div class="card-body">
            
            <div class="rates-grid">
                <!-- PKR to USD -->
                <div class="rate-box">
                    <div class="currency-pair">
                        <img src="https://flagcdn.com/w80/us.png" class="flag-icon" alt="US">
                        <i class="fa-solid fa-arrow-right-long" style="color: #cbd5e1; font-size: 12px;"></i>
                        <img src="https://flagcdn.com/w80/pk.png" class="flag-icon" alt="PK">
                        <span class="pair-text">USD / PKR</span>
                    </div>
                    <div class="rate-value">{{ number_format($usdToPkr, 2) }}</div>
                    <div class="rate-label">Amount per 1.00 USD</div>
                </div>

                <!-- PKR to AED -->
                <div class="rate-box">
                    <div class="currency-pair">
                        <img src="https://flagcdn.com/w80/ae.png" class="flag-icon" alt="AE">
                        <i class="fa-solid fa-arrow-right-long" style="color: #cbd5e1; font-size: 12px;"></i>
                        <img src="https://flagcdn.com/w80/pk.png" class="flag-icon" alt="PK">
                        <span class="pair-text">AED / PKR</span>
                    </div>
                    <div class="rate-value">{{ number_format($aedToPkr, 2) }}</div>
                    <div class="rate-label">Amount per 1.00 AED</div>
                </div>

                <!-- PKR to SAR -->
                <div class="rate-box">
                    <div class="currency-pair">
                        <img src="https://flagcdn.com/w80/sa.png" class="flag-icon" alt="SA">
                        <i class="fa-solid fa-arrow-right-long" style="color: #cbd5e1; font-size: 12px;"></i>
                        <img src="https://flagcdn.com/w80/pk.png" class="flag-icon" alt="PK">
                        <span class="pair-text">SAR / PKR</span>
                    </div>
                    <div class="rate-value">{{ number_format($sarToPkr, 2) }}</div>
                    <div class="rate-label">Amount per 1.00 SAR</div>
                </div>
            </div>

            <div class="reverse-rates">
                <h3><i class="fa-solid fa-circle-info"></i> Buying Rates (1 PKR to Unit)</h3>
                <div class="reverse-grid">
                    <div class="reverse-item">
                        <span class="reverse-label">1.00 PKR</span>
                        <span class="reverse-val">{{ number_format($pkrToUsd, 4) }} USD</span>
                    </div>
                    <div class="divider" style="display: none;"></div>
                    <div class="reverse-item">
                        <span class="reverse-label">1.00 PKR</span>
                        <span class="reverse-val">{{ number_format($pkrToAed, 4) }} AED</span>
                    </div>
                    <div class="reverse-item">
                        <span class="reverse-label">1.00 PKR</span>
                        <span class="reverse-val">{{ number_format($pkrToSar, 4) }} SAR</span>
                    </div>
                </div>
            </div>

            <div class="footer-note">
                Data provided by ExchangeRate-API • Cached for 1 hour • Updated {{ now()->format('H:i T') }}
            </div>
        </div>
    </div>
</div>

</body>
</html>
