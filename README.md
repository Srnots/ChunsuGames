# 🎮 Universal Game Library

A beautiful, modern web application for managing a shared collection of games. Built with HTML, CSS, JavaScript, and PHP with JSON file storage.

## ✨ Features

- 🌐 **Universal Sharing**: Games are shared between all users
- 🎯 **Auto-Detection**: Automatically fetches game titles and icons
- 🔄 **Real-time Sync**: Auto-refreshes every 15 seconds
- 📱 **Responsive Design**: Works perfectly on mobile and desktop
- 🎨 **Modern UI**: Beautiful gradient design with smooth animations
- 🔍 **Smart Validation**: Prevents duplicate URLs and validates inputs
- 📊 **Server Status**: Real-time connection and database status
- 🚀 **Fast & Lightweight**: Pure vanilla JavaScript, no frameworks

## 📁 Files

- `index.html` - Main web application
- `server.php` - Backend API server
- `database.json` - JSON database for storing games
- `test.php` - Server testing and diagnostics
- `README.md` - This documentation

## 🚀 Quick Setup

### 1. Upload Files
Upload all files to your web server directory:
```
your-domain.com/games/
├── index.html
├── server.php
├── database.json
├── test.php
└── README.md
```

### 2. Set Permissions
Make sure PHP can read and write files:
```bash
chmod 644 *.html *.php *.json *.md
chmod 755 .
```

### 3. Test Setup
Visit: `http://your-domain.com/games/test.php`

This will check:
- ✅ PHP version and functionality
- ✅ File permissions
- ✅ Database access
- ✅ API endpoints

### 4. Access Game Library
Visit: `http://your-domain.com/games/index.html`

## 🔧 Requirements

- **Web Server**: Apache, Nginx, or any web server
- **PHP**: Version 7.0 or higher
- **Permissions**: Read/write access to directory
- **Protocol**: Must use HTTP/HTTPS (not file://)

## 📊 How It Works

### Frontend (index.html)
- Modern responsive design with CSS Grid and Flexbox
- Vanilla JavaScript for all functionality
- Automatic favicon detection and fallbacks
- Real-time status indicators
- Toast notifications for user feedback

### Backend (server.php)
- RESTful API with proper HTTP methods
- JSON file-based database
- Input validation and sanitization
- Error handling and logging
- Automatic backups on updates

### Database (database.json)
```json
{
  "games": [
    {
      "id": "game_1234567890",
      "title": "Game Name",
      "url": "https://example.com/game",
      "icon": "https://example.com/favicon.ico",
      "addedAt": "2024-01-01T00:00:00.000Z",
      "addedBy": "User"
    }
  ],
  "metadata": {
    "totalGames": 1,
    "lastUpdated": "2024-01-01T00:00:00.000Z",
    "version": "1.0"
  }
}
```

## 🎯 Usage

### Adding Games
1. Enter the game URL (required)
2. Optionally enter a custom title
3. Click "Add Game"
4. Game appears for everyone instantly

### Playing Games
- Click on any game card to open in new tab
- Or click the "🎮 Play" button

### Deleting Games
- Click the "🗑️ Delete" button
- Confirm the action
- Game is removed for everyone

### Refreshing
- Manual: Click "🔄 Refresh" button
- Automatic: Every 15 seconds in background

## 🐛 Troubleshooting

### "Failed to load games from server"
- **Check PHP**: Make sure PHP is enabled on your hosting
- **Check Permissions**: Ensure files are readable/writable
- **Check Protocol**: Use HTTP/HTTPS, not file://
- **Run Test**: Visit `test.php` for diagnostics

### "Cannot connect to server"
- Verify `server.php` exists and is accessible
- Check web server error logs
- Ensure proper file permissions

### Games not saving
- Check if `database.json` is writable
- Verify directory permissions
- Check PHP error logs
- Run `test.php` for file write test

### Icons not loading
- Some sites block cross-origin requests
- Fallback letters will display instead
- This is normal behavior

## 🔐 Security Notes

- Input validation on both client and server
- SQL injection not applicable (JSON file storage)
- XSS prevention with HTML escaping
- No sensitive data stored
- File-based permissions only

## 🚀 Performance

- **Fast Loading**: Optimized CSS and JavaScript
- **Auto-refresh**: Smart background updates
- **Caching**: Browser caching for static assets
- **Lightweight**: No external dependencies

## 🎨 Customization

### Change Colors
Edit CSS variables in `index.html`:
```css
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --success-color: #4CAF50;
    /* ... */
}
```

### Modify Refresh Rate
Change auto-refresh interval in JavaScript:
```javascript
// Change 15000 (15 seconds) to your preference
setInterval(() => {
    if (!this.isLoading && !document.hidden) {
        this.loadGames(true);
    }
}, 15000);
```

### Adjust Game Limits
Edit `server.php`:
```php
define('MAX_GAMES', 1000); // Change limit
```

## 📱 Mobile Support

- Fully responsive design
- Touch-friendly interface
- Optimized for all screen sizes
- Progressive enhancement

## 🔄 Updates

To update the system:
1. Backup your `database.json` file
2. Replace other files with new versions
3. Test functionality
4. Restore games if needed

## 📞 Support

If you encounter issues:
1. Run `test.php` for diagnostics
2. Check browser console for errors
3. Verify server requirements
4. Check file permissions

## 📄 License

Free to use and modify for personal and commercial projects.

---

**Enjoy your Universal Game Library! 🎮**