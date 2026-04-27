<!-- ═══════════════════════════════════════════════════════════
     E-Vote Admin AI (ChatGPT Style)
     Clean, Modern interface with Speech and scrollable commands
════════════════════════════════════════════════════════════ -->

<div id="admin-chatbot-wrapper" style="position:fixed; bottom:30px; right:30px; z-index:10000; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; transition: all 0.3s ease;">

    <!-- Toggle Button -->
    <button id="admin-bot-toggle" title="Admin AI Assistant" style="width:60px; height:60px; border-radius:50%; background:#10a37f; border:none; color:white; font-size:24px; cursor:pointer; box-shadow:0 4px 15px rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center; transition:transform 0.3s; position:relative; margin-left:auto;">
        <i class="fas fa-robot"></i>
        <span id="admin-bot-badge" style="position:absolute; top:-2px; right:-2px; background:#ef4444; color:white; border-radius:50%; width:18px; height:18px; font-size:10px; display:flex; align-items:center; justify-content:center; font-weight:bold; border:2px solid #fff;">1</span>
    </button>

    <!-- Chat Window -->
    <div id="admin-bot-window" style="display:none; width: clamp(320px, 90vw, 420px); height: 65vh; min-height: 400px; max-height: 650px; background:#343541; border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,0.4); flex-direction:column; overflow:hidden; border:1px solid #565869; margin-bottom:16px;">

        <!-- Header -->
        <div style="background:#343541; color:#ececf1; padding:15px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #565869;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="background:#10a37f; border-radius:4px; padding:4px 6px; font-size:14px; color:white;">
                    <i class="fas fa-bolt"></i>
                </div>
                <div style="font-weight:600; font-size:15px;">Admin AI Assistant</div>
            </div>
            <div style="display:flex; gap:12px; align-items:center;">
                <button id="admin-bot-clear" title="New Chat" style="background:transparent; border:none; color:#c5c5d2; cursor:pointer; font-size:14px; transition:color 0.2s;"><i class="fas fa-plus"></i></button>
                <button id="admin-bot-close" style="background:transparent; border:none; color:#c5c5d2; cursor:pointer; font-size:16px; transition:color 0.2s;"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="admin-bot-messages" style="flex:1; overflow-y:auto; display:flex; flex-direction:column; font-size:15px; background:#343541; scroll-behavior:smooth;"></div>

        <!-- Scrollable Command Chips -->
        <div id="admin-bot-commands" style="padding:12px; display:flex; gap:10px; overflow-x:auto; background:#343541; white-space:nowrap; border-top:1px solid #565869; scroll-behavior:smooth;">
            <!-- Added specific styles to force horizontal scrolling -->
            <div style="display:inline-flex; gap:8px; flex-wrap:nowrap; padding-bottom:5px;">
                <button class="gpt-chip" data-cmd="summary"><i class="fas fa-chart-bar"></i> System Overview</button>
                <button class="gpt-chip" data-cmd="voter_turnout"><i class="fas fa-users"></i> Turnout Report</button>
                <button class="gpt-chip" data-cmd="top_candidates"><i class="fas fa-star"></i> Top Candidates</button>
                <button class="gpt-chip" data-cmd="recent_votes"><i class="fas fa-clock"></i> Recent Votes</button>
                <button class="gpt-chip" data-cmd="pending_voters"><i class="fas fa-user-clock"></i> Pending Approvals</button>
            </div>
        </div>

        <!-- Input Area -->
        <div style="padding:15px; background:#343541; display:flex; gap:10px; justify-content:center;">
            <div style="position:relative; width:100%; display:flex; align-items:center; background:#40414f; border-radius:8px; border:1px solid #565869; padding:5px 10px;">
                <input type="text" id="admin-bot-input" placeholder="Message Admin AI..." style="flex:1; background:transparent; border:none; color:#ececf1; outline:none; font-size:15px; padding:8px 5px;">
                <button id="admin-bot-send" style="background:#10a37f; border:none; color:white; border-radius:6px; padding:6px 10px; cursor:pointer; font-size:14px; display:flex; align-items:center; justify-content:center; transition:background 0.2s;">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Smooth fade in */
