const toggleBtn = document.getElementById('theme-toggle');
const html = document.documentElement;

// Check saved preference
if (localStorage.getItem('theme') === 'dark') {
    html.classList.add('dark');
}

toggleBtn.addEventListener('click', () => {
    if (html.classList.contains('dark')) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
});
