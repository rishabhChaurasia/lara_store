export class Particles {
    constructor(canvasId) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) return;

        this.ctx = this.canvas.getContext('2d');
        this.particles = [];
        this.resizeTimeout = null;
        
        // Configuration
        this.config = {
            particleCount: 100,
            connectionDistance: 100,
            baseSpeed: 0.5,
            colors: {
                light: {
                    particle: 'rgba(0, 0, 0, 0.5)',
                    line: 'rgba(0, 0, 0, 0.1)'
                },
                dark: {
                    particle: 'rgba(255, 255, 255, 0.5)',
                    line: 'rgba(255, 255, 255, 0.1)'
                }
            }
        };

        this.init();
    }

    init() {
        this.resize();
        this.createParticles();
        this.animate();
        
        window.addEventListener('resize', () => {
            clearTimeout(this.resizeTimeout);
            this.resizeTimeout = setTimeout(() => this.resize(), 100);
        });
    }

    resize() {
        // Set canvas size to parent container
        const parent = this.canvas.parentElement;
        this.canvas.width = parent.clientWidth;
        this.canvas.height = parent.clientHeight;
        
        // Re-create particles if dimensions change significantly
        if (this.particles.length === 0) {
            this.createParticles();
        }
    }

    isDarkMode() {
        return document.documentElement.classList.contains('dark');
    }

    createParticles() {
        this.particles = [];
        const count = Math.floor((this.canvas.width * this.canvas.height) / 10000); // Responsive count
        
        for (let i = 0; i < count; i++) {
            this.particles.push({
                x: Math.random() * this.canvas.width,
                y: Math.random() * this.canvas.height,
                vx: (Math.random() - 0.5) * this.config.baseSpeed,
                vy: (Math.random() - 0.5) * this.config.baseSpeed,
                size: Math.random() * 2 + 1
            });
        }
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        const isDark = this.isDarkMode();
        const colors = isDark ? this.config.colors.dark : this.config.colors.light;

        this.particles.forEach((p, index) => {
            // Update position
            p.x += p.vx;
            p.y += p.vy;

            // Bounce off edges
            if (p.x < 0 || p.x > this.canvas.width) p.vx *= -1;
            if (p.y < 0 || p.y > this.canvas.height) p.vy *= -1;

            // Draw particle
            this.ctx.beginPath();
            this.ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
            this.ctx.fillStyle = colors.particle;
            this.ctx.fill();

            // Draw connections
            for (let j = index + 1; j < this.particles.length; j++) {
                const p2 = this.particles[j];
                const dx = p.x - p2.x;
                const dy = p.y - p2.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < this.config.connectionDistance) {
                    this.ctx.beginPath();
                    this.ctx.moveTo(p.x, p.y);
                    this.ctx.lineTo(p2.x, p2.y);
                    this.ctx.strokeStyle = colors.line;
                    this.ctx.lineWidth = 1 - distance / this.config.connectionDistance;
                    this.ctx.stroke();
                }
            }
        });

        requestAnimationFrame(() => this.animate());
    }
}
