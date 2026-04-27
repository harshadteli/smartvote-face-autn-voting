# 🗳️ NextGen E-Voting Platform 
<img src="https://media.licdn.com/dms/image/v2/D4D12AQEMsubhibrsjQ/article-cover_image-shrink_600_2000/B4DZUEOYyeHAAQ-/0/1739532637711?e=2147483647&v=beta&t=s0X5EYL6KxuJC6Sqr8UYde86Fp9Km_ujrTehW7-2tn8" width="100%" height="auto" alt="E-BANNER-IMAGE"/>
<hr>

![E-Voting System](https://img.shields.io/badge/Version-1.2-00b894?style=for-the-badge) ![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

A highly secure, state-of-the-art Electronic Voting System designed to revolutionize democratic elections. Built with modern web technologies, this platform prioritizes biometric security, live artificial intelligence assistance, automated digital documentation, and a stunning "Glassmorphism" UI.

---

## ✨ Unique & Modern Features

### 🛡️ 1. Advanced Biometric Verification
Say goodbye to fraudulent voting. The platform integrates **`face-api.js`** for real-time facial landmark detection and **FingerprintJS** for device verification. Voters are biometrically scanned to ensure strict 1-to-1 identity matching before they can access the voting terminal.

### 🤖 2. Admin AI Command Center
Managing elections has never been this futuristic. The Admin Panel features an integrated **ChatGPT-style AI Assistant** capable of:
- Fetching live database statistics (Turnout, Leading Candidates, Voter Verification Status).
- Rendering responsive data tables directly inside the chat window.
- **Voice Output (TTS):** The AI reads reports aloud with Text-to-Speech technology.

### 📱 3. Responsive Public Chatbot (v2.0)
A floating user assistant loaded with a 14-topic knowledge base to guide voters through the registration and voting process. Features quick-action "Suggestion Chips", smooth animations, and multi-language support.

### 📊 4. Live Analytics & Dashboards
Watch democracy unfold in real-time. Built with **Chart.js**, the platform dynamically updates stunning visual dashboards (Doughnut & Bar charts) showing active vote distributions and turnout statistics without needing page reloads.

### 📄 5. Automated E-Documentation via Google Cloud
Instead of generating static attachments, the system leverages **Google Apps Script** to:
- Automatically generate digital E-Voter Registration Cards (with embedded security QR codes).
- Generate verifiable PDF Voting Receipts upon cast.
- Securely host PDFs on Google Drive to ensure 100% email deliverability (bypassing strict spam algorithms).

### 🌐 6. Native Localization (i18n)
Designed for maximum accessibility, the software features instant, zero-reload language toggling. Currently supports **English, Hindi, and Marathi**.

### 🎨 7. Premium Glassmorphism Design
Built entirely on a custom modern CSS framework, featuring responsive frosted-glass panels, smooth micro-animations, and a responsive layout that adapts flawlessly to Desktop, Tablet, and Mobile devices.

---

## 📜 Academic Research Paper

This project is the technical implementation for an upcoming academic research paper: **"Modern Biometric & Cloud-Integrated Electronic Voting Systems"**.

The research paper details the practical integration of facial recognition APIs, AI-driven administrative models, and automated cloud-hosted receipt verification to ensure transparent and tamper-proof democratic elections.

<div align="left">
  <a href="#" target="_blank">
    <img src="https://img.shields.io/badge/📑_Read_Research_Paper-Pending_Publication-0052cc?style=for-the-badge&logo=googlescholar&logoColor=white" alt="Research Paper Link" />
  </a>
</div>

> *(The official link will be provided here once the paper is published).*

---

## 🚀 Installation & Setup

### Prerequisites
- XAMPP / WAMP / MAMP (PHP 7.4+ recommended)
- MySQL Database

### Step 1: Database Setup
1. Open phpMyAdmin.
2. Create a new database named `evoting_db`.
3. Import the `database.sql` file provided in the root directory.

### Step 2: Configure Environment
1. Navigate to `includes/config.php`.
2. Ensure your database credentials match your local server.
3. Replace `GAS_WEBAPP_URL` with your deployed Google Apps Script URL (found in `GAS-Code.txt`).

### Step 3: Run the Application
Start your Apache and MySQL servers. Navigate to `http://localhost/evoting1.2/` to access the platform.

---

## 🔒 Security Architecture
- **One-Vote Constraint:** Strict session and database checks prevent duplicate voting.
- **XSS & SQLi Prevention:** Complete implementation of PDO prepared statements and HTML entity sanitization.
- **Spam Mitigation:** Automated emails are strictly plain-text structured with trusted Google Drive links to ensure flawless Inbox delivery.

---

## 📁 Directory Structure Overview

```text
/evoting1.2
│
├── /admin                  # Secure Admin Panel & AI Command Center
│   ├── /api                # Live Data APIs for the Admin Chatbot
│   └── /includes           # Admin layout & Chatbot Components
│
├── /assets                 
│   ├── /css                # Global Glassmorphism Styles & i18n logic
│   ├── /js                 # Chatbot Logic & Client-side scripts
│   └── /models             # Pre-trained weights for Face-API.js
│
├── /includes               # Core System Files (Config, Header, Footer)
├── GAS-Code.txt            # Google Apps Script code for PDF automation
└── database.sql            # Core database schema
```

---
*Built to ensure transparency, security, and accessibility for modern elections.*
