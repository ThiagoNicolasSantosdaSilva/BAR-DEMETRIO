// MENU HAMBURGUER
const menu = document.getElementById('mobile-menu');
const navList = document.querySelector('.nav-list');

if (menu) {
  menu.addEventListener('click', () => {
    navList.classList.toggle('active');
    menu.classList.toggle('toggle');
  });
}
