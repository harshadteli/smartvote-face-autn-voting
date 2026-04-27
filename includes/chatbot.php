<!-- ═══════════════════════════════════════════════════════════
     E-Vote AI Assistant Chatbot v2.0
     Updated with: Face Verification, QR Card, Charts,
     Voting Receipt, Multi-Language (i18n) support
════════════════════════════════════════════════════════════ -->

<div id="chatbot-wrapper" style="position:fixed; bottom:30px; right:30px; z-index:10000; font-family:'Poppins','Segoe UI',sans-serif;">

    <!-- Toggle Button -->
    <button id="chatbot-toggle" title="E-Vote Assistant" style="width:62px; height:62px; border-radius:50%; background:linear-gradient(135deg,#6c5ce7,#a29bfe); border:none; color:white; font-size:26px; cursor:pointer; box-shadow:0 6px 20px rgba(108,92,231,0.5); display:flex; align-items:center; justify-content:center; transition:transform 0.3s; position:relative;">
        <i class="fas fa-robot"></i>
        <span id="bot-badge" style="position:absolute; top:-4px; right:-4px; background:#e17055; color:white; border-radius:50%; width:18px; height:18px; font-size:10px; display:flex; align-items:center; justify-content:center; font-weight:700; border:2px solid white;">1</span>
    </button>

    <!-- Chat Window -->
    <div id="chatbot-window" style="display:none; width:360px; max-height:540px; background:rgba(255,255,255,0.97); backdrop-filter:blur(12px); border-radius:22px; box-shadow:0 15px 50px rgba(0,0,0,0.25); flex-direction:column; overflow:hidden; border:1px solid rgba(255,255,255,0.4); margin-bottom:16px;">

        <!-- Header -->
        <div style="background:linear-gradient(135deg,#6c5ce7,#a29bfe); color:white; padding:14px 18px; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:10px; height:10px; background:#55efc4; border-radius:50%; animation:pulse 1.5s infinite; flex-shrink:0;"></div>
                <div>
                    <div style="font-weight:700; font-size:0.95rem; letter-spacing:0.3px;">E-Vote Assistant</div>
                    <div style="font-size:0.7rem; opacity:0.85;">Online · Ask me anything</div>
                </div>
            </div>
            <div style="display:flex; gap:8px; align-items:center;">
                <button id="bot-clear" title="Clear chat" style="background:rgba(255,255,255,0.15); border:none; color:white; cursor:pointer; border-radius:50%; width:30px; height:30px; font-size:12px;"><i class="fas fa-trash-alt"></i></button>
                <button id="chatbot-close" style="background:rgba(255,255,255,0.15); border:none; color:white; cursor:pointer; border-radius:50%; width:30px; height:30px; font-size:14px;"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="chatbot-messages" style="flex:1; padding:16px; overflow-y:auto; display:flex; flex-direction:column; gap:12px; min-height:0; max-height:340px;"></div>

        <!-- Suggestion Chips -->
        <div id="chatbot-suggestions" style="padding:8px 12px; display:flex; gap:7px; overflow-x:auto; border-top:1px solid #eee; background:#fafafa; white-space:nowrap; scrollbar-width:none; flex-shrink:0;">
            <button class="s-chip" data-cmd="How to vote?">🗳️ How to Vote</button>
            <button class="s-chip" data-cmd="Face verification">👤 Face Verify</button>
            <button class="s-chip" data-cmd="QR code voter card">📱 QR Card</button>
            <button class="s-chip" data-cmd="Voting receipt">📄 Receipt</button>
            <button class="s-chip" data-cmd="Live election results">📊 Live Charts</button>
            <button class="s-chip" data-cmd="Change language">🌍 Language</button>
            <button class="s-chip" data-cmd="Election report">🔍 Elections</button>
            <button class="s-chip" data-cmd="Register">📝 Register</button>
        </div>

        <!-- Input Area -->
        <div style="padding:12px 14px; border-top:1px solid #eee; display:flex; gap:8px; background:#f9f9f9; flex-shrink:0;">
            <input type="text" id="chatbot-input" placeholder="Ask me about voting, features..." style="flex:1; border:1.5px solid #e0e0e0; padding:9px 14px; border-radius:25px; outline:none; font-size:13px; font-family:inherit; transition:border 0.2s; background:#fff;">
            <button id="chatbot-send" style="width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#6c5ce7,#a29bfe); border:none; color:white; cursor:pointer; flex-shrink:0; transition:transform 0.2s;">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0%   { transform:scale(0.95); box-shadow:0 0 0 0 rgba(85,239,196,0.7); }
    70%  { transform:scale(1);    box-shadow:0 0 0 8px rgba(85,239,196,0); }
    100% { transform:scale(0.95); box-shadow:0 0 0 0 rgba(85,239,196,0); }
}
@keyframes msgIn {
    from { opacity:0; transform:translateY(8px); }
    to   { opacity:1; transform:translateY(0); }
}
.bot-msg {
    background:#f1f2f6; color:#2d3436;
    padding:10px 14px; border-radius:16px 16px 16px 3px;
    max-width:88%; align-self:flex-start; font-size:13px; line-height:1.5;
    box-shadow:0 2px 6px rgba(0,0,0,0.06); animation:msgIn 0.25s ease;
}
.user-msg {
    background:linear-gradient(135deg,#6c5ce7,#a29bfe); color:white;
    padding:10px 14px; border-radius:16px 16px 3px 16px;
    max-width:88%; align-self:flex-end; font-size:13px; line-height:1.5;
    box-shadow:0 3px 10px rgba(108,92,231,0.3); animation:msgIn 0.25s ease;
}
.bot-msg a { color:#6c5ce7; font-weight:600; text-decoration:none; }
.bot-msg a:hover { text-decoration:underline; }
.s-chip {
    padding:5px 11px; border-radius:15px;
    border:1.5px solid #6c5ce7; background:#fff;
    color:#6c5ce7; font-size:11px; font-weight:600;
    cursor:pointer; transition:all 0.2s; white-space:nowrap;
    font-family:'Poppins','Segoe UI',sans-serif;
}
.s-chip:hover { background:#6c5ce7; color:#fff; transform:translateY(-2px); }
#chatbot-input:focus { border-color:#6c5ce7; }
#chatbot-send:hover  { transform:scale(1.1); }
#chatbot-toggle:hover { transform:scale(1.08); }
#chatbot-messages::-webkit-scrollbar { width:4px; }
#chatbot-messages::-webkit-scrollbar-thumb { background:#ddd; border-radius:4px; }
/* Mobile responsive */
@media (max-width:480px) {
    #chatbot-window { width:calc(100vw - 30px) !important; }
    #chatbot-wrapper { bottom:16px; right:16px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const toggleBtn = document.getElementById('chatbot-toggle');
    const closeBtn  = document.getElementById('chatbot-close');
    const clearBtn  = document.getElementById('bot-clear');
    const chatWin   = document.getElementById('chatbot-window');
    const msgArea   = document.getElementById('chatbot-messages');
    const inputEl   = document.getElementById('chatbot-input');
    const sendBtn   = document.getElementById('chatbot-send');
    const badge     = document.getElementById('bot-badge');

    let opened = false;

    // ── Toggle ──────────────────────────────────────────────
    toggleBtn.addEventListener('click', () => {
        const isOpen = chatWin.style.display === 'flex';
        chatWin.style.display = isOpen ? 'none' : 'flex';
        if (!isOpen && !opened) { opened = true; badge.style.display = 'none'; greetUser(); }
        if (!isOpen) badge.style.display = 'none';
    });
    closeBtn.addEventListener('click', () => { chatWin.style.display = 'none'; });
    clearBtn.addEventListener('click', () => { msgArea.innerHTML = ''; greetUser(); });

    // ── Greeting ─────────────────────────────────────────────
    function greetUser() {
        const h = new Date().getHours();
        const g = h < 12 ? 'Good Morning ☀️' : h < 17 ? 'Good Afternoon 🌤️' : 'Good Evening 🌙';
        botMsg(`${g}! I'm your <b>E-Vote AI Assistant</b>. I can help you with:<br>
            🗳️ How to vote &nbsp;|&nbsp; 👤 Face Verification<br>
            📱 QR Voter Card &nbsp;|&nbsp; 📄 Voting Receipt<br>
            📊 Live Charts &nbsp;|&nbsp; 🌍 Languages<br><br>
            Type a question or tap a button below!`);
    }

    // ── Add messages ─────────────────────────────────────────
    function botMsg(html) {
        const d = document.createElement('div');
        d.className = 'bot-msg';
        d.innerHTML = html;
        msgArea.appendChild(d);
        msgArea.scrollTop = msgArea.scrollHeight;
        speak(d.innerText.slice(0, 150));
    }
    function userMsg(text) {
        const d = document.createElement('div');
        d.className = 'user-msg';
        d.innerText = text;
        msgArea.appendChild(d);
        msgArea.scrollTop = msgArea.scrollHeight;
    }

    // ── Speech ───────────────────────────────────────────────
    function speak(text) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            const u = new SpeechSynthesisUtterance(text.replace(/<[^>]*>/g,''));
            u.rate = 1; u.pitch = 1.05; window.speechSynthesis.speak(u);
        }
    }

    // ══════════════════════════════════════════════════════════
    //  KNOWLEDGE BASE — All system features mapped to keywords
    // ══════════════════════════════════════════════════════════
    const KB = [

        // ── REGISTRATION ──────────────────────────────────────
        {
            keys: ['register','registration','sign up','new voter','new user','create account'],
            reply: `<b>📝 How to Register:</b><br>
                1. Go to <a href="register.php">Register Page</a><br>
                2. Fill in your full name, email, DOB, address<br>
                3. Upload a clear <b>profile picture</b> (used for face verification)<br>
                4. Click <b>"Register & Generate Voter ID"</b><br>
                5. Check your email for your <b>E-Voter Card PDF</b> with QR code<br>
                6. Visit the <a href="verify_voter.php">Verification Page</a> to activate your account`
        },

        // ── HOW TO VOTE ───────────────────────────────────────
        {
            keys: ['how to vote','guide','steps to vote','voting process','cast vote','casting'],
            reply: `<b>🗳️ How to Cast Your Vote:</b><br>
                1. <a href="login.php">Login</a> to your account<br>
                2. Go to your <a href="dashboard.php">Dashboard</a><br>
                3. Click <b>"Vote Now"</b> on an active election<br>
                4. 👤 Complete <b>face verification</b> (camera scan)<br>
                5. Select your candidate<br>
                6. Click <b>"Cast Your Vote Securely"</b><br>
                7. 📄 You'll receive a <b>receipt PDF</b> on your email!`
        },

        // ── FACE VERIFICATION ─────────────────────────────────
        {
            keys: ['face','biometric','face scan','face verification','face recognition','camera','identity'],
            reply: `<b>👤 Biometric Face Verification:</b><br>
                This is a new security feature that ensures <b>only you</b> can vote with your account.<br><br>
                <b>How it works:</b><br>
                • When you click "Vote Now", a camera modal opens<br>
                • Click <b>"Start Face Scan"</b> and allow camera access<br>
                • AI compares your live face to your registered profile picture<br>
                • On a match ✅ — the vote button is enabled<br>
                • On failure ❌ — you can retry the scan<br><br>
                <b>Tips for success:</b><br>
                🌟 Good lighting &nbsp;|&nbsp; 😐 Face the camera directly &nbsp;|&nbsp; 📸 Use the same photo as registration`
        },

        // ── QR CODE ───────────────────────────────────────────
        {
            keys: ['qr','qr code','scan card','voter card qr','qr voter'],
            reply: `<b>📱 QR Code on your E-Voter Card:</b><br>
                Your E-Voter Card PDF now includes a <b>scannable QR code</b>!<br><br>
                <b>What the QR contains:</b><br>
                • Your Voter ID (EPIC Number)<br>
                • Your full name<br>
                • Your date of birth<br><br>
                <b>How it's used:</b><br>
                Election officers scan the QR with any phone to instantly verify your identity — no typing needed!<br><br>
                📧 The PDF is emailed to you when you <a href="register.php">register</a>.`
        },

        // ── VOTER CARD / E-VOTER CARD ─────────────────────────
        {
            keys: ['voter card','e-voter card','epic','voter id','identity card','voter id card'],
            reply: `<b>🪪 Your E-Voter Card:</b><br>
                After registration, a PDF card is emailed to you containing:<br>
                • 📸 Your profile photo<br>
                • 👤 Full Name, DOB, Address<br>
                • 🔑 Unique Voter ID (EPIC Number)<br>
                • 📱 QR Code (for instant scanning)<br><br>
                Use your <b>Voter ID</b> on the <a href="verify_voter.php">Verification Page</a> to activate login.`
        },

        // ── VOTING RECEIPT ────────────────────────────────────
        {
            keys: ['receipt','voting receipt','proof of vote','transaction','confirmation','after voting'],
            reply: `<b>📄 Digital Voting Receipt:</b><br>
                After you successfully cast your vote, you'll automatically receive a <b>PDF receipt</b> by email.<br><br>
                <b>The receipt includes:</b><br>
                • ✅ Your name & the election name<br>
                • 🏛️ Candidate you voted for<br>
                • 🕐 Exact date & time of your vote<br>
                • 🔐 Unique Transaction ID (e.g. TXN-A1B2C3D4E5)<br><br>
                This serves as your <b>official proof of voting</b>. The PDF is printable.`
        },

        // ── LIVE CHARTS / ANALYTICS ───────────────────────────
        {
            keys: ['chart','charts','analytics','live results','graph','pie chart','statistics','trends','dashboard chart'],
            reply: `<b>📊 Live Election Analytics:</b><br>
                Real-time charts are now available on all dashboards!<br><br>
                <b>Voter Dashboard:</b><br>
                • 🍩 Voter participation doughnut<br>
                • 📊 Votes per active election (bar)<br>
                • 👥 Gender-wise turnout (if available)<br><br>
                <b>Admin Dashboard:</b><br>
                • 🏆 Votes per election (horizontal bar)<br>
                • 🍩 Overall participation<br>
                • 👥 Voter status breakdown<br>
                • 📅 Daily registration trend (14 days)<br><br>
                <b>Results Pages:</b> Doughnut + Bar chart per election<br>
                View them at <a href="dashboard.php">Dashboard</a> or <a href="results.php">Results</a>.`
        },

        // ── MULTI-LANGUAGE ────────────────────────────────────
        {
            keys: ['language','lang','hindi','marathi','english','translate','भाषा','i18n','multilanguage'],
            reply: `<b>🌍 Multi-Language Support:</b><br>
                The system now supports <b>3 languages</b>!<br><br>
                <b>Available languages:</b><br>
                🇬🇧 English (EN) &nbsp;|&nbsp; 🇮🇳 Hindi (हि) &nbsp;|&nbsp; 🇮🇳 Marathi (म)<br><br>
                <b>How to switch:</b><br>
                Look at the <b>top navigation bar</b> — you'll see 3 buttons: <b>EN · हि · म</b><br>
                Click any to instantly translate the entire interface.<br><br>
                Your language choice is <b>saved automatically</b> for your next visit!`
        },

        // ── ELECTION REPORT ───────────────────────────────────
        {
            keys: ['election','elections','active election','ongoing','report','election report','results'],
            reply: null,  // handled dynamically below (fetches live data)
            dynamic: 'election_report'
        },

        // ── VERIFY / VERIFICATION ─────────────────────────────
        {
            keys: ['verify','verification','email verify','not verified','activate','set password','voter id verify'],
            reply: `<b>✅ Account Verification Steps:</b><br>
                1. Register on the <a href="register.php">Register Page</a><br>
                2. Check your email for your <b>E-Voter Card PDF</b><br>
                3. Open the PDF and copy your <b>Voter ID</b> (e.g. EV-ABCD1234)<br>
                4. Visit <a href="verify_voter.php">Verify Voter Page</a><br>
                5. Enter your Voter ID and set a password<br>
                6. You can now <a href="login.php">Login</a> and vote! 🎉`
        },

        // ── LOGIN ─────────────────────────────────────────────
        {
            keys: ['login','sign in','log in','password','forgot password'],
            reply: `<b>🔐 Login Help:</b><br>
                • Go to <a href="login.php">Login Page</a><br>
                • Enter your registered <b>Email</b> and <b>Password</b><br><br>
                <b>First time?</b> You need to verify your account first:<br>
                Use the Voter ID from your email PDF on the <a href="verify_voter.php">Verify Page</a> to set your password.`
        },

        // ── SECURITY ──────────────────────────────────────────
        {
            keys: ['secure','security','safe','encrypted','privacy','anonymous','hack','tamper'],
            reply: `<b>🔒 Security Features:</b><br>
                Your voting system is protected by:<br>
                • 👤 <b>Biometric face verification</b> before each vote<br>
                • 🔐 <b>Unique Voter ID</b> — one person, one vote<br>
                • 🗄️ <b>PDO Prepared Statements</b> — SQL injection proof<br>
                • 📧 <b>Email-verified registration</b> via GAS<br>
                • 🔑 <b>Bcrypt password hashing</b><br>
                • 📄 <b>Transaction ID receipt</b> for every vote<br>
                Your vote is <b>anonymous</b> — no one can see who you voted for!`
        },

        // ── ADMIN ─────────────────────────────────────────────
        {
            keys: ['admin','administration','manage','panel','manage election','add candidate'],
            reply: `<b>🛡️ Admin Panel Features:</b><br>
                • 🗳️ Create & manage elections<br>
                • 👤 Add / edit candidates<br>
                • ✅ Verify & manage voters<br>
                • 📊 View live analytics charts<br>
                • 📈 Export election results<br><br>
                Admin login is at <a href="admin/index.php">Admin Panel</a><br>
                (Requires admin credentials)`
        },

        // ── HELP / GENERAL ────────────────────────────────────
        {
            keys: ['help','support','what can you do','features','capabilities','what is'],
            reply: `<b>💡 What I can help with:</b><br>
                📝 Registration &amp; Verification<br>
                🗳️ How to vote step-by-step<br>
                👤 Face biometric verification<br>
                📱 QR code on voter card<br>
                📄 Voting receipt PDF<br>
                📊 Live chart analytics<br>
                🌍 Language switching (EN/HI/MR)<br>
                🔒 Security &amp; privacy<br>
                🛡️ Admin panel guide<br><br>
                Just type your question or tap a chip below!`
        },

        // ── GREETINGS ─────────────────────────────────────────
        {
            keys: ['hello','hi','hey','namaste','namaskar','नमस्ते'],
            reply: `Hello! 👋 Great to see you! I'm your <b>E-Vote AI Assistant</b>.<br>
                Ask me anything about voting, face verification, your QR voter card, or election results!`
        },

        // ── THANKS ────────────────────────────────────────────
        {
            keys: ['thank','thanks','great','awesome','good','helpful','perfect'],
            reply: `You're welcome! 😊 Your vote matters and we're here to make it easy.<br>
                Is there anything else I can help you with?`
        }
    ];

    // ── Process Input ─────────────────────────────────────────
    async function processInput(raw) {
        const q = raw.toLowerCase().trim();

        // Match knowledge base
        for (const item of KB) {
            if (item.keys.some(k => q.includes(k))) {

                // Dynamic: fetch live election data
                if (item.dynamic === 'election_report') {
                    botMsg('Fetching the latest election data… ⏳');
                    try {
                        const res  = await fetch('api/get_election_data.php');
                        const data = await res.json();
                        if (data.status === 'success') {
                            // Remove the "fetching" message
                            msgArea.lastChild && msgArea.removeChild(msgArea.lastChild);
                            botMsg(data.summary + `<br><br><a href="results.php">📊 View Live Charts →</a>`);
                        } else {
                            msgArea.lastChild && msgArea.removeChild(msgArea.lastChild);
                            botMsg(`<b>🗳️ Elections:</b><br>View all active elections on your <a href="dashboard.php">Dashboard</a> or see results at <a href="results.php">Results Page</a>.`);
                        }
                    } catch(e) {
                        msgArea.lastChild && msgArea.removeChild(msgArea.lastChild);
                        botMsg(`Visit <a href="results.php">Results Page</a> to see live election charts and data.`);
                    }
                    return;
                }

                botMsg(item.reply);
                return;
            }
        }

        // Fallback
        botMsg(`I'm not sure about that, but here's what I can help with:<br>
            🗳️ <b>How to vote</b> &nbsp;|&nbsp; 👤 <b>Face verify</b> &nbsp;|&nbsp; 📱 <b>QR Card</b><br>
            📄 <b>Voting receipt</b> &nbsp;|&nbsp; 📊 <b>Live charts</b> &nbsp;|&nbsp; 🌍 <b>Language</b><br>
            🔒 <b>Security</b> &nbsp;|&nbsp; 📝 <b>Register</b> &nbsp;|&nbsp; ✅ <b>Verify</b>`);
    }

    // ── Suggestion chips ─────────────────────────────────────
    document.querySelectorAll('.s-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            const cmd = chip.getAttribute('data-cmd');
            userMsg(cmd);
            setTimeout(() => processInput(cmd), 400);
        });
    });

    // ── Send button / Enter key ──────────────────────────────
    sendBtn.addEventListener('click', () => {
        const t = inputEl.value.trim();
        if (t) { userMsg(t); inputEl.value = ''; setTimeout(() => processInput(t), 400); }
    });
    inputEl.addEventListener('keypress', e => { if (e.key === 'Enter') sendBtn.click(); });

});
</script>