@keyframes fadeInMsg {
    from { opacity:0; transform:translateY(5px); }
    to   { opacity:1; transform:translateY(0); }
}

.gpt-msg-container {
    display: flex;
    padding: 20px;
    gap: 15px;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    color: #ececf1;
    animation: fadeInMsg 0.3s ease-out;
}
.gpt-msg-bot {
    background: #444654;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}
.gpt-msg-user {
    background: #343541;
}

.gpt-avatar {
    width: 30px;
    height: 30px;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 18px;
}
.gpt-avatar-bot {
    background: #10a37f;
    color: white;
}
.gpt-avatar-user {
    background: #5436da;
    color: white;
}

.gpt-content {
    flex: 1;
    line-height: 1.6;
    word-break: break-word;
    font-size: 15px;
}
.gpt-content b, .gpt-content strong { color: #fff; }

.gpt-chip {
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid #565869;
    background: #40414f;
    color: #ececf1;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.gpt-chip:hover {
    background: #565869;
}

#admin-bot-clear:hover, #admin-bot-close:hover { color: #fff !important; }
#admin-bot-send:hover { background: #0e906f; }

/* Scrollbars for messages and commands */
#admin-bot-messages::-webkit-scrollbar, #admin-bot-commands::-webkit-scrollbar { height: 6px; width: 6px; }
#admin-bot-messages::-webkit-scrollbar-thumb, #admin-bot-commands::-webkit-scrollbar-thumb { background: #565869; border-radius: 3px; }
#admin-bot-commands::-webkit-scrollbar-track { background: transparent; }

/* Custom tables inside GPT output */
.gpt-table-wrapper { width: 100%; overflow-x: auto; margin-top: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); }
.gpt-table { width: 100%; border-collapse: collapse; font-size: 14px; background: rgba(0,0,0,0.1); white-space: nowrap; }
.gpt-table th { background: rgba(255,255,255,0.05); padding: 8px 10px; text-align: left; font-weight: 600; border-bottom: 1px solid #565869; }
.gpt-table td { padding: 8px 10px; border-bottom: 1px solid rgba(255,255,255,0.05); }
.gpt-highlight { color: #10a37f; font-weight: 600; }

/* Responsive Adjustments */
@media (max-width: 768px) {
    #admin-chatbot-wrapper { bottom: 20px; right: 20px; }
    #admin-bot-window { height: 60vh; }
}

@media (max-width: 480px) {
    #admin-chatbot-wrapper { bottom: 15px; right: 15px; }
    #admin-bot-window { 
        width: calc(100vw - 30px) !important; 
        height: calc(100vh - 100px); 
        max-height: none;
        margin-bottom: 12px;
    }
    .gpt-msg-container { padding: 15px; gap: 10px; }
    .gpt-avatar { width: 26px; height: 26px; font-size: 14px; }
    .gpt-content { font-size: 14px; }
    .gpt-chip { padding: 6px 10px; font-size: 12px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('admin-bot-toggle');
    const closeBtn  = document.getElementById('admin-bot-close');
    const clearBtn  = document.getElementById('admin-bot-clear');
    const chatWin   = document.getElementById('admin-bot-window');
    const msgArea   = document.getElementById('admin-bot-messages');
    const inputEl   = document.getElementById('admin-bot-input');
    const sendBtn   = document.getElementById('admin-bot-send');
    const badge     = document.getElementById('admin-bot-badge');

    let opened = false;

    // Toggle logic
    toggleBtn.addEventListener('click', () => {
        const isOpen = chatWin.style.display === 'flex';
        chatWin.style.display = isOpen ? 'none' : 'flex';
        if (!isOpen && !opened) { 
            opened = true; 
            badge.style.display = 'none'; 
            botMsg('Hello! I am your Admin AI. I can fetch live data and reports about your elections. How can I help you today?');
        }
        if (!isOpen) badge.style.display = 'none';
    });
    closeBtn.addEventListener('click', () => { chatWin.style.display = 'none'; });
    clearBtn.addEventListener('click', () => { msgArea.innerHTML = ''; botMsg('Started a new chat. How can I assist you?'); });

    // ── Speech Functionality (Similar to user bot) ──
    function speak(text) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            // Remove HTML tags for clean reading
            const cleanText = text.replace(/<[^>]*>/g, '');
            const utterance = new SpeechSynthesisUtterance(cleanText.slice(0, 200)); // Limit length
            utterance.rate = 1; 
            utterance.pitch = 1.0; 
            window.speechSynthesis.speak(utterance);
        }
    }

    // Message renderers
    function botMsg(html, readAloud = true) {
        const wrapper = document.createElement('div');
        wrapper.className = 'gpt-msg-container gpt-msg-bot';
        wrapper.innerHTML = `
            <div class="gpt-avatar gpt-avatar-bot"><i class="fas fa-bolt"></i></div>
            <div class="gpt-content">${html}</div>
        `;
        msgArea.appendChild(wrapper);
        msgArea.scrollTop = msgArea.scrollHeight;
        
        if(readAloud) {
            speak(html);
        }
    }

    function userMsg(text) {
        const wrapper = document.createElement('div');
        wrapper.className = 'gpt-msg-container gpt-msg-user';
        wrapper.innerHTML = `
            <div class="gpt-avatar gpt-avatar-user"><i class="fas fa-user"></i></div>
            <div class="gpt-content">${text}</div>
        `;
        msgArea.appendChild(wrapper);
        msgArea.scrollTop = msgArea.scrollHeight;
    }

    // API Data Fetcher
    async function fetchReport(cmd) {
        // Show loading indicator
        const tempId = 'loading-' + Date.now();
        const wrapper = document.createElement('div');
        wrapper.className = 'gpt-msg-container gpt-msg-bot';
        wrapper.id = tempId;
        wrapper.innerHTML = `
            <div class="gpt-avatar gpt-avatar-bot"><i class="fas fa-bolt"></i></div>
            <div class="gpt-content"><i class="fas fa-circle-notch fa-spin"></i> Generating report...</div>
        `;
        msgArea.appendChild(wrapper);
        msgArea.scrollTop = msgArea.scrollHeight;

        try {
            const res = await fetch(`api/admin_intelligence.php?report=${cmd}`);
            const data = await res.json();
            
            // Remove loading
            document.getElementById(tempId).remove();

            if (data.error) {
                botMsg('I encountered an error while fetching data: ' + data.error);
                return;
            }

            let html = '';
            let plainTextForSpeech = '';

            if (cmd === 'summary') {
                const d = data.data;
                html = `
                    Here is the <b>System Overview</b>:<br><br>
                    • <b>Total Voters:</b> ${d.voters.total} (Active: ${d.voters.active}, Pending: ${d.voters.pending})<br>
                    • <b>Elections:</b> ${d.elections.total} (Active: ${d.elections.active})<br>
                    • <b>Total Votes Cast:</b> ${d.votes.total} (Today: ${d.votes.today})<br>
                    • <b>Overall Turnout:</b> <span class="gpt-highlight">${d.voters.turnout}%</span>
                `;
                plainTextForSpeech = `Here is the system overview. There are ${d.voters.total} total voters, ${d.elections.total} elections, and ${d.votes.total} total votes cast. The overall turnout is ${d.voters.turnout} percent.`;
            
            } else if (cmd === 'voter_turnout') {
                html = `Here is the voter turnout report per election:<br><div class="gpt-table-wrapper"><table class="gpt-table"><tr><th>Election</th><th>Votes</th><th>Turnout</th></tr>`;
                data.data.forEach(r => { html += `<tr><td>${r.title}</td><td>${r.voted}</td><td class="gpt-highlight">${r.pct}%</td></tr>`; });
                if(data.data.length===0) html += `<tr><td colspan="3">No data available.</td></tr>`;
                html += `</table></div>`;
                plainTextForSpeech = "Here is the voter turnout report. Check the table generated on your screen.";

            } else if (cmd === 'top_candidates') {
                html = `Here are the top candidates ranked by votes:<br><div class="gpt-table-wrapper"><table class="gpt-table"><tr><th>Candidate</th><th>Party</th><th>Votes</th></tr>`;
                data.data.forEach(r => { html += `<tr><td>${r.name}</td><td>${r.party}</td><td class="gpt-highlight">${r.votes}</td></tr>`; });
                if(data.data.length===0) html += `<tr><td colspan="3">No data available.</td></tr>`;
                html += `</table></div>`;
                plainTextForSpeech = "Here are the top candidates. I have generated a table with their details.";

            } else if (cmd === 'recent_votes') {
                html = `Here is the recent voting activity log:<br><div class="gpt-table-wrapper"><table class="gpt-table"><tr><th>Voter</th><th>Time</th></tr>`;
                data.data.forEach(r => { html += `<tr><td>${r.full_name}</td><td>${r.voted_at}</td></tr>`; });
                if(data.data.length===0) html += `<tr><td colspan="2">No recent votes found.</td></tr>`;
                html += `</table></div>`;
                plainTextForSpeech = "Here is the recent voting activity. Showing the latest cast votes.";

            } else if (cmd === 'pending_voters') {
                html = `You have <b>${data.total} pending approvals</b>.<br><div class="gpt-table-wrapper"><table class="gpt-table"><tr><th>Name</th><th>Registered</th></tr>`;
                data.data.forEach(r => { html += `<tr><td>${r.full_name}</td><td>${r.registered}</td></tr>`; });
                if(data.data.length===0) html += `<tr><td colspan="2">All caught up! No pending voters.</td></tr>`;
                html += `</table></div>`;
                if(data.total > 0) html += `<br><a href="voters.php" style="color:#10a37f; font-weight:600; text-decoration:none;">Review Voters →</a>`;
                plainTextForSpeech = `You have ${data.total} pending voters waiting for approval.`;
            }

            // Render message and pass custom text for speech
            botMsg(html, false);
            speak(plainTextForSpeech);

        } catch (e) {
            document.getElementById(tempId)?.remove();
            botMsg('I lost connection to the server API. Please try again.');
        }
    }

    // Process NLP-ish Input
    function processInput(text) {
        const q = text.toLowerCase();
        if (q.includes('summary') || q.includes('overview') || q.includes('stats') || q.includes('system')) {
            fetchReport('summary');
        } else if (q.includes('turnout') || q.includes('percentage') || q.includes('participation')) {
            fetchReport('voter_turnout');
        } else if (q.includes('top') || q.includes('candidate') || q.includes('leader')) {
            fetchReport('top_candidates');
        } else if (q.includes('recent') || q.includes('activity') || q.includes('latest')) {
            fetchReport('recent_votes');
        } else if (q.includes('pending') || q.includes('approve') || q.includes('waiting')) {
            fetchReport('pending_voters');
        } else if (q.includes('help')) {
            botMsg(`You can ask me to show:<br><br>
                • <b>System Summary</b><br>
                • <b>Voter Turnout</b><br>
                • <b>Top Candidates</b><br>
                • <b>Recent Votes</b><br>
                • <b>Pending Approvals</b><br><br>
                Or simply click on one of the chips above the input field.`);
        } else if (q.includes('hello') || q.includes('hi ')) {
            botMsg(`Hello! I'm your Admin AI Assistant. How can I help you manage the elections today?`);
        } else {
            botMsg(`I didn't quite catch that. You can ask me for a <b>summary</b>, <b>voter turnout</b>, or <b>top candidates</b>.`);
        }
    }

    // Scrollable Command Chips
    document.querySelectorAll('.gpt-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            const cmd = chip.getAttribute('data-cmd');
            const label = chip.innerText.trim();
            userMsg(label);
            setTimeout(() => fetchReport(cmd), 300);
        });
    });

    // Make chips scrollable with mouse wheel (horizontal)
    const commandsContainer = document.getElementById('admin-bot-commands');
    commandsContainer.addEventListener('wheel', function(e) {
        if (e.deltaY > 0) {
            commandsContainer.scrollLeft += 100;
            e.preventDefault();
        }
        else if (e.deltaY < 0) {
            commandsContainer.scrollLeft -= 100;
            e.preventDefault();
        }
    });

    // Send Button
    sendBtn.addEventListener('click', () => {
        const t = inputEl.value.trim();
        if (t) { userMsg(t); inputEl.value = ''; setTimeout(() => processInput(t), 300); }
    });
    inputEl.addEventListener('keypress', e => { if (e.key === 'Enter') sendBtn.click(); });
});
</script>
