/**
 * i18n.js — Multi-Language Support (English / Hindi / Marathi)
 * E-Voting System v1.2
 */

const translations = {
    en: {
        // Navigation
        nav_home: "Home",
        nav_elections: "Elections",
        nav_results: "Results",
        nav_about: "About Developer",
        nav_dashboard: "Dashboard",
        nav_login: "Login",
        nav_register: "Register",
        nav_logout: "Logout",
        nav_admin: "Admin Panel",

        // Index
        hero_title: "Secure Digital Democracy",
        hero_sub: "Cast your vote from anywhere, anytime. Powered by biometrics & end-to-end encryption.",
        hero_btn_vote: "Vote Now",
        hero_btn_learn: "Learn More",
        feature_secure: "Bank-Grade Security",
        feature_secure_desc: "End-to-end encrypted. Your vote is private, tamper-proof, and anonymous.",
        feature_bio: "Biometric Verification",
        feature_bio_desc: "Face recognition ensures only YOU can vote with your identity.",
        feature_realtime: "Real-Time Results",
        feature_realtime_desc: "Watch live analytics as votes are counted — transparent & instant.",

        // Register
        reg_title: "Voter Registration",
        reg_name: "Full Name",
        reg_email: "Email Address",
        reg_dob: "Date of Birth",
        reg_photo: "Profile Picture",
        reg_address: "Residential Address",
        reg_btn: "Register & Generate Voter ID",
        reg_have_account: "Already have an account?",
        reg_login_link: "Login here",

        // Login
        login_title: "Voter Login",
        login_email: "Email Address",
        login_password: "Password / Voter ID",
        login_btn: "Login Securely",
        login_no_account: "New voter?",
        login_register_link: "Register here",

        // Dashboard
        dash_welcome: "Welcome",
        dash_voter_id: "Voter ID",
        dash_votes_cast: "Votes Cast",
        dash_active_elections: "Active Elections",
        dash_active_title: "Active Elections",
        dash_no_elections: "No active elections at the moment.",
        dash_ends: "Ends",
        dash_already_voted: "Already Voted",
        dash_vote_now: "Vote Now",
        dash_quick_actions: "Quick Actions",
        dash_edit_profile: "Edit Profile",
        dash_view_results: "View Results",
        dash_voting_trends: "Your Voting Trends",

        // Vote
        vote_back: "Back to Dashboard",
        vote_select: "Select your candidate:",
        vote_select_candidate: "Select Candidate",
        vote_cast: "Cast Your Vote Securely",
        vote_warning: "Once cast, your vote cannot be changed.",
        vote_face_title: "Biometric Verification Required",
        vote_face_desc: "Please allow camera access. Your face will be scanned to verify your identity before voting.",
        vote_face_btn: "Start Face Scan",
        vote_face_scanning: "Scanning...",
        vote_face_success: "Identity Verified! ✓",
        vote_face_fail: "Face does not match. Please try again.",

        // Results
        results_title: "Election Results",
        results_winner: "WINNER",
        results_total: "Total Ballots",
        results_candidates: "Candidates",
        results_status: "Election Status",

        // Receipt
        receipt_title: "Voting Receipt",
        receipt_confirmed: "Your Vote Has Been Recorded",
        receipt_election: "Election",
        receipt_candidate: "Candidate Voted For",
        receipt_time: "Time of Vote",
        receipt_id: "Transaction ID",
        receipt_download: "Download Receipt PDF",
        receipt_email: "Receipt sent to your email",

        // Footer
        footer_rights: "E-VOTE System. All Rights Reserved",

        // Language toggle
        lang_label: "Language",
    },
    hi: {
        nav_home: "होम",
        nav_elections: "चुनाव",
        nav_results: "परिणाम",
        nav_about: "डेवलपर के बारे में",
        nav_dashboard: "डैशबोर्ड",
        nav_login: "लॉगिन",
        nav_register: "रजिस्टर",
        nav_logout: "लॉगआउट",
        nav_admin: "एडमिन पैनल",

        hero_title: "सुरक्षित डिजिटल लोकतंत्र",
        hero_sub: "कहीं से भी, कभी भी वोट करें। बायोमेट्रिक्स और एंड-टू-एंड एन्क्रिप्शन द्वारा संचालित।",
        hero_btn_vote: "अभी वोट करें",
        hero_btn_learn: "अधिक जानें",
        feature_secure: "बैंक-ग्रेड सुरक्षा",
        feature_secure_desc: "एंड-टू-एंड एन्क्रिप्टेड। आपका वोट निजी, छेड़छाड़-रोधी और अनाम है।",
        feature_bio: "बायोमेट्रिक सत्यापन",
        feature_bio_desc: "फेस रिकग्निशन सुनिश्चित करता है कि केवल आप ही अपनी पहचान से वोट कर सकते हैं।",
        feature_realtime: "रियल-टाइम परिणाम",
        feature_realtime_desc: "वोट गिनती होने पर लाइव एनालिटिक्स देखें — पारदर्शी और तत्काल।",

        reg_title: "मतदाता पंजीकरण",
        reg_name: "पूरा नाम",
        reg_email: "ईमेल पता",
        reg_dob: "जन्म तिथि",
        reg_photo: "प्रोफ़ाइल फ़ोटो",
        reg_address: "आवासीय पता",
        reg_btn: "रजिस्टर करें और Voter ID बनाएं",
        reg_have_account: "पहले से खाता है?",
        reg_login_link: "यहाँ लॉगिन करें",

        login_title: "मतदाता लॉगिन",
        login_email: "ईमेल पता",
        login_password: "पासवर्ड / Voter ID",
        login_btn: "सुरक्षित लॉगिन करें",
        login_no_account: "नए मतदाता?",
        login_register_link: "यहाँ रजिस्टर करें",

        dash_welcome: "स्वागत है",
        dash_voter_id: "Voter ID",
        dash_votes_cast: "डाले गए वोट",
        dash_active_elections: "सक्रिय चुनाव",
        dash_active_title: "सक्रिय चुनाव",
        dash_no_elections: "अभी कोई सक्रिय चुनाव नहीं है।",
        dash_ends: "समाप्त होता है",
        dash_already_voted: "पहले ही वोट किया",
        dash_vote_now: "अभी वोट करें",
        dash_quick_actions: "त्वरित क्रियाएं",
        dash_edit_profile: "प्रोफ़ाइल संपादित करें",
        dash_view_results: "परिणाम देखें",
        dash_voting_trends: "आपके वोटिंग रुझान",

        vote_back: "डैशबोर्ड पर वापस",
        vote_select: "अपना उम्मीदवार चुनें:",
        vote_select_candidate: "उम्मीदवार चुनें",
        vote_cast: "सुरक्षित रूप से वोट करें",
        vote_warning: "एक बार डाला गया वोट बदला नहीं जा सकता।",
        vote_face_title: "बायोमेट्रिक सत्यापन आवश्यक",
        vote_face_desc: "कृपया कैमरा एक्सेस दें। वोट करने से पहले आपकी पहचान सत्यापित की जाएगी।",
        vote_face_btn: "फेस स्कैन शुरू करें",
        vote_face_scanning: "स्कैन हो रहा है...",
        vote_face_success: "पहचान सत्यापित! ✓",
        vote_face_fail: "चेहरा मेल नहीं खाता। कृपया पुनः प्रयास करें।",

        results_title: "चुनाव परिणाम",
        results_winner: "विजेता",
        results_total: "कुल मतपत्र",
        results_candidates: "उम्मीदवार",
        results_status: "चुनाव स्थिति",

        receipt_title: "मतदान रसीद",
        receipt_confirmed: "आपका वोट दर्ज हो गया है",
        receipt_election: "चुनाव",
        receipt_candidate: "वोट किए गए उम्मीदवार",
        receipt_time: "वोट का समय",
        receipt_id: "ट्रांजेक्शन ID",
        receipt_download: "रसीद PDF डाउनलोड करें",
        receipt_email: "रसीद आपके ईमेल पर भेजी गई",

        footer_rights: "E-VOTE System. सर्वाधिकार सुरक्षित",
        lang_label: "भाषा",
    },
    mr: {
        nav_home: "मुखपृष्ठ",
        nav_elections: "निवडणुका",
        nav_results: "निकाल",
        nav_about: "डेव्हलपरबद्दल",
        nav_dashboard: "डॅशबोर्ड",
        nav_login: "लॉगिन",
        nav_register: "नोंदणी",
        nav_logout: "लॉगआउट",
        nav_admin: "प्रशासक पॅनेल",

        hero_title: "सुरक्षित डिजिटल लोकशाही",
        hero_sub: "कुठूनही, कधीही मतदान करा. बायोमेट्रिक्स आणि एंड-टू-एंड एन्क्रिप्शनद्वारे संचालित।",
        hero_btn_vote: "आत्ता मत द्या",
        hero_btn_learn: "अधिक जाणा",
        feature_secure: "बँक-दर्जाची सुरक्षा",
        feature_secure_desc: "एंड-टू-एंड एन्क्रिप्टेड. तुमचे मत खाजगी, छेडछाड-रोधक आणि गुप्त आहे.",
        feature_bio: "बायोमेट्रिक पडताळणी",
        feature_bio_desc: "फेस रेकग्निशन सुनिश्चित करते की केवळ तुम्हीच तुमच्या ओळखीने मतदान करू शकता.",
        feature_realtime: "रिअल-टाइम निकाल",
        feature_realtime_desc: "मते मोजली जात असताना थेट विश्लेषण पाहा — पारदर्शक आणि तत्काळ.",

        reg_title: "मतदार नोंदणी",
        reg_name: "पूर्ण नाव",
        reg_email: "ईमेल पत्ता",
        reg_dob: "जन्म तारीख",
        reg_photo: "प्रोफाइल छायाचित्र",
        reg_address: "निवासी पत्ता",
        reg_btn: "नोंदणी करा आणि Voter ID तयार करा",
        reg_have_account: "आधीच खाते आहे?",
        reg_login_link: "येथे लॉगिन करा",

        login_title: "मतदार लॉगिन",
        login_email: "ईमेल पत्ता",
        login_password: "पासवर्ड / Voter ID",
        login_btn: "सुरक्षितपणे लॉगिन करा",
        login_no_account: "नवीन मतदार?",
        login_register_link: "येथे नोंदणी करा",

        dash_welcome: "स्वागत आहे",
        dash_voter_id: "Voter ID",
        dash_votes_cast: "टाकलेली मते",
        dash_active_elections: "सक्रिय निवडणुका",
        dash_active_title: "सक्रिय निवडणुका",
        dash_no_elections: "सध्या कोणतीही सक्रिय निवडणूक नाही.",
        dash_ends: "समाप्त होते",
        dash_already_voted: "आधीच मतदान केले",
        dash_vote_now: "आत्ता मत द्या",
        dash_quick_actions: "द्रुत क्रिया",
        dash_edit_profile: "प्रोफाइल संपादित करा",
        dash_view_results: "निकाल पाहा",
        dash_voting_trends: "तुमचे मतदान ट्रेंड",

        vote_back: "डॅशबोर्डवर परत",
        vote_select: "तुमचा उमेदवार निवडा:",
        vote_select_candidate: "उमेदवार निवडा",
        vote_cast: "सुरक्षितपणे मत द्या",
        vote_warning: "एकदा टाकलेले मत बदलता येत नाही.",
        vote_face_title: "बायोमेट्रिक पडताळणी आवश्यक",
        vote_face_desc: "कृपया कॅमेरा प्रवेश द्या. मतदानापूर्वी तुमची ओळख पडताळली जाईल.",
        vote_face_btn: "फेस स्कॅन सुरू करा",
        vote_face_scanning: "स्कॅन होत आहे...",
        vote_face_success: "ओळख पडताळली! ✓",
        vote_face_fail: "चेहरा जुळत नाही. कृपया पुन्हा प्रयत्न करा.",

        results_title: "निवडणूक निकाल",
        results_winner: "विजेता",
        results_total: "एकूण मतपत्रिका",
        results_candidates: "उमेदवार",
        results_status: "निवडणूक स्थिती",

        receipt_title: "मतदान पावती",
        receipt_confirmed: "तुमचे मत नोंदवले गेले आहे",
        receipt_election: "निवडणूक",
        receipt_candidate: "मत दिलेला उमेदवार",
        receipt_time: "मताची वेळ",
        receipt_id: "व्यवहार ID",
        receipt_download: "पावती PDF डाउनलोड करा",
        receipt_email: "पावती तुमच्या ईमेलवर पाठवली गेली",

        footer_rights: "E-VOTE System. सर्व हक्क राखीव",
        lang_label: "भाषा",
    }
};

