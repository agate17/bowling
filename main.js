// Bowling Stats Display JavaScript

// Update leaderboard table headers and sample data, and translate all visible text to Latvian
// In the .leaderboard-table, make it smaller and rounder
const leaderboardData = [
    { rank: 1, name: "Jānis 'Striker' Ozoliņš", average: 210, strikes: 85, spares: 50, games: 26 },
    { rank: 2, name: "Anna 'Bulta' Kalniņa", average: 202, strikes: 80, spares: 48, games: 25 },
    { rank: 3, name: "Mārtiņš 'Precizitāte' Bērziņš", average: 198, strikes: 75, spares: 47, games: 24 },
    { rank: 4, name: "Elīna 'Spēks' Liepa", average: 192, strikes: 70, spares: 45, games: 23 },
    { rank: 5, name: "Rihards 'Ātrums' Siliņš", average: 188, strikes: 68, spares: 44, games: 22 },
    { rank: 6, name: "Līga 'Zibens' Jansone", average: 185, strikes: 65, spares: 43, games: 21 },
    { rank: 7, name: "Edgars 'Bumba' Vītols", average: 180, strikes: 62, spares: 41, games: 20 },
    { rank: 8, name: "Ilze 'Vējš' Krūmiņa", average: 175, strikes: 60, spares: 40, games: 19 }
];

// Translate teamData to Latvian
const teamData = [
    { name: "Trāpītāji", average: 178, wins: 12, losses: 3, members: ["Jānis Ozoliņš", "Anna Kalniņa", "Mārtiņš Bērziņš"] },
    { name: "Ķegļu Sagrauzēji", average: 172, wins: 10, losses: 5, members: ["Elīna Liepa", "Rihards Siliņš", "Līga Jansone"] },
    { name: "Grozītāji", average: 165, wins: 8, losses: 7, members: ["Edgars Vītols", "Ilze Krūmiņa", "Tomass Andersons"] },
    { name: "Rezervisti", average: 158, wins: 6, losses: 9, members: ["Marks Tīlers", "Aija Jansone", "Krišjānis Līcis"] }
];

// Translate trivia questions and answers to Latvian
const triviaQuestions = [
    { question: "Kāds ir maksimālais punktu skaits ideālā boulinga spēlē?", answer: "300 punkti!" },
    { question: "Cik ķegļi ir standarta boulinga spēlē?", answer: "10 ķegļi!" },
    { question: "Kā sauc, ja ar pirmo metienu nogāž visus ķegļus?", answer: "Strike!" },
    { question: "Kā sauc, ja ar otro metienu nogāž visus atlikušos ķegļus?", answer: "Rezerves!" },
    { question: "Cik freimu ir pilnā boulinga spēlē?", answer: "10 freimi!" }
];

let currentTriviaIndex = 0;
const screenDuration = 15000; // 15 seconds per screen

// Initialize displays
function initializeLeaderboard() {
    const tbody = document.getElementById('leaderboardBody');
    tbody.innerHTML = '';
    
    leaderboardData.forEach(player => {
        let rankClass = '';
        if (player.rank === 1) rankClass = 'first';
        else if (player.rank === 2) rankClass = 'second';
        else if (player.rank === 3) rankClass = 'third';
        else if (player.rank >= 4 && player.rank <= 8) rankClass = 'fourthplus';
        
        tbody.innerHTML += `
            <tr>
                <td><span class="rank ${rankClass}">#${player.rank}</span></td>
                <td>${player.name}</td>
                <td>${player.average}</td>
                <td>${player.strikes}</td>
                <td>${player.spares}</td>
                <td>${player.games}</td>
            </tr>
        `;
    });
}

function initializeTeamRankings() {
    const tbody = document.getElementById('teamLeaderboardBody');
    tbody.innerHTML = '';
    teamData.forEach((team, index) => {
        let rankClass = '';
        if (index === 0) rankClass = 'first';
        else if (index === 1) rankClass = 'second';
        else if (index === 2) rankClass = 'third';
        else rankClass = 'fourthplus';
        tbody.innerHTML += `
            <tr>
                <td><span class="rank ${rankClass}">#${index + 1}</span></td>
                <td>${team.name}</td>
                <td>${team.average}</td>
                <td>${team.wins}-${team.losses}</td>
                <td>${team.members.join(', ')}</td>
            </tr>
        `;
    });
}

