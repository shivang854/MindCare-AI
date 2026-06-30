🧠 MindCare AI

An AI-Powered Mental Wellness Platform built using PHP, MySQL, Bootstrap, JavaScript, and Google Gemini API.

📖 About the Project

MindCare AI is a full-stack web application designed to support users in their mental wellness journey through intelligent AI conversations, mood tracking, journaling, wellness analytics, and emergency support features.

The platform enables users to interact with an AI companion, monitor emotional well-being, maintain private journals, analyze mood trends, and securely manage their conversations in a responsive and user-friendly environment.

This project was developed as a portfolio and placement project to demonstrate full-stack web development, API integration, secure authentication, database management, and modern web application design.

✨ Features
🤖 AI Features
AI-powered chatbot using Google Gemini API
Intelligent emotional support conversations
Automatic mood detection from user messages
Risk level detection (Low, Medium, High, Critical)
Voice input using Speech Recognition
AI voice responses using Text-to-Speech
💬 Chat Features
Real-time AI conversations
Chat history management
Search previous conversations
Typing animation
Dark Mode support
Responsive chat interface
📊 Wellness Features
Personal Journal System
Mood Analytics Dashboard
Mood trend visualization using Chart.js
AI-generated wellness insights
PDF report generation
👥 Safety Features
Trusted Emergency Contact
Emergency Email Notification
Critical Risk Detection
Session-based authentication
🔐 Security Features
Password hashing
Prepared SQL statements
Protected environment variables (.env)
Secure API integration
Session management
Input validation
🎨 User Experience
Modern Bootstrap 5 interface
Mobile responsive design
Clean dashboard
Smooth animations (AOS)
Fast and intuitive navigation


🛠️ Tech Stack
Frontend
HTML5
CSS3
Bootstrap 5
JavaScript
Backend
PHP 8
MySQL
AI & APIs
Google Gemini API
Web Speech API (Speech Recognition)
Speech Synthesis API (Text-to-Speech)
Libraries
Chart.js
PHPMailer
Composer
Dotenv
DomPDF
Development Tools
XAMPP
phpMyAdmin
VS Code
Git & GitHub



⚙️ Installation

Follow these steps to set up the project on your local machine.

1. Clone the Repository
git clone https://github.com/your-username/MindCare-AI.git
2. Move the Project

Copy the project folder into your XAMPP htdocs directory.

C:/xampp/htdocs/MindCare-AI
3. Install Dependencies
composer install
4. Configure Environment Variables

Create a .env file in the project root and add:

GEMINI_API_KEY=YOUR_GEMINI_API_KEY
SMTP_EMAIL=YOUR_EMAIL
SMTP_PASSWORD=YOUR_APP_PASSWORD
5. Create the Database
Open phpMyAdmin
Create a database named:
mindcare_ai
Import the provided SQL file.
6. Start the Server
Start Apache
Start MySQL

Open the application in your browser:

http://localhost/MindCare-AI

Passwords are securely hashed before storage.
SQL Injection protection using prepared statements.
Session-based authentication.
Sensitive credentials stored securely in .env.
Protected Composer and environment configuration files.
Input validation and secure database operations.


🚀 Future Improvements
Profile Photo Upload
Forgot Password (Email OTP)
AI Memory for Personalized Conversations
Cloud Deployment
Mobile Application
Multi-language Support
Advanced Mood Prediction using Machine Learning


👨‍💻 Developer

Shivang Tiwari

🎓 B.Tech Computer Science Engineering
🏫 Bennett University
💻 Full Stack Web Development & AI Enthusiast


📄 License
This project is created for educational, learning, and portfolio purposes.