const I18n = {
    currentLang: localStorage.getItem('evoting_lang') || 'en',

    t(key) {
        return (translations[this.currentLang] && translations[this.currentLang][key])
            ? translations[this.currentLang][key]
            : (translations['en'][key] || key);
    },

    setLang(lang) {
        this.currentLang = lang;
        localStorage.setItem('evoting_lang', lang);
        this.applyTranslations();
    },

    applyTranslations() {
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            const translation = this.t(key);
            if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                el.placeholder = translation;
            } else if (el.tagName === 'BUTTON' || el.tagName === 'A') {
                // Preserve icons if they exist
                const icon = el.querySelector('i');
                if (icon) {
                    const iconHtml = icon.outerHTML;
                    el.innerHTML = iconHtml + ' ' + translation;
                } else {
                    el.textContent = translation;
                }
            } else {
                el.textContent = translation;
            }
        });
        // Update html lang attribute
        document.documentElement.lang = this.currentLang;
        // Update toggle buttons active state
        document.querySelectorAll('.lang-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.lang === this.currentLang);
        });
    },

    init() {
        // Apply on page load
        document.addEventListener('DOMContentLoaded', () => {
            this.applyTranslations();
            // Bind click events on lang buttons
            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    this.setLang(btn.dataset.lang);
                });
            });
        });
    }
};

I18n.init();
