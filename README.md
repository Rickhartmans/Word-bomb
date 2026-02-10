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

## 📄 License

Feel free to use and modify for personal or commercial projects.

---

**Created for easy Plesk deployment.** Just upload and play! 🎮
