# Word Bomb

A fast-paced typing game where you race against time to type random words before the bomb explodes!

## 🎮 How to Play

1. Click **Start Game** to begin
2. A random word appears on the screen
3. Type the complete word and press Enter (or just continue typing)
4. Correct words score points and trigger the next round
5. You have 5 seconds per word—don't let the bomb explode!
6. Your high score is automatically saved

## 🚀 Deployment to Plesk

### Option 1: Upload via Plesk File Manager (Easiest)

1. Log in to your Plesk panel
2. Go to **File Manager** → your domain (e.g., magazijn.rickhartmans.nl)
3. Navigate to the **httpdocs** folder (or public_html)
4. Upload these files:
   - `index.html`
   - `css/styles.css`
   - `js/app.js`
5. Visit your domain in a browser—it should work immediately!

### Option 2: Upload via FTP

1. Use an FTP client (FileZilla, WinSCP, etc.)
2. Connect to your server with your FTP credentials
3. Navigate to the **public_html** or **httpdocs** folder
4. Upload the project files maintaining the folder structure:
   ```
   httpdocs/
   ├── index.html
   ├── css/
   │   └── styles.css
   └── js/
       └── app.js
   ```
5. Visit your domain

### Option 3: Using Git (If available on your Plesk)

1. In Plesk, go to **Git** repository section
2. Clone your GitHub repo directly to the public folder
3. The files will be pulled automatically

## 📋 Features

- ✨ Beautiful gradient UI with smooth animations
- 🎯 Real-time typing validation with visual feedback
- ⏱️ Countdown timer with decimal precision
- 🏆 Persistent high score using browser localStorage
- 📱 Fully responsive design (works on mobile, tablet, desktop)
- 🎨 Polished animations and transitions
- ⚡ No dependencies—pure HTML, CSS, and JavaScript

## 📁 Project Structure

```
word-bomb/
├── index.html          # Main HTML file
├── css/
│   └── styles.css      # All styling and animations
└── js/
    └── app.js          # Game logic
```

## 🎨 Customization

### Change Colors

Edit `css/styles.css` and modify the gradient colors:
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Add More Words

Edit `js/app.js` and add words to the `words` array:
```javascript
const words = [
  'YourWord', 'AnotherWord', /* ... more words */
];
```

### Adjust Game Difficulty

In `js/app.js`, change the `timeLeft` value (currently 5 seconds):
```javascript
let timeLeft = 5; // Change this number
```

## 🔧 Technical Details

- **No build process needed** – just open `index.html` in a browser
- **No database required** – scores stored in browser localStorage
- **Works offline** – once loaded, the game runs without internet
- **Mobile friendly** – fully responsive with touch support

## 📊 Leaderboard (server)

This project now includes a simple server-backed leaderboard. It uses two PHP endpoints in `api/`:

- `api/save_score.php` — accepts POST `name` and `score` to save a score
- `api/get_scores.php` — returns the top scores as JSON (query param `max`)

Setup notes for Plesk:

1. Ensure PHP is enabled for the domain (most Plesk plans support PHP).
2. Upload the `api/` folder alongside `index.html` (preserve structure).
3. Make the file `api/scores.json` writable by the webserver user (chmod 0664 or 0666 as needed).
4. Visit the site and test a game — the client will POST scores automatically when a game ends.

Security and limits:

- The PHP scripts perform simple sanitization and cap scores. This is intended as a lightweight leaderboard and not hardened for production abuse.
- For a multi-player or public deployment, consider adding server-side rate limiting, authentication, or storing scores in a database.

### SQLite (recommended)

This project can use SQLite for a lightweight, reliable leaderboard without creating a separate database server. Steps:

1. Upload the `api/` folder to your site (keep the files in place).
2. Ensure the webserver user can create and write files in `api/` (so `leaderboard.sqlite` can be created).
3. Optionally, if you previously had `api/scores.json`, run the migration once by visiting (in a browser or via CLI):

   - Browser: `https://your-domain.tld/Word-bomb/api/migrate_json_to_sqlite.php`
   - CLI (from project folder):
     ```powershell
     php api/migrate_json_to_sqlite.php
     ```

The PHP endpoints `api/save_score.php` and `api/get_scores.php` will automatically use `leaderboard.sqlite`.

## 📄 License

Feel free to use and modify for personal or commercial projects.

---

**Created for easy Plesk deployment.** Just upload and play! 🎮
