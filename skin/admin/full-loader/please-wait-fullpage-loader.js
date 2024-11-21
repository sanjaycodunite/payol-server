function toggleWaitLoader(show) {
    const loader = document.getElementsByClassName('wait-loader');
    if (show) {
        loader.style.display = 'flex';
    } else {
        loader.style.display = 'none';
    }
}