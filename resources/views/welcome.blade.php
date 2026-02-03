<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Odovin - Revolutionary Vehicle Management Platform</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&family=Orbitron:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Global Variables */
        :root {
            --bg-primary: #0a0e1a;
            --bg-secondary: #121827;
            --bg-tertiary: #1a2030;
            --accent-primary: #00d4ff;
            --accent-secondary: #00ffaa;
            --accent-tertiary: #ff00ff;
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --text-tertiary: rgba(255, 255, 255, 0.5);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Chakra Petch', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            overflow-x: hidden;
        }
        
        /* Animated Background */
        .cosmic-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }
        
        .stars {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(2px 2px at 20px 30px, rgba(255, 255, 255, 0.8), transparent),
                radial-gradient(2px 2px at 60px 70px, rgba(255, 255, 255, 0.6), transparent),
                radial-gradient(1px 1px at 50px 50px, rgba(255, 255, 255, 0.4), transparent),
                radial-gradient(1px 1px at 130px 80px, rgba(255, 255, 255, 0.5), transparent),
                radial-gradient(2px 2px at 90px 10px, rgba(255, 255, 255, 0.7), transparent);
            background-repeat: repeat;
            background-size: 200px 200px;
            animation: sparkle 20s linear infinite;
        }
        
        @keyframes sparkle {
            0% { opacity: 0.5; }
            50% { opacity: 1; }
            100% { opacity: 0.5; }
        }
        
        .grid-lines {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(0, 212, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 212, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
            transform: perspective(500px) rotateX(60deg);
            transform-origin: center bottom;
        }
        
        @keyframes gridMove {
            0% { transform: perspective(500px) rotateX(60deg) translateY(0); }
            100% { transform: perspective(500px) rotateX(60deg) translateY(50px); }
        }
        
        .gradient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: float 15s ease-in-out infinite;
        }
        
        .orb-1 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--accent-primary), transparent);
            top: -300px;
            right: -200px;
            opacity: 0.3;
            animation-delay: 0s;
        }
        
        .orb-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, var(--accent-secondary), transparent);
            bottom: -250px;
            left: -150px;
            opacity: 0.25;
            animation-delay: 5s;
        }
        
        .orb-3 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--accent-tertiary), transparent);
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.2;
            animation-delay: 10s;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(100px, -100px) scale(1.1); }
            66% { transform: translate(-100px, 100px) scale(0.9); }
        }
        
        /* Glowing Line Animation */
        .glow-line {
            position: absolute;
            width: 2px;
            height: 100px;
            background: linear-gradient(to bottom, transparent, var(--accent-primary), transparent);
            animation: lineTravel 3s ease-in-out infinite;
        }
        
        @keyframes lineTravel {
            0% { top: -100px; opacity: 0; }
            50% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        
        /* Navigation */
        .nav-container {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            background: rgba(10, 14, 26, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 212, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .nav-container.scrolled {
            background: rgba(10, 14, 26, 0.95);
            box-shadow: 0 10px 40px rgba(0, 212, 255, 0.1);
        }
        
        .nav-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.5);
            animation: logoGlow 3s ease-in-out infinite;
        }
        
        @keyframes logoGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(0, 212, 255, 0.5); }
            50% { box-shadow: 0 0 30px rgba(0, 212, 255, 0.8); }
        }
        
        .logo-icon svg {
            width: 24px;
            height: 24px;
            color: var(--bg-primary);
        }
        
        .logo-text {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 0.05em;
        }
        
        .nav-links {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .nav-link {
            padding: 0.75rem 1.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            letter-spacing: 0.05em;
            transition: all 0.3s ease;
            border-radius: 10px;
            position: relative;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
            transition: width 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--text-primary);
            background: rgba(0, 212, 255, 0.05);
        }
        
        .nav-link:hover::before {
            width: 80%;
        }
        
        .nav-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .btn-ghost {
            padding: 0.75rem 1.5rem;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 212, 255, 0.3);
        }
        
        .btn-ghost:hover {
            background: rgba(0, 212, 255, 0.1);
            border-color: var(--accent-primary);
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        }
        
        .btn-primary {
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: var(--bg-primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 700;
            border-radius: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.5);
        }
        
        /* Hero Section */
        .hero-section {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 120px 2rem 80px;
            overflow: hidden;
        }
        
        .hero-content {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            position: relative;
            z-index: 10;
        }
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 50px;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
            animation: fadeInUp 0.8s ease-out;
        }
        
        .pulse-dot {
            width: 10px;
            height: 10px;
            background: var(--accent-primary);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 15px var(--accent-primary); }
            50% { opacity: 0.5; box-shadow: 0 0 25px var(--accent-primary); }
        }
        
        .badge-text {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--accent-primary);
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        
        .hero-title {
            font-family: 'Orbitron', sans-serif;
            font-size: clamp(2.5rem, 6vw, 5rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.8s ease-out 0.2s backwards;
        }
        
        .title-line {
            display: block;
            background: linear-gradient(135deg, var(--text-primary), var(--text-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .title-gradient {
            display: block;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary), var(--accent-tertiary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% auto;
            animation: shimmer 3s linear infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
        
        .hero-subtitle {
            font-size: clamp(1rem, 2vw, 1.25rem);
            color: var(--text-secondary);
            max-width: 700px;
            line-height: 1.7;
            margin-bottom: 3rem;
            animation: fadeInUp 0.8s ease-out 0.4s backwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* VIN Lookup Card */
        .vin-card {
            max-width: 900px;
            background: rgba(18, 24, 39, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 212, 255, 0.2);
            border-radius: 24px;
            padding: 3rem;
            margin-top: 3rem;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out 0.6s backwards;
        }
        
        .vin-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-primary), transparent);
            animation: scanLine 3s ease-in-out infinite;
        }
        
        @keyframes scanLine {
            0%, 100% { opacity: 0; }
            50% { opacity: 1; }
        }
        
        .vin-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .vin-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(0, 255, 170, 0.2));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(0, 212, 255, 0.3);
            position: relative;
        }
        
        .vin-icon::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
            filter: blur(10px);
        }
        
        .vin-card:hover .vin-icon::after {
            opacity: 0.5;
        }
        
        .vin-icon svg {
            width: 40px;
            height: 40px;
            color: var(--accent-primary);
        }
        
        .vin-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .vin-description {
            font-size: 1rem;
            color: var(--text-secondary);
        }
        
        .vin-input-group {
            margin-bottom: 1.5rem;
        }
        
        .input-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .vin-input {
            width: 100%;
            padding: 1.25rem 5rem 1.25rem 1.5rem;
            background: rgba(10, 14, 26, 0.6);
            border: 2px solid rgba(0, 212, 255, 0.2);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 1.125rem;
            font-family: 'Orbitron', monospace;
            font-weight: 600;
            letter-spacing: 0.1em;
            transition: all 0.3s ease;
        }
        
        .vin-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            background: rgba(10, 14, 26, 0.8);
            box-shadow: 0 0 30px rgba(0, 212, 255, 0.2);
        }
        
        .vin-input.error {
            border-color: #ff3366;
        }
        
        .char-counter {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.875rem;
            font-weight: 700;
            padding: 0.375rem 0.75rem;
            background: rgba(0, 212, 255, 0.1);
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .char-counter.complete {
            color: var(--accent-secondary);
            background: rgba(0, 255, 170, 0.2);
        }
        
        .char-counter.incomplete {
            color: var(--text-tertiary);
        }
        
        .input-hint {
            margin-top: 0.75rem;
            font-size: 0.75rem;
            color: var(--text-tertiary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .input-hint svg {
            width: 14px;
            height: 14px;
        }
        
        .error-message {
            margin-top: 0.75rem;
            font-size: 0.875rem;
            color: #ff3366;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .error-message svg {
            width: 16px;
            height: 16px;
        }
        
        .btn-lookup {
            width: 100%;
            padding: 1.25rem;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: var(--bg-primary);
            border: none;
            border-radius: 12px;
            font-size: 1.125rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-lookup::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-lookup:hover::before {
            left: 100%;
        }
        
        .btn-lookup:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 212, 255, 0.5);
        }
        
        .btn-lookup:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .btn-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        
        .spinner {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Vehicle Result Card */
        .vehicle-result {
            margin-top: 2rem;
            padding: 2rem;
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.05), rgba(0, 255, 170, 0.05));
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 16px;
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .vehicle-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1.5rem;
        }
        
        .vehicle-name {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .vehicle-trim {
            font-size: 1rem;
            color: var(--text-secondary);
        }
        
        .verified-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(0, 255, 136, 0.2);
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #00ff88;
        }
        
        .vehicle-specs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .spec-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .spec-label {
            font-size: 0.75rem;
            color: var(--text-tertiary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .spec-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .btn-add-vehicle {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: var(--bg-primary);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            text-align: center;
            transition: all 0.3s ease;
            display: block;
        }
        
        .btn-add-vehicle:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.5);
        }
        
        /* Trust Badges */
        .trust-badges {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 2rem;
            margin-top: 3rem;
            animation: fadeInUp 0.8s ease-out 0.8s backwards;
        }
        
        .trust-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        
        .trust-badge svg {
            width: 20px;
            height: 20px;
            color: var(--accent-primary);
        }
        
        /* Features Section */
        .features-section {
            position: relative;
            padding: 120px 2rem;
            background: var(--bg-secondary);
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }
        
        .section-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--accent-primary);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1.5rem;
        }
        
        .section-title {
            font-family: 'Orbitron', sans-serif;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--text-primary), var(--text-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .section-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .features-grid {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            background: rgba(26, 32, 48, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 212, 255, 0.1);
            border-radius: 20px;
            padding: 2.5rem;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent-primary), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            border-color: rgba(0, 212, 255, 0.3);
            box-shadow: 0 20px 60px rgba(0, 212, 255, 0.2);
        }
        
        .feature-card:hover::before {
            opacity: 1;
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(0, 255, 170, 0.2));
            border-radius: 16px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(0, 212, 255, 0.3);
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.4);
        }
        
        .feature-icon svg {
            width: 36px;
            height: 36px;
            color: var(--accent-primary);
        }
        
        .feature-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        
        .feature-description {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.7;
        }
        
        /* How It Works Section */
        .how-it-works-section {
            position: relative;
            padding: 120px 2rem;
            background: var(--bg-primary);
        }
        
        .steps-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 4rem;
        }
        
        .step-card {
            text-align: center;
            position: relative;
        }
        
        .step-number {
            width: 100px;
            height: 100px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Orbitron', sans-serif;
            font-size: 3rem;
            font-weight: 900;
            color: var(--bg-primary);
            box-shadow: 0 10px 40px rgba(0, 212, 255, 0.4);
            position: relative;
        }
        
        .step-number::after {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            opacity: 0.3;
            filter: blur(15px);
            z-index: -1;
        }
        
        .step-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        
        .step-description {
            font-size: 1rem;
            color: var(--text-secondary);
            line-height: 1.7;
        }
        
        /* CTA Section */
        .cta-section {
            position: relative;
            padding: 120px 2rem;
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 255, 170, 0.1));
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-primary), transparent);
        }
        
        .cta-content {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
        }
        
        .cta-title {
            font-family: 'Orbitron', sans-serif;
            font-size: clamp(2rem, 4vw, 3.5rem);
            font-weight: 900;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--text-primary), var(--accent-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .cta-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 3rem;
            line-height: 1.7;
        }
        
        .cta-button {
            display: inline-block;
            padding: 1.5rem 4rem;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: var(--bg-primary);
            text-decoration: none;
            border-radius: 12px;
            font-size: 1.25rem;
            font-weight: 800;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .cta-button:hover::before {
            left: 100%;
        }
        
        .cta-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0, 212, 255, 0.5);
        }
        
        /* Footer */
        .footer {
            background: var(--bg-secondary);
            padding: 80px 2rem 40px;
            border-top: 1px solid rgba(0, 212, 255, 0.1);
        }
        
        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 4rem;
            margin-bottom: 4rem;
        }
        
        .footer-brand {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .footer-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .footer-logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .footer-logo-icon svg {
            width: 20px;
            height: 20px;
            color: var(--bg-primary);
        }
        
        .footer-logo-text {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .footer-description {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .footer-section-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        
        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .footer-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .footer-link:hover {
            color: var(--accent-primary);
            padding-left: 0.5rem;
        }
        
        .footer-bottom {
            padding-top: 2rem;
            border-top: 1px solid rgba(0, 212, 255, 0.1);
            text-align: center;
            color: var(--text-tertiary);
            font-size: 0.875rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .hero-section {
                padding: 100px 1rem 60px;
            }
            
            .vin-card {
                padding: 2rem;
            }
            
            .features-grid,
            .steps-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body x-data="vinLookup()">
    <!-- Cosmic Background -->
    <div class="cosmic-bg">
        <div class="stars"></div>
        <div class="grid-lines"></div>
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
        <div class="gradient-orb orb-3"></div>
    </div>

    <!-- Navigation -->
    <nav class="nav-container" x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 50" :class="{ 'scrolled': scrolled }">
        <div class="nav-wrapper">
            <div class="logo-container">
                <div class="logo-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="logo-text">Odovin</span>
            </div>
            
            <div class="nav-links">
                <a href="#features" class="nav-link">Features</a>
                <a href="#how-it-works" class="nav-link">How It Works</a>
                <a href="#" class="nav-link">Services</a>
                <a href="#" class="nav-link">Pricing</a>
            </div>
            
            <div class="nav-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary">Sign Up</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div style="max-width: 900px;">
                <div class="hero-badge">
                    <span class="pulse-dot"></span>
                    <span class="badge-text">Next-Gen Vehicle Management</span>
                </div>
                
                <h1 class="hero-title">
                    <span class="title-line">The Future of</span>
                    <span class="title-gradient">Vehicle Management</span>
                </h1>
                
                <p class="hero-subtitle">
                    Enter your VIN and unlock a revolutionary way to manage your vehicle. 
                    AI-powered maintenance tracking, predictive analytics, and seamless service booking—all in one platform.
                </p>
            </div>

            <!-- VIN Lookup Card -->
            <div class="vin-card">
                <div class="vin-header">
                    <div class="vin-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h2 class="vin-title">Decode Your Vehicle</h2>
                    <p class="vin-description">Enter your 17-character VIN to instantly retrieve complete vehicle specifications</p>
                </div>

                <form @submit.prevent="lookupVin">
                    <div class="vin-input-group">
                        <label for="vin" class="input-label">Vehicle Identification Number</label>
                        <div class="input-wrapper">
                            <input 
                                type="text" 
                                id="vin"
                                x-model="vin"
                                @input="vin = vin.toUpperCase()"
                                maxlength="17"
                                placeholder="1HGBH41JXMN109186"
                                class="vin-input"
                                :class="{ 'error': vinError }"
                            >
                            <div class="char-counter" :class="vin.length === 17 ? 'complete' : 'incomplete'">
                                <span x-text="vin.length"></span>/17
                            </div>
                        </div>
                        <div x-show="vinError" x-cloak class="error-message">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="vinError"></span>
                        </div>
                        <div x-show="!vinError" class="input-hint">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Find VIN on your registration, insurance card, or driver's door frame</span>
                        </div>
                    </div>

                    <button 
                        type="submit"
                        :disabled="loading || vin.length !== 17"
                        class="btn-lookup"
                    >
                        <div class="btn-content">
                            <span x-show="!loading">🚗 Decode Vehicle Now</span>
                            <span x-show="loading" style="display: none;" class="btn-content">
                                <div class="spinner"></div>
                                Decoding Vehicle...
                            </span>
                        </div>
                    </button>
                </form>

                <!-- Vehicle Result -->
                <div x-show="vehicleData" x-cloak class="vehicle-result">
                    <div class="vehicle-header">
                        <div>
                            <h3 class="vehicle-name" x-text="vehicleData?.year + ' ' + vehicleData?.make + ' ' + vehicleData?.model"></h3>
                            <p class="vehicle-trim" x-text="vehicleData?.trim"></p>
                        </div>
                        <div class="verified-badge">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Verified</span>
                        </div>
                    </div>
                    
                    <div class="vehicle-specs">
                        <div class="spec-item">
                            <span class="spec-label">Engine</span>
                            <span class="spec-value" x-text="vehicleData?.engine || 'N/A'"></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Transmission</span>
                            <span class="spec-value" x-text="vehicleData?.transmission || 'N/A'"></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Fuel Type</span>
                            <span class="spec-value" x-text="vehicleData?.fuel_type || 'N/A'"></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Body Type</span>
                            <span class="spec-value" x-text="vehicleData?.specifications?.body_class || 'N/A'"></span>
                        </div>
                    </div>

                    @auth
                        <a :href="'{{ route('vehicles.create') }}?vin=' + vin" class="btn-add-vehicle">
                            Add This Vehicle to My Garage
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn-add-vehicle">
                            Sign Up to Manage This Vehicle
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Trust Badges -->
            <div class="trust-badges">
                <div class="trust-badge">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <span>Bank-Level Security</span>
                </div>
                <div class="trust-badge">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <span>Official NHTSA Data</span>
                </div>
                <div class="trust-badge">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <span>100% Free to Start</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="section-header">
            <span class="section-badge">Powerful Features</span>
            <h2 class="section-title">Everything You Need, Nothing You Don't</h2>
            <p class="section-subtitle">
                Revolutionary tools designed to simplify every aspect of vehicle ownership
            </p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="feature-title">AI-Powered Maintenance</h3>
                <p class="feature-description">
                    Smart algorithms predict when your vehicle needs service based on mileage, time, and driving patterns. Never miss critical maintenance again.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="feature-title">Smart Expense Tracking</h3>
                <p class="feature-description">
                    Automatically categorize and analyze every dollar spent. Get insights on where your money goes and how to optimize vehicle costs.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="feature-title">One-Click Booking</h3>
                <p class="feature-description">
                    Find trusted mechanics nearby, compare prices, read reviews, and book appointments instantly. All in one seamless experience.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="feature-title">Intelligent Reminders</h3>
                <p class="feature-description">
                    Receive timely alerts for registration renewals, insurance expirations, recalls, and scheduled maintenance via push, email, or SMS.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="feature-title">Secure Document Vault</h3>
                <p class="feature-description">
                    Store all vehicle documents—registration, insurance, warranty, receipts—in an encrypted, cloud-based vault accessible anywhere.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="feature-title">Advanced Analytics</h3>
                <p class="feature-description">
                    Generate comprehensive reports with beautiful visualizations. Track fuel efficiency, cost trends, and maintenance history at a glance.
                </p>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="how-it-works-section">
        <div class="section-header">
            <span class="section-badge">Simple Process</span>
            <h2 class="section-title">Get Started in 3 Steps</h2>
            <p class="section-subtitle">
                From VIN entry to complete vehicle management in minutes
            </p>
        </div>

        <div class="steps-container">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3 class="step-title">Enter Your VIN</h3>
                <p class="step-description">
                    Simply enter your 17-character VIN and our system instantly retrieves complete vehicle specifications from official databases.
                </p>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <h3 class="step-title">Set Up Profile</h3>
                <p class="step-description">
                    Add current mileage, upload documents, and let our AI create a personalized maintenance schedule based on manufacturer recommendations.
                </p>
            </div>

            <div class="step-card">
                <div class="step-number">3</div>
                <h3 class="step-title">Sit Back & Relax</h3>
                <p class="step-description">
                    Receive intelligent reminders, book services with one click, track every expense, and never worry about vehicle management again.
                </p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2 class="cta-title">
                Ready to Transform Your Vehicle Experience?
            </h2>
            <p class="cta-subtitle">
                Join thousands of car owners who've simplified their lives with Odovin. 
                Start managing your vehicle like a pro—completely free.
            </p>
            <a href="{{ route('register') }}" class="cta-button">
                Get Started Free
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="footer-logo">
                        <div class="footer-logo-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="footer-logo-text">Odovin</span>
                    </div>
                    <p class="footer-description">
                        The most advanced vehicle management platform. Built for car enthusiasts, designed for everyone.
                    </p>
                </div>

                <div>
                    <h4 class="footer-section-title">Product</h4>
                    <div class="footer-links">
                        <a href="#features" class="footer-link">Features</a>
                        <a href="#" class="footer-link">Pricing</a>
                        <a href="#" class="footer-link">API</a>
                        <a href="#" class="footer-link">Integrations</a>
                    </div>
                </div>

                <div>
                    <h4 class="footer-section-title">Company</h4>
                    <div class="footer-links">
                        <a href="#" class="footer-link">About</a>
                        <a href="#" class="footer-link">Blog</a>
                        <a href="#" class="footer-link">Careers</a>
                        <a href="#" class="footer-link">Press</a>
                    </div>
                </div>

                <div>
                    <h4 class="footer-section-title">Support</h4>
                    <div class="footer-links">
                        <a href="#" class="footer-link">Help Center</a>
                        <a href="#" class="footer-link">Contact</a>
                        <a href="#" class="footer-link">Privacy</a>
                        <a href="#" class="footer-link">Terms</a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2024 Odovin. All rights reserved. Built with precision and passion.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function vinLookup() {
            return {
                vin: '',
                loading: false,
                vinError: '',
                vehicleData: null,
                
                async lookupVin() {
                    this.vinError = '';
                    this.vehicleData = null;
                    
                    if (this.vin.length !== 17) {
                        this.vinError = 'VIN must be exactly 17 characters';
                        return;
                    }
                    
                    this.loading = true;
                    
                    try {
                        const response = await fetch(`/vehicles/decode-vin/${this.vin}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.vehicleData = data.data;
                        } else {
                            this.vinError = data.message || 'Failed to decode VIN. Please check and try again.';
                        }
                    } catch (error) {
                        this.vinError = 'An error occurred. Please try again later.';
                        console.error('VIN Lookup Error:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>