
function toggleDropdown() {
    const dropdown = document.getElementById("userDropdown");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

window.onclick = function (event) {
    if (!event.target.matches('.username')) {
        const dropdown = document.getElementById("userDropdown");
        if (dropdown && dropdown.style.display === "block") {
            dropdown.style.display = "none";
        }
    }
}