<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AudioVibe - Modern Audio Converter</title>
    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Design Tokens & CSS Variables */
        :root {
            --bg-dark: #060813;
            --bg-card: rgba(13, 20, 38, 0.65);
            --blue-primary: #2563eb;
            --blue-glow: #3b82f6;
            --cyan-primary: #06b6d4;
            --cyan-glow: #0891b2;
            --green-success: #10b981;
            --red-error: #ef4444;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(59, 130, 246, 0.3);
            --border-active: rgba(6, 182, 212, 0.6);
            --transition-speed: 0.3s;
        }

        /* Base Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            user-select: none;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Glow Backdrop Gradients */
        .ambient-glow {
            position: absolute;
            border-radius: 50%;
            filter: blur(140px);
            opacity: 0.15;
            pointer-events: none;
            z-index: 0;
        }

        .glow-1 {
            top: -10%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--blue-glow) 0%, transparent 70%);
            animation: floatGlow 25s ease-in-out infinite alternate;
        }

        .glow-2 {
            bottom: -10%;
            right: -10%;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, var(--cyan-glow) 0%, transparent 70%);
            animation: floatGlow 30s ease-in-out infinite alternate-reverse;
        }

        .glow-3 {
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.45) 0%, transparent 70%);
            animation: floatGlow 28s ease-in-out infinite alternate;
        }

        @keyframes floatGlow {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(60px, -40px) scale(1.15); }
            100% { transform: translate(-40px, 80px) scale(0.9); }
        }

        /* Header Navigation */
        header {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 10;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff 30%, var(--blue-glow) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .logo svg {
            width: 32px;
            height: 32px;
            fill: url(#logoGrad);
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color var(--transition-speed);
            cursor: pointer;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--text-main);
        }

        /* Main Content Container */
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Hero Text */
        .hero {
            text-align: center;
            margin-bottom: 40px;
        }

        .hero h1 {
            font-size: 46px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 12px;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff 40%, var(--blue-glow) 70%, var(--cyan-glow) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 16px;
            color: var(--text-muted);
            max-width: 500px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Glassmorphism Panel Card */
        .glass-panel {
            width: 100%;
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            margin-bottom: 40px;
            transition: border var(--transition-speed) ease;
            position: relative;
            overflow: hidden;
        }

        .glass-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.4), transparent);
            pointer-events: none;
        }

        /* SPA Views styling */
        .view-section {
            display: none;
            opacity: 0;
            transform: translateY(15px);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        .view-section.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        /* View 1: Upload Area */
        .upload-zone {
            border: 2px dashed rgba(255, 255, 255, 0.15);
            border-radius: 18px;
            padding: 60px 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color var(--transition-speed), background-color var(--transition-speed);
            position: relative;
        }

        .upload-zone:hover, .upload-zone.dragover {
            border-color: var(--cyan-primary);
            background-color: rgba(6, 182, 212, 0.03);
        }

        .upload-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            border: 1px solid var(--border-color);
            color: var(--cyan-primary);
            transition: transform var(--transition-speed), color var(--transition-speed);
        }

        .upload-zone:hover .upload-icon {
            transform: translateY(-5px);
            color: #ffffff;
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.2);
            background: var(--cyan-primary);
        }

        .upload-zone h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .upload-zone p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        .btn-select {
            display: inline-block;
            background: linear-gradient(135deg, var(--blue-primary), var(--blue-glow));
            color: #ffffff;
            font-weight: 500;
            font-size: 14px;
            padding: 12px 28px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-select:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
        }

        .btn-select:active {
            transform: translateY(0);
        }

        /* View 2: Converter Config */
        .file-info-card {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 15px 20px;
            margin-bottom: 30px;
        }

        .file-icon {
            color: var(--blue-glow);
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(59, 130, 246, 0.1);
            width: 48px;
            height: 48px;
            border-radius: 10px;
        }

        .file-meta {
            flex: 1;
            overflow: hidden;
        }

        .file-name {
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 4px;
        }

        .file-size {
            font-size: 12px;
            color: var(--text-muted);
        }

        .config-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Custom Formats Grid */
        .format-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 30px;
        }

        @media (max-width: 600px) {
            .format-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .format-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
        }

        .format-card:hover {
            border-color: var(--border-hover);
            background: rgba(255, 255, 255, 0.04);
            transform: translateY(-2px);
        }

        .format-card.active {
            border-color: var(--cyan-primary);
            background: rgba(6, 182, 212, 0.08);
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.15);
        }

        .format-name {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .format-badge {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .format-card.active .format-name {
            color: var(--cyan-primary);
        }

        /* Advanced Accordion */
        .advanced-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.01);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px 20px;
            cursor: pointer;
            transition: background var(--transition-speed);
            margin-bottom: 24px;
        }

        .advanced-header:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .advanced-header-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 600;
        }

        .advanced-header-title svg {
            color: var(--text-muted);
        }

        .advanced-chevron {
            transition: transform var(--transition-speed);
        }

        .advanced-chevron.open {
            transform: rotate(180deg);
        }

        .advanced-content {
            display: none;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            padding: 0 10px 24px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        @media (max-width: 600px) {
            .advanced-content {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .form-select {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            border-radius: 8px;
            padding: 10px 14px;
            font-family: inherit;
            font-size: 13px;
            outline: none;
            cursor: pointer;
            transition: border var(--transition-speed);
        }

        .form-select:focus {
            border-color: var(--cyan-primary);
        }

        /* Action Buttons */
        .actions-group {
            display: flex;
            gap: 15px;
        }

        .btn-action {
            flex: 1;
            padding: 14px 28px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--cyan-primary), var(--blue-primary));
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 22px rgba(6, 182, 212, 0.4);
            filter: brightness(1.1);
        }

        .btn-outline {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-color);
        }

        .btn-outline:hover {
            color: var(--text-main);
            border-color: rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.02);
        }

        /* View 3: Converting Page */
        .converting-status {
            text-align: center;
            padding: 20px 0;
        }

        /* Soundwave Visualizer Animation */
        .soundwave {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 7px;
            height: 90px;
            margin-bottom: 30px;
        }

        .soundwave-bar {
            width: 5px;
            height: 12px;
            background: linear-gradient(to top, var(--blue-primary), var(--cyan-primary));
            border-radius: 3px;
            animation: wavePlay 1.2s ease-in-out infinite;
        }

        .soundwave-bar:nth-child(1) { animation-delay: 0.1s; height: 18px; }
        .soundwave-bar:nth-child(2) { animation-delay: 0.4s; height: 26px; }
        .soundwave-bar:nth-child(3) { animation-delay: 0.6s; height: 35px; }
        .soundwave-bar:nth-child(4) { animation-delay: 0.2s; height: 45px; }
        .soundwave-bar:nth-child(5) { animation-delay: 0.5s; height: 32px; }
        .soundwave-bar:nth-child(6) { animation-delay: 0.7s; height: 20px; }
        .soundwave-bar:nth-child(7) { animation-delay: 0.3s; height: 14px; }

        @keyframes wavePlay {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(3.5); }
        }

        .converting-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .converting-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        /* Progress Bar Glow */
        .progress-container {
            position: relative;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            height: 12px;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--blue-primary) 0%, var(--cyan-primary) 100%);
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(6, 182, 212, 0.5);
            transition: width 0.4s ease;
        }

        .progress-meta {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 35px;
        }

        .btn-cancel {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--red-error);
        }

        .btn-cancel:hover {
            background: var(--red-error);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
            transform: translateY(-2px);
        }

        /* View 4: Success Result */
        .success-wrapper {
            text-align: center;
            padding: 10px 0;
        }

        /* Checkmark Draw Animation */
        .success-checkmark {
            width: 76px;
            height: 76px;
            margin: 0 auto 24px;
        }

        .checkmark-circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 3;
            stroke-miterlimit: 10;
            stroke: var(--cyan-primary);
            fill: none;
            animation: strokeCheck 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }

        .checkmark-kick {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            stroke-width: 4;
            stroke: #ffffff;
            fill: none;
            animation: strokeCheck 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.6s forwards;
        }

        @keyframes strokeCheck {
            100% { stroke-dashoffset: 0; }
        }

        .success-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .success-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        /* Stats Comparison Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 15px;
            text-align: left;
        }

        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .stat-val {
            font-size: 16px;
            font-weight: 600;
        }

        .stat-badge {
            display: inline-block;
            background: rgba(16, 185, 129, 0.15);
            color: var(--green-success);
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
            margin-left: 8px;
            vertical-align: middle;
        }

        .btn-download {
            background: linear-gradient(135deg, var(--cyan-primary) 0%, var(--blue-primary) 100%);
            color: #ffffff;
            box-shadow: 0 4px 20px rgba(6, 182, 212, 0.3);
            animation: downloadPulse 2s infinite;
        }

        @keyframes downloadPulse {
            0%, 100% { box-shadow: 0 4px 20px rgba(6, 182, 212, 0.3); }
            50% { box-shadow: 0 4px 30px rgba(6, 182, 212, 0.6); }
        }

        .btn-download:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        /* View 5: Error Screen */
        .error-wrapper {
            text-align: center;
            padding: 20px 0;
        }

        .error-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 20px;
            color: var(--red-error);
            background: rgba(239, 68, 68, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .error-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .error-message {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 30px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.5;
        }

        /* Content Section (Other Pages: Formats / Info) */
        .info-section {
            width: 100%;
            max-width: 800px;
            margin: 0 auto 60px;
            position: relative;
            z-index: 10;
            padding: 0 20px;
        }

        .tabs-header {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 30px;
            gap: 20px;
        }

        .tab-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            padding: 12px 4px;
            cursor: pointer;
            transition: color var(--transition-speed);
            position: relative;
        }

        .tab-btn.active {
            color: var(--text-main);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--cyan-primary);
            box-shadow: 0 0 10px rgba(6, 182, 212, 0.5);
        }

        .tab-panel {
            display: none;
            animation: fadeIn 0.4s ease forwards;
        }

        .tab-panel.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Format Showcase Grid */
        .showcase-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        @media (max-width: 600px) {
            .showcase-grid {
                grid-template-columns: 1fr;
            }
        }

        .showcase-card {
            background: rgba(255, 255, 255, 0.01);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            transition: border var(--transition-speed);
        }

        .showcase-card:hover {
            border-color: rgba(255, 255, 255, 0.15);
        }

        .showcase-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .showcase-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--cyan-primary);
        }

        .showcase-type {
            font-size: 10px;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            font-weight: 600;
        }

        .showcase-desc {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* About Panel details */
        .about-text {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .about-text p {
            margin-bottom: 16px;
        }

        .about-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 24px;
        }

        @media (max-width: 600px) {
            .about-features {
                grid-template-columns: 1fr;
            }
        }

        .feature-item {
            display: flex;
            gap: 12px;
        }

        .feature-icon {
            color: var(--cyan-primary);
            flex-shrink: 0;
            width: 24px;
            height: 24px;
        }

        .feature-body h4 {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .feature-body p {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* Footer Copyright */
        footer {
            width: 100%;
            text-align: center;
            padding: 40px 20px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.25);
            border-top: 1px solid rgba(255, 255, 255, 0.02);
            position: relative;
            z-index: 10;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- Ambient Glow Blobs -->
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>
    <div class="ambient-glow glow-3"></div>

    <!-- Header Navigation -->
    <header>
        <a href="" class="logo">
            <svg viewBox="0 0 24 24">
                <defs>
                    <linearGradient id="logoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#3b82f6" />
                        <stop offset="100%" stop-color="#06b6d4" />
                    </linearGradient>
                </defs>
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14h-2v-4H9V9h2V7h2v2h2v3h-2v4z"/>
            </svg>
            AudioVibe
        </a>
        <div class="nav-links">
            <a class="nav-link active" onclick="document.getElementById('info-section-anchor').scrollIntoView({behavior: 'smooth'})">Features</a>
            <a class="nav-link" href="https://github.com/GyanD/codexffmpeg" target="_blank" rel="noopener">FFmpeg 8.1</a>
        </div>
    </header>

    <!-- Main Workspace -->
    <main>
        <div class="hero">
            <h1>Convert Your Audio</h1>
            <p>Convert your music and recordings locally with high speed, maximum privacy, and no quality loss.</p>
        </div>

        <div class="glass-panel" id="main-panel">
            
            <!-- VIEW 1: UPLOAD ZONE -->
            <div class="view-section active" id="view-upload">
                <div class="upload-zone" id="dropzone">
                    <input type="file" id="file-input" style="display: none;" accept="audio/*">
                    <div class="upload-icon">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <h3>Drag & Drop your audio here</h3>
                    <p>Or browse local files on your device</p>
                    <button class="btn-select" onclick="document.getElementById('file-input').click()">Select File</button>
                </div>
            </div>

            <!-- VIEW 2: CONVERSION OPTIONS -->
            <div class="view-section" id="view-config">
                <div class="file-info-card">
                    <div class="file-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                        </svg>
                    </div>
                    <div class="file-meta">
                        <div class="file-name" id="selected-filename">filename.wav</div>
                        <div class="file-size" id="selected-filesize">0.0 MB</div>
                    </div>
                </div>

                <div class="config-title">Convert To:</div>
                <div class="format-grid">
                    <div class="format-card active" data-format="mp3">
                        <div class="format-name">MP3</div>
                        <div class="format-badge">Lossy Standard</div>
                    </div>
                    <div class="format-card" data-format="wav">
                        <div class="format-name">WAV</div>
                        <div class="format-badge">Lossless PCM</div>
                    </div>
                    <div class="format-card" data-format="ogg">
                        <div class="format-name">OGG</div>
                        <div class="format-badge">Vorbis Web</div>
                    </div>
                    <div class="format-card" data-format="flac">
                        <div class="format-name">FLAC</div>
                        <div class="format-badge">Audiophile</div>
                    </div>
                    <div class="format-card" data-format="m4a">
                        <div class="format-name">M4A</div>
                        <div class="format-badge">AAC Quality</div>
                    </div>
                    <div class="format-card" data-format="aac">
                        <div class="format-name">AAC</div>
                        <div class="format-badge">Raw Stream</div>
                    </div>
                    <div class="format-card" data-format="wma">
                        <div class="format-name">WMA</div>
                        <div class="format-badge">Windows Audio</div>
                    </div>
                </div>

                <!-- Advanced Settings Accordion -->
                <div class="advanced-header" id="advanced-toggle">
                    <div class="advanced-header-title">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        Advanced Audio Settings
                    </div>
                    <div class="advanced-chevron" id="advanced-arrow">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                <div class="advanced-content" id="advanced-panel">
                    <div class="form-group">
                        <label for="bitrate-select">Bitrate Quality</label>
                        <select class="form-select" id="bitrate-select">
                            <option value="auto" selected>Auto (Keep Original)</option>
                            <option value="320k">320 kbps (High Quality)</option>
                            <option value="256k">256 kbps (Good)</option>
                            <option value="192k">192 kbps (Standard)</option>
                            <option value="128k">128 kbps (Low Size)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sample-select">Sample Rate</label>
                        <select class="form-select" id="sample-select">
                            <option value="auto" selected>Auto</option>
                            <option value="48000">48000 Hz (Studio)</option>
                            <option value="44100">44100 Hz (CD)</option>
                            <option value="22050">22050 Hz (Low)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="channels-select">Audio Channels</label>
                        <select class="form-select" id="channels-select">
                            <option value="auto" selected>Auto</option>
                            <option value="stereo">Stereo (2 Channels)</option>
                            <option value="mono">Mono (1 Channel)</option>
                        </select>
                    </div>
                </div>

                <div class="actions-group">
                    <button class="btn-action btn-outline" id="btn-config-back">Back</button>
                    <button class="btn-action btn-primary" id="btn-convert-start">Convert File</button>
                </div>
            </div>

            <!-- VIEW 3: CONVERTING PROGRESS -->
            <div class="view-section" id="view-converting">
                <div class="converting-status">
                    <!-- Soundwave CSS Visualizer -->
                    <div class="soundwave">
                        <div class="soundwave-bar"></div>
                        <div class="soundwave-bar"></div>
                        <div class="soundwave-bar"></div>
                        <div class="soundwave-bar"></div>
                        <div class="soundwave-bar"></div>
                        <div class="soundwave-bar"></div>
                        <div class="soundwave-bar"></div>
                    </div>

                    <div class="converting-title">Converting your file...</div>
                    <div class="converting-subtitle" id="conversion-meta-details">Processing file details</div>

                    <div class="progress-container">
                        <div class="progress-bar" id="pb-inner"></div>
                    </div>
                    <div class="progress-meta">
                        <div id="pb-percent">0%</div>
                        <div id="pb-duration">00:00:00 / 00:00:00</div>
                    </div>

                    <button class="btn-action btn-cancel" id="btn-convert-cancel">Cancel Process</button>
                </div>
            </div>

            <!-- VIEW 4: SUCCESS RESULT -->
            <div class="view-section" id="view-success">
                <div class="success-wrapper">
                    <!-- Checkmark SVG Draw -->
                    <div class="success-checkmark">
                        <svg viewBox="0 0 52 52">
                            <circle class="checkmark-circle" cx="26" cy="26" r="25" />
                            <path class="checkmark-kick" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                        </svg>
                    </div>

                    <div class="success-title">Conversion Finished!</div>
                    <div class="success-subtitle">Your audio file is ready for download</div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-label">Original File</div>
                            <div class="stat-val" id="stat-orig-val">0.0 MB</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Converted Size</div>
                            <div class="stat-val">
                                <span id="stat-conv-val">0.0 MB</span>
                                <span class="stat-badge" id="stat-saved-badge">0% Saved</span>
                            </div>
                        </div>
                    </div>

                    <div class="actions-group">
                        <button class="btn-action btn-outline" id="btn-success-reset">Convert Another</button>
                        <button class="btn-action btn-download" id="btn-download-file">Download File</button>
                    </div>
                </div>
            </div>

            <!-- VIEW 5: ERROR PAGE -->
            <div class="view-section" id="view-error">
                <div class="error-wrapper">
                    <div class="error-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="error-title">Conversion Failed</div>
                    <div class="error-message" id="error-details">An unexpected error occurred during audio processing. Please try again.</div>
                    
                    <button class="btn-action btn-outline" id="btn-error-reset" style="max-width: 200px; margin: 0 auto;">Try Again</button>
                </div>
            </div>

        </div>

        <!-- Information Tabs anchor -->
        <div id="info-section-anchor" style="padding-top: 10px;"></div>

        <!-- Extra Information Tabs (Other Pages) -->
        <div class="info-section">
            <div class="tabs-header">
                <button class="tab-btn active" data-tab="tab-formats">Supported Formats</button>
                <button class="tab-btn" data-tab="tab-about">Why AudioVibe?</button>
            </div>

            <div class="tab-panel active" id="tab-formats">
                <div class="showcase-grid">
                    <div class="showcase-card">
                        <div class="showcase-header">
                            <div class="showcase-name">MP3 Format</div>
                            <span class="showcase-type">Lossy</span>
                        </div>
                        <div class="showcase-desc">
                            The universal standard for music files. Offers excellent file compression with reasonable audio quality, making it compatible with almost any device.
                        </div>
                    </div>
                    <div class="showcase-card">
                        <div class="showcase-header">
                            <div class="showcase-name">WAV Format</div>
                            <span class="showcase-type">Lossless</span>
                        </div>
                        <div class="showcase-desc">
                            Uncompressed CD-quality audio. Contains full, raw audio waveform data. Perfect for editing, archiving, and studio production but yields large file sizes.
                        </div>
                    </div>
                    <div class="showcase-card">
                        <div class="showcase-header">
                            <div class="showcase-name">FLAC Format</div>
                            <span class="showcase-type">Lossless</span>
                        </div>
                        <div class="showcase-desc">
                            Free Lossless Audio Codec. Compresses audio data without discarding any information, providing bit-perfect sound quality at half the file size of WAV.
                        </div>
                    </div>
                    <div class="showcase-card">
                        <div class="showcase-header">
                            <div class="showcase-name">OGG Format</div>
                            <span class="showcase-type">Lossy</span>
                        </div>
                        <div class="showcase-desc">
                            Vorbis compression standard. Highly efficient format commonly used for gaming audio streams and web development due to open-source licensing.
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-panel" id="tab-about">
                <div class="about-text">
                    <p>
                        AudioVibe is a high-performance web utility built to demonstrate secure and efficient local file conversions. The system employs a sandboxed execution stack using a local <strong>PHP 8.5</strong> instance combined with a compiled <strong>FFmpeg 8.1</strong> core processor.
                    </p>
                    <div class="about-features">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="feature-body">
                                <h4>100% Privacy-Focused</h4>
                                <p>All operations take place inside your server's isolated file cache. There are no uploads to external cloud processors.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="feature-body">
                                <h4>Asynchronous Multithreading</h4>
                                <p>Long conversions run inside lightweight background workers. You can cancel tasks instantly without crashing the engine.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Copyright -->
    <footer>
        &copy; 2026 AudioVibe Studio. Powered by PHP-FFmpeg Core. All rights reserved.
    </footer>

    <!-- App Frontend JavaScript Logic -->
    <script>
        // SPA Controller & Core Logic Namespace
        const App = {
            state: {
                taskId: null,
                fileName: null,
                fileSize: 0,
                currentFormat: 'mp3',
                progressInterval: null,
                errorCount: 0,
                maxErrors: 5,
                isAdvancedOpen: false
            },

            // Dom Element Caching
            dom: {
                dropzone: document.getElementById('dropzone'),
                fileInput: document.getElementById('file-input'),
                mainPanel: document.getElementById('main-panel'),
                viewUpload: document.getElementById('view-upload'),
                viewConfig: document.getElementById('view-config'),
                viewConverting: document.getElementById('view-converting'),
                viewSuccess: document.getElementById('view-success'),
                viewError: document.getElementById('view-error'),
                
                selectedFilename: document.getElementById('selected-filename'),
                selectedFilesize: document.getElementById('selected-filesize'),
                
                advancedToggle: document.getElementById('advanced-toggle'),
                advancedArrow: document.getElementById('advanced-arrow'),
                advancedPanel: document.getElementById('advanced-panel'),
                
                formatCards: document.querySelectorAll('.format-card'),
                
                btnConfigBack: document.getElementById('btn-config-back'),
                btnConvertStart: document.getElementById('btn-convert-start'),
                btnConvertCancel: document.getElementById('btn-convert-cancel'),
                btnSuccessReset: document.getElementById('btn-success-reset'),
                btnDownloadFile: document.getElementById('btn-download-file'),
                btnErrorReset: document.getElementById('btn-error-reset'),
                
                pbInner: document.getElementById('pb-inner'),
                pbPercent: document.getElementById('pb-percent'),
                pbDuration: document.getElementById('pb-duration'),
                conversionMetaDetails: document.getElementById('conversion-meta-details'),
                
                statOrigVal: document.getElementById('stat-orig-val'),
                statConvVal: document.getElementById('stat-conv-val'),
                statSavedBadge: document.getElementById('stat-saved-badge'),
                errorDetails: document.getElementById('error-details'),
                
                tabBtns: document.querySelectorAll('.tab-btn'),
                tabPanels: document.querySelectorAll('.tab-panel')
            },

            init() {
                this.bindEvents();
            },

            // Register Event Handlers
            bindEvents() {
                // Drag & Drop Handlers
                const dropzone = this.dom.dropzone;
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        dropzone.classList.add('dragover');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        dropzone.classList.remove('dragover');
                    }, false);
                });

                dropzone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    if (files.length > 0) {
                        this.handleFileSelect(files[0]);
                    }
                });

                this.dom.fileInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        this.handleFileSelect(e.target.files[0]);
                    }
                });

                // Format Cards Click Handler
                this.dom.formatCards.forEach(card => {
                    card.addEventListener('click', () => {
                        this.dom.formatCards.forEach(c => c.classList.remove('active'));
                        card.classList.add('active');
                        this.state.currentFormat = card.dataset.format;
                    });
                });

                // Advanced Accordion Toggle
                this.dom.advancedToggle.addEventListener('click', () => {
                    this.state.isAdvancedOpen = !this.state.isAdvancedOpen;
                    if (this.state.isAdvancedOpen) {
                        this.dom.advancedPanel.style.display = 'grid';
                        this.dom.advancedArrow.classList.add('open');
                    } else {
                        this.dom.advancedPanel.style.display = 'none';
                        this.dom.advancedArrow.classList.remove('open');
                    }
                });

                // Navigation Button Handlers
                this.dom.btnConfigBack.addEventListener('click', () => {
                    this.switchView('upload');
                    this.resetUploadState();
                });

                this.dom.btnConvertStart.addEventListener('click', () => {
                    this.startConversion();
                });

                this.dom.btnConvertCancel.addEventListener('click', () => {
                    this.cancelConversion();
                });

                this.dom.btnSuccessReset.addEventListener('click', () => {
                    this.switchView('upload');
                    this.resetUploadState();
                });

                this.dom.btnErrorReset.addEventListener('click', () => {
                    this.switchView('upload');
                    this.resetUploadState();
                });

                this.dom.btnDownloadFile.addEventListener('click', () => {
                    if (this.state.taskId) {
                        window.location.href = `api.php?action=download&task_id=${this.state.taskId}`;
                    }
                });

                // Information Tabs Swapping
                this.dom.tabBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        this.dom.tabBtns.forEach(b => b.classList.remove('active'));
                        this.dom.tabPanels.forEach(p => p.classList.remove('active'));
                        
                        btn.classList.add('active');
                        const targetPanel = document.getElementById(btn.dataset.tab);
                        if (targetPanel) {
                            targetPanel.classList.add('active');
                        }
                    });
                });
            },

            // File Upload Handler via AJAX
            handleFileSelect(file) {
                if (!file.type.startsWith('audio/') && !this.hasAudioExtension(file.name)) {
                    alert('Please select an audio file (e.g. mp3, wav, flac, ogg, etc.)');
                    return;
                }

                this.state.fileName = file.name;
                this.state.fileSize = file.size;

                this.dom.selectedFilename.textContent = file.name;
                this.dom.selectedFilesize.textContent = this.formatSize(file.size);

                // Instantly upload the file
                const formData = new FormData();
                formData.append('audio_file', file);

                // Show basic uploading loading state inside drag & drop zone
                this.dom.dropzone.innerHTML = `
                    <div class="soundwave">
                        <div class="soundwave-bar"></div>
                        <div class="soundwave-bar"></div>
                        <div class="soundwave-bar"></div>
                        <div class="soundwave-bar"></div>
                    </div>
                    <h3>Uploading File...</h3>
                    <p style="margin-bottom: 0;">Uploading ${file.name} to converter cache</p>
                `;

                fetch('api.php?action=upload', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.state.taskId = data.task_id;
                        this.switchView('config');
                    } else {
                        throw new Error(data.message || 'Upload failed');
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.showErrorView(err.message || 'Error occurred while uploading audio file.');
                });
            },

            // Start Conversion API call
            startConversion() {
                if (!this.state.taskId) return;

                const bitrate = document.getElementById('bitrate-select').value;
                const sampleRate = document.getElementById('sample-select').value;
                const channels = document.getElementById('channels-select').value;

                const params = new URLSearchParams();
                params.append('task_id', this.state.taskId);
                params.append('format', this.state.currentFormat);
                params.append('bitrate', bitrate);
                params.append('sample_rate', sampleRate);
                params.append('channels', channels);

                this.dom.conversionMetaDetails.textContent = `Preparing: ${this.state.fileName} to ${this.state.currentFormat.toUpperCase()}`;
                this.switchView('converting');
                
                // Initialize progress indicators
                this.updateProgressBar(0, '00:00:00 / 00:00:00');

                fetch('api.php?action=convert', {
                    method: 'POST',
                    body: params
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Start polling progress log
                        this.pollStatus();
                    } else {
                        throw new Error(data.message || 'Failed to start conversion');
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.showErrorView(err.message || 'Failed to initialize FFmpeg conversion process.');
                });
            },

            // Poll conversion progress — resilient, retries on network errors
            pollStatus() {
                this.state.errorCount = 0;

                const checkStatus = () => {
                    fetch(`api.php?action=status&task_id=${this.state.taskId}`)
                    .then(res => {
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        return res.json();
                    })
                    .then(data => {
                        // Reset error count on any successful response
                        this.state.errorCount = 0;

                        if (data.success) {
                            if (data.status === 'success') {
                                this.stopPolling();
                                this.updateProgressBar(100, '');
                                // Small delay so the bar visually hits 100% before switching
                                setTimeout(() => this.showSuccessView(data.converted_size), 400);
                            } else if (data.status === 'error') {
                                this.stopPolling();
                                this.showErrorView(data.message);
                            } else if (data.status === 'converting') {
                                const timeStr = (data.current_time && data.total_time)
                                    ? `${data.current_time} / ${data.total_time}`
                                    : '';
                                this.updateProgressBar(data.percent || 0, timeStr);
                            }
                        } else {
                            // API-level error — count it but don't stop
                            this.state.errorCount++;
                            console.warn('Status API error:', data.message);
                            if (this.state.errorCount >= this.state.maxErrors) {
                                this.stopPolling();
                                this.showErrorView('Could not reach the server after multiple attempts.');
                            }
                        }
                    })
                    .catch(err => {
                        this.state.errorCount++;
                        console.warn(`Poll error #${this.state.errorCount}:`, err.message);
                        // Only abort after maxErrors consecutive failures
                        if (this.state.errorCount >= this.state.maxErrors) {
                            this.stopPolling();
                            this.showErrorView('Connection lost. Please refresh and try again.');
                        }
                    });
                };

                // Poll every 1200ms — gives single-threaded PHP server breathing room
                checkStatus();
                this.state.progressInterval = setInterval(checkStatus, 1200);
            },

            stopPolling() {
                if (this.state.progressInterval) {
                    clearInterval(this.state.progressInterval);
                    this.state.progressInterval = null;
                }
            },

            // Cancel running background task
            cancelConversion() {
                this.stopPolling();
                
                if (!this.state.taskId) {
                    this.switchView('upload');
                    this.resetUploadState();
                    return;
                }

                const params = new URLSearchParams();
                params.append('task_id', this.state.taskId);

                fetch('api.php?action=cancel', {
                    method: 'POST',
                    body: params
                })
                .then(() => {
                    this.switchView('upload');
                    this.resetUploadState();
                })
                .catch(err => {
                    console.error(err);
                    this.switchView('upload');
                    this.resetUploadState();
                });
            },

            // Update UI view states (SPA switch)
            switchView(viewName) {
                const views = [
                    { name: 'upload', el: this.dom.viewUpload },
                    { name: 'config', el: this.dom.viewConfig },
                    { name: 'converting', el: this.dom.viewConverting },
                    { name: 'success', el: this.dom.viewSuccess },
                    { name: 'error', el: this.dom.viewError }
                ];

                views.forEach(v => {
                    if (v.name === viewName) {
                        v.el.classList.add('active');
                    } else {
                        v.el.classList.remove('active');
                    }
                });
            },

            // Render Success stats
            showSuccessView(convertedSizeBytes) {
                this.dom.statOrigVal.textContent = this.formatSize(this.state.fileSize);
                this.dom.statConvVal.textContent = this.formatSize(convertedSizeBytes);
                
                // Calculate space saved percentage
                const diff = this.state.fileSize - convertedSizeBytes;
                const savedPercent = Math.round((diff / this.state.fileSize) * 100);

                if (savedPercent > 0) {
                    this.dom.statSavedBadge.textContent = `${savedPercent}% Smaller`;
                    this.dom.statSavedBadge.style.background = 'rgba(16, 185, 129, 0.15)';
                    this.dom.statSavedBadge.style.color = 'var(--green-success)';
                    this.dom.statSavedBadge.style.display = 'inline-block';
                } else if (savedPercent < 0) {
                    this.dom.statSavedBadge.textContent = `${Math.abs(savedPercent)}% Larger`;
                    this.dom.statSavedBadge.style.background = 'rgba(239, 68, 68, 0.15)';
                    this.dom.statSavedBadge.style.color = 'var(--red-error)';
                    this.dom.statSavedBadge.style.display = 'inline-block';
                } else {
                    this.dom.statSavedBadge.style.display = 'none';
                }

                this.switchView('success');
            },

            // Render Error UI state
            showErrorView(errMsg) {
                this.dom.errorDetails.textContent = errMsg || 'An unexpected error occurred during audio processing.';
                this.switchView('error');
            },

            // UI helper: format sizes
            formatSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            },

            // Progress bar DOM controller
            updateProgressBar(percent, durationStr) {
                this.dom.pbInner.style.width = `${percent}%`;
                this.dom.pbPercent.textContent = `${percent}%`;
                this.dom.pbDuration.textContent = durationStr;
            },

            // Reset drag and drop interface
            resetUploadState() {
                this.state.taskId = null;
                this.state.fileName = null;
                this.state.fileSize = 0;
                this.dom.fileInput.value = '';

                // Restore default drag and drop inner HTML
                this.dom.dropzone.innerHTML = `
                    <input type="file" id="file-input" style="display: none;" accept="audio/*">
                    <div class="upload-icon">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <h3>Drag & Drop your audio here</h3>
                    <p>Or browse local files on your device</p>
                    <button class="btn-select" onclick="document.getElementById('file-input').click()">Select File</button>
                `;

                // Re-bind the file input listener since the HTML got replaced
                document.getElementById('file-input').addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        this.handleFileSelect(e.target.files[0]);
                    }
                });
            },

            // Fallback extension verification for files without MIME types
            hasAudioExtension(filename) {
                const ext = filename.split('.').pop().toLowerCase();
                const audioExts = ['mp3', 'wav', 'ogg', 'flac', 'm4a', 'aac', 'wma', 'aiff', 'amr', 'opus', 'webm', 'mp4'];
                return audioExts.includes(ext);
            }
        };

        // Run application on load
        document.addEventListener('DOMContentLoaded', () => {
            App.init();
        });
    </script>
</body>
</html>
