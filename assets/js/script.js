function updateJam() {
    const waktu = new Date();
    const jam = waktu.getHours().toString().padStart(2, '0');
    const menit = waktu.getMinutes().toString().padStart(2, '0');
    const detik = waktu.getSeconds().toString().padStart(2, '0');
    document.getElementById("jam").textContent = `${jam}:${menit}:${detik}`;
}
setInterval(updateJam, 1000);
updateJam();


