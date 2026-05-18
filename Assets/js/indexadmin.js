document.querySelectorAll('.menu a').forEach(link => {
    if(link.textContent === 'Keluar') {
        link.addEventListener('click', function(e) {
            if(!confirm("Apakah Anda yakin ingin logout?")) {
                e.preventDefault();
            }
        });
    }
});
