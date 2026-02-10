// Word list for the game
const words = [
  'JavaScript', 'Computer', 'Programming', 'Developer', 'Algorithm',
  'Function', 'Variable', 'Database', 'Internet', 'Website',
  'Application', 'Software', 'Hardware', 'Network', 'Server',
  'Client', 'Browser', 'Console', 'Framework', 'Library',
  'Document', 'Element', 'Attribute', 'Property', 'Method',
  'Object', 'Array', 'String', 'Number', 'Boolean',
  'Condition', 'Loop', 'Switch', 'Class', 'Constructor',
  'Interface', 'Module', 'Package', 'Repository', 'Version',
  'Debug', 'Error', 'Warning', 'Exception', 'Syntax',
  'Performance', 'Security', 'Encryption', 'Authentication', 'Authorization',
  'Cloud', 'Container', 'Virtual', 'Machine', 'Process',
  'Memory', 'Storage', 'Cache', 'Queue', 'Stack'
];

let currentWord = '';
let score = 0;
let highScore = localStorage.getItem('wordBombHighScore') || 0;
let gameRunning = false;
let timeLeft = 5;
let timerInterval = null;

const elements = {
  word: document.getElementById('word'),
  input: document.getElementById('input'),
  score: document.getElementById('score'),
  timer: document.getElementById('timer'),
  highscore: document.getElementById('highscore'),
  startBtn: document.getElementById('start'),
  restartBtn: document.getElementById('restart'),
  bomb: document.querySelector('.bomb'),
  overlay: document.getElementById('overlay'),
  overlayRestart: document.getElementById('overlay-restart'),
  endTitle: document.getElementById('end-title'),
  endMsg: document.getElementById('end-msg'),
  finalScore: document.getElementById('final-score')
};

// Initialize
elements.highscore.textContent = highScore;

// Event listeners
elements.startBtn.addEventListener('click', startGame);
elements.restartBtn.addEventListener('click', startGame);
elements.overlayRestart.addEventListener('click', startGame);
elements.input.addEventListener('input', handleInput);
elements.input.addEventListener('keypress', (e) => {
  if (e.key === 'Enter') handleInput();
});

function startGame() {
  score = 0;
  timeLeft = 5;
  gameRunning = true;
  
  elements.score.textContent = score;
  elements.input.value = '';
  elements.input.focus();
  elements.startBtn.classList.add('hidden');
  elements.restartBtn.classList.remove('hidden');
  elements.overlay.classList.add('hidden');
  elements.bomb.classList.remove('exploding');
  
  nextWord();
  startTimer();
}

function nextWord() {
  currentWord = words[Math.floor(Math.random() * words.length)];
  elements.word.textContent = currentWord;
  elements.input.value = '';
  elements.input.classList.remove('correct', 'wrong');
  timeLeft = 5;
  elements.timer.textContent = '5.0';
}

function startTimer() {
  if (timerInterval) clearInterval(timerInterval);
  
  const startTime = Date.now();
  const duration = 5000;
  
  timerInterval = setInterval(() => {
    const elapsed = Date.now() - startTime;
    timeLeft = Math.max(0, 5 - elapsed / 1000);
    elements.timer.textContent = timeLeft.toFixed(1);
    
    if (timeLeft <= 0) {
      endGame();
    }
  }, 50);
}

function handleInput() {
  if (!gameRunning) return;
  
  const input = elements.input.value.trim();
  
  if (input.toLowerCase() === currentWord.toLowerCase()) {
    elements.input.classList.add('correct');
    elements.input.classList.remove('wrong');
    
    score++;
    elements.score.textContent = score;
    
    setTimeout(() => {
      nextWord();
    }, 300);
  } else if (input.length > 0 && !currentWord.toLowerCase().startsWith(input.toLowerCase())) {
    elements.input.classList.add('wrong');
    elements.input.classList.remove('correct');
  } else {
    elements.input.classList.remove('correct', 'wrong');
  }
}

function endGame() {
  gameRunning = false;
  clearInterval(timerInterval);
  
  // Update high score
  if (score > highScore) {
    highScore = score;
    localStorage.setItem('wordBombHighScore', highScore);
    elements.highscore.textContent = highScore;
  }
  
  // Show end screen
  elements.bomb.classList.add('exploding');
  elements.startBtn.classList.remove('hidden');
  elements.restartBtn.classList.add('hidden');
  elements.input.disabled = true;
  
  setTimeout(() => {
    elements.endTitle.textContent = score > 0 ? 'Game Over!' : 'Boom!';
    elements.finalScore.textContent = score;
    elements.endMsg.innerHTML = score > 0 
      ? `You scored <strong>${score}</strong> word${score !== 1 ? 's' : ''}!`
      : 'You scored <strong>0</strong>. Try again!';
    elements.overlay.classList.remove('hidden');
    elements.input.disabled = false;
  }, 600);
}

// Focus input on load
elements.input.focus();
