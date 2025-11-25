import './bootstrap';
import { Particles } from './particles';

document.addEventListener('DOMContentLoaded', () => {
    new Particles('particles-canvas');
});

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
