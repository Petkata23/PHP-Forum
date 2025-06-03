# 🚀 Forum
### Modern PHP Forum System

Easy-to-use forum platform with user profiles, categories, and modern design. Built with PHP and MySQL.

## ✨ Features

### 👥 Users
- User registration and login
- Custom avatars
- User profiles with activity history

### 📄 Content
- Category creation and management
- Rich text formatting for topics
- Real-time replies and discussions
- Content search

### 🎨 Design
- Modern responsive design
- Dark/Light theme support
- Mobile-optimized interface

## 🛠️ Technical Details

### Project Structure
```
forum/
├── views/      # PHP view files
├── includes/   # Configuration and common functions
├── layout/     # CSS and other assets
└── uploads/    # Uploaded files (avatars)
```

### Requirements
- PHP 8.0+
- MySQL 5.7+
- Apache/Nginx server
- mod_rewrite enabled

## 🚀 Installation

1. Clone the repository:
```bash
git clone https://github.com/Petkata23/PHP-Forum.git
```

2. Create MySQL database and import structure:
```sql
CREATE DATABASE forum;
```

3. Configure database connection in `includes/config.php`

4. Set proper permissions:
```bash
chmod 755 uploads/
chmod 644 uploads/.htaccess
```

## 🔒 Security
- SQL injection protection
- Password hashing with bcrypt
- Input validation
- Upload file protection

## 📝 License
This project is licensed under the [MIT License](LICENSE) 
