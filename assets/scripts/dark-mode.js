const modeButton = document.getElementById('mode-button');
document.addEventListener('DOMContentLoaded', () => {
    const darkModeStored = localStorage.getItem('mode') || 'light';
    document.documentElement.classList.add(darkModeStored);
});
modeButton.addEventListener('click', (e) => {
    const darkModeStored = localStorage.getItem('mode') || 'light';

    if (darkModeStored === 'dark') {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('mode', 'light');
    } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem('mode', 'dark');
    }
});
