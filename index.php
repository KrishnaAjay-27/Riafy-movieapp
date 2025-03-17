<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MovieFlix</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo">MovieFlix</div>
            <div class="nav-links">
                <a href="#home" class="nav-link active">Home</a>
                <a href="#about" class="nav-link">About</a>
                <a href="#contact" class="nav-link">Contact</a>
            </div>
            <div class="auth-buttons">
                <button class="btn btn-primary" onclick="window.location.href='login.php'">Login</button>
                <button class="btn btn-primary" onclick="window.location.href='register.php'">Register</button>
            </div>
        </nav>
    </header>

    <section id="home" class="section active">
        <div class="hero">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <!-- Slides will be populated dynamically -->
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>

        <div class="movies-section">
            <h2 class="section-title">Popular Movies</h2>
            <div class="movies-grid" id="movies-grid">
                <!-- Movies will be loaded here -->
            </div>
        </div>
    </section>

    <section id="about" class="section">
        <div class="about-container">
            <h2 class="section-title">About MovieFlix</h2>
            <div class="about-grid">
                <div class="about-card">
                    <div class="about-icon">🎬</div>
                    <h3>Vast Collection</h3>
                    <p>Access thousands of movies from different genres, eras, and countries. From classic masterpieces to the latest blockbusters.</p>
                </div>
                <div class="about-card">
                    <div class="about-icon">🌟</div>
                    <h3>High Quality</h3>
                    <p>Enjoy movies in stunning HD and 4K quality, with crystal clear audio for the perfect viewing experience.</p>
                </div>
                <div class="about-card">
                    <div class="about-icon">📱</div>
                    <h3>Watch Anywhere</h3>
                    <p>Stream on any device - smart TVs, smartphones, tablets, or laptops. Your entertainment follows you everywhere.</p>
                </div>
                <div class="about-card">
                    <div class="about-icon">💫</div>
                    <h3>Personalized</h3>
                    <p>Get personalized recommendations based on your watching history and preferences.</p>
                </div>
            </div>
            <div class="about-features">
                <div class="feature-text">
                    <h3>Why Choose MovieFlix?</h3>
                    <ul class="feature-list">
                        <li>✓ No ads or interruptions</li>
                        <li>✓ New movies added weekly</li>
                        <li>✓ Cancel anytime</li>
                        <li>✓ Family-friendly content options</li>
                        <li>✓ Multiple profiles for family members</li>
                    </ul>
                </div>
                <div class="stats-container">
                    <div class="stat-item">
                        <span class="stat-number">10K+</span>
                        <span class="stat-label">Movies</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">5M+</span>
                        <span class="stat-label">Users</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">190+</span>
                        <span class="stat-label">Countries</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="section">
        <div class="contact-container">
            <h2 class="section-title">Get in Touch</h2>
            <div class="contact-content">
                <div class="contact-info">
                    <div class="contact-card">
                        <div class="contact-icon">📧</div>
                        <h3>Email Us</h3>
                        <p>We'd love to hear from you!</p>
                    </div>
                </div>
                <form class="contact-form">
                    <div class="form-group">
                        <input type="email" placeholder="Your Email" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2025 MovieFlix. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // OMDB API Integration
        const API_KEY = 'c51f139'; // Replace with your OMDB API key
        
        // Featured movies for slider
        const featuredMovies = [
            'Avengers: Endgame',
            'Dune',
            'Spider-Man: No Way Home',
            'The Batman',
            'Top Gun: Maverick'
        ];

        async function initializeSlider() {
            const swiperWrapper = document.querySelector('.swiper-wrapper');
            
            for (const movie of featuredMovies) {
                try {
                    const response = await fetch(`https://www.omdbapi.com/?t=${encodeURIComponent(movie)}&apikey=${API_KEY}`);
                    const data = await response.json();
                    
                    if (data.Response === 'True') {
                        const slide = `
                            <div class="swiper-slide">
                                <img src="${data.Poster}" alt="${data.Title}">
                                <div class="slide-content">
                                    <h2>${data.Title}</h2>
                                    <p>${data.Year} • ${data.Genre}</p>
                                    <button class="btn btn-primary">Watch Now</button>
                                </div>
                            </div>
                        `;
                        swiperWrapper.innerHTML += slide;
                    }
                } catch (error) {
                    console.error('Error fetching movie:', error);
                }
            }

            // Initialize Swiper
            new Swiper(".mySwiper", {
                effect: "fade",
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
            });
        }

        async function fetchPopularMovies() {
            const movieTitles = ['The Shawshank Redemption', 'The Godfather', 'The Dark Knight', 
                               'Pulp Fiction', 'Fight Club', 'Inception', 'Interstellar', 
                               'The Matrix', 'Goodfellas', 'Se7en'];
            
            const moviesGrid = document.getElementById('movies-grid');
            
            for (const title of movieTitles) {
                try {
                    const response = await fetch(`https://www.omdbapi.com/?t=${encodeURIComponent(title)}&apikey=${API_KEY}`);
                    const data = await response.json();
                    
                    if (data.Response === 'True') {
                        const movieCard = `
                            <div class="movie-card">
                                <img src="${data.Poster}" alt="${data.Title}" class="movie-poster">
                                <div class="movie-info">
                                    <h3 class="movie-title">${data.Title}</h3>
                                    <p class="movie-year">${data.Year}</p>
                                </div>
                            </div>
                        `;
                        moviesGrid.innerHTML += movieCard;
                    }
                } catch (error) {
                    console.error('Error fetching movie:', error);
                }
            }
        }

        // Navigation
        const navLinks = document.querySelectorAll('.nav-link');
        const sections = document.querySelectorAll('.section');

        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);
                
                sections.forEach(section => {
                    section.classList.remove('active');
                });
                
                targetSection.classList.add('active');
                
                navLinks.forEach(link => link.classList.remove('active'));
                link.classList.add('active');

                // Smooth scroll to section
                targetSection.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });

        // Load movies when the page loads
        document.addEventListener('DOMContentLoaded', () => {
            initializeSlider();
            fetchPopularMovies();
        });
    </script>

    <!-- Add this before </body> -->
    <div id="movieModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2></h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Content will be dynamically populated -->
            </div>
        </div>
    </div>
</body>
</html>
