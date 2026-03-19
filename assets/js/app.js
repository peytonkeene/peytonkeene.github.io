document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.querySelector('.sidebar');
  const topbar = document.querySelector('.topbar');

  if (!sidebar || !topbar) {
    return;
  }

  topbar.addEventListener('dblclick', () => {
    sidebar.classList.toggle('sidebar-condensed');
  });
});
