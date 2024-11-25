function toggleWaitLoader(show) {
    const loader = document.getElementById('wait-loader'); // Corrected method name
    if (show) {
        loader.style.display = 'flex'; // Assuming the loader is a flex container
    } else {
        loader.style.display = 'none';
    }
}