function updateTrivia() {
    const question = document.getElementById('triviaQuestion');
    const answer = document.getElementById('triviaAnswer');
    const currentTrivia = triviaQuestions[currentTriviaIndex];
    
    question.textContent = currentTrivia.question;
    answer.textContent = `Answer: ${currentTrivia.answer}`;
    
    currentTriviaIndex = (currentTriviaIndex + 1) % triviaQuestions.length;
}

function showAchievementBanner() {
    const achievements = [
        "🎯 NEW HIGH SCORE! 🎯",
        "🔥 PERFECT GAME! 🔥",
        "⚡ STRIKE STREAK! ⚡",
        "🏆 TOURNAMENT WINNER! 🏆"
    ];
    
    // Randomly show achievement banner (10% chance)
    if (Math.random() < 0.1) {
        const banner = document.createElement('div');
        banner.className = 'achievement-banner';
        banner.textContent = achievements[Math.floor(Math.random() * achievements.length)];
        document.body.appendChild(banner);
        
        setTimeout(() => {
            document.body.removeChild(banner);
        }, 3000);
    }
}

function updateProgressBar() {
    const progressBar = document.getElementById('progressBar');
    const startTime = Date.now();
    
    function animate() {
        const elapsed = Date.now() - startTime;
        const progress = (elapsed / screenDuration) * 100;
        
        progressBar.style.width = Math.min(progress, 100) + '%';
        
        if (progress < 100) {
            requestAnimationFrame(animate);
        }
    }
    
    animate();
}

// Automatic section rotation with progress bar
const screens = ['leaderboard', 'playerOfWeek', 'teamRankings', 'trivia'];
let currentScreen = 0;
function showScreen(index) {
    screens.forEach((id, i) => {
        document.getElementById(id).classList.toggle('active', i === index);
    });
}
function updateProgressBar() {
    const progressBar = document.getElementById('progressBar');
    const startTime = Date.now();
    function animate() {
        const elapsed = Date.now() - startTime;
        const progress = (elapsed / screenDuration) * 100;
        progressBar.style.width = Math.min(progress, 100) + '%';
        if (progress < 100) {
            requestAnimationFrame(animate);
        }
    }
    animate();
}
function nextScreen() {
    currentScreen = (currentScreen + 1) % screens.length;
    showScreen(currentScreen);
    updateProgressBar();
}
initializeLeaderboard();
initializeTeamRankings();
showScreen(0);
updateProgressBar();
setInterval(nextScreen, screenDuration);

// Fullscreen button functionality
const fullscreenBtn = document.getElementById('fullscreen-btn');
fullscreenBtn.addEventListener('click', () => {
    if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.mozFullScreenElement && !document.msFullscreenElement) {
        // Enter fullscreen
        const docElm = document.documentElement;
        if (docElm.requestFullscreen) {
            docElm.requestFullscreen();
        } else if (docElm.mozRequestFullScreen) { /* Firefox */
            docElm.mozRequestFullScreen();
        } else if (docElm.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
            docElm.webkitRequestFullscreen();
        } else if (docElm.msRequestFullscreen) { /* IE/Edge */
            docElm.msRequestFullscreen();
        }
    } else {
        // Exit fullscreen
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) { /* Firefox */
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) { /* Chrome, Safari & Opera */
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) { /* IE/Edge */
            document.msExitFullscreen();
        }
    }
});
// Listen for fullscreen change to hide/show admin button
function handleFullscreenChange() {
    if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) {
        document.body.classList.add('fullscreen-active');
    } else {
        document.body.classList.remove('fullscreen-active');
    }
}
document.addEventListener('fullscreenchange', handleFullscreenChange);
document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
document.addEventListener('mozfullscreenchange', handleFullscreenChange);
document.addEventListener('MSFullscreenChange', handleFullscreenChange); 