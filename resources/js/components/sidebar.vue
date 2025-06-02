<template>
  <nav id="sidebar" :class="{ active: isActive }">
    <ul class="sidebar-wrapper">
      <li v-for="item in menuItems" :key="item.id" class="sidebar-item" :class="{ 'has-sub': item.submenu, active: item.active }">
        <a href="#" class="sidebar-link" @click.prevent="toggleSubmenu(item)">
          {{ item.title }}
        </a>
        <ul v-if="item.submenu" class="submenu" v-show="item.active">
          <li v-for="sub in item.submenu" :key="sub.id">
            <a :href="sub.link">{{ sub.title }}</a>
          </li>
        </ul>
      </li>
    </ul>
    <button class="burger-btn" @click="toggleSidebar">Toggle Sidebar</button>
  </nav>
</template>

<script>
export default {
  data() {
    return {
      isActive: true,
      menuItems: [
        { id: 1, title: 'Dashboard', active: false },
        { id: 2, title: 'Usuarios', active: false, submenu: [
          { id: 21, title: 'Agregar', link: '/usuarios/agregar' },
          { id: 22, title: 'Listado', link: '/usuarios/listado' }
        ]},
        // agrega tus items reales aquí
      ],
    };
  },
  methods: {
    toggleSidebar() {
      this.isActive = !this.isActive;
    },
    toggleSubmenu(item) {
      item.active = !item.active;
    },
  },
};
</script>

<style scoped>
/* tus estilos para el sidebar */
#sidebar {
  width: 250px;
  transition: all 0.3s ease;
}
#sidebar.active {
  margin-left: 0;
}
#sidebar:not(.active) {
  margin-left: -250px;
}
.submenu {
  display: none;
  padding-left: 1rem;
}
.submenu[style*="display: block"] {
  display: block;
}
</style>
