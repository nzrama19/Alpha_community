function switchTab(event, tabName) {
    event.preventDefault();

    // Masquer tous les tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));

    // Désactiver tous les boutons
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(btn => btn.classList.remove('active'));

    // Afficher le tab sélectionné
    document.getElementById(tabName).classList.add('active');
    event.target.closest('.tab-button').classList.add('active');
}
